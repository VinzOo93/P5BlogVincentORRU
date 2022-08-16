<?php

namespace App\Validator;

use App\Manager\ArticleManager;
use App\Router\Request;

class ArticleCreationValidator
{
    private int $limitStr = 10;
    private int $max = 255;
    private string $needle = ';';

    public function validate(array $articleCreation): bool
    {
        $rules = $this->setUpRules();
        $articleManager = new ArticleManager();

        try {
            foreach ($rules as $property => $rule) {

                foreach ($rule as $ru => $value) {
                    switch ($ru) {
                        case 'notBlank':
                            if ($articleCreation[$property] === '') {
                                return $this->redirectToAddArticleForm("Les saisies : titre et contenu sont obligatoire !");
                            }
                        case 'min':
                            if (strlen($articleCreation[$property]) < $value && $value === $this->limitStr) {
                                return $this->redirectToAddArticleForm("Le contenu doit au minimum comprendre $this->limitStr caractères !");
                            }
                        case 'max':
                            if (strlen($articleCreation[$property]) > $value && $value === $this->max) {
                                return $this->redirectToAddArticleForm("Les champs de saisie sauf celui du contenu doivent au maximum comprendre $this->max caractères !");
                            }
                        case 'jpg':
                            if ($articleCreation[$property] === $value && $property === 'image') {
                                return $this->redirectToAddArticleForm("L'ajout d'image est obligatoire et doit au être format JPG !");
                            }
                        case 'separated':
                            if (explode($this->needle, $articleCreation[$property]) === false && $property === 'tags'){
                                $this->redirectToAddArticleForm("Veuillez remplir le champ tag comme suivi => bateau;chat;chocolat !");
                            }
                        case 'uniq':
                            if ($articleManager->selectOneArticleByTitle($articleCreation[$property]) && $property === 'title') {
                                return $this->redirectToAddArticleForm("Le titre saisie est déja utilisé !");
                            }
                    }
                }
            }
            return true;

        } catch (\Exception $exception) {
            return $this->redirectToAddArticleForm("Erreur lors de l'ajoute de l'utilisateur $exception");
        }
    }

    private function setUpRules(): array
    {
        return [
            'title' =>
                [
                    'notBlank',
                    'max' => $this->max,
                    'uniq'
                ],
            'tags' =>
                [
                    'max' => $this->max,
                    'separated' => $this->needle
                ],
            'content' =>
                [
                    'notBlank',
                    'min' => $this->limitStr
                ],
            'image' =>
                [   
                    'jpg' => false,
                    'max' => $this->max
                ]
        ];
    }

    private function redirectToAddArticleForm($str): bool
    {
        $request = new  Request();
        $request->redirectToRoute('newPost', [
            'error' => $str
        ]);
        return false;
    }
}
<?php

namespace App\Validator;

use App\Manager\ArticleManager;
use App\Router\Request;

class ArticleUpdateValidator
{
    private int $limitStr = 10;
    private int $max = 255;
    private string $needle = ';';

    public function validate(array $articleUpdate, $slug): bool
    {
        $rules = $this->setUpRules();
        $articleManager = new ArticleManager();

        try {
            foreach ($rules as $property => $rule) {

                foreach ($rule as $ru => $value) {
                    switch ($ru) {
                        case 'notBlank':
                            if ($articleUpdate[$property] === '') {
                                return $this->redirectToUpdateArticleForm("Les saisies : titre et contenu sont obligatoire !", $slug);
                            }
                        case 'min':
                            if (strlen($articleUpdate[$property]) < $value && $value === $this->limitStr) {
                                return $this->redirectToUpdateArticleForm("Le contenu doit au minimum comprendre $this->limitStr caractères !", $slug);
                            }
                        case 'max':
                            if (strlen($articleUpdate[$property]) > $value && $value === $this->max) {
                                return $this->redirectToUpdateArticleForm("Les champs de saisie sauf celui du contenu doivent au maximum comprendre $this->max caractères !", $slug);
                            }
                        case 'jpg':
                            if ($articleUpdate[$property] === $value && $property === 'image') {
                                return $this->redirectToUpdateArticleForm("L'ajout d'image est obligatoire et doit au être format JPG !", $slug);
                            }
                        case 'separated':
                            if (explode($this->needle, $articleUpdate[$property]) === false && $property === 'tags'){
                                $this->redirectToUpdateArticleForm("Veuillez remplir le champ tag comme suivi => bateau;chat;chocolat !", $slug);
                            }
                        case 'uniq':
                            if ($property === 'title'){
                                $registredTitle = $articleManager->selectOneArticleByTitle($articleUpdate[$property]);
                                $articleFormId = $articleManager->selectIdArticleBySlug($slug);
                                if ($articleUpdate[$property] === $registredTitle['title'] && $articleFormId['id_article'] !== $registredTitle['id_article']) {
                                    return $this->redirectToUpdateArticleForm("Le titre saisie est déja utilisé !", $slug);
                                }
                            }

                    }
                }
            }
            return true;

        } catch (\Exception $exception) {
            return $this->redirectToUpdateArticleForm("Erreur lors de l'ajoute de l'utilisateur $exception", $slug);
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

    private function redirectToUpdateArticleForm($str, $slug): bool
    {
        $request = new  Request();
        $request->redirectToRoute('showFormUpdateArticle', [
            'error' => $str,
            'slug' => $slug
        ]);
        return false;
    }
}
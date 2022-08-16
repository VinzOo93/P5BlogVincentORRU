<?php

namespace App\Validator;

use App\Router\Request;

class CommentCreationValidator
{
    private int $limitStr = 10;

    public function validate(array $commentCreation, $slug): bool
    {
        $rules = $this->setUpRules();
        try {
            foreach ($rules as $property => $rule) {
                foreach ($rule as $ru => $value) {
                    switch ($ru) {
                        case 'notBlank':
                            if ($commentCreation[$property] === '') {
                                return $this->redirecToArticle("Un commentaire ne peut pas être publié vide !", $slug);
                            }
                        case 'min':
                            if (strlen($commentCreation[$property]) < $value) {
                                return $this->redirecToArticle("La taille d'un commentaire doit être supérieur à $this->limitStr caractères", $slug);
                            }
                    }
                }
            }
            return true;

        } catch (\Exception $exception) {
            return $this->redirecToArticle("Erreur lors de l'ajout du commentaire $exception", $slug);
        }
    }

    private function setUpRules(): array
    {
        return [
            'content' =>
                [
                    'notBlank',
                    'min' => $this->limitStr
                ]
        ];
    }

    private function redirecToArticle($str, $slug): bool
    {
        $request = new  Request();
        $request->redirectToRoute('article', [
            'slug' => $slug,
            'error' => $str
        ]);
        return false;
    }

}
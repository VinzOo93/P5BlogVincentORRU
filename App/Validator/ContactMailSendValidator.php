<?php

namespace App\Validator;

use App\Router\Request;

class ContactMailSendValidator
{
    private int $max = 255;
    private int $limitStr = 10;


    public function validate(array $contentMail): bool
    {
        $rules = $this->setUpRules();

        try {
            foreach ($rules as $property => $rule) {
                foreach ($rule as $ru => $value) {
                    switch ($ru) {
                        case 'notBlank':
                            if ($contentMail[$property] === '') {
                                return $this->redirectToHome("Le formaulaire doit être entièrement complété.");
                            }
                        case 'min':
                            if (strlen($contentMail[$property]) < $value && $value === $this->limitStr) {
                                return $this->redirectToHome("Le message doit contenir au minimum un nombre $this->limitStr caractères.");
                            }
                        case 'max':
                            if (strlen($contentMail[$property]) > $value && $value === $this->max) {
                                return $this->redirectToHome("Les champs de saisie doivent au maximum comprendre $this->max caractères.");
                            }
                        case 'email':
                            if (filter_var($contentMail[$property], FILTER_VALIDATE_EMAIL) === false && $property === 'email') {
                                return $this->redirectToHome("Veuillez saisir une adresse mail valide.");
                            }
                    }
                }
            }
            return true;

        } catch (\Exception $exception) {
            return $this->redirectToHome("Erreur lors de l'ajout de l'utilisateur $exception");
        }
    }

    private function setUpRules(): array
    {
        return [
            'name' =>
                [
                    'notBlank',
                    'max' => $this->max
                ],
            'email' =>
                [
                    'notBlank',
                    'max' => $this->max,
                    'email',
                ],
            'message' =>
                [
                    'notBlank',
                    'min' => $this->limitStr
                ]
        ];
    }

    private function redirectToHome($str): bool
    {
        $request = new  Request();
        $request->redirectToRoute('home', [
            'error' => $str
        ]);
        return false;
    }
}
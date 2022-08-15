<?php

namespace App\Validator;

use App\Manager\UserManager;
use App\Router\Request;

class UserCreationValidator
{
    private int $passwordLimit = 6;
    private int $max = 255;

    public function validate(array $userCreation): bool
    {
        $rules = $this->setUpRules();
        $userManager = new UserManager();

        try {
            foreach ($rules as $property => $rule) {
                foreach ($rule as $ru => $value) {
                    switch ($ru) {
                        case 'notBlank':
                            if ($userCreation[$property] === '') {
                                return $this->redirectToAddUserForm("Les saisies : nom, prénom, email sont obligatoire");
                            }
                        case 'min':
                            if (strlen($userCreation[$property]) < $value && $value === $this->passwordLimit) {
                                return $this->redirectToAddUserForm("Le mot de passe doit au minimum comprendre $this->passwordLimit caractères");
                            }
                        case 'max':
                            if (strlen($userCreation[$property]) > $value && $value === $this->max) {
                                return $this->redirectToAddUserForm("Les champs de saisie doivent au maximum comprendre $this->max caractères");
                            }
                        case 'email':
                            if (filter_var($userCreation[$property], FILTER_VALIDATE_EMAIL ) === false && $property === 'email') {
                                return $this->redirectToAddUserForm("Veuillez saisir une adresse mail valide");
                            }
                        case 'uniq':
                            if ($userManager->selectByMail($userCreation[$property]) && $property === 'email') {
                                return $this->redirectToAddUserForm("L'identfiant email est déja utilisé !");
                            }
                        case 'jpg':
                            if ($userCreation[$property] === $value && $property === 'image'){
                                return $this->redirectToAddUserForm("L'image doit au être format JPG");
                            }
                    }
                }
            }
            return true;

        } catch (\Exception $exception) {
            return $this->redirectToAddUserForm("Erreur lors de l'ajoute de l'utilisateur $exception");
        }

        return true;

    }

    private function setUpRules(): array
    {
        return [
            'name' =>
                [
                    'notBlank',
                    'max' => $this->max
                ],
            'firstname' =>
                [
                    'notBlank',
                    'max' => $this->max
                ],
            'email' =>
                [
                    'notBlank',
                    'max' => $this->max,
                    'email',
                    'uniq'
                ],
            'password' =>
                [
                    'notBlank',
                    'max' => $this->max,
                    'min' => $this->passwordLimit
                ],
            'image' =>
                [
                    'jpg' => false,
                    'max' => $this->max

                ],
        ];
    }

    private function redirectToAddUserForm($str): bool
    {
        $request = new  Request();
        $request->redirectToRoute('register', [
            'error' => $str
        ]);
        return false;
    }
}
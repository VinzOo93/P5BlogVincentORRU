<?php

namespace App\Manager;

class AuthManager extends QueryManager
{
    private string $user = 'user';
    private string $id = 'id_user';
    private string $role = 'role';
    private string $email = 'email';
    private string $password = 'password';


    public function checkForLogIn($email, $password)
    {

        return $this->fetchOneNoLeftJoin($this->user, "$this->id, $this->role" ,[$this->email => $email, $this->password => $password]);

    }
}
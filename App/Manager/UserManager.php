<?php

namespace App\Manager;


class UserManager extends QueryManager
{
    private string $user = 'user';
    private string $all = '*';
    private string $id = 'id';
    private string $name = 'name';
    private string $firstName = 'firstName';
    private string $email = 'email';
    private string $password = 'password';

    public function selectAllUsers()
    {
        return $this->fetchAll($this->all, $this->user);
    }

    public function insertUser($name, $firstName, $email, $password)
    {
        $this->insert($this->user,[$this->name => $name, $this->firstName => $firstName, $this->email => $email, $this->password => $password]);
    }

    public function selectUser($id)
    {
        return $this->fetchOneById($this->all,$this->user, $id);
    }

    public  function  amendUser($id, $name, $firstName, $email, $password)
    {
        $this->update($this->user,[$this->id => $id,$this->name => $name, $this->firstName => $firstName, $this->email => $email, $this->password => $password]);
    }

    public  function  deleteUser($id)
    {
        $this->delete($this->user,$id);
    }

}
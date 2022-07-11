<?php

namespace App\Manager;


class UserManager extends QueryManager
{
    private string $user = 'user';
    private string $all = '*';
    private string $id = 'id_user';
    private string $name = 'name';
    private string $firstName = 'firstName';
    private string $email = 'email';
    private string $role = 'role';
    private string $password = 'password';
    private string $picture = 'picture';

    public function selectAllUsers()
    {
        return $this->fetchAll($this->all, $this->user);
    }

    public function selectByMail($email)
    {
        return $this->fetchOneNoLeftJoin($this->user, $this->email, [$this->email => $email]);
    }

    public function insertUser($name, $firstName, $email, $role, $picture, $password)
    {
        $this->insert($this->user,[$this->name => $name, $this->firstName => $firstName, $this->email => $email, $this->role => $role, $this->picture => $picture, $this->password => $password]);
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
<?php

namespace App\Manager;

use App\DataBaseConnexion\PdoConnexion;


class UserManager
{
    private $db;

    public function __construct()
    {
        $this->db = new PdoConnexion();
    }

    public function selectAllUsers()
    {
        $userStatement = $this->db->connectToDb()->prepare("SELECT * From user");
        $userStatement->execute();

        return $userStatement->fetchAll();
    }

    public function insertUser($name, $firstName, $email, $password)
    {
        $sql = "INSERT INTO 
                    user(name, firstName, email, password)
                    VALUES( ?, ? , ?, ?)";
        $stmt = $this->db->connectToDb()->prepare($sql);
        $stmt->execute([$name, $firstName, $email, $password]);

    }

    public function selectUser($id)
    {
        $userStatement = $this->db->connectToDb()->prepare("SELECT * From user WHERE id = '".$id."'");
        $userStatement->execute();

        return $userStatement->fetchObject();
    }
}
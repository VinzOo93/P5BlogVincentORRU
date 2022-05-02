<?php

namespace App\Controller;

use App\DataBaseConnexion\PdoConnexion;
use App\Helper\TwigHelper;
use mysql_xdevapi\Exception;

class CrudUserController
{
    static function showForm()
    {
        $twig = TwigHelper::loadTwig();
        return $twig->render('formAddUser.html.twig');
    }

    static function addUser($name, $firstName,$email, $password){
        try {
            $db = PdoConnexion::ConnectToDB();
            $sql = "INSERT INTO 
                    user(name, firstName, email, password)
                    VALUES( ?, ? , ?, ?)";
            $stmt = $db->prepare($sql);
            $stmt->execute([$name, $firstName, $email, $password]);

            echo 'Le nouvel utilisateur a été ajouté <br>';
            echo 'voici les données <pre>' . print_r($_POST) . '</pre> <br>';
            echo '<a href="/home">Accueil</a>';
            return $stmt;
        }  catch (Exception $e){
            echo 'erreur lors de l\'ajout' .$e ;
        }
    }
}
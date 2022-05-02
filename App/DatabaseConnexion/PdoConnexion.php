<?php
namespace App\DataBaseConnexion;

use PDO;

class PdoConnexion
{
    static function ConnectToDB(){
      return  new PDO(
            'mysql:host=127.0.0.1:3306;dbname=Blog;charset=utf8',
            'root',
            'root'
        );
    }
}
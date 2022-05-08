<?php

namespace App\DataBaseConnexion;

use PDO;

class PdoConnexion
{
    public function connectToDB()
    {
        $ini = parse_ini_file("Config/config.ini", true,INI_SCANNER_RAW);

        return new PDO(
            $ini["database"]["dsn"],
            $ini["database"]["db_user"],
            $ini["database"]["db_password"]
        );
    }
}
<?php

namespace App\Helper;

use App\Manager\UserManager;

class FunctionHelper
{
    public function getStringBetween($string, $start, $end)
    {
        $string = ' ' . $string;
        $ini = strpos($string, $start);
        if ($ini == 0) return '';
        $ini += strlen($start);
        $len = strpos($string, $end, $ini) - $ini;
        return substr($string, $ini, $len);
    }

    public function removeSpecialAndAccent($string): string
    {

        $unwantedArray = ['Š' => 'S', 'š' => 's', 'Ž' => 'Z', 'ž' => 'z', 'À' => 'A', 'Á' => 'A', 'Â' => 'A', 'Ã' => 'A', 'Ä' => 'A', 'Å' => 'A', 'Æ' => 'A', 'Ç' => 'C', 'È' => 'E', 'É' => 'E',
            'Ê' => 'E', 'Ë' => 'E', 'Ì' => 'I', 'Í' => 'I', 'Î' => 'I', 'Ï' => 'I', 'Ñ' => 'N', 'Ò' => 'O', 'Ó' => 'O', 'Ô' => 'O', 'Õ' => 'O', 'Ö' => 'O', 'Ø' => 'O', 'Ù' => 'U',
            'Ú' => 'U', 'Û' => 'U', 'Ü' => 'U', 'Ý' => 'Y', 'Þ' => 'B', 'ß' => 'Ss', 'à' => 'a', 'á' => 'a', 'â' => 'a', 'ã' => 'a', 'ä' => 'a', 'å' => 'a', 'æ' => 'a', 'ç' => 'c',
            'è' => 'e', 'é' => 'e', 'ê' => 'e', 'ë' => 'e', 'ì' => 'i', 'í' => 'i', 'î' => 'i', 'ï' => 'i', 'ð' => 'o', 'ñ' => 'n', 'ò' => 'o', 'ó' => 'o', 'ô' => 'o', 'õ' => 'o',
            'ö' => 'o', 'ø' => 'o', 'ù' => 'u', 'ú' => 'u', 'û' => 'u', 'ý' => 'y', 'þ' => 'b', 'ÿ' => 'y'];

        $string = preg_replace('/[^A-Za-z0-9\-]/', '', $string);

        return strtr($string, $unwantedArray);

    }

    public function startSession()
    {
        if (session_status() == PHP_SESSION_NONE) {
            session_start();
        }
    }

    public function checkActiveUserInSession()
    {
        if (!empty($_SESSION)) {
            $userManager = new UserManager();
            return $userManager->selectUser($_SESSION['userId']);
        }
    }

    public function uploadImage($newDirPath)
    {
        $imageTmpName = $_FILES['image']['tmp_name'];
        $imgName = $_FILES['image']['name'];

        if ($_FILES['image']['size'] != 0 && pathinfo($imgName, PATHINFO_EXTENSION) == 'jpg') {
            $imgSlug = "$newDirPath/$imgName";
            mkdir($newDirPath);
            move_uploaded_file(
                $imageTmpName,
                $imgSlug
            );
            $slugImageToSlug = str_split($imgSlug, 17);
            unset($slugImageToSlug[0]);
        } else {
            return false;
        }

        return $slugImageToSlug;

    }
}
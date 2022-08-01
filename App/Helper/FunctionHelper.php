<?php

namespace App\Helper;

use App\Manager\UserManager;
use App\Router\Request;

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

        $string = preg_replace('~[^\pL\d]+~u', '-', $string);

        $string = iconv('utf-8', 'us-ascii//TRANSLIT', $string);

        $string = preg_replace('~[^-\w]+~', '', $string);

        $string = preg_replace('~-+~', '-', $string);

        return strtolower($string);

    }

    public function avoidSqlErrorForString($string)
    {
        return preg_replace("/'/", "''", $string);
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

    public function mustBeAuthentificated()
    {
        $request = new Request();

        if (empty($_SESSION)) {
            $request->redirectToRoute('login');
        } else {
            return true;
        }
    }

    public function uploadImage($newDirPath)
    {
        $imageTmpName = $_FILES['image']['tmp_name'];
        $imgName = $_FILES['image']['name'];


        if (pathinfo($imgName, PATHINFO_EXTENSION) == 'jpg') {
            $imgName = uniqid() . '.jpg';
            $imgSlug = "$newDirPath/$imgName";

            if (!file_exists($newDirPath)) {
                mkdir($newDirPath);
            }
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
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

    public function checkAdminSession() {

        $request = new Request();

        if (isset($_SESSION) && !empty($_SESSION)) {
            $role = $_SESSION['userRole'];
            if ($role === 'admin') {
            return true;
            } elseif ($role === 'user') {
                return false;
            }
            else {
                session_unset();
                session_destroy();
                session_write_close();
                $request->redirectToRoute('login');
            }
        }
        return false;
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

    public function deleteImage($imagePath, $pathUploadDir)
    {
        if ($imagePath != null) {
            $pathFile = "$pathUploadDir$imagePath";
            if (file_exists($pathFile)) {
                $folder = substr($imagePath, 0, strpos($imagePath, '/', 10));
                $folderPath = "$pathUploadDir$folder";
                array_map('unlink', glob("$folderPath/*.*"));
                rmdir($folderPath);
            return  true;
            }
        } else {
            return false;
        }
    }
}
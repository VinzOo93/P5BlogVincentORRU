<?php

namespace App\Helper;

use Twig;

class TwigHelper
{
    public function loadTwig(): Twig\Environment
    {
        $loader = new \Twig\Loader\FilesystemLoader('./templates');

        return new  Twig\Environment($loader);
    }
}
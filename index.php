<?php
require "vendor/autoload.php";

use \App\Helper\Form;
echo Form::input();
try {
    $db = new PDO(
        'mysql:host=127.0.0.1:3306;dbname=Blog;charset=utf8',
        'root',
        'root'
    );

    $userStatement = $db->prepare("SELECT name FROM user WHERE id = 1");
    $userStatement->execute();
    $users = $userStatement->fetchAll();
    // EN PHP
    foreach ($users as $user) {
        ?>
        <div style="color: cornflowerblue">
            <h1>Welcome to my blog with PHP</h1>
            <h3><?php echo 'Blog de ' .$user['name']; ?></h3>
        </div>
        <?php
    }
    ?> ===================================
    <?php
    // EM TWIG
    $loader = new \Twig\Loader\FilesystemLoader('./templates');
    $twig = new  Twig\Environment($loader);

    echo $twig->render('index.html.twig', ['users' => $users]);



    } catch (Exception $e) {
    die('Ereur: ' . $e->getMessage());
}



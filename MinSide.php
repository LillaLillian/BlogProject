<?php
// innlogging -----------------------------
require_once('Bruker.class.php');
@session_start();

// autoload -------------------------------
require_once 'db_autorisering.php';
spl_autoload_register(function ($class_name) {
    require_once $class_name . '.class.php';
});

// twig ------------------------------------
require_once 'vendor/autoload.php';
$loader = new Twig_Loader_Filesystem('templates');
$twig = new Twig_Environment($loader);

// ------------------------------------------

$tittel = "minside";
$bloggReg = new BloggRegister($db);
Blogg::settDb($db);



if (isset($_SESSION['innlogget']) && $_SESSION['innlogget'] && $_SESSION['bruker']->verifyUser()) {
    $innlogget = "innlogget";
    $bruker_id = $_SESSION['bruker_id'];
    $kallenavn = $_SESSION['kallenavn'];

    echo $twig->render('Header.twig', array('tittel' => "standard", 'tittel' => $tittel, 'kallenavn' => $kallenavn, 'innlogget' => $innlogget));

    if ($blogger = $bloggReg->hentEiersBlogger($bruker_id)) {
        echo $twig->render('MinSide.twig', array('Blogger' => $blogger, 'innlogget' => $innlogget));
    } else {
        echo $twig->render('MinSide.twig', array('innlogget' => $innlogget));
    }


} else {
    echo $twig->render('Header.twig', array('tittel' => "standard"));
    header("Location: ../Blogg/LoggInn.php");
}


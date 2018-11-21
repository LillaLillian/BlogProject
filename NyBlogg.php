<?php

// Opprett ny blogg

// innlogging -----------------------------
require_once('Bruker.class.php');
@session_start();

// autoload -------------------------------
require_once("db_autorisering.php");
spl_autoload_register(function ($class_name) {
    require_once $class_name . '.class.php';
});

// twig -----------------------------------
require_once 'vendor/autoload.php';
$loader = new Twig_Loader_Filesystem('templates');
$twig = new Twig_Environment($loader);

// ----------------------------------------

Blogg::settDb($db);

if(isset($_SESSION['innlogget']) && $_SESSION['innlogget'] && $_SESSION['bruker']->verifyUser()){
    $bruker_id = $_SESSION['bruker_id'];
    echo $twig->render('Header.twig', array('tittel' => "standard", 'innlogget' => "innlogget"));

    if (isset($_POST['submit'])) {
        header( "refresh:5;url=MinSide.php" );

        $tittel = filter_input(INPUT_POST,'tittel',FILTER_SANITIZE_SPECIAL_CHARS);
        $stil = filter_input(INPUT_POST,'stil',FILTER_SANITIZE_SPECIAL_CHARS);
        $b = new Blogg();
        $b->opprett($tittel, $stil, $bruker_id);
        $b->lagre();
        echo $twig->render('NyBlogg.twig', array('blogg' => "opprettet"));;
    } else{
        echo $twig->render('NyBlogg.twig');
    }
} else {
    echo $twig->render('Header.twig', array('tittel' => "standard"));
    header("Location:../Blogg/LoggInn.php");
}
?>

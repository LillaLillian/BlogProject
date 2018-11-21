<?php

// Legg inn kommentar

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


if (isset($_SESSION['innlogget']) && $_SESSION['innlogget'] && $_SESSION['bruker']->verifyUser()) {
    $bloggReg = new BloggRegister($db);
    Innlegg::settDb($db);


    $innlegg_id = intval($_POST['innlegg_id']);

    $bruker_id = intval($_POST['bruker_id']);

    $blogg_id = intval($_POST['blogg_id']);

    if (isset($_POST['kommentar_id'])) {
        $kommentar_id = intval($_POST['kommentar_id']);
    } else {
        $kommentar_id = null;
    }

    $tekst = filter_input(INPUT_POST, 'tekst', FILTER_SANITIZE_SPECIAL_CHARS);

    Kommentar::settDb($db);
    $kommentar = new Kommentar();


    $kommentar->opprett($tekst, $innlegg_id, $kommentar_id, $bruker_id);

    $kommentar->lagre();

    header("Location: ../Blogg/VisInnlegg.php?id=" . $innlegg_id);
} else{
    echo $twig->render('Header.twig', array('tittel' => "standard"));
    header("Location:../Blogg/LoggInn.php");
}

?>

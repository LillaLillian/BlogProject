<?php

// Sletter kommentarer

// innlogget? -----------------------------
require_once('Bruker.class.php');
@session_start();

if (!isset($_SESSION['innlogget'])) {
    $_SESSION['side'] = "../Blogg/NyttInnlegg.php";
    header("Location: ../Blogg/LoggInn.php");
    exit;
}
// autoload -------------------------------
require_once("db_autorisering.php");
spl_autoload_register(function ($class_name) {
    require_once $class_name . '.class.php';
});

// twig ------------------------------------
require_once 'vendor/autoload.php';
$loader = new Twig_Loader_Filesystem('templates');
$twig = new Twig_Environment($loader);

// ------------------------------------------

$kommentar_id = intval($_GET['id']);
Kommentar::settDb($db);
$kommentar = Kommentar::visEtt($kommentar_id);

$blogg_id = intval($_POST['blogg_id']);
$innlegg_id = intval($_POST['innlegg_id']);

$bloggReg = new BloggRegister($db); // for header
$blogg = $bloggReg->visEnBlogg($blogg_id); // for header


if (isset ($_SESSION['innlogget']) && $_SESSION['bruker_id'] == ($_POST['blogg_eier']) && $_SESSION['bruker']->verifyUser()) {
    header("refresh:5;url=VisInnlegg.php?id=" . $innlegg_id);


    echo $twig->render('Header.twig', array('tittel' => "bloggtittel", 'Blogg' => $blogg));

    if ($kommentar->slett()) {

        echo $twig->render('VisInnlegg.twig', array('kommentar' => "slettet"));
    } else{
        echo "Kommentaren kunne ikke slettes";
    }
} else {
    echo $twig->render('Header.twig', array('tittel' => "standard"));
    header("Location:../Blogg/LoggInn.php");
}
?>
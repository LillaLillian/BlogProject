<?php

// innlogging -----------------------------
require_once('Bruker.class.php');
@session_start();

if(!isset($_SESSION['innlogget'])){
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

$blogg_id = intval($_POST['blogg_id']);
$bloggReg = new BloggRegister($db);
$blogg = $bloggReg->visEnBlogg($blogg_id);

if (isset ($_SESSION['innlogget']) && $_SESSION['bruker_id'] == ($_POST['blogg_eier'])  && $_SESSION['bruker']->verifyUser()) {
    header("refresh:5;url=VisBlogg.php?id=" . $blogg_id);

    $bloggReg->slettInnlegg($_GET['id']);

    echo $twig->render('Header.twig', array('tittel' => "bloggtittel", 'Blogg' => $blogg));
    echo $twig->render('VisInnlegg.twig', array('innlegg' => "slettet"));

} else{

    echo $twig->render('Header.twig', array('tittel' => "standard"));
    header("Location:../Blogg/LoggInn.php");
}
?>
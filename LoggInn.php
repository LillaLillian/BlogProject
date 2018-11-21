<?php

// Innloggingsside

// innlogging-------------------------------
require_once('Bruker.class.php');
session_start();

// loading ---------------------------------
if (file_exists($auth = "db_autorisering.php")) {
    require_once($auth);
} else {
    print("<h1>Mangler konfigurasjonsfil for oppkobling mot databasen</h1>");
    exit;
}

// twig -----------------------------------
require_once 'vendor/autoload.php';
$loader = new Twig_Loader_Filesystem('templates');
$twig = new Twig_Environment($loader);

// -----------------------------------------

echo $twig->render('Header.twig', array('tittel' => "standard"));

//sjekk om innlogget. kode hentet fra sikkerhetslaboppgave og modifisert
if (!isset($_SESSION['innlogget'])) {

    if (isset($_POST['submit'])) {

        // Logger inn bruker, hvis suksessfull sendt til  MinSide
        if (Bruker::loggInn($db, $_POST['brukernavn'])) {
            header("Location: MinSide.php");
            exit;
        } else {
            // Innlogging feilet
            echo $twig->render('LoggInn.twig', array('innlogging' => "feilet"));

        }
    } else {  //Viser logg inn siden
        echo $twig->render('LoggInn.twig');
        exit;
    }
} else { // Bruker er allerede logget inn, sendes til MinSide
    header("Location: MinSide.php");

}

//logg ut
if (isset($_GET['logout'])) {
    unset($_SESSION['innlogget']);
    header("Location: BloggRegister.php");
    exit;
}


?>
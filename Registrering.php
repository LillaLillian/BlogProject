<?php

// Registrering av ny bruker

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

// -----------------------------------------

$brukerReg = new BrukerRegister($db);
$registrert = ""; //Variabel som sendes til twig

if (isset($_SESSION['innlogget']) && $_SESSION['innlogget']) { // ikke mulig å registrere ny bruker mens innlogget
    header("Location:../Blogg/MinSide.php");

} else {

    if (isset($_POST['submit'])) {
        $brukernavn = $_POST['brukernavn'];
        $fornavn = $_POST['fornavn'];
        $etternavn = $_POST['etternavn'];
        $epost = $_POST['epost']; // bør teste for gyldig input -> sjekkes av bootstrap i twigfilen
        $passord = $_POST['passord'];
        $hash = password_hash($passord, PASSWORD_DEFAULT);
        $epost_id = md5(uniqid(rand(), 1));

        $bruker = new Bruker($brukernavn, $fornavn, $etternavn);
        $bruker->settAlt($brukernavn, $fornavn, $etternavn, $epost, $hash, $epost_id);


        echo $twig->render('Header.twig', array('tittel' => "standard"));

        if ($brukerReg->leggTilBruker($bruker) != 0) {


// create a new cURL resource
            $ch = curl_init();

// set URL and other appropriate options
            curl_setopt($ch, CURLOPT_URL, "http://kark.uit.no/internett/php/mailer/mailer.php?address=" . $epost . "&url=http://localhost/Blogg/Registrering.php?id=" . $epost_id);
            curl_setopt($ch, CURLOPT_HEADER, 0);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
// grab URL and pass it to the browser
            $output =
                curl_exec($ch);


// close cURL resource, and free up system resources
            curl_close($ch);
            $registrert = "registrert";
            echo $twig->render('Registrering.twig', array('registrert' => $registrert));
        } else {
            $registrert = "mislykket";
            echo $twig->render('Registrering.twig', array('registrert' => $registrert));
        }

    } elseif (isset($_GET['id'])) {
        // aktiver bruker

        echo $twig->render('Header.twig', array('tittel' => "standard"));

        if (Bruker::aktiverBruker($db, $_GET['id'])) {
            $registrert = "aktivert";
            echo $twig->render('Registrering.twig', array('registrert' => $registrert));
        }
    } else {
        echo $twig->render('Header.twig', array('tittel' => "standard"));
        echo $twig->render('Registrering.twig', array('registrert' => $registrert));

    }
}
?>
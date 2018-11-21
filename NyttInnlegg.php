<?php

// Skriv nytt innlegg

// innlogget? -----------------------------
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

// twig -----------------------------------
require_once 'vendor/autoload.php';
$loader = new Twig_Loader_Filesystem('templates');
$twig = new Twig_Environment($loader);

// -----------------------------------------


if(isset($_SESSION['innlogget']) && $_SESSION['innlogget'] && $_SESSION['bruker']->verifyUser()) {
// define variables and set to empty values
    $bruker = 1;
    $tittelErr = "";
    $innleggErr = "";
    $tittel = $innlegg = "";
    $bloggReg = new BloggRegister($db);

// TEST $_GET and $_POST variables
    if (0): // change 1 to 0 to prevent showing
        echo '<pre>';
        echo '$_GET => ';
        print_r($_GET);
        echo '<hr>';

        echo '$_POST => ';
        print_r($_POST);
        echo '<hr>';
        echo '</pre>';
        die;
    endif;
    if (isset($_GET['id']) && ctype_digit($_GET['id'])) {
        $id = intval($_GET['id']);

        if ($_SERVER["REQUEST_METHOD"] == "POST") {

            if (empty($_POST["tittel"])) {
                $tittelErr = "Skriv tittel/emne";
            } else {
                $tittel = test_input($_POST["tittel"]);
                // check if tittel only contains letters and whitespace
                if (!preg_match("/^[a-zA-Z ]*$/", $tittel)) {
                    $nameErr = "Only letters and white space allowed";
                }
            }

            if (empty($_POST["innlegg"])) {
                $innlegg = "";
            } else {
                $innlegg = test_input($_POST["innlegg"]);
            }


        }

        $r = new Innlegg();
        if (isset($_POST['submit'])) {
            $r->opprett($tittel, $innlegg, $_POST['id'], $bruker);
            $innleggId = $bloggReg->leggTilInnlegg($r);
            $arkiv->save($InnleggId);

            $blogg = $bloggReg->visEnBlogg($id);
            echo $twig->render('Header.twig', array('tittel' => "bloggtittel", 'Blogg' => $blogg));
            echo $twig->render('NyttInnlegg.twig', array('Blogg' => $blogg, 'bruker' => $bruker, 'bloggId' => $id, 'innlegg' => "postet"));

        }

        if ($blogg = $bloggReg->visEnBlogg($id) && $_SESSION['innlogget']) {

            echo $twig->render('Header.twig', array('tittel' => "bloggtittel", 'Blogg' => $blogg));
            echo $twig->render('NyttInnlegg.twig', array('Blogg' => $blogg, 'bruker' => $bruker, 'bloggId' => $id));
        } else {

        }
    } else { //Setter denne koden her for å teste oppretting av nye innlegg selv om man ikke er logget inn. endres når vi fikser login
        $id = intval($_POST['id']);
        $innlegg = test_input($_POST["innlegg"]);
        $tittel = test_input($_POST["tittel"]);
        //echo "Logg inn for å skrive innlegg";
        $r = new Innlegg();
        if (isset($_POST['submit'])) {
            $r = new Innlegg();
            $r->opprett($tittel, $innlegg, $id, $bruker);
            $innleggId = $bloggReg->leggTilInnlegg($r);
            echo "InnleggId: " . $innleggId . " opprettet! ";
        }
    }
    function test_input($data)
    {  //kode hentet fra w3schools.com
        $data = trim($data);
        $data = stripslashes($data);
        $data = htmlspecialchars($data);
        return $data;
    }
} else {
    echo $twig->render('Header.twig', array('tittel' => "standard"));
    header("Location:../Blogg/LoggInn.php");
}
?>

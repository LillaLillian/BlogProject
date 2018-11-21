<?php

// Skriv nytt innlegg
require_once('Bruker.class.php');
@session_start();

// autoload -------------------------------
require_once("db_autorisering.php");
spl_autoload_register(function ($class_name) {
    require_once $class_name . '.class.php';
});

// twig -----------------------------------
require_once 'vendor/autoload.php';
define('FILNAVN_TAG', 'bildeFil');
$loader = new Twig_Loader_Filesystem('templates');
$twig = new Twig_Environment($loader);

// -----------------------------------------

$arkiv = new filarkiv($db, $twig);
$bloggReg = new BloggRegister($db);

Innlegg::settDb($db);
Stikkord::settDb($db);

if (isset($_SESSION['innlogget']) && $_SESSION['innlogget'] && $_SESSION['bruker']->verifyUser()) {
    if (isset($_GET['id']) && ctype_digit($_GET['id'])) {
        $id = intval($_GET['id']);

        if ($blogg = $bloggReg->visEnBlogg($id)) {

            $alle_stikkord = array();
            $alle_stikkord = Stikkord::visAlle();

            echo $twig->render('Header.twig', array('tittel' => "bloggtittel", 'Blogg' => $blogg, 'innlogget' => "innlogget"));
            echo $twig->render('NyttInnlegg.twig', array('Blogg' => $blogg, 'alle_stikkord' => $alle_stikkord));
        }
    } else {
        $id = intval($_POST['id']);

        $bruker_id = intval($_POST['bruker_id']);

        $tittel = filter_input(INPUT_POST, 'tittel', FILTER_SANITIZE_SPECIAL_CHARS);

        $tekst = filter_input(INPUT_POST, 'tekst', FILTER_SANITIZE_SPECIAL_CHARS);
        $stikkordliste = array();
        $stikkordliste = $_POST['valgte_stikkord'];

        $blogg = $bloggReg->visEnBlogg($id);


        if (isset($_POST['submit'])) {
            header("refresh:3;url=VisBlogg.php?id=" . $id);
            $inn = new Innlegg();
            $inn->opprett($tittel, $tekst, $id, $bruker_id);

            $inn->lagre();

            foreach ($stikkordliste as $stikkord) {
                $stikk = new Stikkord();
                $stikk->opprett($stikkord);
                $stikk->lagre();
                $stikk->lagreForInnlegg($inn->hentId());
            }

            $arkiv->save($inn->hentId());

            echo $twig->render('Header.twig', array('tittel' => "standard", 'innlogget' => "innlogget"));
            echo $twig->render('NyttInnlegg.twig', array('Blogg' => $blogg, 'innlegg' => "postet"));

        }
    }
} else {
    echo $twig->render('Header.twig', array('tittel' => "standard"));
    header("Location:../Blogg/LoggInn.php");
}

?>

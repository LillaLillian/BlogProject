<?php

// Viser 1 innlegg

// innlogget? -----------------------------
require_once('Bruker.class.php');
@session_start();

// autoload ----------------------------------
spl_autoload_register(function ($class_name) {
    require_once $class_name . '.class.php';
});
require_once 'db_autorisering.php';
//define('FILNAVN_TAG', 'bildeFil');

// twig --------------------------------------
require_once 'vendor/autoload.php';
$loader = new Twig_Loader_Filesystem('templates');
$twig = new Twig_Environment($loader);

// --------------------------------------------

Innlegg::settDb($db);

$bloggReg = new BloggRegister($db);
$filArkiv = new filarkiv($db, $twig);
$innlogget = "";    // variabel til twig

if (isset($_GET['id']) && ctype_digit($_GET['id'])) {
    $id = intval($_GET['id']);
    $innlegg = Innlegg::visEtt($id);
    $bloggId = $innlegg->hentBlogg_id();
    $vedlegg = $filArkiv->visOversikt2($id);
    $blogg = $bloggReg->visEnBlogg($bloggId);



    $innlegg->oekAntalltreff();
    $innlegg->settKommentarer();
    $innlegg->settStikkordListe();
if (isset($_SESSION['innlogget'])){
    $innlogget = "innlogget";
}
    echo $twig->render('Header.twig', array('tittel' => "bloggtittel", 'Blogg' => $blogg, 'innlogget' => $innlogget));

    if (isset ($_SESSION['innlogget']) && $_SESSION['bruker_id'] == $blogg->hentEier() && $_SESSION['bruker']->verifyUser()) {
        $innlogget = "innloggetEier";
        $bruker_id = $_SESSION['bruker_id'];

        echo $twig->render('VisInnlegg.twig', array('bruker_id' => $bruker_id, 'innlogget' => $innlogget, 'Blogg' => $blogg, 'Innlegg' => $innlegg, 'fil' => $vedlegg));

    } elseif (isset ($_SESSION['innlogget']) && $_SESSION['innlogget'] && $_SESSION['bruker']->verifyUser()) {
        $innlogget = "innlogget";
        $bruker_id = $_SESSION['bruker_id'];

        echo $twig->render('VisInnlegg.twig', array('bruker_id' => $bruker_id, 'innlogget' => $innlogget, 'Blogg' => $blogg, 'Innlegg' => $innlegg, 'fil' => $vedlegg));

    } else {
        echo $twig->render('VisInnlegg.twig', array('Blogg' => $blogg, 'Innlegg' => $innlegg, 'fil' => $vedlegg));
    }

}
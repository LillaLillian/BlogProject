<?php

// Viser 1 blogg

// innlogget? -----------------------------
require_once('Bruker.class.php');
@session_start();

// autoload ----------------------------------
spl_autoload_register(function ($class_name) {
    require_once $class_name . '.class.php';
});
require_once 'db_autorisering.php';

// twig --------------------------------------
require_once 'vendor/autoload.php';
$loader = new Twig_Loader_Filesystem('templates');
$twig = new Twig_Environment($loader);

// --------------------------------------------

Innlegg::settDb($db);

$bloggReg = new BloggRegister($db);
$innlogget = "";    // variabel til twig


if (isset($_GET['id']) && ctype_digit($_GET['id'])) {
    $id = intval($_GET['id']);

    if ($blogg = $bloggReg->visEnBlogg($id)) {

        $blogg_id = $blogg->hentId();

        $innleggTabell = array();

        Arkiv::settDb($db);
        $arkivTabell = array();
        $arkivTabell = Arkiv::visAlleArkiv($blogg_id);

        $maaned = (int) date("n");
        $aar = (int) date("Y");


        if (isset($_GET['aar']) and isset($_GET['maaned'])) {

            $aar = filter_input(INPUT_GET,'aar',FILTER_SANITIZE_NUMBER_INT);
            $maaned = filter_input(INPUT_GET,'maaned',FILTER_SANITIZE_NUMBER_INT);
            $innleggTabell = Innlegg::visArkiv($blogg_id, $aar, $maaned);

        } else {

            // finner den siste maaneden som hadde innlegg
            $nyeste_dato = finnNyesteDato(Innlegg::visAlleFraBlogg($blogg_id));

            $aar = (int) $nyeste_dato->format("Y");
            $maaned = (int) $nyeste_dato->format("n");


            $innleggTabell = Innlegg::visArkiv($blogg_id, $aar, $maaned);


        }


        foreach($innleggTabell as $innl) {
            $innl->oekAntalltreff();
            $innl->settKommentarer();
            $innl->settStikkordListe();

        }
        if (isset ($_SESSION['innlogget'])){
            $innlogget = "innlogget";
        }
        echo $twig->render('Header.twig', array('tittel' => "bloggtittel", 'Blogg' => $blogg, 'innlogget' => $innlogget));


        if (isset ($_SESSION['innlogget']) && $_SESSION['bruker_id'] == $blogg->hentEier() && $_SESSION['bruker']->verifyUser()) {
            $innlogget = "innloggetEier";
            echo $twig->render('VisBlogg.twig', array('innlogget' => $innlogget, 'Blogg' => $blogg, 'innleggTabell' => $innleggTabell, 'arkivTabell' => $arkivTabell));
        }
        else {
            echo $twig->render('VisBlogg.twig', array('Blogg' => $blogg, 'innleggTabell' => $innleggTabell, 'arkivTabell' => $arkivTabell));
        }
    } else {
        echo "Kunne ikke lese inn blogger";
    }


} else {

    header("Location:../Blogg/Hjem.php");
}

// finner datoen til det nyeste innlegget
function finnNyesteDato($innleggTabell) {



    $nyeste_dato = new DateTime();
    $nyeste_dato->setDate(1900,1,1); // setter en gammel dato

    foreach ($innleggTabell as $Innlegg) {
        $dato_tekst = $Innlegg->hentTidspunkt();

        $innlegg_dato = new DateTime($dato_tekst);

        if ($innlegg_dato > $nyeste_dato) {
            $nyeste_dato = $innlegg_dato;
        }
    }

    return $nyeste_dato;
}


?>
<?php
// Håndterer søkefeltet

// innlogget? -----------------------------
@session_start();

// autoload -------------------------------
require_once 'db_autorisering.php';
spl_autoload_register(function ($class_name) {
    require_once $class_name . '.class.php';
});
// twig --------------------------------------
require_once 'vendor/autoload.php';
$loader = new Twig_Loader_Filesystem('templates');
$twig = new Twig_Environment($loader);
//-------------------------------------------
Innlegg::settDb($db);
$sok = new Sok($db);
$innlogget = "";
if(isset ($_SESSION['innlogget']) && $_SESSION['innlogget']){
    $innlogget = "innlogget";
}

if (isset($_POST['sok'])) {
    $sokeord = $_POST['sok'];

    echo $twig->render('Header.twig', array('tittel' => "standard", 'innlogget' => $innlogget));

// Søk i innhold -----------------------------
    $innholdResultat = $sok->sokInnhold($sokeord);
    $innleggInnhold[] = array();

    for ($i = 0; $i < count($innholdResultat); $i++) {
        $innleggInnhold[$i] = Innlegg::visEtt($innholdResultat[$i]['id']);
    }

// Søk i stikkord ------------------------------
    $stikkordResultat = $sok->sokStikkord($sokeord);
    $innleggStikkord[] = array();

    for ($i = 0; $i < count($stikkordResultat); $i++) {
        $innleggStikkord[$i] = Innlegg::visEtt($stikkordResultat[$i]['Innlegg_id']);
    }

    echo $twig->render('Sok.twig', array('innleggInnhold' => $innleggInnhold, 'innleggStikkord' => $innleggStikkord, 'sokeord' => $sokeord, 'sok' => "sokefelt"));



} elseif (isset($_GET['stikkord'])) {
    $stikkord = $_GET['stikkord'];

    // Klikk på stikkord ------------------------------
    $stikkordResultat = $sok->sokStikkord($stikkord);
    $innleggStikkord[] = array();

    for ($i = 0; $i < count($stikkordResultat); $i++) {
        $innleggStikkord[$i] = Innlegg::visEtt($stikkordResultat[$i]['Innlegg_id']);
    }

    echo $twig->render('Header.twig', array('tittel' => "standard", 'innlogget' => $innlogget));
    echo $twig->render('Sok.twig', array('innleggStikkord' => $innleggStikkord, 'sokeord' => $stikkord));

// --------------------------------------------
}
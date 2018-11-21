<?php
require_once('bruker.class.php');
@session_start();

// sjekk at bruker er innlogget
// kode hentet fra sikkerhetslaboppgave og modifisert
if (!isset($_SESSION['innlogget'])) {

    //ta vare paa siden i session slik at vi kan sende brukeren tilbake til denne
    $_SESSION['side'] ="temp.php";
    header("Location: logginn.php");
    exit;
}
else
{
    // bruker er innlogget
    echo "<h1>Startsiden </h1>";
    if($_SESSION['bruker']->verifyUser()) {
        echo "Velkommen<br />";
        echo $_SESSION['bruker']->hentBrukernavn();
        echo "Du er innlogget!";
        echo " - antall besok  er ";
        echo $_SESSION['bruker']->getHits();
        include('logout.php');
    }
    else {
        echo "Hijacked....";
        exit;
    }

}

?>
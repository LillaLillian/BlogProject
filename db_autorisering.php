<?php

// basert på fil fra labøvinger

$tjener = "kark.uit.no";
$dbnavn = "stud_v18_heen";
$brukernavn = "stud_v18_heen";
$passord = "kebab";

try
{
    $db = new PDO("mysql:host=$tjener;dbname=$dbnavn", $brukernavn, $passord);
}
catch(PDOException $e)
{
    //throw new Exception($e->getMessage(), $e->getCode);
    print($e->getMessage());
}

?>
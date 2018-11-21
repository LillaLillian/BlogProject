<?php
/**
 * Created by PhpStorm.
 * User: laogong
 * Date: 05/05/2018
 * Time: 23:44
 */



// Klasse som representerer en måned med innlegg

class Arkiv {

    private static $db;
    private $aar;
    private $maaned;
    private $antall;

    public function __construct() {

    }

    static public function settDb($database) {
        self::$db = $database;
    }

    // returnerer tabell med Arkiv-objekter for hver maaned hvor innlegg ble laget
    static public function visAlleArkiv(int $id) {
        $alle_arkiv = array();


        try {
            $setning = self::$db->prepare("SELECT YEAR(opprettet) AS aar, MONTH(opprettet) AS maaned, COUNT(*) AS antall FROM Innlegg WHERE Blogg_id=:id GROUP BY YEAR(opprettet),MONTH(opprettet) ORDER BY opprettet DESC");
            $setning->bindParam(':id',$id,PDO::PARAM_INT);
            $setning->execute();

            while ($arkiv = $setning->fetchObject('Arkiv') )
            {

                    $alle_arkiv[] = $arkiv;

            }

        }
        catch (Exception $e) {
            print $e->getMessage() . PHP_EOL;
        }

        return $alle_arkiv;

    }

    public function nullstill() {
        $this->antall = null;
        $this->aar = null;
        $this->maaned = null;
    }

    public function hentAar() {
        return $this->aar;
    }

    public function hentMaaned() {
        return $this->maaned;
    }

    // for testing
    public function settMaaned($maaned) {
        $this->maaned = $maaned;
    }

    public function hentMaanedNavn() {
        $navnetabell = array("Januar", "Februar", "Mars", "April", "Mai", "Juni", "Juli", "August", "September", "Oktober", "November", "Desember");
        return $navnetabell[$this->maaned-1];
    }

    public function hentAntall() {
        return $this->antall;
    }

}

?>
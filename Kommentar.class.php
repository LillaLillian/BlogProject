<?php
/**
 * Created by PhpStorm.
 * User: laogong
 * Date: 10/04/2018
 * Time: 14:16
 */

// Klasse som er knyttet opp mot Kommentar-tabellen i databasen

class Kommentar {
    static private $db;
    private $id;
    private $opprettet;
    private $tekst;
    private $Innlegg_id;
    private $Kommentar_id;
    private $Bruker_id;
    private $kallenavn;



    public function __construct() {

    }

    public function opprett( $tekst, $Innlegg_id, $Kommentar_id, $Bruker_id) {

        $this->tekst = $tekst;
        $this->Innlegg_id = $Innlegg_id;
        $this->Kommentar_id = $Kommentar_id;
        $this->Bruker_id = $Bruker_id;

    }

    // finner alle kommentarer for et gitt innlegg i databasen og returnerer et array av kommentar-objekter
    static public function visAlleForInnlegg(int $Innlegg_id) {
        $kommentarTabell= array();

        try
        {

            $setning = self::$db->prepare("SELECT Kommentar.*,CASE WHEN Kommentar.Kommentar_id IS NULL THEN Kommentar.id WHEN Kommentar.Kommentar_id < Kommentar.id THEN Kommentar.Kommentar_id END AS Minst ,Bruker.kallenavn FROM Kommentar, Bruker WHERE Kommentar.Bruker_id=Bruker.id AND Kommentar.Innlegg_id=:id ORDER BY Minst DESC");
            $setning->bindParam(':id',$Innlegg_id,PDO::PARAM_INT);
            $setning->execute();

            while ($kommentar = $setning->fetchObject('Kommentar') )
            {
                $kommentarTabell[] = $kommentar;
            }

        }
        catch (Exception $e) {
            print $e->getMessage() . PHP_EOL;
        }

        return $kommentarTabell;
    }

    // finner en kommentar basert paa Kommentar-id og returnerer et Kommentar-objekt
    static public function visEtt(int $id) {

        try
        {
            $setning = self::$db->prepare("SELECT Kommentar.*, Bruker.kallenavn FROM Kommentar, Bruker WHERE Kommentar.Bruker_id=Bruker.id AND Kommentar.id=:id");
            $setning->bindParam(':id',$id,PDO::PARAM_INT);
            $setning->execute();

            $kommentar = $setning->fetchObject('Kommentar');
        }
        catch (Exception $e) {
            print $e->getMessage() . PHP_EOL;
        }
        return $kommentar;
    }

    static public function settDb($database) {
        self::$db = $database;
    }

    public function lagre() {

        try
        {
            $sql_tekst = "INSERT INTO Kommentar ";

            if (!empty($this->Kommentar_id)) {
                $sql_tekst .= "(tekst,Innlegg_id, Kommentar_id, Bruker_id)";
            }
            else {
                $sql_tekst .= "(tekst,Innlegg_id, Bruker_id)";
            }



            $sql_tekst .= "VALUES (";
            $sql_tekst .= "'".$this->hentTekst()."',";
            $sql_tekst .= "'".$this->hentInnlegg_id()."',";

            if (!empty($this->Kommentar_id)) {
                $sql_tekst .= "'".$this->hentKommentar_id()."',";
            }

            $sql_tekst .= "'".$this->hentEier()."'";
            $sql_tekst .= ")";
            $setning = self::$db->prepare($sql_tekst);

            if($setning->execute()) {
                $this->settId(self::$db->lastInsertId());
            }

        }
        catch (Exception $e) {
            print $e->getMessage() . PHP_EOL;
        }

        return $setning;

    }

    public function slett() {

        try
        {
            $id = $this->id;
            $sql_tekst = "DELETE FROM Kommentar WHERE id = :id;";
            $setning = self::$db->prepare($sql_tekst);
            $setning->bindParam(':id',$id,PDO::PARAM_INT);

            if($setning->execute()){
                return true;
            }

        }
        catch (Exception $e) {
            print $e->getMessage() . PHP_EOL;
        }

        return false;

    }

    public function hentId() {
        return $this->id;
    }

    public function hentTidspunkt() {
        return $this->opprettet;
    }

    public function hentTekst() {
        return $this->tekst;
    }

    public function hentInnlegg_id() {
        return $this->Innlegg_id;
    }

    public function hentKommentar_id() {
        return $this->Kommentar_id;
    }

    public function hentEier() {
        return $this->Bruker_id;
    }

    public function hentKallenavn() {
        return $this->kallenavn;
    }

    public function settId(int $id) {
        $this->id = $id;
    }

    public function settTekst($tekst) {
        $this->tekst = $tekst;
    }


}

?>
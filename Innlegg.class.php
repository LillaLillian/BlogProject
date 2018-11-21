<?php

// Klasse som er knyttet opp mot Innlegg-tabellen i databasen

class Innlegg {

    static private $db;
    private $id;
    private $tittel;
    private $tekst;
    private $opprettet;
    private $Blogg_id;
    private $Bruker_id;
    private $antall_treff;
    private $stikkordListe = array();
    private $kommentarer = array();
    private $kallenavn;
    private $antall_kommentarer;


    // maa være tom pga PDO-funksjonen fetchObject() ?
    public function __construct() {

    }

    public function opprett($tittel, $tekst, $Blogg_id, $Bruker_id) {
        $this->tittel = $tittel;
        $this->tekst = $tekst;
        $this->Blogg_id = $Blogg_id;
        $this->Bruker_id = $Bruker_id;
        $this->antall_treff = 0;
    }

    static public function settDb($database) {
        self::$db = $database;
    }

    // finner alle innlegg i databasen og returnerer et array av objekter
    static public function visAlle() {
        $innleggTabell= array();

        try
        {

            $setning = self::$db->prepare("SELECT Innlegg.*, Bruker.kallenavn FROM Innlegg, Bruker");
            $setning->execute();

            while ($innlegg = $setning->fetchObject('Innlegg') )
            {
                $innleggTabell[] = $innlegg;
            }

        }
        catch (Exception $e) {
            print $e->getMessage() . PHP_EOL;
        }

        return $innleggTabell;
    }

    // finner alle innlegg for en gitt blogg og returnerer et array av objekter
    static public function visAlleFraBlogg(int $id) {
        $innleggTabell= array();

        try
        {
            $setning = self::$db->prepare("SELECT Innlegg.*, Bruker.kallenavn FROM Innlegg, Bruker WHERE Innlegg.Bruker_id=Bruker.id AND Innlegg.Blogg_id=:id");
            $setning->bindParam(':id',$id,PDO::PARAM_INT);
            $setning->execute();

            while ($innlegg = $setning->fetchObject('Innlegg') )
            {
                $innleggTabell[] = $innlegg;
            }
        }
        catch (Exception $e) {
            print $e->getMessage() . PHP_EOL;
        }
        return $innleggTabell;
    }

    // finner alle innlegg for en gitt maaned i et gitt aar
    static public function visArkiv(int $id, int $aar, int $maaned) {
        $innleggTabell= array();

        try
        {
            $setning = self::$db->prepare("SELECT Innlegg.*, Bruker.kallenavn FROM Innlegg, Bruker WHERE Innlegg.Bruker_id=Bruker.id AND Innlegg.Blogg_id=:id AND YEAR(Innlegg.opprettet)=:aar AND MONTH(Innlegg.opprettet)=:maaned ORDER BY opprettet DESC");
            $setning->bindParam(':id',$id,PDO::PARAM_INT);
            $setning->bindParam(':aar',$aar,PDO::PARAM_INT);
            $setning->bindParam(':maaned',$maaned,PDO::PARAM_INT);
            $setning->execute();

            while ($innlegg = $setning->fetchObject('Innlegg') )
            {
                $innleggTabell[] = $innlegg;
            }
        }
        catch (Exception $e) {
            print $e->getMessage() . PHP_EOL;
        }
        return $innleggTabell;
    }

    // finner ett innlegg basert paa Innlegg-id og returnerer et Innlegg-objekt
    static public function visEtt(int $id) {

        try
        {
            $setning = self::$db->prepare("SELECT Innlegg.*, Bruker.kallenavn FROM Innlegg, Bruker WHERE Innlegg.Bruker_id=Bruker.id AND Innlegg.id=:id");
            $setning->bindParam(':id',$id,PDO::PARAM_INT);
            $setning->execute();

            $innlegg = $setning->fetchObject('Innlegg');
        }
        catch (Exception $e) {
            print $e->getMessage() . PHP_EOL;
        }
        return $innlegg;
    }

    // lagrer et innlegg i databasen
    public function lagre() {

        try
        {
            $sql_tekst = "INSERT INTO Innlegg ";
            $sql_tekst .= "(tittel, tekst, Blogg_id, Bruker_id, antall_treff)";
            $sql_tekst .= "VALUES (";
            $sql_tekst .= "'".$this->hentTittel()."',";
            $sql_tekst .= "'".$this->hentTekst()."',";
            $sql_tekst .= "'".$this->hentBlogg_id()."',";
            $sql_tekst .= "'".$this->hentEier()."',";
            $sql_tekst .= "'".$this->hentAntallTreff()."'";
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

    // sletter et innlegg i databasen
    public function slett() {

        try
        {
            $id = $this->id;
            $sql_tekst = "DELETE FROM Innlegg ";
            $sql_tekst .= "WHERE id = :id;";

            $setning = self::$db->prepare($sql_tekst);

            $setning->bindParam(':id',$id,PDO::PARAM_INT);

            $setning->execute();

        }
        catch (Exception $e) {
            print $e->getMessage() . PHP_EOL;
        }

        return $setning;

    }



    public function hentId() {
        return $this->id;
    }

    public function hentTittel() {
        return $this->tittel;
    }

    public function hentTekst() {
        return $this->tekst;
    }

    public function hentTidspunkt() {
        return $this->opprettet;
    }

    public function hentBlogg_id() {
        return $this->Blogg_id;
    }

    public function hentEier() {
        return $this->Bruker_id;
    }

    public function hentAntallTreff() {
        return $this->antall_treff;
    }

    public function hentKallenavn() {
        return $this->kallenavn;
    }

    public function hentKommentarer() {
        return $this->kommentarer;
    }

    public function hentAntallKommentarer() {
        $stmt = self::$db->prepare("SELECT COUNT(*) AS antall FROM Kommentar WHERE Innlegg_id = :innlegg_id");
        $stmt->bindParam(':innlegg_id', $this->id, PDO::PARAM_INT);
        $stmt->execute();

        if ($rad = $stmt->fetch()) {
            $this->antall_kommentarer = $rad["antall"];

            return $this->antall_kommentarer;
        } else{
            return 0;
        }

    }

    public function hentStikkordListe() {
        return $this->stikkordListe;
    }

    public function settId($id) {
        $this->id = (int) $id;
    }

    public function settTittel($tittel) {
        $this->tittel = $tittel;
    }

    public function settTekst($tekst) {
        $this->tekst = $tekst;
    }

    public function settEier($Bruker_id) {
        $this->Bruker_id = $Bruker_id;
    }
    public function oekAntallTreff() {
        $innleggId = $this->id;
        $antall = $this->hentAntallTreff() + 1;
        $sql = "UPDATE Innlegg SET antall_treff=$antall WHERE id=$innleggId";
        $setning = self::$db->prepare($sql);
        $setning->bindParam(':id',$this->id,PDO::PARAM_INT);
        $setning->execute();
    }

    // legger inn kommentarer fra et array med Kommentar-objekter. Setter antall kommentarer.
    public function settKommentarer() {
        Kommentar::settDb(self::$db);
        $this->kommentarer = Kommentar::visAlleForInnlegg($this->id);
        $this->antall_kommentarer = count($this->kommentarer);
    }

    // henter inn tilhørende stikkord fra databasen og legger de til et objekt
    public function settStikkordListe() {

        try
        {
            $setning = self::$db->prepare("SELECT Stikkord.id, Stikkord.tekst, Innlegg_stikkord.Innlegg_id FROM Stikkord, Innlegg_stikkord WHERE Stikkord.id=Innlegg_stikkord.Stikkord_id AND Innlegg_stikkord.Innlegg_id=:id");
            $setning->bindParam(':id',$this->id,PDO::PARAM_INT);
            $setning->execute();

            while ($stikkord = $setning->fetchObject('Stikkord') )
            {
                $this->stikkordListe[] = $stikkord;
            }

        }
        catch (Exception $e) {
            print $e->getMessage() . PHP_EOL;
        }


    }

}

?>
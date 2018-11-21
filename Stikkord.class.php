<?php
/**
 * Created by PhpStorm.
 * User: laogong
 * Date: 13/04/2018
 * Time: 12:17
 */

// Klasse som er knyttet opp mot Stikkord-tabellen i databasen

class Stikkord {
    private static $db;
    private $id;
    private $tekst;
    private $Innlegg_id;

    public function __construct() {

    }

    public function opprett( $tekst) {

        $this->tekst = $tekst;

    }

    // finner alle stikkord for et gitt innlegg i databasen og returnerer et array av stikkord-objekter
    static public function visAlleForInnlegg(int $Innlegg_id) {

            $stikkordTabell= array();

            try
            {

                $setning = self::$db->prepare("SELECT Stikkord.id, Stikkord.tekst, Innlegg_stikkord.Innlegg_id FROM Stikkord, Innlegg_stikkord WHERE Stikkord.id=Innlegg_stikkord.Stikkord_id AND Innlegg_stikkord.Innlegg_id = :id");
                $setning->bindParam(':id',$Innlegg_id,PDO::PARAM_INT);
                $setning->execute();

                while ($stikkord = $setning->fetchObject('Stikkord') )
                {
                    $stikkordTabell[] = $stikkord;
                }

            }
            catch (Exception $e) {
                print $e->getMessage() . PHP_EOL;
            }

            return $stikkordTabell;
        }

    static public function settDb($database) {
        self::$db = $database;
    }

    // returnerer tabell med Stikkord-objekter som kun har stikkord og id
    static public function visAlle() {
        $alle_stikkord = array();

        try {
            $setning = self::$db->prepare("SELECT * FROM Stikkord");
            $setning->execute();

            while ($stikkord = $setning->fetchObject('Stikkord') )
            {
                $alle_stikkord[] = $stikkord;

            }

        }
        catch (Exception $e) {
            print $e->getMessage() . PHP_EOL;
        }

        return $alle_stikkord;

    }

    // lagrer stikkord i databasen
    public function lagre() {

        try
        {
            $sql_tekst = "INSERT INTO Stikkord ";
            $sql_tekst .= "(tekst)";
            $sql_tekst .= "VALUES (";
            $sql_tekst .= "'".$this->hentTekst()."'";
            $sql_tekst .= ")";
            $setning = self::$db->prepare($sql_tekst);


            if($setning->execute()) {
                $this->settId(self::$db->lastInsertId());
            } else
                // dersom stikkordet allerede finnes, hent id til stikkordet og bruk det
                {
                $tekst=$this->hentTekst();
                $sql_tekst = "SELECT * FROM Stikkord WHERE tekst = :tekst";

                $setning = self::$db->prepare($sql_tekst);
                $setning->bindParam(':tekst',$tekst,PDO::PARAM_STR);
                $setning->execute();
                $stikkord = $setning->fetchObject('Stikkord');
                $this->settId($stikkord->hentId());

            }

        }
        catch (Exception $e) {
            print $e->getMessage() . PHP_EOL;
        }

        return $setning;

    }

    // lagrer kobling mellom innlegg og stikkord i databasen
    public function lagreForInnlegg(int $innleggId) {

        $this->Innlegg_id = $innleggId;

        try
        {

            $sql_tekst = "INSERT INTO Innlegg_stikkord ";
            $sql_tekst .= "(Stikkord_id, Innlegg_id)";
            $sql_tekst .= "VALUES (";
            $sql_tekst .= "'".$this->hentId()."',";
            $sql_tekst .= "'".$this->hentInnleggId()."'";
            $sql_tekst .= ")";
            $setning = self::$db->prepare($sql_tekst);
            $setning->execute();

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
            $sql_tekst = "DELETE FROM Stikkord ";
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


    public function hentTekst() {
        return $this->tekst;
    }

    public function hentInnleggId() {
        return $this->Innlegg_id;
    }

    public function settId(int $id) {
        $this->id = $id;
    }

    public function settTekst($tekst) {
        $this->tekst = $tekst;
    }


}

?>
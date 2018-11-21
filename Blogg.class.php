<?php
/**
 * Created by PhpStorm.
 * User: laogong
 * Date: 10/04/2018
 * Time: 14:16
 */

// Klasse som er knyttet opp mot Blogg-tabellen i databasen

class Blogg
{
    static private $db;
    private $id;
    private $tittel;
    private $stil;
    private $Bruker_id;
    private $kallenavn;

    public function __construct()
    {

    }

    static public function settDb($database)
    {
        self::$db = $database;
    }

    public function opprett($tittel, $stil, $Bruker_id)
    {
        $this->tittel = $tittel;
        $this->stil = $stil;
        $this->Bruker_id = $Bruker_id;
    }

    public function lagre()
    {
        $tittel = $this->hentTittel();
        $stil = $this->hentStil();
        $Bruker_id = $this->hentEier();

        try {
            $sql = "INSERT INTO Blogg (tittel, stil, Bruker_id) 
            VALUES (:tittel, :stil, :Bruker_id)";

            $stmt = self::$db->prepare($sql);
            $stmt->bindParam(':tittel', $tittel);
            $stmt->bindParam(':stil', $stil);
            $stmt->bindParam(':Bruker_id', $Bruker_id);
            if ($stmt->execute()) {
                $this->settId(self::$db->lastInsertId());

            }
        } catch (Exception $e) {
            print $e->getMessage() . PHP_EOL;
        }
    }

    public function hentAntallInnlegg()
    {
        $stmt = self::$db->prepare("SELECT COUNT(*) AS antall FROM Innlegg WHERE Blogg_id = :blogg_id");
        $stmt->bindParam(':blogg_id', $this->id, PDO::PARAM_INT);
        $stmt->execute();

        if ($rad = $stmt->fetch()) {
            $antall = $rad["antall"];

            return $antall;
        } else{
            return 0;
        }


    }

    public function hentId()
    {
        return $this->id;
    }

    public function hentTittel()
    {
        return $this->tittel;
    }

    public function hentStil()
    {
        return $this->stil;
    }

    public function hentEier()
    {
        return $this->Bruker_id;
    }

    public function hentKallenavn()
    {
        return $this->kallenavn;
    }

    public function settTittel($tittel)
    {
        $this->tittel = $tittel;
    }

    public function settStil($stil)
    {
        $this->stil = $stil;
    }

    public function settId($id)
    {
        $this->id = $id;
    }

    public function settEier($Bruker_id)
    {
        $this->Bruker_id = $Bruker_id;
    }

}

?>
<?php

// Klasse som brukes for å hente fra blogger i databasen

class BloggRegister {

    private $db;

    public function __construct(PDO $db) {
        $this->db = $db;
    }

    public function hentEiersBlogger(int $id)
    {
        $blogger = array();

        try
        {
            $setning = $this->db->prepare("SELECT Blogg.*, Bruker.kallenavn FROM Blogg INNER JOIN Bruker ON Blogg.Bruker_id = Bruker.id AND Bruker.id=:id");
            $setning->bindParam(':id',$id,PDO::PARAM_INT);
            $setning->execute();

            while ($blogg = $setning->fetchObject('Blogg') )
            {
                $blogger[] = $blogg;
            }
        }
        catch (Exception $e) {
            print $e->getMessage() . PHP_EOL;
        }

        return $blogger;
    }

    public function visAlleBlogger(): array
    {
        $blogger= array();

        try
        {
            $setning = $this->db->prepare("SELECT Blogg.*, Bruker.kallenavn FROM Blogg, Bruker WHERE Blogg.Bruker_id=Bruker.id ORDER BY tittel");
            $setning->execute();

            while ($blogg = $setning->fetchObject('Blogg') )
            {
                $blogger[] = $blogg;
            }

        }
        catch (Exception $e) {
            print $e->getMessage() . PHP_EOL;
        }

        return $blogger;
    }

    public function visEnBlogg(int $id): Blogg {


        try
        {
            $setning = $this->db->prepare("SELECT Blogg.*, Bruker.kallenavn FROM Blogg, Bruker WHERE Blogg.Bruker_id=Bruker.id AND Blogg.id=:id");
            $setning->bindParam(':id',$id,PDO::PARAM_INT);
            $setning->execute();

            $blogg = $setning->fetchObject('Blogg');


        }
        catch (Exception $e) {
            print $e->getMessage() . PHP_EOL;
        }

        return $blogg;
    }

    public function visAlleInnlegg(): array
    {
        $innleggTabell= array();

        try
        {

            $setning = $this->db->prepare("SELECT Innlegg.*, Bruker.kallenavn FROM Innlegg INNER JOIN Bruker ON Innlegg.Bruker_id = Bruker.id ORDER BY opprettet DESC");
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

    public function visInnlegg(int $id) {
        $innleggTabell= array();

        try
        {

            $setning = $this->db->prepare("SELECT Innlegg.*, Bruker.kallenavn FROM Innlegg, Bruker WHERE Innlegg.Bruker_id=Bruker.id AND Innlegg.Blogg_id=:id");
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

    public function visAlleKommentarer(): array
    {
        $kommentarer= array();

        try
        {
            // maa gjoeres om til prepared setninger
            $resultat = $this->db->query("SELECT Kommentar.*, Bruker.kallenavn FROM Kommentar, Bruker WHERE Kommentar.Bruker_id=Bruker.id");
            while ($kommentar = $resultat->fetchObject('Kommentar') )
            {
                $kommentarer[] = $kommentar;
            }

        }
        catch (Exception $e) {
            print $e->getMessage() . PHP_EOL;
        }

        return $kommentarer;
    }

    public function visAlleStikkord(): array
    {
        $stikkordTabell= array();

        try
        {

            $setning = $this->db->prepare("SELECT Stikkord.id, Stikkord.tekst, Innlegg_stikkord.Innlegg_id FROM Stikkord, Innlegg_stikkord WHERE Stikkord.id=Innlegg_stikkord.Stikkord_id");
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

    public function leggTilInnlegg(Innlegg $innlegg)
    {
        $title = $innlegg->hentTittel();
        $text = $innlegg->hentTekst();
        $blogg = $innlegg->hentBlogg_id();
        $eier = $innlegg->hentEier();
        try
        {
            $stmt = $this->db->prepare("INSERT INTO Innlegg (tittel, tekst, Blogg_id, Bruker_id, antall_treff)
					VALUES (:tittel, :tekst, :blogg, :bruker, 0)");
            $stmt->bindParam(':tittel', $title, PDO::PARAM_STR);
            $stmt->bindParam(':tekst', $text, PDO::PARAM_STR);
            $stmt->bindParam(':blogg', $blogg, PDO::PARAM_STR);
            $stmt->bindParam(':bruker', $eier, PDO::PARAM_STR);
            $result = $stmt->execute();

            if($result) {
                return $this->db->lastInsertId();
            }
            else {
                echo "Feil ved innsetting av post!";
                return false;
            }
        }
        catch(Exception $e) { $this->NotifyUser("En feil oppstod", $e->getMessage()); }

    }

    //public function slettInnlegg(Innlegg $innlegg) : int
    public function slettInnlegg(int $id)
    {
        //$id = $innlegg->hentId();
        $sql = "DELETE FROM Innlegg WHERE id=$id";
        $sql_vedlegg = "DELETE FROM Vedlegg WHERE Innlegg_id = $id";
        try
        {
            //$this->db->query($sql) === TRUE;

            if ($this->db->query($sql)) {

                }
            else {
                $this->db->query($sql_vedlegg);
                $this->db->query($sql);

            }


      /*      if ($this->db->query($sql) === TRUE) {
                echo "Resultat 1 (positivt?)";
            } else {
                echo "Resultat 2 (negativt?)";
            }*/ // Tror ikke dette fungerte
        }
        catch(Exception $e) { $this->NotifyUser("En feil oppstod", $e->getMessage()); }

    }
}


?>
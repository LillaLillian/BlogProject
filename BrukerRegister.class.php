<?php
/**
 * Created by PhpStorm.
 * User: ole
 * Date: 07.04.2018
 * Time: 14:53
 */

class BrukerRegister implements BrukerInterface
{
    private $db;

    public function __construct(PDO $db)
    {
        $this->db = $db;
    }

    public function visAlleBrukere(): array
    {
        $brukere = array();
        $sql = 'SELECT * FROM Bruker ORDER BY etternavn';

        try {
            $result = $this->db->query($sql);
            $brukere = $result->fetchAll(PDO::FETCH_CLASS, "Bruker");
        } catch (Exception $e) {
            print $e->getMessage() . PHP_EOL;
        }

        return $brukere;
    }

    public function visBruker(int $id): bruker
    {
        try {
            $result = $this->db->prepare("SELECT * FROM Bruker WHERE id = :id");
            $result->bindParam(":id", $id);
            $result->execute();

            if ($result->rowCount() > 0) {
                return $result->fetchObject("Bruker");
            }
        } catch (InvalidArgumentException $e) {
            print $e->getMessage() . PHP_EOL;
        }
    }

    public function leggTilBruker(bruker $bruker): int
    {
        $brukernavn = $bruker->hentBrukernavn();
        $fornavn = $bruker->hentFornavn();
        $etternavn = $bruker->hentEtternavn();
        /*$rettigheter=$bruker->hentRettigheter();*/
        $epost = $bruker->hentEpost();
        $passord = $bruker->hentPassord();
        $epost_id = $bruker->hentEpostId();


        try {
            $sql = "INSERT INTO Bruker (fornavn, etternavn, epost, kallenavn, passord_hash, epost_id) 
            VALUES (:fornavn, :etternavn, :epost, :brukernavn, :passord, :epost_id)";

            $stmt = $this->db->prepare($sql);
            $stmt->bindParam(':fornavn', $fornavn);
            $stmt->bindParam(':etternavn', $etternavn);
            $stmt->bindParam(':epost', $epost);
            $stmt->bindParam(':brukernavn', $brukernavn);
            $stmt->bindParam(':passord', $passord);
            $stmt->bindParam(':epost_id', $epost_id);

            if ($stmt->execute()) {
                $last_id = $this->db->lastInsertId();
                return $last_id;
            } else {
                return 0;
            }

        } catch (InvalidArgumentException $e) {
            print $e->getMessage() . PHP_EOL;
        }
    }


    public function endreBruker(bruker $bruker): int
    {
        $id = $bruker->hentId();
        $etternavn = $bruker->hentEtternavn();
        $fornavn = $bruker->hentFornavn();
        $brukernavn = $bruker->hentBrukernavn();
        $rettigheter = $bruker->hentRettigheter();
        $passord = $bruker->hentPassord();
        $epost = $bruker->hentEpost();

        $sql = "UPDATE Bruker SET fornavn=:first, etternavn=:last, epost=:epost, brukernavn=:brukernavn, rettigheter=:rettigheter, passord=:passord WHERE id=$id";

        $stmt = $this->db->prepare($sql);
        $stmt->bindParam(':first', $fornavn);
        $stmt->bindParam(':last', $etternavn);
        $stmt->bindParam(':epost', $epost);
        $stmt->bindParam(':brukernavn', $brukernavn);
        $stmt->bindParam(':rettigheter', $rettigheter);
        $stmt->bindParam(':passord', $passord);

        if ($stmt->execute()) {
            echo "<p>Endret " . date('H:i, jS F Y') . "</p>";
        } else {
            echo "Unable to update record";
        }
        return $id;
    }
}
<?php

class Sok {
    private $db;

    public function __construct(PDO $db) {
        $this->db = $db;
    }

    function sokInnhold(string $sokeord) {
        $idTabell= array();
        $param = "%{$sokeord}%";
        try
        {
            $stmt = $this->db->prepare("SELECT id FROM Innlegg WHERE tittel LIKE :param OR tekst LIKE :param");
            $stmt->bindParam(':param', $param, PDO::PARAM_STR);
            $stmt->execute();
            while($innleggId = $stmt->fetch()){
                $idTabell[] = $innleggId;
            }
        }
        catch (Exception $e) {
            print $e->getMessage() . PHP_EOL;
        }
        return $idTabell;
    }

    function sokStikkord($sokeord) {
        $idTabell= array();
        try
        {
            $stmt = $this->db->prepare("SELECT Innlegg_id FROM Innlegg_stikkord WHERE Stikkord_id = (SELECT id FROM Stikkord WHERE tekst = :sokeord)");
            $stmt->bindParam(':sokeord', $sokeord, PDO::PARAM_STR);
            $stmt->execute();
            while($innleggId = $stmt->fetch()){
                $idTabell[] = $innleggId;
            }


        }
        catch (Exception $e) {
            print $e->getMessage() . PHP_EOL;
        }
        return $idTabell;
    }

}
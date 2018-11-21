<?php

class filarkiv { // har kopiert kode fra vedleggforelesningen, og modifisert så det fungerer i vår database
        
        private $db; 
        private $twig;
   
        function __construct($db, $twig) {
           
            $this->db = $db; 
            $this->twig = $twig;
        } 
                
        private function NotifyUser($strHeader, $strMessage)
        {
            echo $this->twig->render('VisInnlegg.twig', array('status' => $strHeader, 'statusMessage' => $strMessage ));
        } 
                                       
        public function save(int $id) {
            $innleggId = $id;
            $file = $_FILES[FILNAVN_TAG]['tmp_name'];
            $name = $_FILES[FILNAVN_TAG]['name'];
            $type = $_FILES[FILNAVN_TAG]['type'];
            $size = $_FILES[FILNAVN_TAG]['size'];
            if(is_uploaded_file($file) && $size != 0)
            {
                    try
                    {
                        $data = file_get_contents($file);
                        $stmt = $this->db->prepare("INSERT INTO `Vedlegg` (storrelse, dato, mimetype, filnavn, kode, Innlegg_id)
					                                      VALUES (:size, now(), :mimetype, :filnavn, :kode, :innleggId)");
                        $stmt->bindParam(':size', $size, PDO::PARAM_INT);
                        $stmt->bindParam(':mimetype', $type, PDO::PARAM_STR);
                        $stmt->bindParam(':filnavn', $name, PDO::PARAM_STR);
                        $stmt->bindParam(':kode', $data, PDO::PARAM_LOB);
                        $stmt->bindParam(':innleggId', $innleggId, PDO::PARAM_INT);

                        $stmt->execute();
                    }
                    catch(Exception $e) {
                        }
            }

        }        

        // Viser oversikt over alle filer i db        
        public function visOversikt()
        {

            try
            {
                $stmt = $this->db->query("SELECT id, filnavn, dato FROM Vedlegg order by dato");
                $alleFiler = $stmt->fetchAll();

            }
            catch(Exception $e) { NotifyUser("En feil oppstod", $e-getMessage()); }

            echo $this->twig->render('VisInnlegg.twig', array('filene' => $alleFiler, 'skript' => $_SERVER['PHP_SELF'] ));
        }

    public function visOversikt2(int $id) // viser filinfo for vedlegg til et innlegg
    {
        try
        {
            $stmt = $this->db->query("SELECT id, filnavn, dato FROM Vedlegg WHERE Innlegg_id = $id");
            $files = $stmt->fetchAll();
        }
        catch(Exception $e) { NotifyUser("En feil oppstod", $e-getMessage()); }
        return $files;
    }
    /*
        Viser en fil fra databasen
    */
        public function visFil(int $id)
        {
            try
            {

                $stmt = $this->db->prepare("SELECT mimetype,kode,filnavn,dato FROM Vedlegg WHERE id = :id");
                $stmt->bindParam(':id', $id, PDO::PARAM_INT);
                $stmt->execute();
                if(!$item = $stmt->fetch(PDO::FETCH_ASSOC)) {
                    throw new InvalidArgumentException('Invalid id: ' . $id);
                }
                else {
                    $filnavn = $item['filnavn'];
                    $type = $item['mimetype'];
                    $data = $item['kode'];
                    $dato = $item['dato'];


                    // sett opp Mime type og Filnavn i header i henhold til verdier fra databasen
                    Header( "Content-type: $type" );
                    Header("Content-Disposition: filename=\"$filnavn\"");
                    Header("Content-Disposition: dato=\"$dato\"");
                    // Skriv bildet/filen til klienten
                    echo $data;
                }

        
   
            }
            catch(Exception $e) { $this->NotifyUser("En feil oppstod", $e->getMessage()); }

        }
}
?>
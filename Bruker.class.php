<?php

class bruker
{
    private $id;
    private $fornavn;
    private $etternavn;
    private $epost;
    private $brukernavn;
    private $passord;
    private $epost_id;
    private $IP_adress;
    private $userAgent;

    function __construct($brukernavn, $fornavn, $etternavn) {
        $this->brukernavn = $brukernavn;
        $this->fornavn = $fornavn;
        $this->etternavn = $etternavn;
        $this->IP_adress = $_SERVER['REMOTE_ADDR'];
        $this->userAgent = $_SERVER['HTTP_USER_AGENT'];
    }
    // Kode fra forelesning.

    public function verifyUser(){
        if($this->IP_adress == $_SERVER['REMOTE_ADDR'] && $this->userAgent == $_SERVER['HTTP_USER_AGENT']){
            return true;
        } else {
            return false;
        }
    }

    static public function aktiverBruker($db, $id){
        $stmt = $db->prepare("UPDATE Bruker SET active = 1 WHERE epost_id = :id");
        $stmt->bindParam(':id', $id, PDO::PARAM_STR);
        if($stmt->execute()) {
            return true;
        }
        else {
            return false;
        }
    }

    static function loggInn($db, $brukernavn_post){
        $stmt = $db->prepare("SELECT id, fornavn, etternavn, passord_hash, active FROM Bruker WHERE kallenavn=:bruker");
        $stmt->bindParam(':bruker', $brukernavn_post, PDO::PARAM_STR);
        $stmt->execute();

        if($rad = $stmt->fetch() )
        {
            $hash = $rad["passord_hash"];
            $etternavn = $rad["etternavn"];
            $fornavn = $rad["fornavn"];
            $bruker_id = $rad["id"];
            $active = $rad['active'];
        }
        if(password_verify($_POST['passord'], @$hash) && $active == 1){ // @ stopper feilmelding når innlogging feiler
            $_SESSION['innlogget'] = true;
            $_SESSION['bruker'] = new Bruker($brukernavn_post, $fornavn, $etternavn);
            $_SESSION['bruker_id'] = $bruker_id;
            $_SESSION['kallenavn'] = $brukernavn_post;

            return true;
         }

         else return false;

    }

    function settAlt(string $brukernavn, string $fornavn, string $etternavn, string $epost, string $passord, string $epost_id) {
        $this->fornavn = $fornavn;
        $this->etternavn = $etternavn;
        $this->epost = $epost;
        $this->brukernavn = $brukernavn;
        $this->passord = $passord;
        $this->epost_id = $epost_id;
    }

    function hentId() {
        return $this->id;
    }
    function hentNavn() {
        return $this->fornavn . " " . $this->etternavn;
    }
    function hentEtternavn() {
        return $this->etternavn;
    }
    function hentFornavn() {
        return $this->fornavn;
    }
    function hentBrukernavn() {
        return $this->brukernavn;
    }

    function hentEpost() {
        return $this->epost;
    }
    function hentPassord() {
        return $this->passord;
    }

    function hentEpostId(){
        return $this->epost_id;
    }

    function settFornavn($fornavn) {
        $this->fornavn = $fornavn;
    }
    function settEtternavn($etterNavn) {
        $this->etternavn = $etterNavn;
    }
    function settBrukernavn($brukernavn) {
        $this->brukernavn = $brukernavn;
    }

    function settPassord($passord) {
        $this->passord = $passord;
    }
    function settEpost($epost) {
        $this->epost = $epost;
    }
}
?>
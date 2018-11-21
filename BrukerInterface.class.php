<?php

interface BrukerInterface
{
    public function visAlleBrukere() : array;                   // Returnerer array med bruker referanser
    public function visBruker(int $id) : bruker;                // Returnerer referanse til bruker med angitt id
    public function leggTilBruker(bruker $bruker) : int;        // Returnerer id for nyopprettet bruker
    public function endreBruker(bruker $bruker) : int;          // Returnerer id for endret bruker..
}

<?php

// Bloggregisteret

// innlogget? -----------------------------
@session_start();

// autoload -------------------------------
require_once 'db_autorisering.php';
spl_autoload_register(function ($class_name) {
    require_once $class_name . '.class.php';
});

// twig ------------------------------------
require_once 'vendor/autoload.php';
$loader = new Twig_Loader_Filesystem('templates');
$twig = new Twig_Environment($loader);

// ------------------------------------------

$bloggReg = new BloggRegister($db);
$innlogget = "";    // variabel til twig
Blogg::settDb($db);


if(isset($_SESSION['innlogget']) && $_SESSION['innlogget']){
    $innlogget = "innlogget";
}

echo $twig->render('Header.twig', array('tittel' => "standard", 'innlogget' => $innlogget));

if ($blogger = $bloggReg->visAlleBlogger()) {
    echo $twig->render('BloggRegister.twig', array('Blogger' => $blogger, 'innlogget' => $innlogget, 'tittel' => "standard"));

} else {
    echo "Kunne ikke lese inn blogger";
}


?>
<?php


    spl_autoload_register(function ($class_name) { // har kopiert kode fra vedleggforelesningen, og modifisert så det fungerer i vår database
        require_once $class_name . '.class.php';
    });
    require_once '../vendor/autoload.php';
    require_once '../db_autorisering.php';
    define('FILNAVN_TAG', 'bildeFil');


    $loader = new Twig_Loader_Filesystem('../filopplasting');
    $twig = new Twig_Environment($loader);

    $arkiv = new filarkiv($db, $twig);
      
    if(isset($_GET['id']) && ctype_digit($_GET['id']))
    {
        $id = intval($_GET['id']);
        $arkiv->visFil($id);
    }     
    
    else 
    {
        if(isset($_POST['post_file']))
        {
            $arkiv->save();
        }

        $arkiv->visOversikt();  

    } 
      
?>
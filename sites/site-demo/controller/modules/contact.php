<?php

$backgroundImage    = $target->attributes['background-image']->content()['file'];
$headline           = $target->attributes['headline']->content();
$headlineBody       = $target->attributes['body']->content();


$result = false;
if( filter_has_var(INPUT_POST, "bouton_formulaire") )
{
    $prenom     = filter_input(INPUT_POST, "prenom");
    $nom        = filter_input(INPUT_POST, "nom");
    $adresse    = filter_input(INPUT_POST, "email");
    $societe    = filter_input(INPUT_POST, "societe");
    $message    = filter_input(INPUT_POST, "question");
    
    $subject    = "[".strtoupper($societe)."] ".strtoupper($nom)." ".ucfirst(strtolower($prenom));
    $header     = "From: \"".$subject."\"<".$adresse.">\n";
    
    $result = mail("info@witch-case.com", $subject, $message, $header);
}


include $module->getDesignFile();
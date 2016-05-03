<?php

if( filter_has_var(INPUT_POST, "login") 
    && isset($_SESSION['user']['connexionID'])
){
    header('location : http://'.$localisation->siteAccess);
    exit();
}

session_destroy();

$contextFile = "login.php";

include $module->getDesignFile();
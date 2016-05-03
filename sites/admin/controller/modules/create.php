<?php

require_once 'system/classes/targets/Draft.php';
require_once 'system/classes/Structure.php';
require_once 'system/classes/Location.php';
require_once 'system/classes/Localisation.php';

$userID         = $_SESSION["user"]["connexionID"];
$action         = false;
$messages       = [];

if( filter_has_var(INPUT_POST, "cancelButton") || filter_has_var(INPUT_POST, "exitButton") )
{   $action = "redirectToVisualisation";    }

elseif( filter_has_var(INPUT_POST, "create") )
{   $action = "create";    }

else
{   $action = "displayForm"; }

if( $action == "redirectToVisualisation" )
{
    $localisationID =   filter_input(   INPUT_GET, 
                                        "localisation", 
                                        FILTER_VALIDATE_INT 
                        );
    
    $destination    =   new Localisation($localisationID);
    $redirectionURI = "http://".$localisation->siteAccess.$destination->url;
    
    header('Location: '.$redirectionURI );
    exit();
}

if( $action == "create" )
{
    $structure      =   filter_input(   INPUT_POST, "structure" );
    $localisationID =   filter_input(   INPUT_GET, 
                                        "localisation", 
                                        FILTER_VALIDATE_INT
                        );
    $name           =   filter_input(   INPUT_POST, "name" );
    $customUrl      =   filter_input(   INPUT_POST, "customUrl" );
    $description    =   filter_input(   INPUT_POST, "description" );
    $modulePost     =   filter_input(   INPUT_POST, "module" );
    
    $location = Location::getFromLocalisationID($localisationID); 
    
    $parentsID = [];
    foreach( $location->localisations as $parentLocalisation )
    {   $parentsID[] = $parentLocalisation->id; }
    
    if( !in_array($modulePost, $configuration->getModules($localisation->site)) )
    {   $modulePost = 'view';   }
    
    if( !empty($structure) )
    {
        $draft      =   new Draft( strtolower($structure) );
        
        $result     =   $draft->create( $parentsID, 
                                        $modulePost,
                                        $name, 
                                        $description, 
                                        $customUrl
                        );
        
        $redirectionURI = "http://".$localisation->siteAccess."/edit?localisation=".$result;
    }
    else
    {
        
        $result =   Location::create(   $parentsID, 
                                        $name, 
                                        $modulePost, 
                                        '', 
                                        NULL, 
                                        $description, 
                                        $customUrl
                    );
        
        $destination = new Localisation($result);
        
        $redirectionURI = "http://".$localisation->siteAccess.$destination->url;
    }
    
    if( !$result )
    {
        $message    = "Could not create draft, please try again";
        $messages[] = $message;
        
        $action = "displayForm";
    }
    else
    {
        header('Location: '.$redirectionURI );
        exit();
    }
}

if( $action == "displayForm" )
{
    $targetLocalisationID   = filter_input(INPUT_GET, "localisation", FILTER_VALIDATE_INT);
    $targetLocation         = Location::getFromLocalisationID($targetLocalisationID);
    
    $sites = [];
    foreach( $targetLocation->localisations as $linkedSite => $linkedLocalisation )
    {   if( strcmp($localisation->site, $linkedSite) != 0
            && in_array( $linkedSite, $localisation->administratedSites() )
        ) {
            $sites[]    =   [   "label"     => ucfirst($linkedSite),
                                "value"     => $linkedLocalisation->id, 
                                "checked"   => true
                            ];
    }   }
    
    $modulesVar     = $configuration->getModulesVariables( $localisation->site );
    $defaultModule  = $modulesVar["create"]["defaultModule"];
    
    $modulesDisplay = [];
    foreach( $modulesVar as $moduleName => $variables )
    {
        $modulesDisplay[] = [
                                'name'      => $moduleName,
                                'default'   => ( strcmp($defaultModule, $moduleName) == 0 )
                            ];
    }
    
    $structureList = Structure::listStructures();
    
    $structures = [];
    foreach( $structureList as $structureItem )
    {
        $structures[]   =   [   "label" => ucfirst($structureItem['name']),
                                "value" => $structureItem['name']
                            ];
    }
    
    include $module->getDesignFile();
}
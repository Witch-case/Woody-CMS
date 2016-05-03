<?php

require_once 'system/classes/targets/Content.php';
require_once 'system/classes/targets/Draft.php';
require_once 'system/classes/targets/Archive.php';
require_once 'system/classes/Structure.php';

if( filter_has_var(INPUT_POST, "publishStructure") )
{   $action = "publishStructure";   }

elseif( filter_has_var(INPUT_POST, "deleteStructures") )
{   $action = "deleteStructures";   }

elseif( filter_has_var(INPUT_GET, "view") )
{   $action = "viewStructure";  }

elseif( filter_has_var(INPUT_POST, "createStructure") 
        || strcmp( filter_input(INPUT_POST, 'currentAction'), "creatingStructure" ) == 0
) {
    $action = "createStructure";
}

elseif( filter_has_var(INPUT_GET, "edit") 
        ||  filter_has_var(INPUT_POST, "deleteAttribute")
        ||  filter_has_var(INPUT_POST, "addAttribute")
){
    $action = "editStructure";
}

else
{   $action = "listStructures"; }


$messages = [];
$baseUri  = "http://".$localisation->siteAccess.$localisation->url;

if( strcmp($action, "publishStructure") == 0 )
{
    $structureName      = filter_input( INPUT_GET, 'edit' );
    $structure          = new Structure($structureName, true);
    
    $attributesPost =   filter_input(   INPUT_POST,
                                        "attributes",
                                        FILTER_DEFAULT,
                                        FILTER_REQUIRE_ARRAY
                        );
    
    $attributes = [];
    foreach( $attributesPost as $attributesPostData )
    {
        $attributeType  = $attributesPostData['type'];
        $attributeClass = ucfirst( $attributeType );
        
        require_once $module->requireAttributeClass($attributeClass);
        
        $parameters = [];
        if( isset($attributesPostData['parameters']) && is_array($attributesPostData['parameters']) )
        {   $parameters = $attributesPostData['parameters'];    }
        
        $attribute    = new $attributeClass(
                                Localisation::cleanupString($attributesPostData['name']),
                                $parameters
                        );
        
        $attributes[] = $attribute;
    }
    $log->debug($attributes, false, 1);
    if( !$structure->publish($attributes) )
    {
        $messages[] = "Publication failed, please try again";
        $action     = "editStructure";
    }
    else 
    {
        $messages[] = "Publication of structure ".$structure->name." successfull";
        $action     = "listStructures";
    }
}

if( strcmp($action, "createStructure") == 0 )
{
    if( strcmp(filter_input(INPUT_POST, "currentAction"), "creatingStructure") == 0 )
    {
        $nextStep = true;
        $namePost = filter_input(INPUT_POST, "name");
        
        if( !$namePost )
        {
            $nextStep = false;
            $messages[] = "Vous devez saisir un nom valide pour votre structure.";
        }
        
        if( $nextStep )
        {
            $name       = Localisation::cleanupString( $namePost );
            $structure  = new Structure($name);
            
            if( $structure->exist )
            {
                $nextStep = false;
                $messages[] = "Le nom que vous avez saisi est déjà utilisé, veuillez en saisir un autre.";
            }
        }
        
        if( $nextStep )
        {
            $uri = $baseUri."?edit=".$name;
            
            $structureCopyPost = filter_input(INPUT_POST, "structureCopy");
            
            if( $structureCopyPost )
            {   $uri .= "&base=".$structureCopyPost;    }
            
            header('Location :'.$uri);
            exit;
        }
    }
    
    $structuresData = Structure::listStructures(true);
    
    include $module->getDesignFile('modules/structures/create.php');
}

if( strcmp($action, "editStructure") == 0 )
{
    $structureName = filter_input( INPUT_GET, 'edit' );
    
    $attributes = [];
    if( !filter_has_var(INPUT_POST, "currentAction") 
        || strcmp( filter_input(INPUT_POST, "currentAction"), "editingStructure" ) != 0
    ){
        if( filter_has_var(INPUT_GET, "base") )
        {   $structure = new Structure( filter_input(INPUT_GET, "base") );  }
        else
        {   $structure = new Structure($structureName); }
        
        foreach( $structure->attributes as $attribute )
        {   $attributes[] = $attribute; }
    }
    else
    {
        $deleteAttributePost    =   filter_input(   INPUT_POST,
                                                    "deleteAttribute",
                                                    FILTER_DEFAULT,
                                                    FILTER_REQUIRE_ARRAY
                                    );
        if( !$deleteAttributePost )
        {   $deleteAttributePost = [];  }
        
        $attributesPost =   filter_input(   INPUT_POST,
                                            "attributes",
                                            FILTER_DEFAULT,
                                            FILTER_REQUIRE_ARRAY
                            );
        
        if( !$attributesPost )
        {   $attributesPost = [];   }
        
        foreach( $attributesPost as $indice => $attributePostData )
        {   if( !isset($deleteAttributePost[$indice]) )
            {
                $attributeType  = $attributePostData['type'];
                $attributeClass = ucfirst($attributeType);
                
                require_once $module->requireAttributeClass($attributeClass);
                
                if( isset($attributePostData['parameters']) )
                {   $parameters = $attributePostData['parameters']; }
                else
                {   $parameters = [];   }
                
                $newAttribute = new $attributeClass( $attributePostData['name'], $parameters );
                $attributes[] = $newAttribute;
        }   }
        
        if( filter_has_var(INPUT_POST, "addAttribute") )
        {
            $attributeType  = filter_input(INPUT_POST, "addAttributType");
            $attributeClass = ucfirst($attributeType);
            
            require_once $module->requireAttributeClass($attributeClass);
            
            $newAttribute = new $attributeClass( 'Entrez un nom' );
            $attributes[] = $newAttribute;
        }
    }
    
    $attributesList = $configuration->getAllAttributes($localisation->site);
    
    include $module->getDesignFile('modules/structures/edit.php');
}

if( strcmp($action, "viewStructure") == 0 )
{
    $structureName      = filter_input(INPUT_GET, 'view');
    $structure          = new Structure($structureName);
    $creationDateTime   = $structure->createTime();
    if( $structure->isArchive )
    {
        $attributes         = $structure->archivedAttributes;
        $archivedAttributes = [];
    }
    else
    {
        $attributes         = $structure->attributes;
        $archivedAttributes = $structure->archivedAttributes;
    }
    
    $modificationHref   = $baseUri."?edit=".$structure->name;
    
    include $module->getDesignFile('modules/structures/view.php');
}

if( strcmp($action, "deleteStructures") == 0 )
{
    $structuresPost =   filter_input(   INPUT_POST,
                                        "structures",
                                        FILTER_DEFAULT,
                                        FILTER_REQUIRE_ARRAY
                        );
    
    if( $structuresPost )
    {   foreach( $structuresPost as $structureName )
        {
            $structure = new Structure($structureName);
            if( !$structure->delete() )
            {   $messages[] = "Deletion of ".$structureName." failed";  }
            else
            {   $messages[] = "Structure ".$structureName." successfully deleted";  }
    }   }
    
    $action = "listStructures";
}

if( strcmp($action, "listStructures") == 0 )
{
    $baseUriOrder           =   $baseUri;
    $baseUriArchives        =   $baseUri;
    $getSeparatorOrder      =   '?';
    $getSeparatorArchives   =   '?';
    $newSeparator           =   '&';
    $orderfield             =   filter_input(INPUT_GET, 'orderfield');
    $order                  =   filter_input(INPUT_GET, 'order');
    $displayArchives        =   filter_input(INPUT_GET, "archives");
    
    // Archive display links and settings
    $archives = false;
    if( strcmp($displayArchives, 'yes') == 0 )
    {
        $archives    = true;
        $archiveHref =  [   "name" => "Cacher archives", 
                            "href" => $baseUriArchives.$getSeparatorArchives."archives=no"
                        ];  
    }
    else
    {
        $archiveHref =  [   "name" => "Voir archives", 
                            "href" =>   $baseUriArchives.$getSeparatorArchives."archives=yes"
                        ];
    }

    
    $ordersArray =  [
                        'name'      => 'asc',
                        'created'   => 'asc',
                    ];
    
    if( $orderfield && isset($ordersArray[$orderfield]) 
        && $order && in_array($order, ['asc', 'desc'])
    ){
        $fieldset = "orderfield=".$orderfield."&order=".$order;
        
        $baseUriArchives        .= $getSeparatorArchives.$fieldset;
        $getSeparatorArchives   =  $newSeparator;
    }
    
    if( $displayArchives && in_array($displayArchives, ['yes', 'no']) )
    {
        $fieldset = "archives=".$displayArchives;

        $baseUriOrder      .= $getSeparatorOrder.$fieldset;
        $getSeparatorOrder  = $newSeparator;
    }
    
    // Ordering links and settings
    $orders = [];
    if( $orderfield && $order )
    {
        // For direct SQL ordering (if not set "priority asc" will be applied)
        if( in_array($orderfield, ['name', 'created']) 
            && in_array($order, ['asc', 'desc']) 
        ){
            $orders[$orderfield] = $order; 
        }
        
        // For selected Ordering link to be desc
        if( isset($ordersArray[$orderfield]) 
            && strcmp($order, 'asc') == 0
        ){
            $ordersArray[$orderfield] = 'desc';
        }
    }
    
    $baseUriOrder .= $getSeparatorOrder.'orderfield=';
    $headers =  [
                    'Nom'                   => $baseUriOrder.'name&order='.$ordersArray['name'], 
                    'Archive'               => false, 
                    'Quantité Brouillons'   => false,
                    'Quantité Contenus'     => false,
                    'Quantité Archives'     => false,
                    'Création'              => $baseUriOrder.'created&order='.$ordersArray['created'], 
                    'Modifier'              => false, 
                ];
    
    if( !$archives )
    {   unset($headers['Archive']); }
    
    $structuresListData = Structure::listStructures($archives, $orders);
    $count              = count($structuresListData);
    
    $structures = [];
    foreach( $structuresListData as $valueArray )
    {
        $structure          = $valueArray['name'];
        $creationDateTime   = new ExtendedDateTime($valueArray['created']);
        $countArray         = Structure::countElements($structure);

        $displayValues  =   [   "name"          => $structure, 
                                "viewHref"      => $baseUri."?view=".$structure, 
                                "draftCount"    => $countArray['draft'], 
                                "contentCount"  => $countArray['content'], 
                                "archiveCount"  => $countArray['archive'], 
                                "modifyHref"    => $baseUri."?edit=".$structure, 
                                "creation"      => $creationDateTime
                            ];

        if( $archives )
        {
            if( $valueArray['is_archive'] )
            {
                $isArchive = 'oui';
                $displayValues["modifyHref"] = false;
            }
            else
            {   $isArchive = 'non'; }
            
            $displayValues["isArchive"] = $isArchive;
        }
        
        $structures[]   =   $displayValues;
        
    }
    
    include $module->getDesignFile();
}
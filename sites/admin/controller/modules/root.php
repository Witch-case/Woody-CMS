<?php

require_once 'system/classes/Structure.php';
require_once 'system/classes/Location.php';
require_once 'system/classes/datatypes/ExtendedDateTime.php';
require_once 'system/classes/targets/Draft.php';
require_once 'system/classes/targets/Content.php';


if( filter_has_var(INPUT_POST, "publishtRootDescription") )
{   $action = "publishDescription";   }

elseif( filter_has_var(INPUT_POST, "changePriorities") )
{   $action = "changePriorities";   }

elseif( filter_has_var(INPUT_POST, "deleteChildren") )
{   $action = "deleteChildren"; }

else
{   $action = "view";   }

//$log->debug($_POST, 'POST');

$messages = [];

if( $action == "publishDescription" )
{
    $description = filter_input(INPUT_POST, 'rootDescription');
    
    if( $localisation->edit( false, $localisation->name, $description ) )
    {   $localisation->description = $description;  }
    
    $action = "view";
}

if( $action == "changePriorities" )
{
    $priorities =   filter_input(   INPUT_POST, 
                                    "priorities", 
                                    FILTER_DEFAULT, 
                                    FILTER_REQUIRE_ARRAY    
                    );
    
    if( !is_array($priorities) )
    {
        $message    = "All priorities update failed";
        $messages[] = $message;
        $log->debug($message.", priority POST variables missing");
    }
    else
    {
        foreach( $priorities as $localisationID => $newPriority )
        {   if( !Localisation::changePriority($localisationID, $newPriority) )
            {
                $message    = "Priority update fail for ".$names[$localisationID];
                $messages[] = $message;
                $log->error( $message );
        }   }
    }
    
    $action = "view";
}

if( $action == "view" )
{
    $baseUri            =   "http://".$localisation->siteAccess;
    $baseUriNav         =   $baseUri;
    $baseUriDraft       =   $baseUri;
    $baseUriContent     =   $baseUri;
    $getSeparatorNav    =   '?';
    $getSeparatorDraft  =   '?';
    $getSeparatorContent=   '?';
    $navfield           =   filter_input(INPUT_GET, 'navfield');
    $navorder           =   filter_input(INPUT_GET, 'navorder');
    $draftfield         =   filter_input(INPUT_GET, 'draftfield');
    $draftorder         =   filter_input(INPUT_GET, 'draftorder');
    $contentfield       =   filter_input(INPUT_GET, 'contentfield');
    $contentorder       =   filter_input(INPUT_GET, 'contentorder');
    
    if( $navfield && in_array($navfield, ['name', 'site', 'type', 'priority'])
        && $navorder && in_array($navorder, ['asc', 'desc'])
    ){
        $baseUriDraft           .= $getSeparatorDraft."navfield=".$navfield."&navorder=".$navorder;
        $getSeparatorDraft      =  '&';
        $baseUriContent         .= $getSeparatorContent."navfield=".$navfield."&navorder=".$navorder;
        $getSeparatorContent    =  '&';
    }
    
    if( $draftfield && in_array($draftfield, ['name', 'site', 'type', 'modified'])
        && $draftorder && in_array($draftorder, ['asc', 'desc'])
    ){
        $baseUriNav         .= $getSeparatorNav."draftfield=".$draftfield."&draftorder=".$draftorder;
        $getSeparatorNav    =  '&';
        $baseUriContent     .= $getSeparatorContent."draftfield=".$draftfield."&draftorder=".$draftorder;
        $getSeparatorContent=  '&';
    }
    
    if( $contentfield && in_array($contentfield, ['name', 'site', 'type', 'modified'])
        && $contentorder && in_array($contentorder, ['asc', 'desc'])
    ){
        $baseUriNav         .= $getSeparatorNav."contentfield=".$contentfield."&contentorder=".$contentorder;
        $getSeparatorNav    =  '&';
        $baseUriDraft       .= $getSeparatorDraft."contentfield=".$contentfield."&contentorder=".$contentorder;
        $getSeparatorDraft  =  '&';
    }
    
    // NAV TABLE
    $navorders =    [
                        'name'      => 'asc',
                        'site'      => 'asc',
                        'type'      => 'asc',
                        'priority'  => 'asc',
                    ];
    
    $orders = [];
    if( $navfield && $navorder )
    {
        // For direct SQL ordering (if not set "priority asc" will be applied)
        if( in_array($navfield, ['name', 'priority']) 
            && in_array($navorder, ['asc', 'desc']) 
        ){   
            $orders[$navfield] = $navorder; 
        }
        
        // For selected Ordering link to be desc
        if( isset($navorders[$navfield]) 
            && strcmp($navorder, 'asc') == 0
        ){
            $navorders[$navfield] = 'desc';
        }
    }
    
    $children = $localisation->children(true, true, $orders);
    
    // Ordering links
    $baseUriNav .= $getSeparatorNav.'navfield=';
    $childrenHeader =   [
                            'Nom'       => $baseUriNav.'name&navorder='.$navorders['name'], 
                            'Site'      => $baseUriNav.'site&navorder='.$navorders['site'], 
                            'Type'      => $baseUriNav.'type&navorder='.$navorders['type'], 
                            'Priorité'  => $baseUriNav.'priority&navorder='.$navorders['priority']
                        ];
    
    // Formating results
    $childrenElements   = [];
    $siteSortArray      = [];
    $typeSortArray      = [];
    foreach( $children as $i => $child )
    {
        $childrenElements[$i] = [];
        
        $childrenElements[$i]['id']         = $child->id;
        $childrenElements[$i]['name']       = $child->name;
        $childrenElements[$i]['priority']   = $child->priority;
        
        if( strcmp($child->module, 'view') != 0 || !$child->has_target )
        {
            $childrenElements[$i]['href'] = $baseUri."/view?id=".$child->id;
            $childrenElements[$i]['type'] = "Module";
        }
        else
        {
            $childrenElements[$i]['type'] = ucfirst( $child->target_structure );
            $childrenElements[$i]['href'] = $baseUri.$child->url;
        }
        $typeSortArray[$i] = $childrenElements[$i]['type'];
        
        $site = '';
        foreach( $child->getLocation()->localisations as $linkedSite => $linkedLocalisation )
        {   if( strcmp($linkedSite, $localisation->site) != 0 )
            {
                if( !empty($site) )
                {   $site .= ", ";  }

                $site .= $linkedSite;   
        }   }
        
        if( empty($site) )
        {   $site .= $child->site;  }
        
        $childrenElements[$i]['site']   = $site;
        $siteSortArray[$i]              = $site;
    }
    
    // PHP ordering
    if( $navfield && in_array($navfield, ['site', 'type'])
        && $navorder && in_array($navorder, ['asc', 'desc']) 
    ){
        $tmpArray           = $childrenElements;
        $childrenElements   = [];
        
        if( strcmp($navfield, 'site') == 0 )
        {
            if( strcmp($navorder, 'asc') == 0 )
            {   asort($siteSortArray);  }
            else
            {   arsort($siteSortArray); }
            
            foreach( $siteSortArray as $key => $value )
            {   $childrenElements[] = $tmpArray[$key];    }
        }
        else
        {
            if( strcmp($navorder, 'asc') == 0 )
            {   asort($typeSortArray);  }
            else
            {   arsort($typeSortArray); }
            
            foreach( $typeSortArray as $key => $value )
            {   $childrenElements[] = $tmpArray[$key];    }
        }
    }
    
    // DRAFT TABLE
    $draftorders =  [
                        'name'      => 'asc',
                        'site'      => 'asc',
                        'type'      => 'asc',
                        'modified'  => 'asc',
                    ];
    
    // For selected Ordering link to be desc
    if( $draftfield && $draftorder 
        && isset($draftorders[$draftfield]) 
        && strcmp($draftorder, 'asc') == 0
    ){
        $draftorders[$draftfield] = 'desc';
    }
    
    // Ordering links
    $baseUriDraft .= $getSeparatorDraft.'draftfield=';
    $draftsHeader   =   [
                            'Nom'       => $baseUriDraft.'name&draftorder='.$draftorders['name'], 
                            'Site'      => $baseUriDraft.'site&draftorder='.$draftorders['site'], 
                            'Type'      => $baseUriDraft.'type&draftorder='.$draftorders['type'], 
                            'Modifié'   => $baseUriDraft.'modified&draftorder='.$draftorders['modified']
                        ];
    
    $connexionID = $_SESSION["user"]["connexionID"];
    
    $structures = Structure::listStructures();
    
    $myDrafts =[];
    foreach( $structures as $i => $structure )
    {
        $data = Draft::searchUserDraftsData($structure['name'], $connexionID);
        
        foreach( $data as $i => $value )
        {
            $data[$i]['structure']  = $structure['name'];
            $myDrafts[]             = $data[$i];
        }
    }
    
    // Formating results
    $myDraftElements    = [];
    $nameSortArray      = [];
    $siteSortArray      = [];
    $typeSortArray      = [];
    $modifiedSortArray  = [];
    foreach( $myDrafts as $i => $myDraft )
    {
        $displayArray = [];
        
        $displayArray['href']   = $baseUri.$myDraft['url'];
        $displayArray['name']   = $myDraft['name'];
        $nameSortArray[$i]      = $myDraft['name'];
        
        $draftLocation = Location::getFromLocalisationID( $myDraft['id'] );
        
        $site = '';
        foreach( $draftLocation->localisations as $linkedSite => $linkedLocalisation )
        {   if( strcmp($linkedSite, $localisation->site) != 0 )
            {
                if( !empty($site) )
                {   $site .= ", ";  }
                
                $site .= $linkedSite; 
        }   }
        
        if( empty($site) )
        {   $site .= $localisation->site;  }
        
        $displayArray['site']           = $site;
        $siteSortArray[$i]              = $site;
        
        $displayArray['type']           = $myDraft['structure'];
        $typeSortArray[$i]              = $myDraft['structure'];
        
        $displayArray['modified']       = $myDraft['modification_date'];
        $modifiedSortArray[$i]          = $myDraft['modification_date'];
        
        $date = new ExtendedDateTime($myDraft['modification_date']);
        $displayArray['modification']   = $date->format("H:i:s d/m/Y");
        
        $myDraftElements[] = $displayArray;
    }    
    
    // Default Ordering
    if( !$draftfield || !in_array($draftfield, ['name', 'site', 'type', 'modified'])
        || !$draftorder || !in_array($draftorder, ['asc', 'desc'])
    ){
        $draftfield = 'modified';
        $draftorder = 'desc';
    }
    
    // PHP ordering
    $tmpArray           = $myDraftElements;
    $myDraftElements    = [];
    if( strcmp($draftfield, 'name') == 0 )
    {
        if( strcmp($draftorder, 'asc') == 0 )
        {   asort($nameSortArray);  }
        else
        {   arsort($nameSortArray); }

        foreach( $nameSortArray as $key => $value )
        {   $myDraftElements[] = $tmpArray[$key];   }
    }
    elseif( strcmp($draftfield, 'site') == 0 )
    {
        if( strcmp($draftorder, 'asc') == 0 )
        {   asort($siteSortArray);  }
        else
        {   arsort($siteSortArray); }

        foreach( $siteSortArray as $key => $value )
        {   $myDraftElements[] = $tmpArray[$key];   }
    }
    elseif( strcmp($draftfield, 'type') == 0 )
    {
        if( strcmp($draftorder, 'asc') == 0 )
        {   asort($typeSortArray);  }
        else
        {   arsort($typeSortArray); }

        foreach( $typeSortArray as $key => $value )
        {   $myDraftElements[] = $tmpArray[$key];   }
    }
    else
    {
        if( strcmp($draftorder, 'asc') == 0 )
        {   asort($modifiedSortArray);  }
        else
        {   arsort($modifiedSortArray); }

        foreach( $modifiedSortArray as $key => $value )
        {   $myDraftElements[] = $tmpArray[$key];    }
    }
    
    // CONTENT TABLE
    $contentorders =    [
                            'name'      => 'asc',
                            'site'      => 'asc',
                            'type'      => 'asc',
                            'modified'  => 'asc',
                        ];
    
    // For selected Ordering link to be desc
    if( $contentfield && $contentorder 
        && isset($contentorders[$contentfield]) 
        && strcmp($contentorder, 'asc') == 0
    ){
        $contentorders[$contentfield] = 'desc';
    }
    
    // Ordering links
    $baseUriContent .= $getSeparatorContent.'contentfield=';
    $contentsHeader =   [
                            'Nom'       => $baseUriContent.'name&contentorder='.$contentorders['name'], 
                            'Site'      => $baseUriContent.'site&contentorder='.$contentorders['site'], 
                            'Type'      => $baseUriContent.'type&contentorder='.$contentorders['type'], 
                            'Modifié'   => $baseUriContent.'modified&contentorder='.$contentorders['modified']
                        ];
    
    $myContents =[];
    foreach( $structures as $i => $structure )
    {
        $data = Content::searchUserContentsData($structure['name'], $connexionID, 0, 6);
        
        foreach( $data as $i => $value )
        {
            $data[$i]['structure']  = $structure['name'];
            $myContents[]             = $data[$i];
        }
    }
    
    // Formating results
    $myContentsElements = [];
    $nameSortArray      = [];
    $siteSortArray      = [];
    $typeSortArray      = [];
    $modifiedSortArray  = [];
    foreach( $myContents as $i => $myContent )
    {
        $displayArray = [];
        
        $displayArray['href']   = $baseUri.$myContent['url'];
        $displayArray['name']   = $myContent['name'];
        $nameSortArray[$i]      = $myContent['name'];
        
        $contentLocation = Location::getFromLocalisationID( $myContent['id'] );
        
        $site = '';
        foreach( $contentLocation->localisations as $linkedSite => $linkedLocalisation )
        {   if( strcmp($linkedSite, $localisation->site) != 0 )
            {
                if( !empty($site) )
                {   $site .= ", ";  }
                
                $site .= $linkedSite;
        }   }
        
        if( empty($site) )
        {   $site .= $localisation->site;   }
        
        $displayArray['site']           = $site;
        $siteSortArray[$i]              = $site;
        
        $displayArray['type']           = $myContent['structure'];
        $typeSortArray[$i]              = $myContent['structure'];
        
        $displayArray['modified']       = $myContent['modification_date'];
        $modifiedSortArray[$i]          = $myContent['modification_date'];
        
        $date = new ExtendedDateTime($myContent['modification_date']);
        $displayArray['modification']   = $date->format("H:i:s d/m/Y");
        
        $myContentsElements[] = $displayArray;
    }
    
    // Default Ordering
    if( !$contentfield || !in_array($contentfield, ['name', 'site', 'type', 'modified'])
        || !$contentorder || !in_array($contentorder, ['asc', 'desc'])
    ){
        $contentfield = 'modified';
        $contentorder = 'desc';
    }
    
    // PHP ordering
    $tmpArray           = $myContentsElements;
    $myContentsElements = [];

    if( strcmp($contentfield, 'name') == 0 )
    {
        if( strcmp($contentorder, 'asc') == 0 )
        {   asort($nameSortArray);  }
        else
        {   arsort($nameSortArray); }

        foreach( $nameSortArray as $key => $value )
        {   $myContentsElements[] = $tmpArray[$key];   }
    }
    elseif( strcmp($contentfield, 'site') == 0 )
    {
        if( strcmp($contentorder, 'asc') == 0 )
        {   asort($siteSortArray);  }
        else
        {   arsort($siteSortArray); }

        foreach( $siteSortArray as $key => $value )
        {   $myContentsElements[] = $tmpArray[$key];   }
    }
    elseif( strcmp($contentfield, 'type') == 0 )
    {
        if( strcmp($contentorder, 'asc') == 0 )
        {   asort($typeSortArray);  }
        else
        {   arsort($typeSortArray); }

        foreach( $typeSortArray as $key => $value )
        {   $myContentsElements[] = $tmpArray[$key];   }
    }
    else
    {
        if( strcmp($contentorder, 'asc') == 0 )
        {   asort($modifiedSortArray);  }
        else
        {   arsort($modifiedSortArray); }

        foreach( $modifiedSortArray as $key => $value )
        {   $myContentsElements[] = $tmpArray[$key];    }
    }
    
    $createElementHref = $baseUri."/create?localisation=".$localisation->id;
    $createModuleHref  = $baseUri."/createmodule";
    $createSiteHref    = $baseUri."/createsite";
    
    
    include $module->getDesignFile();
}
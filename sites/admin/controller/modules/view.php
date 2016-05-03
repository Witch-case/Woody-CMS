<?php

if( filter_has_var(INPUT_POST, "changePriorities") )
{   $action = "changePriorities";   }

elseif( filter_has_var(INPUT_POST, "deleteButton") )
{   $action = "delete";   }

elseif( filter_has_var(INPUT_POST, "publishButton") )
{   $action = "publish"; }

elseif( filter_has_var(INPUT_POST, "deleteChildren") )
{   $action = "deleteChildren"; }

else
{   $action = "view";   }

require_once 'system/classes/Location.php';
$messages = [];

if( filter_has_var(INPUT_GET, "id") )
{
    $targetLocalisation = new Localisation(filter_input(INPUT_GET, "id"));  
    $viewTarget         = $targetLocalisation->getTarget();
}
else
{
    $targetLocalisation = $localisation;
    $viewTarget         = $target;
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
        {   if( !Location::changePriority($localisationID, $newPriority) )
            {
                $message    = "Priority update fail";
                $messages[] = $message;
                $log->error( $message );
                break;
        }   }
    }
    
    $action = "view";
}

if( $action == "publish" )
{
    if( !$viewTarget->publish() )
    {
        $message    = "La publication a échoué";
        $messages[] = $message;
        $log->error($message);
    }
    else
    {
        $messages[] = "La publication a réussi";
        $uri        = "http://".$localisation->siteAccess.$localisation->url;
        
        if( filter_has_var(INPUT_GET, "id") )
        {   $uri .= "?=".filter_input(INPUT_GET, "id");  }
        
        header('Location: '.$uri );
        exit();
    }
    
    $action = "view";
}

if( $action == "delete" )
{
    $parent         = $targetLocalisation->parents()[0];
    $redirectionURI = "http://".$localisation->siteAccess.$parent['url'];
    
    if( $targetLocalisation->has_target )
    {   $result = $viewTarget->delete();    }
    else
    {   $result = $targetLocalisation->delete();  }
    
    if( !$result )
    {
        $message    = "La suppression a échoué";
        $messages[] = $message;
        $log->error($message);
        
        $action = "view";
    }
    else
    {
        header('Location: '.$redirectionURI );
        exit();
    }
}

if( $action == "deleteChildren" )
{
    $childrenID =   filter_input(   INPUT_POST, 
                                    "childrenID", 
                                    FILTER_DEFAULT, 
                                    FILTER_REQUIRE_ARRAY    
                    );
    
    if( !is_array($childrenID) )
    {
        $message    = "All deletion failed";
        $messages[] = $message;
        $log->error($message.", childrenID POST variables missing");
    }
    else
    {   foreach( $childrenID as $deleteLocalisationId )
        {
            $deleteLocalisation = new Localisation( $deleteLocalisationId );
            $deleteLocalisation->delete();
    }   }
    
    $action = "view";
}

if( $action == "view" )
{
    $baseUri            =   "http://".$localisation->siteAccess;
    if( $targetLocalisation->has_target )
    {
        $modificationHref   =   $baseUri."/edit?localisation=".$targetLocalisation->id;
        $creationHref       =   $baseUri."/create?localisation=".$targetLocalisation->id;
        $structureHref      =   $baseUri."/structures?view=".$viewTarget->structure;
        
        $structureIcon      = $module->getImageFile($viewTarget->structure.".png");
        if( !$structureIcon )
        {   $structureIcon = $module->getImageFile("defaultStructure.png"); }
        
        $targetType         = ucfirst($targetLocalisation->target_type);
        
        $targetTypeIcon     = $module->getImageFile($targetLocalisation->target_type.".png");
        
        if( isset($viewTarget->publication_date) )
        {   $publicationDate    = "Publié le ".$viewTarget->publication_date->frenchFormat(true);   }
        elseif( isset($viewTarget->creation_date) )
        {   $publicationDate    = "Créé le ".$viewTarget->creation_date->frenchFormat(true);    }
        elseif( isset($viewTarget->archive_date) )
        {   $publicationDate    = "Archivé le ".$viewTarget->archive_date->frenchFormat(true);    }

        if( isset($viewTarget->creator) )
        {   $creator    =   $viewTarget->creator->value;   }
        elseif( isset($viewTarget->archiver) )
        {   $creator    =   $viewTarget->archiver->value;   }

        if( isset($viewTarget->modification_date) )
        {   $modificationDate   = $viewTarget->modification_date->frenchFormat(true);   }
        elseif( isset($viewTarget->last_modification_date) )
        {   $modificationDate   = $viewTarget->last_modification_date->frenchFormat(true);   }

        if( isset($viewTarget->modificator) )
        {   $modificator    =   $viewTarget->modificator->value;   }
        elseif( isset($viewTarget->archiver) )
        {   $modificator    =   $viewTarget->last_modificator->value;   }

        if( strcmp($targetLocalisation->target_type, "content") == 0 )
        {   $draftCount = $viewTarget->countActiveDrafts(); }
        else
        {   $draftCount = false;    }
    }
    
    $locationsHref      =   $baseUri."/locations?id=".$targetLocalisation->id;
    
    $locationsDisplay   = [];
    
    if( $targetLocalisation->has_target )
    {   $locations      = $targetLocalisation->sameTargetLocations(); }
    else
    {
        $locations = [];
        foreach( Location::getFromLocationID($targetLocalisation->location_id)->localisations as $localisation_item )
        {
            if( !isset( $locations[$localisation_item->location_id]) )
            {   $locations[$localisation_item->location_id] = [];   }
            
            $locations[$localisation_item->location_id][] = $localisation_item->data;
        }
    }
    
    foreach( $locations as $location => $locationLocalisations )
    {
        $locationsDisplay[$location] = [];
        foreach( $locationLocalisations as $key => $locationLocalisationsItem )
        {   if( strcmp($locationLocalisationsItem['site'], $localisation->site) != 0 )
            {   continue;   }
            else
            {
                $locationsDisplay[$location]['id'] = $locationLocalisationsItem['id'];
                $locationsDisplay[$location]['name'] = $locationLocalisationsItem['name'];
                $locationsDisplay[$location]['description'] = $locationLocalisationsItem['description'];
                
                $buffer         = explode( '/', $locationLocalisationsItem['url'] );
                $parentDisplay  = "/".$buffer[count($buffer) - 2];
                
                $locationsDisplay[$location]['parentDisplay'] = $parentDisplay;
                
                $locationsDisplay[$location]['href'] = "http://".$localisation->siteAccess.$locationLocalisationsItem['url'];
        }   }
    }
    
    $deleteButton       = "Supprimer";
    $publishButton      = "Publier";
    $displayChildren    = true;
    if( $targetLocalisation->has_target )
    {   switch( $viewTarget->type ) 
        {
            case 'draft':
                $displayChildren    = false;
                break;
            
            case 'content':
                $publishButton      = false;
                $deleteButton       = "Archiver";
                break;
                
            case 'archive':
                $publishButton      = "Restaurer";
                $modificationHref   = false;
                break;
    }   }
    
    if( $displayChildren )
    {
        if( !filter_has_var(INPUT_GET, "id") )
        {
            $baseUriNav             =   $baseUri.$localisation->url;
            $baseUriArchives        =   $baseUri.$localisation->url;
            $baseUriLimit           =   $baseUri.$localisation->url;
            $baseUriPage            =   $baseUri.$localisation->url;
            $getSeparatorNav        =   '?';
            $getSeparatorArchives   =   '?';
            $getSeparatorLimit      =   '?';
            $getSeparatorPage       =   '?';
        }
        else
        {
            $baseUriNav             =   $baseUri."/view?id=".$targetLocalisation->id;
            $baseUriArchives        =   $baseUri."/view?id=".$targetLocalisation->id;
            $baseUriLimit           =   $baseUri."/view?id=".$targetLocalisation->id;
            $baseUriPage            =   $baseUri."/view?id=".$targetLocalisation->id;
            $getSeparatorNav        =   '&';
            $getSeparatorArchives   =   '&';
            $getSeparatorLimit      =   '&';
            $getSeparatorPage       =   '&';
        }
        
        $newSeparator           =   '&';
        $navfield               =   filter_input(INPUT_GET, 'navfield');
        $navorder               =   filter_input(INPUT_GET, 'navorder');
        $displayArchives        =   filter_input(INPUT_GET, "archives");
        $limit                  =   filter_input(INPUT_GET, "limit", FILTER_VALIDATE_INT);
        $page                   =   filter_input(INPUT_GET, "page", FILTER_VALIDATE_INT);
        
        $navorders =    [
                            'name'      => 'asc',
                            'module'    => 'asc',
                            'state'     => 'asc',
                            'priority'  => 'asc',
                        ];
        
        if( $navfield && isset($navorders[$navfield]) 
            && $navorder && in_array($navorder, ['asc', 'desc'])
        ){
            $fieldset = "navfield=".$navfield."&navorder=".$navorder;
            
            $baseUriArchives        .= $getSeparatorArchives.$fieldset;
            $getSeparatorArchives   =  $newSeparator;
            $baseUriLimit           .= $getSeparatorLimit.$fieldset;
            $getSeparatorLimit      =  $newSeparator;
            $baseUriPage            .= $getSeparatorPage.$fieldset;
            $getSeparatorPage       =  $newSeparator;
        }
        
        if( $displayArchives && in_array($displayArchives, ['yes', 'no']) )
        {
            $fieldset = "archives=".$displayArchives;
            
            $baseUriNav        .= $getSeparatorNav.$fieldset;
            $getSeparatorNav   =  $newSeparator;
            $baseUriLimit      .= $getSeparatorLimit.$fieldset;
            $getSeparatorLimit =  $newSeparator;
            $baseUriPage       .= $getSeparatorPage.$fieldset;
            $getSeparatorPage  =  $newSeparator;
        }
        
        if( $limit )
        {
            $fieldset = "limit=".$limit;
            
            $baseUriNav             .= $getSeparatorNav.$fieldset;
            $getSeparatorNav        =  $newSeparator;
            $baseUriArchives        .= $getSeparatorArchives.$fieldset;
            $getSeparatorArchives   =  $newSeparator;
            $baseUriPage            .= $getSeparatorPage.$fieldset;
            $getSeparatorPage       =  $newSeparator;
        }
        
        if( $page )
        {
            $fieldset = "page=".$page;
            
            $baseUriNav             .= $getSeparatorNav.$fieldset;
            $getSeparatorNav        =  $newSeparator;
            $baseUriArchives        .= $getSeparatorArchives.$fieldset;
            $getSeparatorArchives   =  $newSeparator;
            $baseUriLimit           .= $getSeparatorLimit.$fieldset;
            $getSeparatorLimit      =  $newSeparator;
        }
        
        // Ordering links and settings
        $orders = [];
        if( $navfield && $navorder )
        {
            // For direct SQL ordering (if not set "priority asc" will be applied)
            if( in_array($navfield, ['name', 'module', 'priority']) 
                && in_array($navorder, ['asc', 'desc']) 
            ){
                $orders[$navfield] = $navorder; 
            }
            elseif( strcmp('state', $navfield) == 0 )
            {   $orders['target_table'] = $navorder; }
            
            // For selected Ordering link to be desc
            if( isset($navorders[$navfield]) 
                && strcmp($navorder, 'asc') == 0
            ){
                $navorders[$navfield] = 'desc';
            }
        }
        
        $baseUriNav .= $getSeparatorNav.'navfield=';
        $childrenHeader =   [
                                'Nom'               => $baseUriNav.'name&navorder='.$navorders['name'], 
                                'Module'            => $baseUriNav.'module&navorder='.$navorders['module'], 
                                'Etat'              => $baseUriNav.'state&navorder='.$navorders['state'], 
                                'Type'              => false, 
                                'Status'            => false, 
                                'Priorité'          => $baseUriNav.'priority&navorder='.$navorders['priority']
                            ];

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
        
        $childrenCount =    $targetLocalisation->childrenCount(   true, 
                                                            $archives
                            );
        
        if( !$limit )
        {   $limit = 10;    }
        
        $limits = [];
        foreach( [10, 20, 50] as $limitValue )
        {
            if( $limit == $limitValue )
            {   $href = false;  }
            else
            {   $href  = $baseUriLimit.$getSeparatorLimit."limit=".$limitValue; }
            
            $limits[$limitValue] =  $href;
        }
        
        if( !$page )
        {   $page = 1;  }
        
        $offset     = ($page - 1) * $limit;
        $nbPages    = ceil( (int) $childrenCount / $limit );
        
        // MAX pages numbers beetween 'Previous' and 'Next'
        $ceil = 21;
        
        $interval = floor( ($ceil - 3) / 4 );
        
        $pageLinksAccess = [];
        for( $i=1; $i<= $nbPages; $i++ )
        {
            if( $nbPages <= $ceil 
                || $i <= $interval
                || $i > ($nbPages - $interval)
                || ( $i >= ($page - $interval) && $i <= ($page + $interval) )
            ){
                $pageLinksAccess[] = $i;
            }
            elseif( $i == ($interval + 1)
                    || $i == ($nbPages - $interval -1)
            ){
                $pageLinksAccess[] = "...";
            }
        }
        
        $pages = [];
        foreach( $pageLinksAccess as $i )
        {
            if( $i == 1 && $page > 1 )
            {
                $pages["Précédent"] =   [   'name'  =>  "Précédent", 
                                            'href'  =>  $baseUriPage.$getSeparatorPage."page=".($page - 1)
                                        ];
            }
            
            if( $i == $page )
            {
                $pages[] =  [   'name'  =>  "<".$i.">", 
                                'href'  =>  false
                            ];
            }
            elseif( !is_numeric($i) )
            {
                $pages[] =  [   'name'  =>  $i, 
                                'href'  =>  false
                            ];
            }
            else
            {
                $pages[] =  [   'name'  =>  $i, 
                                'href'  =>  $baseUriPage.$getSeparatorPage."page=".$i
                            ];
            }
            
            if( $i == $nbPages && $page < $nbPages )
            {
                $pages["Suivant"] = [   'name'  =>  "Suivant", 
                                        'href'  =>  $baseUriPage.$getSeparatorPage."page=".($page + 1)
                                    ];
            }
        }
        
        $childrenLocalisations =    $targetLocalisation->children(    true, 
                                                                $archives, 
                                                                $orders, 
                                                                $offset, 
                                                                $limit
                                    );
        
        $children = [];
        foreach( $childrenLocalisations as $i => $child )
        {   
            $children[$i]['id']         = $child->id;
            $children[$i]['location_id']= $child->location_id;
            $children[$i]['name']       = $child->name;
            $children[$i]['module']     = $child->module;
            $children[$i]['status']     = $child->status;
            $children[$i]['priority']   = $child->priority;
            
            if( strcmp($child->module, 'view') != 0 || !$child->has_target )
            {   $children[$i]['href']       = $baseUri."/view?id=".$child->id;  }
            else
            {   $children[$i]['href']       = $baseUri.$child->url; }
            
            if( !$child->has_target )
            {
                $children[$i]['state']      = " - ";
                $children[$i]['type']       = "Module";
            }
            else
            {
                $buffer = explode("_", $child->target_table);
                
                $children[$i]['state']      = ucfirst($buffer[0]);
                $children[$i]['type']       = ucfirst($buffer[1]);
            }
            
        }
    }
    
    // Breadcrumb
    $parents    = $targetLocalisation->parents();
    
    $goUp = false;
    if( isset($parents[0]) )
    {
        $goUp['href']   = $baseUri.$parents[0]['url'];
        $goUp['img']    = $module->getImageFile("up_arrow.png");
    }
    
    include $module->getDesignFile();
}
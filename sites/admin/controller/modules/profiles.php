<?php

require_once 'system/classes/Profile.php';

$messages = [];

if( filter_has_var(INPUT_POST, "publishProfile") )
{   $action = "publishProfile"; }

elseif( filter_has_var(INPUT_POST, "createProfile")
        ||  filter_has_var(INPUT_POST, "deletePolice") 
        ||  filter_has_var(INPUT_POST, "addPolice")
) {
    $action = "createProfile";
}

elseif( filter_has_var(INPUT_POST, "deleteProfiles") )
{   $action = "deleteProfile";  }

elseif( filter_input(INPUT_GET, "view", FILTER_VALIDATE_INT) )
{   $action = "viewProfile";    }

elseif( filter_input(INPUT_GET, "modify", FILTER_VALIDATE_INT) )
{   $action = "modifyProfile";  }

else
{   $action = "listProfiles";   }


if( strcmp($action, "publishProfile") == 0 )
{
    $name = filter_input(INPUT_POST, "name");
    
    if( !$name || strlen($name) == 0 )
    {
        $messages[] = "You must enter a name to this profile ";
        $action = "createProfile";
    }
    else
    {
        $db->begin();
        
        $deletion = true;
        $modifyID = filter_input(INPUT_GET, "modify", FILTER_VALIDATE_INT);
        if( $modifyID )
        {
            $profile    = new Profile( $modifyID );
            if( isset($profile->name) )
            {
                $deletion   = $profile->delete();
                $messages[] = "Profile ".$profile->name." with ID ".$profile->id." deleted ";
            }
        }
        
        $profile = Profile::create( $name );
        
        if( !$profile || !$deletion ) 
        {
            $message =  "Cannot write user profile ";
            $log->error($message);
            $db->rollback();
            
            $message .= ", please try again";
            $messages[] = $message;
            
            include $module->getDesignFile('modules/profiles/edit.php');
        }
        else
        {
            $continue = true;
            $oldPolicesIds  =   filter_input(   INPUT_POST,
                                                "polices",
                                                FILTER_DEFAULT,
                                                FILTER_REQUIRE_ARRAY
                                );
            
            if( $oldPolicesIds )
            {   foreach( $oldPolicesIds as $oldPoliceId )
                {
                    $police = $_SESSION['work_polices'][$oldPoliceId];
                    
                    $buffer = explode(" - ", $police['module']);
                    $policeModule = $buffer[0];
                    $policeAction = $buffer[1];
                    
                    if( isset($police["inherit"]) && $police["inherit"] == 1 )
                    {   $policeInherit = 1; }
                    else
                    {   $policeInherit = 0; }
                    
                    if( !isset($police["limitation_profile"]) )
                    {   $policeLimitationProfile = 0;   }
                    elseif( $police["limitation_profile"] == 0 )
                    {   $policeLimitationProfile = $profile->id;  }
                    else
                    {   $policeLimitationProfile = $police["limitation_profile"];   }
                    
                    $policeID = $profile->addPolice(    $policeModule, 
                                                        $policeAction, 
                                                        $police['localisation']->id, 
                                                        $policeInherit, 
                                                        $police['limitation'], 
                                                        $policeLimitationProfile
                                );
                    
                    if( !$policeID ) 
                    {
                        $message =  "Cannot write user profile police ";
                        $log->error($message);
                        $db->rollback();

                        $message .= ", please try again";
                        $messages[] = $message;

                        $continue = false;
                        break;
                    }
            }   }
            
            if( !$continue )
            {   include $module->getDesignFile('modules/profiles/edit.php');    }
            else 
            {
                $buffer = explode( " - ", filter_input(INPUT_POST, "module") );
                $policeModule = $buffer[0];
                $policeAction = $buffer[1];
                
                if( filter_has_var(INPUT_POST, "inherit") 
                    && filter_input(INPUT_POST, "inherit") == 1 
                ){
                    $policeInherit = 1;
                }
                else
                {   $policeInherit = 0; }
                
                $limitation_profilePost = filter_input(INPUT_POST, "limitation_profile");
                if( !$limitation_profilePost )
                {   $policeLimitationProfile = 0;   }
                elseif( $limitation_profilePost == 0 )
                {   $policeLimitationProfile = $profileID;  }
                else
                {   $policeLimitationProfile = $limitation_profilePost; }
                
                $localisationId = filter_input(INPUT_POST, "localisation", FILTER_VALIDATE_INT);
                $limitation     = filter_input(INPUT_POST, "limitation");
                
                $policeID = $profile->addPolice(    $policeModule, 
                                                    $policeAction, 
                                                    $localisationId, 
                                                    $policeInherit, 
                                                    $limitation, 
                                                    $policeLimitationProfile
                            );
                
                if( !$policeID ) 
                {
                    $message =  "Cannot write user profile police ";
                    $log->error($message);
                    $db->rollback();
                    
                    $message .= ", please try again";
                    $messages[] = $message;
                    
                    $action = 
                    include $module->getDesignFile('modules/profiles/edit.php');
                }
                else
                {
                    $db->commit();
                    $messages[] = "Profile ".$name." with ID ".$profile->id." created ";
                    $action = "listProfiles";
                }
            }
        }
    }
}

if( strcmp($action, "modifyProfile") == 0 )
{
    $profileId  = filter_input(INPUT_GET, "modify", FILTER_VALIDATE_INT);
    $profile    = new Profile( $profileId );
    
    if( isset($profile->name) )
    {
        $name = $profile->name;
        
        $polices = [];
        foreach( $profile->polices as $police )
        {
            $buffer = $police->formArray();
            
            $policeLocalisation = $buffer["localisation"];
            
            $buffer['localisation_display'] = $policeLocalisation->name;
            $buffer['localisation_display'] .= " [".$policeLocalisation->site."]";
            
            if( $buffer['inherit'] )
            {   $buffer['localisation_display'] .= " et sous-arborescence"; }
            
            if( strcmp($buffer["limitation"], 'profile') == 0 )
            {
                $buffer["limitation_profile"] = $police->group_fk_profile;
                $buffer["limitation_display"] = "profile ";
                
                if( $buffer["limitation_profile"] == $police->id )
                {   $buffer["limitation_display"] .= "self";    }
                else
                {
                    $destProfile = new Profile($buffer["limitation_profile"]);
                    if( isset($destProfile->name) )
                    {   $buffer["limitation_display"] .= $destProfile->name;}
                    else
                    {   $buffer["limitation_display"] .= 'DELETED - please create another police and delete this one'; }
                }
            }
            else
            {
                $buffer["limitation_profile"] = 0;
                $buffer["limitation_display"] = $buffer["limitation"];
            }
            
            $polices[] = $buffer;
        }
        
        $action = "createProfile";
    }
    else
    {
        $messages[] = "This profile doesn't seem to exist anymore ";
        $action = "listProfiles";
    }
}

if( strcmp($action, "createProfile") == 0 )
{
    $moduleActions = $configuration->getModulesActions($localisation->site);
    
    $allLocalisations = $localisation->getAllData();
    
    foreach( $allLocalisations as $key => $allLocalisations_item )
    {
        $level = 0;
        foreach( $allLocalisations_item as $label => $value )
        {   if( strcmp("level_", substr($label, 0, 6)) == 0 )
            {   if( !is_null($value) )
                {   $level = (int) substr($label, 6);   }
        }   }
        
        $spacing = "";
        for( $i=2; $i<=$level; $i++ )
        {   $spacing .= "&nbsp;&nbsp;&nbsp;";   }
        
        if( $level > 1 )
        {   $spacing .= "-&nbsp;";  }
        
        $allLocalisations[$key]['spacing'] = $spacing;
    }
    
    $profiles   = [];
    $profiles[] = [ 'id' => 0, 'name' => 'self' ];
    foreach( Profile::listProfiles() as $targetProfile )
    {   if( !isset($profile) || $profile->id != $targetProfile->id )
        {
            $profiles[] =   [   'id' => $targetProfile->id, 
                                'name' => $targetProfile->name
                            ];
    }   }
    
    if( !filter_has_var(INPUT_GET, "modify") 
        || filter_has_var(INPUT_POST, "currentAction") 
    ) {
        $name = "";
        if( filter_has_var(INPUT_POST, "name") )
        {   $name = filter_input(INPUT_POST, "name");   }
        
        $deletePoliceKeyArray = [];
        $deletePolicePost = filter_input(   INPUT_POST,
                                            "deletePolice",
                                            FILTER_DEFAULT,
                                            FILTER_REQUIRE_ARRAY
                            );
        if( $deletePolicePost )
        {   foreach( $deletePolicePost as $key => $value )
            {   $deletePoliceKeyArray[] = $key; }
        }
        
        $polices = [];
        $oldPolicesIds =    filter_input(   INPUT_POST,
                                            "polices",
                                            FILTER_DEFAULT,
                                            FILTER_REQUIRE_ARRAY
                            );
        
        if( $oldPolicesIds )
        {   foreach( $oldPolicesIds as $key => $oldPoliceId )
            {   if( !in_array($key, $deletePoliceKeyArray) )
                {
                    $polices[] = $_SESSION['work_polices'][$key];
        }   }   }
    }
    
    if( filter_has_var(INPUT_POST, "addPolice") )
    {
        $newPolice =    [   'id'            =>  'new',           
                            'module'        =>  filter_input(INPUT_POST, "module"), 
                            'localisation'  =>  filter_input(INPUT_POST, "localisation"), 
                            'limitation'    =>  filter_input(INPUT_POST, "limitation") 
                        ];
        if( filter_has_var(INPUT_POST, "inherit") && (filter_input(INPUT_POST, "inherit") == 1) )
        {   $newPolice['inherit'] = 1;  }
        else
        {   $newPolice['inherit'] = 0;  }
        
        foreach( $allLocalisations as $allLocalisations_item )
        {   if( $allLocalisations_item['id'] ==  filter_input(INPUT_POST, "localisation") )
            {
                $newPolice['localisation_display'] = $allLocalisations_item['name'];
                $newPolice['localisation_display'] .= " [".$allLocalisations_item['site']."]";
                
                if( $newPolice['inherit'] == 1 )
                {   $newPolice['localisation_display'] .= " et sous-arborescence";  }
                
                break;
        }   }
        
        $newPolice['limitation_display'] = filter_input(INPUT_POST, "limitation");
        
        if( strcmp(filter_input(INPUT_POST, "limitation"), 'profile') == 0 )
        {   foreach( $profiles as $key => $profiles_item )
            {   if( $profiles_item['id'] == filter_input(INPUT_POST, "limitation_profile") )
                {
                    $newPolice['limitation_profile'] =  $profiles[$key];
                    
                    $newPolice['limitation_display'] .= " ".$profiles[$key]['name'];
                    break;
        }   }   }
        
        $polices[] = $newPolice;
    }
    
    $_SESSION['work_polices'] = $polices;
    
    include $module->getDesignFile('modules/profiles/edit.php');
}

if( strcmp($action, "deleteProfile") == 0 )
{
    $profileIDsPost =   filter_input(   INPUT_POST,
                                        "profileIDs",
                                        FILTER_VALIDATE_INT,
                                        FILTER_REQUIRE_ARRAY
                        );
    if( $profileIDsPost )
    {   foreach( $profileIDsPost as $idProfileToDelete )
        {
            if( !is_numeric($idProfileToDelete) )
            {   continue;   }
            
            $profile    = new Profile($idProfileToDelete);
            
            if( !$profile->delete() )
            {
                $message =  "Profile ".$profile->name." with ID ";
                $message .= $profile->id." can't be deleted ";
                $log->error($message);
                
                $message .= ", please try again";
                $message .= "<br/>If problem persist, please contact administrator";
                
                $messages[] = $message;
            }
            else
            {   $messages[] = "Profile ".$profile->name." with ID ".$profile->id." deleted ";   }
    }   }
    
    $action = "listProfiles";
}

if( strcmp($action, "viewProfile") == 0 )
{
    $profileId  = filter_input(INPUT_GET, "view", FILTER_VALIDATE_INT);
    $profile    = new Profile( $profileId );
    
    if( isset($profile->name) )
    {
        $baseUri            =   "http://".$localisation->siteAccess."/profiles";
        $modificationHref   =   $baseUri."?modify=".$profile->id;
        
        foreach( $profile->polices as $indice => $police )
        {
            $buffer = $police->formArray();
            
            $policeLocalisation = $buffer["localisation"];
            
            $buffer['localisation_display'] = $policeLocalisation->name;
            $buffer['localisation_display'] .= " [".$policeLocalisation->site."]";
            
            if( $buffer['inherit'] )
            {   $buffer['localisation_display'] .= " et sous-arborescence"; }
            
            $profile->polices[$indice]->localisation_display = $buffer['localisation_display'];
            
            if( strcmp($buffer["limitation"], 'profile') == 0 )
            {
                $buffer["limitation_profile"] = $police->group_fk_profile;
                $buffer["limitation_display"] = "profile ";
                
                if( $buffer["limitation_profile"] == $police->id )
                {   $buffer["limitation_display"] .= "self";    }
                else
                {
                    $destProfile = new Profile($buffer["limitation_profile"]);
                    if( isset($destProfile->name) )
                    {   $buffer["limitation_display"] .= $destProfile->name;}
                    else
                    {   $buffer["limitation_display"] .= 'DELETED - please modify'; }
                }
            }
            else
            {
                $buffer["limitation_profile"] = 0;
                $buffer["limitation_display"] = $buffer["limitation"];
            }
            
            $profile->polices[$indice]->limitation_display = $buffer["limitation_display"];
        }
        
        include $module->getDesignFile('modules/profiles/view.php');
    }
    else
    {
        $messages[] = "This profile doesn't seem to exist anymore ";
        $action = "listProfiles";
    }
}

if( strcmp($action, "listProfiles") == 0 )
{
    $baseUri                =   "http://".$localisation->siteAccess;
    $baseUriOrder           =   $baseUri.$localisation->url;
    $baseUriLimit           =   $baseUri.$localisation->url;
    $baseUriPage            =   $baseUri.$localisation->url;
    $getSeparatorOrder      =   '?';
    $getSeparatorLimit      =   '?';
    $getSeparatorPage       =   '?';
    $newSeparator           =   '&';
    $orderfield             =   filter_input(INPUT_GET, 'orderfield');
    $order                  =   filter_input(INPUT_GET, 'order');
    $limit                  =   filter_input(INPUT_GET, "limit", FILTER_VALIDATE_INT);
    $page                   =   filter_input(INPUT_GET, "page", FILTER_VALIDATE_INT);
    
    $ordersArray =  [
                        'name'      => 'asc',
                        'created'   => 'asc',
                    ];
    
    if( $orderfield && isset($ordersArray[$orderfield]) 
        && $order && in_array($order, ['asc', 'desc'])
    ){
        $fieldset = "orderfield=".$orderfield."&order=".$order;
        
        $baseUriLimit           .= $getSeparatorLimit.$fieldset;
        $getSeparatorLimit      =  $newSeparator;
        $baseUriPage            .= $getSeparatorPage.$fieldset;
        $getSeparatorPage       =  $newSeparator;
    }
    
    if( $limit )
    {
        $fieldset = "limit=".$limit;
        
        $baseUriOrder             .= $getSeparatorOrder.$fieldset;
        $getSeparatorOrder        =  $newSeparator;
        $baseUriPage            .= $getSeparatorPage.$fieldset;
        $getSeparatorPage       =  $newSeparator;
    }
    
    if( $page )
    {
        $fieldset = "page=".$page;
        
        $baseUriOrder             .= $getSeparatorOrder.$fieldset;
        $getSeparatorOrder        =  $newSeparator;
        $baseUriLimit           .= $getSeparatorLimit.$fieldset;
        $getSeparatorLimit      =  $newSeparator;
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
                    'Nom'               => $baseUriOrder.'name&order='.$ordersArray['name'], 
                    'Création'          => $baseUriOrder.'created&order='.$ordersArray['created'], 
                    'Modifier'          => false, 
                ];
    
    $count  = Profile::countProfiles();
    
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
    $nbPages    = ceil( (int) $count / $limit );
    
    // MAX pages numbers beetween 'Previous' and 'Next'
    $ceil = 40;

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
    
    $profiles = Profile::listProfiles( $orders, $offset, $limit );
    
    $baseUri            =   "http://".$localisation->siteAccess;
    $modificationHref   =   $baseUri."/profiles?modify=";
    $viewHref           =   $baseUri."/profiles?view=";
    
    include $module->getDesignFile();
}
<?php

require_once 'system/classes/targets/Draft.php';

$result         = false;
$action         = false;
$redirectionURI = false;
$messages       = [];

if( filter_input(INPUT_POST, "editDraftID", FILTER_VALIDATE_INT) )
{   $action = "editDraft";  }

elseif( filter_has_var(INPUT_POST, "cancelButton") || filter_has_var(INPUT_POST, "exitButton") )
{   $action = "redirectToVisualisation";    }

elseif( count(filter_input_array(INPUT_POST)) == 0 || filter_has_var(INPUT_POST, "login") )
{   $action = "searchDrafts";   }

elseif( filter_has_var(INPUT_POST, "newDraftButton") )
{   $action = "createDraft";    }

elseif( filter_has_var(INPUT_POST, "discardButton") )
{   $action = "discardDraft";   }

elseif( filter_has_var(INPUT_POST, "storeButton") || filter_has_var(INPUT_POST, "storeExitButton") )
{   $action = "saveDraft";  }

elseif( filter_has_var(INPUT_POST, "publishButton") )
{   $action = "publish";    }

else
{   $log->error("Current action cannot be identified in 'edit' module", true);    }

 
$targetLocalisationID   = filter_input(INPUT_GET, "localisation", FILTER_VALIDATE_INT);
$targetLocalisation     = new Localisation( $targetLocalisationID );
$structure              = $targetLocalisation->target_structure;

if( strcmp($targetLocalisation->target_type, "draft") == 0 )
{   if( strcmp($action, "searchDrafts") == 0 )
    {
        $action         = "editDraft";
        $editDraftID    = $targetLocalisation->target_fk;
    }
    elseif( strcmp($action, "discardDraft") == 0 )
    {
        $parent         = $targetLocalisation->parents()[0];
        
        if( strcmp($parent['module'], 'view') != 0 || empty($parent['target_table']) )
        {   $url = "/view?id=".$parent['id'];   }
        else
        {   $url = $parent['url'];  }

        $redirectionURI = "http://".$localisation->siteAccess.$url;
}   }

if( $action == "saveDraft" )
{
    $draftID = filter_input(INPUT_POST, "draftID", FILTER_VALIDATE_INT);
    
    $draft   = new Draft( $structure );
    
    if( !$draft->fetch( $draftID ) )
    {
        $message    = "Cannot retrieve ".$structure." draft of ID: ".$draftID;
        $messages[] = $message;
        $log->error($message);
        $action = "searchDrafts";
    }
    else
    {
        if( !$draft->edit(filter_input_array( INPUT_POST )) )
        {
            $message    = "Cannot save ".$draft->name." ".$structure;
            $message   .= " draft of ID: ".filter_input(INPUT_POST, "draftID");
            $log->error($message);
            
            $message   .= "<br/>Please try again";
            $message   .= "<br/>If problem persist, please contact administrator\n";
            
            $messages[] = $message;
        }
        else
        {   $messages[] = "Draft saved at ".date('H:i:s');  }
        
        if( filter_has_var(INPUT_POST, "storeExitButton") && $result !== false )
        {   $action = "redirectToVisualisation";    }
        else
        {   include $module->getDesignFile(); }
    }
}

if( $action == "publish" )
{
    $draftID    = filter_input(INPUT_POST, "draftID", FILTER_VALIDATE_INT);
    $draft      = new Draft( $structure );
    
    if( !$draftID || !$draft->fetch($draftID) )
    {
        $message    = "Cannot retrieve ".$structure." draft of ID: ".$draftID;
        $messages[] = $message;
        $log->error($message);
        $action = "searchDrafts";
    }
    else
    {   if( $draft->publish( filter_input_array(INPUT_POST) ) )
        {   $action = "redirectToVisualisation";    }
        else
        {
            $message    = "ERROR publication of ".$draft->name." ".$structure;
            $message   .= " did not work ";
            $log->error($message);
            
            $message   .= "<br/>Please try again";
            $message   .= "<br/>If problem persist, please contact administrator\n";
            
            $messages[] = $message;
            
            include $module->getDesignFile();
    }   }
}

if( $action == "discardDraft" )
{
    $draftID = filter_input(INPUT_POST, "draftID", FILTER_VALIDATE_INT);
    
    $draft      = new Draft( $structure );
    
    if( !$draft->fetch($draftID) )
    {
        $message    = "Cannot retrieve ".$structure." draft of ID: ".$draftID;
        $messages[] = $message;
        $log->error($message);
        $action = "searchDrafts";
    }
    else
    {
        if( !$draft->delete() )
        {
            $message    = "Cannot discard ".$structure." draft of ID: ".$draftID;
            $messages[] = $message;
            $log->error($message);
            
            $editDraftID = $draftID;
            $action = "editDraft";
        }
        else
        {   $action = "redirectToVisualisation";    }
    }
}

if( $action == "redirectToVisualisation" )
{
    if( !$redirectionURI )
    {
        if( strcmp($targetLocalisation->module, 'view') != 0 || !$targetLocalisation->has_target )
        {   $url = "/view?id=".$targetLocalisation->id; }
        else
        {   $url = $targetLocalisation->url;    }
        
        $redirectionURI = "http://".$localisation->siteAccess.$url;
    }
    
    header('Location: '.$redirectionURI );
    exit();
}

if( $action == "editDraft" )
{
    if( !isset($editDraftID) )
    {   $editDraftID = filter_input(INPUT_POST, "editDraftID", FILTER_VALIDATE_INT);    }
    
    $draft      = new Draft( $structure );
    
    $result     = $draft->fetch( $editDraftID );
    
    include $module->getDesignFile();
}

if( $action == "searchDrafts" ) 
{
    $currentDrafts = Draft::searchDrafts($structure, filter_input(INPUT_GET, "localisation"));
    
    if( count($currentDrafts) == 0 )
    {   $action = "createDraft";    }
    
    elseif( count($currentDrafts) > 0 )
    {
        $draftsColumns  =   [   'ID', 
                                'Name', 
                                'Modified', 
                                'Modificator',
                                'Created',
                                'Creator'
                            ];
        
        $drafts = [];
        foreach( $currentDrafts as $currentDrafts_item )
        {
            $buffer         = [];
            $buffer['id']   = $currentDrafts_item->id;
            $buffer[]       = $currentDrafts_item->name;
            $buffer[]       = $currentDrafts_item->modification_date->format('d-m-Y H:i:s');
            $buffer[]       = $currentDrafts_item->modificator->value;
            $buffer[]       = $currentDrafts_item->creation_date->format('d-m-Y H:i:s');
            $buffer[]       = $currentDrafts_item->creator->value;
            
            $drafts[] = $buffer;
        }
        
        include $module->getDesignFile('modules/chooseDraft.php');
    }
    
}

if( $action == "createDraft" )
{
    $draft      = new Draft( $structure );
    $result     = $draft->createFromContent( $targetLocalisation );
    
    include $module->getDesignFile();
}
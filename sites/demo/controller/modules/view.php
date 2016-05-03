<?php

$backgroundImage    = $target->attributes['background-image']->content()['file'];
$headline           = $target->attributes['headline']->content();
$headlineBody       = $target->attributes['body']->content();

if( isset($target->attributes["contents"]) )
{
    $contents = $target->attributes["contents"]->content();
    
    $downloads  = [];
    $images     = [];
    $bodies     = [];
    $columns    = [];
    $links      = [];
    foreach( $contents as $content )
    {
        if( isset($content["attributes"]["download"]) )
        {   $downloads[]= $content["attributes"]["download"]->content();    }
        else
        {   $downloads[]= false;    }
        
        if( isset($content["attributes"]["image"]) )
        {   $images[]   = $content["attributes"]["image"]->content();   }
        else
        {   $images[]   = false;    }
        
        if( isset($content["attributes"]["body"]) )
        {   $bodies[]   = $content["attributes"]["body"]->content();    }
        else
        {   $bodies[]   = false;    }
        
        if( isset($content["attributes"]["visit"]) )
        {   $links[]    = $content["attributes"]["visit"]->content();   }
        else
        {   $links[]    = false;    }
        
        if( isset($content["attributes"]["left-column"]) 
            && isset($content["attributes"]["center-column"]) 
            && isset($content["attributes"]["right-column"]) 
        ){
            $columns[]  =   [
                'left'      =>  $content["attributes"]["left-column"]->content(), 
                'center'    =>  $content["attributes"]["center-column"]->content(), 
                'right'     =>  $content["attributes"]["right-column"]->content()
            ];
        }
        else
        {
            $columns[]  =   [
                'left'      =>  false, 
                'center'    =>  false, 
                'right'     =>  false
            ];
        }
    }
    
    include $module->getDesignFile();
}



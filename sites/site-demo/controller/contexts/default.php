<?php

$baseUri    = "http://".$localisation->siteAccess;
$folder     = "contexts/".$localisation->site;

$rootLocalisation   = false;
$rootContent        = false;
if( $localisation->id == $configuration->get($localisation->site, 'rootID') )
{
    $rootLocalisation   = $localisation;
    $rootContent        = $target;
}

//menuPart
$cache  = Cache::get($folder, 'menu');
if( $cache 
    && !$rootLocalisation
){
    include $cache;
}
else
{
    if( !$rootLocalisation )
    {   $rootLocalisation = new Localisation($configuration->get($localisation->site, 'rootID'));   }
    
    $menu = [];
    foreach(  $rootLocalisation->children() as $child )
    {
        $menu[] =   [
            'name'  =>  $child->name,
            'url'   =>  $baseUri.$child->url,
        ];
    }
    
    Cache::create($folder, 'menu', $menu );
}

// context data
$cache  = Cache::get($folder, 'contextData');
if( $cache 
    && !$rootContent
){
    include $cache;
}
else
{
    if( !$rootContent )
    {
        $rootLocalisation   = new Localisation($configuration->get($localisation->site, 'rootID'));
        $rootContent        = $rootLocalisation->getTarget();
    }
    
    $contextData['meta-title']          = $rootContent->attributes['meta-title']->content();
    $contextData['meta-description']    = $rootContent->attributes['meta-description']->content();
    $contextData['meta-keywords']       = $rootContent->attributes['meta-keywords']->content();
    $contextData['logo']                = $rootContent->attributes['logo']->content();
    $contextData['contact-email']       = $rootContent->attributes['contact-email']->content();
    $contextData['download-highlight']  = $rootContent->attributes['download-highlight']->content();
    $contextData['footer-left']         = $rootContent->attributes['footer-left']->content();
    $contextData['footer-right']        = $rootContent->attributes['footer-right']->content();
    
    Cache::create($folder, 'contextData', $contextData );    
}

include $context->getDesignFile();
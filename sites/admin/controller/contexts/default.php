<?php

$baseUri            =   "http://".$localisation->siteAccess;

$breadcrumbs = [];
foreach( array_reverse($localisation->parents()) as $i => $data )
{
    $breadcrumbs[] =    array(
                            "name"      => $data['name'],
                            "href"      => $baseUri.$data['url']
                        );
}

$menu = array(
            array(
                'name'  =>  "Explorer", 
                'href'  =>  $baseUri, 
                'class' =>  ''
            ),
            array(
                'name'  =>  "Profiles",
                'href'  =>  $baseUri."/profiles", 
                'class' =>  ''
            ),
            array(
                'name'  =>  "Structures",
                'href'  =>  $baseUri."/structures", 
                'class' =>  'last'
            ),
        );

include $context->getDesignFile();

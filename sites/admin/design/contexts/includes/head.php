<?php

$module->addCssFile('layout.css');
?>

<title>Woody CMS Admin</title>
<meta charset="utf-8">

<? foreach( $module->getCssFiles() as $cssFile ) { ?>
    <link   rel="stylesheet" 
            type="text/css" 
            href="<?=$cssFile?>" />
<? } ?>

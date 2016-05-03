<?php

?>

<div class="wrapper row1">
    <header id="header" class="clear">
        <div id="hgroup">
            <a href="#">
                <img    id="logo" 
                        src="<?=$module->getImageFile('logo_woody_gris.png')?>" 
                        alt="Woody CMS" 
                        title="Woody CMS"/>
            </a>
        </div>
        <? if( isset($menu) ) { ?>
            <nav>
                <ul>
                    <? foreach( $menu as $item ) { ?>
                        <li class="<?=$item['class']?>">
                            <a href="<?=$item['href']?>">
                                <?=$item['name']?>
                            </a>
                        </li>
                    <? } ?>
                </ul>
            </nav>
        <? } ?>
    </header>
</div>

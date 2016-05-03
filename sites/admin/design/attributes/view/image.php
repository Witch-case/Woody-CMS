<?php

$module->addCssFile('imageAttributeView.css');
?>

<? if( $srcFile ) { ?>
    <p>
        <img src="<?=$srcFile?>" class="imageAttributeView" />
    </p>
    <p>
        <strong><?=$this->values['title']?></strong>
        <? if( $this->values['link'] ) { ?>
            =>
            <a href="<?=$this->values['link']?>" target="_blank">
                <?=$this->values['link']?>
            </a>
        <? } ?>
    </p>
<? } else { ?>
    Pas d'image
<? } ?>
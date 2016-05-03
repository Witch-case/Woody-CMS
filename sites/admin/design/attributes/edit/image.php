<?php

?>

<? if( $srcFile ) { ?>
    <p>
        <h2>Fichier image actuel</h2>
        
        <img src="<?=$srcFile?>" 
             height="100" />
        <input  type="submit"
                name="storeButton"
                class="deleteImage"
                style=" background:url(<?=$module->getImageFile('disconnect.png')?>) no-repeat;
                        width: 16px;
                        height: 16px;
                        border: none;
                        font-size: 0;"
                value="@_<?=$this->type.'#filedelete__'.$this->name?>" />
    </p>
<? } ?>
    
<p>
    <h2>Sélectionner fichier image</h2>
    
    <input  type="file" 
            name="@_<?=$this->type.'#fileupload__'.$this->name?>" />
</p>

<p>
    <h2>Légende de l'image</h2>
    <input  type="text" 
            name="@_<?=$this->type.'#title__'.$this->name?>"
            value="<?=$this->values['title']?>" />
</p>

<p>
    <h2>cible du lien (url)</h2>
    <input  type="text" 
            name="@_<?=$this->type.'#link__'.$this->name?>"
            value="<?=$this->values['link']?>" />
</p>

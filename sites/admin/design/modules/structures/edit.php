<?php

$module->addCssFile('structures.css');
?>

<h1>
    Modifier la Structure&nbsp;: <?=$structureName?>
</h1>

<div class="errorMessages" >
    <? foreach( $messages as $message ) { ?>
        <p><?=$message?></p>
    <? } ?>
</div>

<form   method="post"
        name="editStructureForm">
    <input  type="hidden"
            name="currentAction"
            value="editingStructure" />
    
    <? foreach( $attributes as $indice => $attribute ) { ?>
        <fieldset class="attributeField" >
            <legend>Attribut <?=$attribute->name?> [<?=$attribute->type?>]</legend>
            <p>
                <h3>Nom</h3>
                <input  type="text" 
                        value="<?=$attribute->name?>" 
                        name="attributes[<?=$indice?>][name]" />
            </p>
            
            <p>
                <h3>Type</h3>
                <?=$attribute->type?>
                <input  type="hidden"
                        name="attributes[<?=$indice?>][type]"
                        value='<?=$attribute->type?>' />
            </p>
            
            <? if( count($attribute->parameters) > 0 ) { ?>
                <!--<h2>Paramètres</h2>-->
                <? foreach( $attribute->parameters as $name => $parameterData ) {?>
                    <p>
                        <?=call_user_func_array([$attribute, $attribute->parameters[$name]['inputForm']], [$indice])?>
                    </p>
                <? } ?>
            <? } ?>
            
            <input  type="submit"
                    name="deleteAttribute[<?=$indice?>]"
                    value="Supprimer attribut" />
        </fieldset>
    <? } ?>
    
    <fieldset class="attributeField">
        <legend>Nouvel Attribut</legend>
        <h3>Type</h3>
        <select name="addAttributType">
            <? foreach( $attributesList as $attributeName ) { ?>
                <option value="<?=$attributeName?>">
                    <?=$attributeName?>
                </option>
            <? } ?>
        </select>
        
        <input  type="submit"
                name="addAttribute"
                value="Ajouter Attribut" />
    </fieldset>
    
    <input  type="submit"
            name="publishStructure"
            value="Publier Structure" />
    <a href="<?="http://".$localisation->siteAccess."/structures"?>">
        <input  type="button"
                name="cancel"
                value="Annuler" />
    </a>
</form>

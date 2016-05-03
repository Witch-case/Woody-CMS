<?php

$module->addCssFile('structures.css');
$module->addJsFile('structures.js');
?>

<h1>
    Voir Structure : <?=$structure->name?>
    <? if( $structure->isArchive ) { ?>
        [ARCHIVE]
    <? } ?>
</h1>

<p class="left modified">
    Dernière modification le <?=$creationDateTime->frenchFormat(true)?>
</p>

<h2>Attributs</h2>

<? foreach( $attributes as $attribute ) { ?>
    <fieldset class="attributeField">
        <legend><?=$attribute->name?> [<?=$attribute->type?>]</legend>
        <p>
            <h3>Nom</h3>
            <?=$attribute->name?>
        </p>
        <p>
            <h3>Type</h3>
            <?=$attribute->type?>
        </p>
        
        <? if( count($attribute->parameters) > 0 ) { ?>
            <? foreach( $attribute->parameters as $name => $parameterData ) {?>
                <p>
                    <h3><?=$name?></h3>
                    <?=$parameterData['value']?>
                </p>
            <? } ?>
        <? } ?>
    </fieldset>
<? } ?>

<? if(count($archivedAttributes)) { ?>
    <div id="archivedAttributes" style="display: none;">
        <? foreach( $archivedAttributes as $attribute ) { ?>
            <fieldset class="archivedAttributeField">
                <legend>ARCHIVED <?=$attribute->name?> [<?=$attribute->type?>]</legend>
                <p>
                    <h3>Nom</h3>
                    <?=$attribute->name?>
                </p>
                <p>
                    <h3>Type</h3>
                    <?=$attribute->type?>
                </p>

                <? if( count($attribute->parameters) > 0 ) { ?>
                    <? foreach( $attribute->parameters as $parameter) { ?>
                        <p>
                            <h3><?=$name?></h3>
                            <?=$parameterData['value']?>
                        </p>
                    <? } ?>
                <? } ?>

            </fieldset>
        <? } ?>
        
        <div id="hideArchivesHref" >
            <span onclick="javascript: hideArchivedAttributes();">
                Cacher les attributs archivés
            </span>
        </div>
    </div>
    
    <div id="showArchivesHref" >
        <span onclick="javascript: showArchivedAttributes();">
            Voir les attributs archivés
        </span>
    </div>
<? } ?>

<div id="action-controls">
    <a href="<?=$baseUri?>">
        <input  type="button" 
                title="Revenir à la liste des structures" 
                value="Liste des structures" 
                name="listButton" 
                class="button" />
    </a>
    
    <? if( !$structure->isArchive ) { ?>
        <a href="<?=$modificationHref?>">
            <input  type="button" 
                    title="Modifier cette structure" 
                    value="Modifier" 
                    name="modifyButton" 
                    class="button" />
        </a>
    <? } ?>
    
</div>

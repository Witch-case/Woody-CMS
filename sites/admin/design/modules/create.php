<?php

$module->addJsFile('create.js');
?>

<h1>
    Création de votre nouvel élément
</h1>

<div class="errorMessages">
    <? foreach( $messages as $message ) { ?>
    <p><?=$message?></p>
    <? } ?>
</div>

<p>
    Vous devez nommer l'emplacement, préciser une url spécifique s'il y a lieu, 
    et éventuellement décrire ce que vous associez à cet emplacement.
</p>

<form id="contentCreationForm" method="post" >
    <input type="hidden" value="create" name="create" />
    
    <fieldset>
        <legend>Emplacement</legend>
        <h2>Nom</h2>
        <p>
            Vous devez impérativement donner un nom à votre nouveau contenu. 
        </p>
        <p>
            <input  type="text"
                    name="name"
                    id="name"
                    value="" />
        </p>
        
        <h2>URL customisée</h2>
        <p>
            Cette valeur correspond au dernier mot de l'URL, elle sera nettoyée. <br/>
            Si laissée vide la valeur par défaut sera celle utilisée pour le nom (nettoyée). 
        </p>
        <input  type="text"
                name="customUrl"
                id="customUrl"
                value="" />
        <br/>
        <br/>
        
        <h2>Description</h2>
        <textarea name="description"></textarea>
    </fieldset>
    
    <p>
        Pour créer un simple contenu visualisable, laissez le nom du module à 'view'
    </p>
    
    <fieldset>
        <legend>Module</legend>
        <h3>Nom du module</h3>
        <p>
            <select name="module">
                <? foreach( $modulesDisplay as $moduleDisplayData ) { ?>
                    <option <? if($moduleDisplayData['default']){ ?>
                                selected="selected"
                            <? } ?>
                            value="<?=$moduleDisplayData['name']?>">
                        <?=$moduleDisplayData['name']?>
                    </option>
                <? } ?>
            </select>
        </p>
    </fieldset>
    
    <p>
        Certains modules ne nécessitent pas de contenu, dans ce cas vous devez signaler 
        l'absence de contenu.
    </p>
    
    <fieldset>
        <legend>Contenu</legend>
        <h2>Type de contenu :</h2>
        <select name="structure">
            <option value="">
                Pas de contenu
            </option>
            <? foreach($structures as $structure) { ?>
                <option value="<?=$structure['value']?>">
                    <?=$structure['label']?>
                </option>
            <? } ?>
        </select>

        <? /*
        <input  type="radio" 
                name="structure" 
                value="" />
            Pas de contenu
        <br/>

        <? foreach($structures as $structure) { ?>
            <input  type="radio" 
                    name="structure" 
                    value="<?=$structure['value']?>" />
            <?=$structure['label']?>
            <br/>
        <? } ?>
        <br />*/ ?>
    </fieldset>
    <br/>
    
    <input  type="button" 
            title="Créer un brouillon pour le nouveau contenu" 
            value="Créer" 
            name="createButton" 
            class="button" 
            onclick="javascript: testForm()"/>
    <input  type="submit" 
            title="Retourner à la visualisation" 
            value="Annuler" 
            name="cancelButton" 
            class="button" />
</form>

<?php

$module->addCssFile('profiles.css');
$module->addJsFile('profiles.js');
?>

<h1>
    <? if( isset($_GET['modify']) ) { ?>
        Modifier le 
    <? } else { ?>
        Créer un nouveau 
    <? } ?>
    Profil utilisateur
</h1>

<div class="errorMessages" >
    <? foreach( $messages as $message ) { ?>
    <p><?=$message?></p>
    <? } ?>
</div>

<p>
    Le caractère "*" signifit tous les modules ou toutes les actions suivant son emplacement
</p>
<p>
    La localisation ne s'applique qu'à un site et ne s'étend pas à son emplacement d'administration
</p>
<p>
    Pour la validité des droits, "all" signifit que la police s'applique pour tous contenus, 
    "self" signifit que cette police ne s'applique que pour ses propres contenus utilisateur, 
    si un profile est désigné la police s'applique pour le groupe d'utilisateurs associés à ce profile
</p>

<form   method="post"
        name="createProfileForm">
    <input  type="hidden"
            name="currentAction"
            value="creatingProfile" />
    
    <h2>Nom du profil</h2>
    <? if( isset($_GET['modify']) ) { ?>
        <input  type="text" 
                value="<?=$name?>" 
                name="name" 
                readonly />
    <? } else { ?>
        <input  type="text" 
                value="<?=$name?>" 
                name="name" />
    <? } ?>
    
    <? foreach( $polices as $indice => $police ) { ?>
        <fieldset class="attributeField">
            <legend>Police <?=($indice + 1)?></legend>
            
            <input  type="hidden"
                    name="polices[]"
                    value='<?=$indice?>' />
            
            <h3>Module - Action</h3>
            <p>
                <?=$police['module']?>
            </p>
            
            <h3>Localisation [site]</h3>
            <p>
                <?=$police['localisation_display']?>
            </p>
            
            <h3>Validité des droits pour l'utilisateur</h3>
            <p>
                <?=$police['limitation_display']?>
            </p>
            
            <input  type="submit"
                    name="deletePolice[<?=$indice?>]"
                    value="Supprimer police" />
        </fieldset>
    <? } ?>
    
    
    <fieldset  class="attributeField">
        <legend>Nouvelle Police : sera prise en compte si vous décidez de publier</legend>
        <h3>Module - Action</h3>
        <p>
            <select name="module">
                <? foreach( $moduleActions as $moduleName => $moduleActions_item ) { ?>
                    <!--<option disabled>
                        <?=$moduleName?>
                    </option>-->
                    <? foreach( $moduleActions_item as $moduleAction ) { ?>
                        <option value="<?=$moduleName?> - <?=$moduleAction?>">
                            <?=$moduleName?> - <?=$moduleAction?>
                        </option>
                    <? } ?>
                <? } ?>
            </select>
        </p>
        
        <h3>Localisation [site] (url)</h3>
        <p>
            <select name="localisation">
                <? foreach( $allLocalisations as  $allLocalisations_item ) { ?>
                    <option value="<?=$allLocalisations_item['id']?>">
                        <?=" [".$allLocalisations_item['site']."]"?>
                        <?=$allLocalisations_item['spacing'].$allLocalisations_item['name']?>
                        <?=" (".$allLocalisations_item['url'].")"?>
                    </option>
                <? } ?>
            </select>
        </p>
        
        <h3>Sous-arborscence ?</h3>
        <p>
            <input  type="checkbox" 
                    name="inherit" 
                    value="1" 
                    checked="checked"/>
        </p>
        
        <h3>Validité des droits pour l'utilisateur</h3>
        <p>
            <select name="limitation" id="limitation" onchange="limitationChange();">
                <option value="all">
                    Tous contenus
                </option>
                <option value="self">
                    Contenus propres
                </option>
                <option value="profile">
                    Contenus générés par le profil ci-dessous
                </option>
            </select>
        </p>

        <p>
            <div id="limitation_profile" style="display:none;">
                <select name="limitation_profile">
                    <? foreach( $profiles as $profiles_item ) { ?>
                        <option value="<?=$profiles_item['id']?>">
                            <?=$profiles_item['name']?>
                        </option>
                    <? } ?>
                </select>
            </div>
        </p>
    </fieldset>
    
    <input  type="submit"
            name="publishProfile"
            value="Publier Profil" />
    <input  type="submit"
            name="addPolice"
            value="Ajouter une police" />
    <a href="<?="http://".$localisation->siteAccess."/profiles"?>">
    <input  type="button"
            name="cancel"
            value="Annuler" />
    </a>
</form>

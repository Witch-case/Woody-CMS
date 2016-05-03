<?php

$module->addCssFile('edit.css');
$module->addJsFile('edit.js');
?>

<h1>Edition de <?=$targetLocalisation->name?> [<?=$draft->structure?>]</h1>

<p>
    <?=$targetLocalisation->description?>
</p>
<div class="errorMessages">
    <? foreach( $messages as $message ) { ?>
    <p><?=$message?></p>
    <? } ?>
</div>

<form name="modify" method="post" enctype="multipart/form-data">
    <? if( isset($draft->id) ) { ?>
        <input type="hidden" value="<?=$draft->id?>" name="draftID" />
    <? } ?>
    <? if( $draft->structure ) { ?>
        <input type="hidden" value="<?=$draft->structure?>" name="draftType" />
    <? } ?>
    
    <div id="attributesEdit">
        <? foreach( $draft->attributes as $attribute ) { ?>
            <fieldset>
                <legend><?=$attribute->name?> [<?=$attribute->type?>]</legend>
                <? $attribute->edit() ?>
        </fieldset>
        <br/>
        <? } ?>
    </div>
    
    <div id="actionsEdit">
        <input  class="defaultbutton" 
                type="submit" 
                title="Publier le contenu du brouillon en cours de modification. Ce brouillon sera alors la version publiée." 
                value="Publier" 
                name="publishButton" />
        <input  class="button" 
                type="submit" 
                title="Sauvegarder le contenu du brouillon qui est actuellement modifié et continuer la modification. Utilisez ce bouton périodiquement pour sauvegarder votre travail tout en le modifiant." 
                value="Enregistrer le brouillon" 
                name="storeButton" />
        <input  class="button"
                type="button" 
                value="Quitter"
                title="Quitter" 
                onclick="quit();" />
        <input  class="button" 
                type="submit" 
                title="Supprimer le brouillon actuellement modifié. Cette action supprimera aussi les traductions qui appartiennent au brouillon (si elles existent)." 
                onclick="return confirmDiscard( '<?=$draft->creation_date->frenchFormat(true)?>', '<?=$draft->creator->value?>' );" 
                value="Supprimer le brouillon" 
                name="discardButton" />
    </div>
    
    <div id="saveOnQuit">
        <input  class="button" 
                type="submit" 
                title="Enregistrer le brouillon en cours de modification et quitter le mode modification. Utilisez ce bouton pour enregistrer votre travail pour revenir plus tard." 
                value="Enregistrer le brouillon et quitter" 
                name="storeExitButton" />
        <input  class="button" 
                type="submit" 
                title="Quitter sans sauvegarder le brouillon en cours de modification. Utilisez ce bouton pour annuler vos dernières modifications." 
                value="Quitter sans sauvegarder" 
                name="exitButton" />
        <input  class="button"
                type="button" 
                value="Annuler"
                title="Annuler" 
                onclick="cancelQuit();" />
                
    </div>
</form>

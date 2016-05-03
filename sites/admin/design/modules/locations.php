<?php

$module->addCssFile('locations.css');
$module->addJsFile('locations.js');
?>

<h1>Gérer les emplacements</h1>

<div class="errorMessages">
    <? foreach( $messages as $message ) { ?>
        <p><?=$message?></p>
    <? } ?>
</div>

<form   method="post"
        name="editLocationForm">
    <div class="respiration">
        <? foreach( $locations as $linkId => $linkedLocations ) { ?>
            <div class="onglets">
                sites:
                <? foreach( $linkedLocations as $location ){ ?>
                    <span   class="onglet_0 onglet" 
                            id="onglet_link_<?=$linkId?>_<?=$location['id']?>" 
                            onclick="javascript:change_onglet( 'link_<?=$linkId?>', '<?=$location['id']?>' );">
                        <?=$location['site']?>
                    </span>
                <? } ?>
            </div>

            <? foreach( $linkedLocations as $i => $location ) { ?>
                <div    class="contenu_onglet" 
                        id="contenu_link_<?=$linkId?>_<?=$location['id']?>">
                    <p>
                        Localisation ID : <?=$location['id']?>, Emplacement ID : <?=$linkId?>
                    </p>

                    <h2>URL</h2>
                    <?=$location['uriBasePart']?>
                    <input  type="text" 
                            name="uriEditPart[<?=$location['id']?>]" 
                            value="<?=$location['uriEditPart']?>" />

                    <h2>Nom</h2>
                    <input  type="text" 
                            name="name[<?=$location['id']?>]" 
                            value="<?=$location['name']?>" />

                    <h2>Description</h2>
                    <textarea name="description[<?=$location['id']?>]"><?=$location['description']?></textarea>
                </div>
            <? } ?>

            <? if( count($locations) > 1 ) { ?>
                <input  type="submit"
                        name="deleteLocation[<?=$linkId?>]"
                        value="Supprimer Emplacement" />
                <br/>
                <br/>

            <? } ?>
        <? } ?>
    </div>
    <br/>
    
    <input  type="submit"
            name="publishLocationsEdit"
            value="Publier modifications" />
    <input  type="submit"
            name="addLocation"
            value="Ajouter un emplacement" />
    <a href="<?=$viewUri?>">
        <input  type="button"
                name="return"
                value="Retour à la visualisation" />
    </a>
</form>

<script type="text/javascript">
//<!--
        var anc_onglet = {
        <? foreach( $locations as $linkId => $linkedLocations ) { ?>
            link_<?=$linkId?>:'<?=$linkedLocations[0]['id']?>',
        <? } ?>
        };
        
        var init_section = [];
        var init_onglet  = [];
        
        <? foreach( $locations as $linkId => $linkedLocations ) { ?>
            init_section.push('link_<?=$linkId?>');
            init_onglet.push(anc_onglet.link_<?=$linkId?>);
        <? } ?>
//-->
</script>
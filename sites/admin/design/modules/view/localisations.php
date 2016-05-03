<?php

$module->addJsFile('localisations.js');
?>

<h2>Emplacement(s) :</h2>

<div class="onglets">
    parent(s):
    <? foreach( $locationsDisplay as $location ){ ?>
        <span   class="onglet_0 onglet" 
                id="onglet_<?=$location['id']?>" 
                onclick="javascript:change_onglet('<?=$location['id']?>');">
            <?=$location['parentDisplay']?>
        </span>
    <? } ?>
</div>

<? foreach( $locationsDisplay as $location ){ ?>
    <div    class="contenu_onglet" 
            <? if( $location['id'] != $targetLocalisation->id ) { ?>style="display: none;"<? } ?>
            id="contenu_onglet_<?=$location['id']?>" >
        <a href="<?=$location['href']?>">
            <strong><?=$location['name']?></strong>
        </a>
        
        <p>
            <?=$location['description']?>
        </p>
    </div>
<? } ?>

<script type="text/javascript">
//<!--
    var anc_onglet = '<?=$targetLocalisation->id?>';
//-->
</script>

<div id="modifyLocations">
    <a href="<?=$locationsHref?>">
        <input  type="submit" 
                title="Gérer les emplacements et leurs descriptions." 
                value="Gérer Emplacements" 
                class="button" />
    </a>
</div>

<?php

$module->addCssFile('view.css');
?>

<div id="maincontent">
    <!-- Maincontent START -->
    
    <? if( $targetLocalisation->has_target ) { ?><div id="maincontent-center"><? } ?>
        
    <h1 class="context-title">
        <?=$targetLocalisation->name?>
    </h1>
    
    <div id="info">
        <p>
            <img    height="24"                 
                    alt="Icone module" 
                    title="Module"
                    src="<?=$module->getImageFile("module.png")?>" 
                    class="transparent-png-icon" />
            &nbsp;
            <?=$targetLocalisation->module?>
        </p>

        <? if( $targetLocalisation->has_target ) { ?>
            <p>
                <img    height="24" 
                        width="24" 
                        title="<?=$targetType?>" 
                        alt="<?=$targetType?>" 
                        src="<?=$targetTypeIcon?>" 
                        class="transparent-png-icon" />
                &nbsp;
                <?=$targetType?>
            </p>
            <p>
                <a href="<?=$structureHref?>">
                    <img    height="24" 
                            title="Voir structure <?=$viewTarget->structure?>" 
                            alt="icone structure <?=$viewTarget->structure?>" 
                            src="<?=$structureIcon?>" 
                            class="transparent-png-icon" />
                </a>
                &nbsp;
                <?=$viewTarget->structure?>
            </p>

            <div class="context-information">
                <p class="left modified">
                    <?=$publicationDate?>, par
                    <?=$creator?>
                </p>

                <p class="left modified">
                    Dernière modification le <?=$modificationDate?>, par
                    <?=$modificator?>
                </p>
                <div class="break"></div>
            </div>

        <? } else { ?>
            <p>
                Pas de contenu associé
            </p>

        <? } ?>
    </div>
    
    <div class="errorMessages">
        <? foreach( $messages as $message ) { ?>
            <p><?=$message?></p>
        <? } ?>
    </div>
    
    <div id="firstRespiration">
        <? include $module->getDesignFile('modules/view/localisations.php'); ?>
    </div>
    
    <div class="respiration">
        <? include $module->getDesignFile('modules/view/children.php'); ?>
    </div>

    <div class="break"></div>
        
    <? if( $targetLocalisation->has_target ) { ?></div>
        <div id="maincontent-right">
            <? include $module->getDesignFile('modules/view/target.php'); ?>
        </div>
    <? } ?>
    <!-- Maincontent END -->
</div>

<div class="clear"></div>
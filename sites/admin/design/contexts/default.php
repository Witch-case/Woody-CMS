<?php

?>

<!DOCTYPE html>
<html lang="en" dir="ltr">
    <head>
        <? include $module->getDesignFile('contexts/includes/head.php'); ?>
    </head>
    
    <body>
        <? include $module->getDesignFile('contexts/includes/header.php'); ?>
        
        <!-- content -->
        <div class="wrapper row2">
            <div id="container" class="clear">
                <div id="currentuser">
                    <h4>Utilisateur en cours</h4>
                    <p>
                        <a  id="currentuser-a"  
                            href="<?=$user->getLocalisationUri()?>">
                            <?=$_SESSION['user']['signature']?>
                        </a>
                        &nbsp;
                        <a href="<?=$baseUri."/login"?>" >
                            <img width="13" height="13" 
                                 class="transparent-png-icon" 
                                 src="<?=$module->getImageFile('disconnect.png')?>" 
                                 alt="se déconnecter" 
                                 title="Se déconnecter">
                        </a>
                    </p>
                </div>

                <div id="path">
                    <p class="path">
                        <span class="path-here-text">Vous &ecirc;tes ici&nbsp;:</span>
                        <? foreach( $breadcrumbs as $breadcrumb ) { ?>
                            <span class="path">
                                <a href="<?=$breadcrumb['href']?>">
                                    <?=$breadcrumb['name']?>
                                </a>
                            </span>
                            &nbsp;/&nbsp;
                        <? } ?>
                        <span class="path">
                             <?=$localisation->name?>
                        </span>
                    </p>
                </div>
                <div class="clear"></div>
                <hr class="hide" />

                <?=$module_result?>
                
            </div>
        </div>
        
        <!-- footer -->
        <? include $module->getDesignFile('contexts/includes/footer.php'); ?>
    </body>
</html>
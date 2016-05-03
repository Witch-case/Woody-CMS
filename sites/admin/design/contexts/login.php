<?php

?>

<!DOCTYPE html>
<html lang="fr">
    <head>
        <? include $module->getDesignFile('contexts/includes/head.php'); ?>    
    </head>
    
    <body>
        <? include $module->getDesignFile('contexts/includes/header.php'); ?>
        
        <!-- content -->
        <div class="wrapper row2">
            <div id="container" class="clear">
                <div id="columns">
                    <div id="maincolumn">
                        <hr class="hide" />
                        
                        <?=$module_result?>
                        
                    </div>
                    <div class="break"></div>
                </div>
            </div>
        </div>
        
        <!-- footer -->
        <? include $module->getDesignFile('contexts/includes/footer.php'); ?>
    </body>
</html>
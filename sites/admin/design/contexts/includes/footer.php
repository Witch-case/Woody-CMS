<?php

?>

<div class="wrapper row3">
    <footer id="footer" class="clear">
        <p class="fl_left">
            <a  href="http://www.witch-case.com" 
                target="_blank">
                Witch Case
            </a> 
            Copyright&copy;2016 - All Rights Reserved
        </p>
        <p class="fl_right">
            For more information see 
            <a  href="http://www.witch-case.com" 
                target="_blank">
                Witch Case
            </a>
        </p>
    </footer>
    
    <? foreach( $module->getJsFiles() as $jsFile ) { ?>
        <script src="<?=$jsFile?>"></script>
    <? } ?>
</div>
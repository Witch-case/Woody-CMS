<?php

?>

<p>
    <strong>signature&nbsp;:</strong>
    <?=$this->values['name']?>
</p>

<p>
    <strong>email&nbsp;:</strong>
    <?=$this->values['email']?>
</p>

<p>
    <strong>login&nbsp;:</strong>
    <?=$this->values['login']?>
</p>

<strong>profile(s)&nbsp;:</strong>
<ul>
    <? foreach( $this->values['profiles'] as $profile ) { ?>
        <li>
            <?=$profile?>
        </li>
    <? } ?>
</ul>
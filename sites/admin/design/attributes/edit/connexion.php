<?php

?>

<? if( isset($this->values['user_connexionId']) ) { ?>
    <input  type="hidden"
            name="<?=$this->tableColumns['user_connexionId']?>"
            value="__last_value__" />
<? } ?>

<h2>signature</h2>
<p>
    <input  type="text" 
            name="@_<?=$this->type.'#name__'.$this->name?>"
            value="<?=$this->values['name']?>" />
</p>

<h2>email</h2>
<p>
    <input  type="text" 
            name="@_<?=$this->type.'#email__'.$this->name?>"
            value="<?=$this->values['email']?>" />
</p>

<h2>login</h2>
<p>
    <input  type="text" 
            name="@_<?=$this->type.'#login__'.$this->name?>"
            value="<?=$this->values['login']?>" />
</p>

<h2>password</h2>
<p>
    <input  type="password" 
            name="@_<?=$this->type.'#password__'.$this->name?>"
            value="<?=$this->values['pass_display']?>" />
</p>

<h2>confirm password</h2>
<p>
    <input  type="password" 
            name="@_<?=$this->type.'#password_confirm__'.$this->name?>"
            value="<?=$this->values['pass_display']?>" />
</p>

<h2>Profile(s)</h2>
<p>
    <select multiple
            name="@_<?=$this->type.'#profiles__'.$this->name.'[]'?>">
        <? foreach( $profiles as $profile ) { ?>
            <option <? if( in_array($profile->name, $this->values['profiles']) ) { ?>
                        selected
                    <? } ?>
                    value="<?=$profile->name?>">
                <?=$profile->name?>
            </option>
        <? } ?>
    </select>
</p>
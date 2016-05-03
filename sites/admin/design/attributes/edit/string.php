<?php

?>

<h2>
    <?=$this->name?>&nbsp;[<?=$this->type?>]
</h2>
<p>
    <input  type="text" 
            name="<?=$this->tableColumns['string']?>" 
            value="<?=htmlentities($this->values['string'])?>" />
</p>


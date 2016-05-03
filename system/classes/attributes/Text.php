<?php

require_once 'system/classes/attributes/Attribute.php';

class Text extends Attribute {
    
    function Text( $attributeName, $params=[] )
    {
        $this->type     = "text";
        $this->name     = $attributeName;
        
        $this->tableColumns =   [
                                    "text"    =>  "@_text__".$attributeName
                                ];
        $this->values       =   [
                                    "text"    =>  ""
                                ];
        
        $this->dbFields     =   array(
                                    "`@_text__".
                                        $attributeName.
                                        "` text DEFAULT NULL"
                                );
    }
    
    function content()
    {
        if( $this->values['text'] )
        {   return $this->values['text']; }
        else
        {   return false;   }
    }
}

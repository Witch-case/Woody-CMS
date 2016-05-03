<?php

require_once 'system/classes/attributes/Attribute.php';

class String extends Attribute {
    
    function String( $attributeName, $params=[] )
    {
        $this->type     = "string";
        $this->name     = $attributeName;
        
        $this->tableColumns =   [
                                    "string"    =>  "@_string__".$this->name
                                ];
        $this->values       =   [
                                    "string"    =>  ""
                                ];
        $this->parameters   =   [
                                    'lenght'    =>  [
                                                        'verifyMethod'  => 'verifyLenght', 
                                                        'inputForm'     => 'lenghtForm',
                                                        'value'         => 511
                                                    ]
                                ];
        
        foreach( $params as $name => $value )
        {
            if( isset($this->parameters[$name]) 
                && call_user_func('self::'.$this->parameters[$name]['verifyMethod'], $value) 
            ){
                $this->parameters[$name]['value'] = $value;
            }
            elseif( isset($value["Field"]) )
            {
                $columnData = self::splitColumn($value["Field"]);
                
                if( strcmp($columnData["type"], $this->type) == 0 
                    && strcmp($columnData["name"], $this->name) == 0
                    && strcmp(substr($value["Type"], 0, 7), "varchar") == 0
                ){
                    $lenght = substr($value["Type"], 8, -1);
                    $this->parameters['lenght']['value'] = (int) $lenght;
                }

            }
        }
        
        $this->dbFields     =   array(
                                    "`@_string__".
                                        $this->name.
                                        "` varchar(".
                                        $this->parameters['lenght']['value'].
                                        ") DEFAULT NULL"
                                );
    }
    
    function content()
    {
        if( $this->values['string'] )
        {   return $this->values['string']; }
        else
        {   return false;   }
    }
    
    static function verifyLenght( $lenght )
    {
        if( !is_numeric($lenght) )
        {   return false;   }
        
        if( strcmp($lenght, (int) $lenght) != 0 )
        {   return false;   }
        
        if( (int) $lenght <= 0 )
        {   return false;   }
        
        return true;
    }
    
    function lenghtForm( $indice )
    {
        global $module;
        
        $title  = "lenght";
        
        $inputAttributes =  [
            'name'      =>  "attributes[".$indice."][parameters][".$title."]",
            'type'      =>  false,  // Possible values : color, date, datetime, datetime-local, 
                                    // email, month, number, range, search, tel, time, url, week
            'disabled'  =>  false,  //Specifies that an input field should be disabled
            'max'       =>  false,  //Specifies the maximum value for an input field
            'maxlength' =>  false,  //Specifies the maximum number of character for an input field
            'min'       =>  false,  //Specifies the minimum value for an input field
            'pattern'   =>  false,  //Specifies a regular expression to check the input value against
            'readonly'  =>  false,  //Specifies that an input field is read only (cannot be changed)
            'required'  =>  false,  //Specifies that an input field is required (must be filled out)
            'size'      =>  false,  //Specifies the width (in characters) of an input field
            'step'      =>  false,  //Specifies the legal number intervals for an input field
            'value'     =>  false,  //Specifies the default value for an input field
        ];
        
        $inputAttributes['type']    = "text";
        $inputAttributes['value']   = $this->parameters['lenght']['value'];
        
        include $module->getDesignFile('attributes/edit/parameters/simpleInput.php');
        
        return;
    }
}

<?php

class Attribute {
    
    var $parameters     = [];
    var $dbFields       = [];
    var $values         = [];
    var $tableColumns   = [];
    var $joinTables     = [];
    var $joinFields     = [];
    var $joinConditions = [];
    var $leftJoin       = [];
    var $groupBy        = [];
    
    function set( $args )
    {
        foreach( $args as $valueColumn => $value )
        {   foreach( $this->tableColumns as $label => $column )
            {   if( strcmp($column, $valueColumn) == 0 )
                {
                    $this->values[$label] = $value;
                    break;
        }   }   }
        
        return true;
    }
    
    function content()
    {
        if( !is_array($this->values) )
        {   return $this->values;   }
        
        elseif( count($this->values) == 1 )
        {
            foreach( $this->values as $value )
            {   return $value;  }
        }
        else
        {
            return $this->values;
        }
    }
    
    function setValue( $key, $value )
    {
        $this->values[$key] = $value;
        
        return;
    }
    
    function display( $filename=false )
    {
        global $module;
        
        if( !$filename )
        {   $filename = strtolower( $this->type );  }
        
        $file = $module->getControllerFile("attributes/view/".$filename.".php");
        
        if( $file )
        {   include $file;  }
        
        return;
    }
    
    function edit( $filename=false )
    {
        global $module;
        
        if( !$filename )
        {   $filename = strtolower ($this->type);   }
        
        $file = $module->getControllerFile("attributes/edit/".$filename.".php");
        
        if( $file )
        {   include $file;  }
        
        return true;
    }
    
    function create( $target )
    {
        return true;
    }
    
    function save( $target )
    {
        return true;
    }
    
    function delete()
    {
        return true;
    }
    
    static function splitColumn( $columnName )
    {
        if( strcmp(substr($columnName, 0, 2), "@_") != 0 )
        {   return false;   }
        
        $buffer         = explode("__", substr($columnName, 2));
        $attributeType  = $buffer[0];
        $attributeName  = $buffer[1];
        
        $attributeElement = false;
        if( strstr($attributeType, "#") )
        {
            $buffer = explode("#", $attributeType);
            $attributeType      = $buffer[0];
            $attributeElement   = $buffer[1];
        }
        
        return  array(
                    'name'      =>  $attributeName,
                    'type'      =>  $attributeType,
                    'element'   =>  $attributeElement
                );
    }
    
}

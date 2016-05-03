<?php

require_once 'system/classes/attributes/ExternalTableAttribute.php';

class Links extends ExternalTableAttribute {
    
    function Links( $attributeName, $params=[] )
    {
        $this->type         =   "links";
        $this->name         =   $attributeName;
        
        parent::__construct();
        
        $this->values['hrefs']          =   [];
        $this->values['texts']          =   [];
        $this->values['externals']      =   [];
        
        $this->joinFields['hrefs']  =   [
            'field' =>  "`".$this->type.'_'.$this->name.'`.href',
            'alias' =>  "@_".$this->type."#hrefs__".$this->name
        ];
        
        $this->joinFields['texts']  =   [
            'field' =>  "`".$this->type.'_'.$this->name.'`.text',
            'alias' =>  "@_".$this->type."#texts__".$this->name
        ];

        $this->joinFields['externals']  =   [
            'field' =>  "`".$this->type.'_'.$this->name.'`.external',
            'alias' =>  "@_".$this->type."#externals__".$this->name
        ];
    }
    
    function set( $args )
    {
        foreach( $args as $argColumn => $value )
        {   switch( $argColumn )
            {
                case $this->tableColumns['content_key']:
                    
                    $this->values['content_key'] = $value;
                    break;
                
                case '@_'.$this->type.'#hrefs__'.$this->name:
                    
                    if(is_array($value) )
                    {
                        $this->values['hrefs'] = [];
                        
                        foreach( $value as $i => $valueItem )
                        {   if( !empty($valueItem) )
                            {
                                $this->values['hrefs'][$i] = $valueItem;
                                
                                if( isset($args['@_'.$this->type.'#externals__'.$this->name][$i]) )
                                {
                                    $externalValue = $args['@_'.$this->type.'#externals__'.$this->name][$i];
                                    if( is_array($externalValue) )
                                    {   $externalValue = $externalValue[0]; }
                                    
                                    $this->values['externals'][$i] = $externalValue;
                                }
                                else
                                {   $this->values['externals'][$i] = 0; }
                        }   }
                    }
                    break;
                
                case '@_'.$this->type.'#texts__'.$this->name:
                    
                    if(is_array($value) )
                    {
                        $this->values['texts'] = [];
                        foreach( $value as $i => $valueItem )
                        {   if( !empty($valueItem) )
                            {   
                                $this->values['texts'][$i] = $valueItem;   
                        }   }
                    }
                    break;
        }   }
        
        return true;
    }
    
    private function insert( $content_key )
    {
        $limitIndice = count($this->values['hrefs']);
        
        if( count($this->values['texts']) > $limitIndice )
        {   $limitIndice = count($this->values['texts']);  }
        
        $externalTableFields = [];
        for( $i=0; $i < $limitIndice; $i++ )
        {
            $externalTableFields[$i] = [];
            
            $externalTableFields[$i]['href']        = $this->values['hrefs'][$i];
            $externalTableFields[$i]['text']        = $this->values['texts'][$i];
            $externalTableFields[$i]['external']    = $this->values['externals'][$i];
        }
        
        return $this->globalInsert( $content_key, $externalTableFields );
    }
}
<?php

require_once 'system/classes/attributes/Attribute.php';
require_once 'system/classes/targets/Content.php';

class Get_contents extends Attribute {
    
    function Get_contents( $attributeName, $params=[] )
    {
        global $localisation;
        global $log;
        
        $this->type         =   "get_contents";
        $this->name         =   $attributeName;
        $this->id           =   $this->type.'_'.$this->name;
        $this->blankcontent =   false;
        
        $this->parameters   =   [
            'structure' =>  [
                                'verifyMethod'  => 'verifyStrcuture', 
                                'inputForm'     => 'structureForm',
                                'value'         => 'article'
                            ]
        ];
        
        foreach( $params as $name => $value )
        {   if( isset($this->parameters[$name]) 
                && call_user_func('self::'.$this->parameters[$name]['verifyMethod'], $value) 
            ){
                $this->parameters[$name]['value'] = $value;
            }
            elseif( isset($value["Field"]) )
            {
                $columnData = self::splitColumn($value["Field"]);
                
                if( strcmp($columnData["type"], $this->type) == 0 
                    && strcmp($columnData["name"], $this->name) == 0
                    && strcmp(substr($columnData["element"], 0, 4), "get_") == 0
                ){
                    $this->parameters['structure']['value'] = substr($columnData["element"], 4);
                }
        }   }
        
        $this->tableColumns =   [
            'actif'             =>  "@_".$this->type."#get_".
                                        $this->parameters['structure']['value'].
                                        "__".$this->name,
            'fk_localisation'   =>  "@_".$this->type."#fk_localisation".
                                        "__".$this->name,
            'depth'             =>  "@_".$this->type."#depth".
                                        "__".$this->name,
            'order'             =>  "@_".$this->type."#order".
                                        "__".$this->name,
            'limit'             =>  "@_".$this->type."#limit".
                                        "__".$this->name,
            
            /*'fk_localisation'   =>  "@_".$this->type."#content_".
                                        $this->parameters['structure']['value'].
                                        "__".$this->name,
            'limit'             =>  "@_".$this->type."#limit".
                                        "__".$this->name,*/
        ];
        
        $this->values['actif']              = 0;
        $this->values['fk_localisation']    = 0;
        $this->values['limit']              = 0;
        $this->values['depth']              = 0;
        $this->values['order']              = 'priority asc';
        
        $this->dbFields     =   [
            "`".$this->tableColumns['actif']."` ".
                "TINYINT(1) DEFAULT NULL", 
            "`".$this->tableColumns['fk_localisation']."` ".
                "int(11) DEFAULT NULL", 
            "`".$this->tableColumns['limit']."` ".
                "int(11) DEFAULT NULL", 
            "`".$this->tableColumns['depth']."` ".
                "int(11) DEFAULT NULL", 
            "`".$this->tableColumns['order']."` ".
                "varchar(511) DEFAULT NULL", 
        ];
        
        $this->leftJoin = [
            [
                'table'     =>  "localisation",
                'alias'     =>  $this->id."_localisation", 
                'condition' =>  "`".$this->id."_localisation`.id = `".$this->tableColumns['fk_localisation']."`"
            ],
        ];
        
        $leftJoinCondition  = "";
        for( $i=1; $i<=$localisation->maxDepth; $i++ )
        {
            if( $i > 1 )
            {   $leftJoinCondition .= "AND ";    }
            
            $leftJoinCondition .= "( ";
            
            $leftJoinCondition .=   "( `".$this->id."_localisation`.level_".$i." IS NOT NULL ";
            $leftJoinCondition .=       "AND `".$this->id."_child`.level_".$i." = ";
            $leftJoinCondition .=       "`".$this->id."_localisation`.level_".$i." ) ";
            
            if( $i > 1 )
            {
                $leftJoinCondition .=   "OR ";
                $leftJoinCondition .=   "`".$this->id."_localisation`.level_".$i." IS NULL ";
                //$leftJoinCondition .=   "( `".$this->id."_localisation`.level_".$i." IS NULL ";
                //$leftJoinCondition .=       "AND `".$this->id."_child`.level_".$i." IS NOT NULL ) ";
            }
            
            $leftJoinCondition .= ") ";
        }
        
        $this->leftJoin[]   =   [
                'table'     =>  "localisation",
                'alias'     =>  $this->id."_child", 
                'condition' =>  "( ".$leftJoinCondition." ".
                                    "AND `".$this->id."_child`.target_table = ".
                                        "'content_".$this->parameters['structure']['value']."' ".
                                    "AND `".$this->id."_child`.id <> `".$this->id."_localisation`.id )"
        ];
        
        $this->leftJoin[]   =   [
                'table'     =>  "content_".$this->parameters['structure']['value'],
                'alias'     =>  $this->id."_target", 
                'condition' =>  "`".$this->id."_child`.target_fk = `".$this->id."_target`.id"
        ];
        
        if( !$this->blankcontent )
        {   $this->blankcontent = new Content( $this->parameters['structure']['value'] );   }
        
        $this->values['targets'] = [];
        foreach( $this->blankcontent->attributes as $blankAttribute )
        {   foreach( $blankAttribute->tableColumns as $columnLabel => $columnName )
            {
                $label = "target_".$blankAttribute->type."_".$blankAttribute->name.":".$columnLabel;
                
                $this->joinFields[$label] = [
                    'field' =>  "`".$this->id."_target`.`".$columnName."` ",
                    'alias' =>  "@_".$this->type."#".$label."__".$this->name
                    
                ];
        }   }
        
        $this->joinFields['target_id']   =   [
            'field' =>  "`".$this->id."_target`.id ",
            'alias' =>  "@_".$this->type."#target_id__".$this->name
        ];
        
        $this->joinFields['target_publication_date'] =   [
            'field' =>  "`".$this->id."_target`.publication_date",
            'alias' =>  "@_".$this->type."#target_publication_date__".$this->name
        ];
        
        $this->joinFields['target_modification_date'] =   [
            'field' =>  "`".$this->id."_target`.modification_date",
            'alias' =>  "@_".$this->type."#target_modification_date__".$this->name
        ];
        /*
        $this->joinFields['id']     =   [
            'field' =>  "`".$this->id."_localisation`.id",
            'alias' =>  "@_".$this->type."#fk_localisation__".$this->name
        ];
        */
        $this->joinFields['name']   =   [
            'field' =>  "`".$this->id."_child`.name",
            'alias' =>  "@_".$this->type."#localisation_name__".$this->name
        ];
        
        $this->joinFields['description']    =   [
            'field' =>  "`".$this->id."_child`.description",
            'alias' =>  "@_".$this->type."#localisation_description__".$this->name
        ];
        
        $this->joinFields['priority']   =   [
            'field' =>  "`".$this->id."_child`.priority",
            'alias' =>  "@_".$this->type."#localisation_priority__".$this->name
        ];
        
        $this->groupBy      =   [
                                     '`@_'.$this->type.'#target_id__'.$this->name.'`'
                                ];
    }
    
    function set( $args )
    {
        global $module;
        
        if( !isset($args["@_".$this->type."#target_id__".$this->name][0]) )
        {   return parent::set($args);  }
        
        $publicationDates = $args["@_".$this->type."#target_publication_date__".$this->name];
        arsort($publicationDates);
        
        $indiceOrder = array_keys($publicationDates);
        
        foreach( $indiceOrder as $indice )
        {
            $this->values["targets"][] = [
                'id'                =>  $args["@_".$this->type."#target_id__".$this->name][$indice], 
                'publication_date'  =>  $args["@_".$this->type."#target_publication_date__".$this->name][$indice], 
                'modification_date' =>  $args["@_".$this->type."#target_modification_date__".$this->name][$indice], 
                'priority'          =>  $args["@_".$this->type."#localisation_priority__".$this->name][$indice],
                'name'              =>  $args["@_".$this->type."#localisation_name__".$this->name][$indice],
                'description'       =>  $args["@_".$this->type."#localisation_description__".$this->name][$indice],
            ];
        }
        
        foreach( $args as $argColumn => $value )
        {   if( isset($value) )
            {
                $attributeClass= false;
                $attributeName = false;
                $attributeVar  = false;

                if( in_array($argColumn, $this->tableColumns) )
                {
                    $indice                 = array_flip($this->tableColumns)[$argColumn];
                    $this->values[$indice]  = $value;
                }
                else
                {
                    $columnData = self::splitColumn($argColumn);

                    if( $columnData 
                        && strcmp($columnData["type"], $this->type) == 0
                        && strcmp($columnData["name"], $this->name) == 0
                        && strcmp(substr($columnData["element"], 0, 7), "target_") == 0
                    ){
                        $element = substr($columnData["element"],  7);

                        $buffer = explode("_", $element);
                        foreach($buffer as $key => $buffer_item)
                        {
                            if( $key == 0 )
                            {   $attributeClass =   ucfirst($buffer_item);    }
                            else
                            {   $attributeClass .=  "_".$buffer_item;   }

                            unset($buffer[$key]);

                            $classFile = $module->getClassFile("attributes/".$attributeClass.".php");

                            if( $classFile )
                            {
                                require_once $classFile;
                                $buffer = implode("_", $buffer);
                                $buffer = explode(":", $buffer);

                                $attributeName = $buffer[0];
                                $attributeVar  = $buffer[1];
                                break;
                            }
                        }

                        if( $attributeClass && $attributeName && $attributeVar )
                        {   foreach( $indiceOrder as $i => $indice )
                            {
                                if( !isset($this->values["targets"][$i]['attributes']) )
                                {   $this->values["targets"][$i]['attributes'] = [];    }

                                if( !isset($this->values["targets"][$i]['attributes'][$attributeName]) )
                                {   $this->values["targets"][$i]['attributes'][$attributeName] = [];    }

                                if( !isset($this->values["targets"][$i]['attributes'][$attributeName]['class']) )
                                {   $this->values["targets"][$i]['attributes'][$attributeName]['class'] = $attributeClass;    }

                                if( !isset($this->values["targets"][$i]['attributes'][$attributeName]['data']) )
                                {   $this->values["targets"][$i]['attributes'][$attributeName]['data'] = [];    }

                                $this->values["targets"][$i]['attributes'][$attributeName]['data'][$attributeVar] = $value[$indice];
                        }   }
                    }
                }
        }   }
        
        foreach( $this->values["targets"] as $tagetIndice => $attributeTarget )
        {   foreach( $attributeTarget["attributes"] as $attributeName => $attributeData )
            {
                $class      = $attributeData["class"];
                
                $attribute  = new $class( $attributeName );
                
                $data = [];
                foreach( $attributeData["data"] as $var => $value )
                {   if( count($attributeData["data"]) == 1 )
                    {
                        $data[ "@_".strtolower($class)."__".$attributeName ] = $value;
                    }
                    else 
                    {
                        $data[ "@_".strtolower($class)."#".$var."__".$attributeName ] = $value;
                }   }
                
                $attribute->set($data);
                
                $this->values["targets"][$tagetIndice]["attributes"][$attributeName] = $attribute;
        }   }
        
        $buffer     = explode(' ', $this->values['order']);
        $orderField = $buffer[0];
        $orderType  = $buffer[1];
        
        $orderArray = [];
        foreach( $this->values["targets"] as $targetData )
        {   $orderArray[]   = $targetData[$orderField]; }
        
        if( $orderType == "asc" )
        {   asort($orderArray); }
        else
        {   arsort($orderArray);    }
        
        $orderedTargets = [];
        foreach( array_keys($orderArray) as $key )
        {   $orderedTargets[] = $this->values['targets'][$key]; }
        
        $this->values['targets'] = $orderedTargets;
        
        return true;
    }
    
    function content()
    {
        return $this->values['targets'];
    }
    
    static function verifyStrcuture( $structure )
    {
        $blankcontent = new Content($structure);
        
        return $blankcontent->exist;
    }
    
    function ordersList()
    {
        return [
            'priority asc', 
            'priority desc', 
            'publication_date asc', 
            'publication_date desc', 
            'modification_date asc', 
            'modification_date desc', 
        ];
    }
    
    
    function structureForm( $indice )
    {
        global $module;
        
        $title  = "structure";
        
        $selectAttributes =  [
            'name'      =>  "attributes[".$indice."][parameters][".$title."]",
            'autofocus' =>  false,  //Specifies that the drop-down list should automatically 
                                    //get focus when the page loads
            'disabled'	=>  false,  //Specifies that a drop-down list should be disabled
            'form'	=>  false,  //Defines one or more forms the select field belongs to
            'multiple'	=>  false,  //Specifies that multiple options can be selected at once
            'required'	=>  false,  //Specifies that the user is required to select a value before submitting the form
            'size'	=>  false,  //Defines the number of visible options in a drop-down list
        ];
        
        $structuresListData = Structure::listStructures(false);
        
        $optionsAttributes   =  [];
        foreach( $structuresListData as $structureData )
        {
            if( strcmp($structureData["name"], $this->parameters['structure']['value']) == 0 )
            {   $selected = 'selected'; }
            else
            {   $selected = false;  }
            
            $optionsAttributes[ $structureData["name"] ] = [
                'value'     =>  $structureData["name"], //Specifies the value to be sent to a server
                'disabled'  =>  false,                  //Specifies that an option should be disabled
                'label'     =>  false,                  //Specifies a shorter label for an option
                'selected'  =>  $selected,              //Specifies that an option should be 
                                                        //pre-selected when the page loads
            ];
        }
        
        include $module->getDesignFile('attributes/edit/parameters/simpleSelect.php');
        
        return;
    }
}
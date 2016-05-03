<?php

require_once 'system/classes/datatypes/ExtendedDateTime.php';
require_once 'system/classes/datatypes/Signature.php';
require_once 'system/classes/attributes/Attribute.php';

class Target {
    
    static $dbFields    =   array(
                                "`id` int(11) unsigned NOT NULL AUTO_INCREMENT",
                                "`name` varchar(255) DEFAULT NULL",
                                "`context` varchar(511) DEFAULT NULL",
                            );
    
    static $primaryDbField = "PRIMARY KEY (`id`) ";
    
    function Target( $table )
    {
        global $log;
        global $db;
        global $module;
        
        $this->table                = $table;
        
        $this->name                 = "";
        $this->context              = "";
        
        $this->exist                = true;
        
        $this->attributes           = [];
        
        $query  =   "SHOW COLUMNS FROM `".$this->table."` WHERE Field LIKE '@_%'";
        
        $data   = $db->selectQuery($query);
        
        if( $data === false && $db->errno() != 1146 )
        {   $log->error("Can't access to information_schema of: ".$this->table." in database", true);   }
        
        if( !$data )
        {   $this->exist = false;   }
        
        else 
        {   foreach( $data as $columnItem )
            {
                $columnData = Attribute::splitColumn($columnItem["Field"]);
                
                if( !isset($this->attributes[ $columnData['name'] ]) )
                {
                    $className = ucfirst($columnData['type']);
                    $classFile = $module->getClassFile( 'attributes/'.$className.'.php' );
                    
                    require_once $classFile;
                    
                    $this->attributes[ $columnData['name'] ] =  new $className( $columnData['name'], $data );
                }
        }   }
    }
    
    protected function fetchTarget( $id, $datatypes )
    {
        global $db;
        global $log;
        
        $multipleResults = false;
        
        $query  =   "SELECT target.*";
        foreach( $datatypes['Signature'] as $column )
        {   $query  .=  ", ".$column.".name AS ".$column."__signature"; }
        
        foreach( $this->attributes as $attribute )
        {   foreach( $attribute->joinFields as $field )
            {
                $query  .=  ", ".$field['field']." AS `".$field['alias']."`";
        }   }
        
        $query  .=  " ";
        
        $query  .=  "FROM `".$db->escape_string($this->table)."` AS target";
        
        foreach( $this->attributes as $attribute )
        {   foreach( $attribute->leftJoin as $leftJoin )
            {
                $multipleResults = true;
                
                $query  .=  " LEFT JOIN `".$leftJoin['table']."` ";
                $query  .=  "AS `".$leftJoin['alias']."` ";
                $query  .=  "ON ".$leftJoin['condition'];
        }   }
        
        foreach( $datatypes['Signature'] as $column )
        {   $query  .=  ", `user_connexion` AS ".$column;   }
        
        foreach( $this->attributes as $attribute )
        {   foreach( $attribute->joinTables as $table )
            {
                $query  .=  ", `".$table['table']."` AS `".$table['alias']."`";
        }   }
        
        $query  .=  " ";
        
        $query  .=  "WHERE target.id = '".$db->escape_string($id)."' ";
        foreach( $datatypes['Signature'] as $column )
        {   $query  .=  "AND ".$column.".id = target.".$column." "; }
        
        foreach( $this->attributes as $attribute )
        {   foreach( $attribute->joinConditions as $condition )
            {
                $query  .=  "AND ".$condition." ";
        }   }
        
        $groupByArray = [];
        foreach( $this->attributes as $attribute )
        {   foreach( $attribute->groupBy as $groupBy )
            {
                $groupByArray[] =  $groupBy;
        }   }
        
        if( count($groupByArray) > 0 )
        {
            $query .= " GROUP BY ".implode(", ", $groupByArray);
        }
        
        if( $multipleResults )
        {
            $result     = $db->multipleRowsQuery($query);
            
            if( empty($result) )
            {
                $log->debug($query, "Query doesn't get any results");
                return false;
            }
            
            $targetData = $result[0];
            
            foreach( $targetData as $resultColumn => $unused )
            {
                $columnData = Attribute::splitColumn($resultColumn);
                
                if( !$columnData )
                {   continue;   }
                
                foreach( $this->attributes as $attribute )
                {
                    if( count($attribute->leftJoin) > 0 )
                    {
                        if( strcmp($attribute->type, $columnData['type']) == 0
                            && strcmp($attribute->name, $columnData['name']) == 0
                            && !in_array($resultColumn, $attribute->tableColumns) 
                        ){
                            $groupByArray = [];
                            foreach( $attribute->groupBy as $groupByColumn )
                            {   $groupByArray[trim($groupByColumn, '`')] = []; }
                            
                            $data = [];
                            foreach( $result as $resultItem )
                            {
                                $getData = true;
                                foreach( $attribute->groupBy as $groupByColumn )
                                {
                                    $groupByColumn = trim($groupByColumn, '`');
                                    if( in_array($resultItem[$groupByColumn], $groupByArray[$groupByColumn]) )
                                    {
                                        $getData = false;
                                        break;
                                    }
                                    else
                                    {   $groupByArray[$groupByColumn][] = $resultItem[$groupByColumn];  }
                                }
                                
                                if( $getData )
                                {   $data[] = $resultItem[$resultColumn];   }
                            }
                            
                            $targetData[$resultColumn] = $data;
                        }
                    }   
                }
            }
        }
        else
        {
            $targetData = $db->singleRowQuery($query);
            
            if( !is_array($targetData) )
            {
                $log->debug($query, "Query doesn't get any results");
                return false;
            }
        }
        
        if( $targetData )
        {   return $this->set($targetData); }
        else
        {   return false;   }
    }
    
    protected function setTarget( $args, $datatypes )
    {
        $doneAttributes = [];
        foreach( $args as $argLabel => $argValue )
        {
            if( strcmp("@", substr($argLabel, 0, 1)) != 0 )
            {
                if( in_array($argLabel, $datatypes['ExtendedDateTime']) )
                {
                    $this->$argLabel = new ExtendedDateTime($argValue);
                }
                elseif( in_array($argLabel, $datatypes['Signature']) )
                {
                    $this->$argLabel =  new Signature(  $argLabel, 
                                                        $argValue, 
                                                        $args[$argLabel."__signature"] 
                                        );
                    unset($args[$argLabel."__signature"]);
                }
                elseif( strcmp("context", $argLabel) == 0 )
                {
                    $context = $this->formatContext($argValue);
                }
                else 
                {
                    $this->$argLabel = $argValue;
                }
            }
            else
            {
                $columnData = Attribute::splitColumn($argLabel);
                
                if( !isset($this->attributes[ $columnData['name'] ]) )
                {
                    continue;
                }
                elseif( !in_array($columnData['name'], $doneAttributes) )
                {
                    $doneAttributes[] = $columnData['name'];
                    $this->attributes[ $columnData['name'] ]->set($args);
                }
            }
        }
        return true;
    }
    
    function edit( $args )
    {
        global $db;
        
        foreach( $this->attributes as $attribute )
        {
            $attribute->set( $args );
            $attribute->save( $this );
        }
        
        $query  =   "UPDATE `".$db->escape_string($this->table)."` SET ";
        
        if( strcmp($this->type, 'archive')  != 0 )
        {
            $currentDate    = date("Y-m-d H:i:s");
            $userID         = $_SESSION["user"]["connexionID"];
            
            $this->modificator          = $userID; 
            $this->modification_date    = new ExtendedDateTime($currentDate);
            
            $query  .=  "`modificator` = '".$userID."', ";
            $query  .=  "`modification_date` = '".$this->modification_date->sqlFormat()."', ";
        }
        
        $first = true;
        foreach( $this->attributes as $attribute )
        {   foreach( $attribute->tableColumns as $key => $tableColumn  )
            {   if( strcmp($attribute->values[$key], "__last_value__") != 0 )
                {
                    $value  =   $db->escape_string($attribute->values[$key]);

                    if( $first )
                    {   $first = false; }
                    else
                    {   $query  .=  ", ";   }

                    $query  .=  "`".$tableColumn."` = '".$value."'";
        }   }   }
        
        $query  .=  " WHERE `id` = '".$db->escape_string($this->id)."' ";
        
        return $db->updateQuery($query);
    }

    
    function formatContext( $contextString )
    {
        if( !$contextString )
            return false;
        
        $items = explode(",", $contextString);
        
        $this->context = array();
        foreach( $items as $item )
        {
            if( strstr($item, ":") === false )
                continue;
            
            $buffer = explode( ":", trim($item) );
            $this->context[trim($buffer[0])] = trim($buffer[1]);
        }
        
        return $this->context;
    }
}

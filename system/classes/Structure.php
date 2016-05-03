<?php

require_once 'system/classes/Localisation.php';
require_once 'system/classes/targets/Content.php';
require_once 'system/classes/targets/Archive.php';
require_once 'system/classes/targets/Draft.php';

class Structure {
    
    function Structure( $structureName, $getDraft=false )
    {
        $this->name         = trim($structureName);
        $this->created      = false;
        $this->exist        = true;
        $this->isArchive    = false;
        $this->attributes   = [];
        
        $this->content = new Content( $this->name );
        $this->archive = new Archive( $this->name );
        
        if( $getDraft )
        {   $this->draft = new Draft( $this->name );    }
        
        if( $this->content->exist )
        {   $this->attributes = $this->content->attributes;   }
        
        elseif( $this->archive->exist )
        {   $this->isArchive = true;    }
        
        else
        {   $this->exist = false;   }
        
        $this->archivedAttributes = [];
        foreach( $this->archive->attributes as $archiveAttribute )
        {
            $match = false;
            foreach( $this->attributes as $contentAttribute )
            {   if( strcmp($archiveAttribute->type, $contentAttribute->type) == 0 
                    && strcmp($archiveAttribute->name, $contentAttribute->name) == 0 
                ) {
                    $match = true;
                    break;
            }   }
            
            if( !$match )
            {   $this->archivedAttributes[] = $archiveAttribute;    }
        }
    }
    
    function createTime()
    {
        global $db;
        
        if( $this->created )
        {   return $this->created;  }
        
        $query  =   "SELECT table_name, create_time  ";
        $query  .=  "FROM information_schema.tables ";
        $query  .=  "WHERE table_type = 'BASE TABLE' ";
        $query  .=  "AND table_name LIKE 'archive_".$this->name."' ";
        
        $data = $db->singleRowQuery($query);
        $this->created = new ExtendedDateTime($data['create_time']);
        
        return $this->created;
    }
    
    function publish( $attributes )
    {
        global $db;
global $log;
        
        foreach( ['archive', 'draft', 'content'] as $type )
        {
            $class = ucfirst( $type );
            
            if( !$this->$type->exist )
            {
                $query  =   "CREATE TABLE `".$type."_".$db->escape_string($this->name)."` ( ";
                
                foreach( Target::$dbFields as $dbField ) 
                {   $query  .=  $dbField.", ";  }
                
                foreach( $class::$dbFields as $dbField ) 
                {   $query  .=  $dbField.", ";  }
                
                foreach( $attributes as $attribute )
                {   foreach( $attribute->dbFields as $dbField )
                    {   $query  .=  $dbField.", ";  }
                }
                
                $query  .=  Target::$primaryDbField;
                
                $query  .=  ") ENGINE=InnoDB AUTO_INCREMENT=10 DEFAULT CHARSET=utf8mb4 ";
                
                if( !$db->createQuery($query) )
                {   return false;   }
            }
            else
            {
                $target =   $this->$type;
                
                $query  =   "ALTER TABLE `".$type."_".$db->escape_string($this->name)."` ";
                
                foreach( Target::$dbFields as $dbField ) 
                {   $query  .=  "MODIFY COLUMN ".$dbField.", "; }
                
                foreach( $class::$dbFields as $dbField ) 
                {   $query  .=  "MODIFY COLUMN ".$dbField.", "; }
                
                $first              = true;
                $attributesNames    = [];
                foreach( $attributes as $attribute )
                {
                    $attributesNames[] = $attribute->name;
                    if( isset($target->attributes[$attribute->name]) )
                    {   $dbFieldPrefix = "MODIFY COLUMN ";  }
                    else
                    {   $dbFieldPrefix = "ADD ";    }
                    
                    foreach( $attribute->dbFields as $dbField )
                    {
                        if($first)
                        {   $first = false; }
                        else
                        {   $query  .=  ", ";   }
                        
                        $query  .=  $dbFieldPrefix.$dbField;
                    }
                }
                
                if( strcmp($type, 'archive') != 0 )
                {   foreach( $target->attributes as $attributeName => $attribute )
                    {   if( !in_array($attribute->name, $attributesNames) )
                        {   foreach( $attribute->tableColumns as $column )
                            {
                                if($first)
                                {   $first = false; }
                                else
                                {   $query  .=  ", ";   }
                                
                                $query  .=  "DROP `".$column."` ";
                }   }   }   }
//$log->debug($query, "QUERY");die("DEBUG");
                
                if( !$db->alterQuery($query) )
                {   return false;   }
            }
        }
        
        return true;
    }
    
    function delete()
    {
        global $db;
        global $log;
        
        $query = "SELECT * FROM `localisation` ";
        $query .= "WHERE target_table LIKE 'draft_".$this->name."' ";
        $query .= "OR target_table LIKE 'content_".$this->name."' ";
        
        $datas = $db->selectQuery($query);
        
        $db->begin();
        $commit = true;
        foreach( $datas as $data )
        {
            $deleteLocalisation = new Localisation($data['id'], $data);
            if( !$deleteLocalisation->delete() )
            {
                $commit = false;
                break;
            }
            //$log->debug($deleteLocalisation, 'LOCALISATION');
        }
        
        if( $commit )
        {   $db->commit();  }
        
        $query = "DROP TABLE `content_".$db->escape_string($this->name)."` ";
        if( $db->deleteQuery($query) )
        {
            $query = "DROP TABLE `draft_".$db->escape_string($this->name)."` ";
            
            if( !$db->deleteQuery($query) )
            {   return false;   }
        }
        else
        {   return false;   }
        
        return true;
    }
    
    static function listStructures( $archivesDisplay=false, $orders=[] )
    {
        global $db;
        
        if( isset($orders['created']) )
        {
            $orders['create_time'] = $orders['created'];
            unset($orders['created']);
        }
        
        if( isset($orders['name']) )
        {
            $orders['table_name'] = $orders['name'];
            unset($orders['name']);
        }
        
        if( !isset($orders['table_name']) )
        {   $orders['table_name'] = 'asc';  }
        
        $query  =   "SELECT table_name, create_time  ";
        $query  .=  "FROM information_schema.tables ";
        $query  .=  "WHERE table_type = 'BASE TABLE' ";
        
        $query  .=  "AND ( table_name LIKE 'content_%' ";
        $query  .=  "OR table_name LIKE 'draft_%' ";
        $query  .=  "OR table_name LIKE 'archive_%' ) ";
        
        $query  .=  "ORDER BY ";
        $first  =   true;
        foreach( $orders as $field => $order )
        {   if( in_array($field, ['table_name', 'create_time']) 
                && in_array($order, ['asc', 'desc']) 
            ){
                if( $first )
                {   $first = false; }
                else
                {   $query .= ", ";   }
                
                $query  .=  $field." ".$order." ";
        }   }
        
        $data = $db->multipleRowsQuery($query);
        
        $archives   = [];
        $contents   = [];
        $drafts     = [];
        $createTimes= [];
        foreach( $data as $item )
        {
            $tableName  = $item['table_name'];
            $prefix     = substr($tableName, 0, 8);
            
            if( strcmp($prefix, "archive_") == 0 )
            {
                $archives[] = substr($tableName, 8);    
                $createTimes[]  = $item['create_time'];
            }
            
            elseif( strcmp($prefix, "content_") == 0 )
            {   $contents[]     = substr($tableName, 8);    }
            
            else
            {
                $prefix = substr($tableName, 0, 6);
                
                if( strcmp($prefix, "draft_") == 0 )
                {   $drafts[] = substr($tableName, 6);  }
            }
        }
        
        $structures = [];
        foreach( $archives as $i => $Contentstructure )
        {
            $isArchive = true;
            if( in_array($Contentstructure, $contents) && in_array($Contentstructure, $drafts) )
            {   $isArchive = false; }
            
            if( !$isArchive
                || $archivesDisplay
            ){
                $structures[] = [ 
                                    'name'      => $Contentstructure, 
                                    'created'   => $createTimes[$i], 
                                    'is_archive'=> $isArchive
                                ];
        }   }
        
        return $structures;
    }
    
    static function count( $archives=false )
    {
        global $db;
        
        $query  =   "SELECT count(*)  ";
        $query  .=  "FROM information_schema.tables ";
        $query  .=  "WHERE table_type = 'BASE TABLE' ";
        
        if( !$archives )
        {   $query  .=  "AND table_name LIKE 'content_%' "; }
        else
        {   $query  .=  "AND table_name LIKE 'archive_%' "; }
        
        $data = $db->singleRowQuery($query);
        
        return $data['count(*)'];
    }
    
    static function countElements( $structure )
    {
        global $db;
        
        $count = [];
        foreach( ['draft', 'content', 'archive'] as $type ) 
        {
            $query  =   "SELECT COUNT(*) ";
            $query  .=  "FROM `".$type."_".$structure."` ";
            
            $countData  = $db->singleRowQuery($query);
            
            if( $countData !== false )
            {   $count[$type] = $countData['COUNT(*)']; }
            else
            {   $count[$type] = "Not available";    }
        }
        
        return $count;
    }
}

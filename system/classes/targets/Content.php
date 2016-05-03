<?php

require_once 'system/classes/targets/Target.php';
require_once 'system/classes/Localisation.php';

class Content extends Target {
    
    static $dbFields    =   array(
                                "`creator` int(11) DEFAULT NULL",
                                "`publication_date` datetime DEFAULT NULL",
                                "`modificator` int(11) DEFAULT NULL",
                                "`modification_date` datetime DEFAULT NULL",
                            );

    static $datatypes   =   array(
                                'Signature'         =>  array(
                                                            'creator', 
                                                            'modificator'
                                                        ),
                                'ExtendedDateTime'  =>  array(
                                                            'publication_date', 
                                                            'modification_date'
                                                        )
                            );
    
    function Content( $structure )
    {
        $this->type                 =   'content';
        $this->structure            =   $structure;
        $this->creator              =   new Signature('', '', '');
        $this->publication_date     =   new ExtendedDateTime("0000-00-00 00:00:00");
        $this->modificator          =   new Signature('', '', '');
        $this->modification_date    =   new ExtendedDateTime("0000-00-00 00:00:00");
        
        parent::__construct("content_".$structure);
    }
    
    function fetch( $id )
    {
        return $this->fetchTarget( $id, self::$datatypes );
    }
    
    function set( $args )
    {
        return $this->setTarget( $args, self::$datatypes );
    }
    
    function attribute( $attributeName )
    {
        return $this->attributes[$attributeName];
    }
    
    function countActiveDrafts()
    {
        global $db;
        
        $draftTable = "draft_".$this->structure;
        
        $query  =   "SELECT COUNT(*) FROM `".$db->escape_string($draftTable)."` ";
        $query  .=  "WHERE `content_key` = '".$this->id."' ";
        $query  .=  "AND `content_key` = '".$this->id."' ";
        $query  .=  "AND `creation_date` > '".$this->modification_date->format('Y-m-d H:i:s')."' ";
        
        $draftCount = $db->countQuery($query);
        
        return $draftCount;
    }
    
    function delete()
    {
        global $db;
        global $log;
        
        $archiveTable   = "archive_".$this->structure;
        $archiveID      = $this->archive();
        
        if( !$archiveID )
        {   return false;   }
        
        if( !Localisation::changeTarget( $this->table, $this->id, $archiveTable, $archiveID ) )
        {   return false;   }
        
        $draftTable = "draft_".$this->structure;
        
        $query  =   "SELECT * FROM `".$db->escape_string($draftTable)."` ";
        $query  .=  "WHERE content_key = '".$db->escape_string($this->id)."'";
        
        $draftsData = $db->multipleRowsQuery($query);
        
        if( $draftsData === false )
        {
            $log->error("Search Drafts query failed: \"".$query."\" possible corrupted Database !");
            return false;
        }
        
        foreach( $draftsData as $draftsDataItem)
        {
            $draft  = new self( $this->structure );
            $draft->set($draftsDataItem);
            
            if( !$draft->delete() )
            {
                $message    = "Cannot delete and archive draft ".$draft->name;
                $message   .= ", aborting publication ";
                $log->error($message);
                
                return false;
            }
        }
        
        $query  =   "DELETE FROM `".$this->table."` ";
        $query  .=  "WHERE id='".$this->id."' ";
        
        return $db->deleteQuery( $query );
    }
    
    function archive()
    {
        global $db;
        
        require_once 'system/classes/targets/Archive.php';
        
        $userID         = $_SESSION["user"]["connexionID"];
        $currentDate    = date("Y-m-d H:i:s");
        
        $query  =   "INSERT INTO `archive_".$db->escape_string($this->structure)."` ";
        $query  .=  "(`content_key`, `name`, `context`, `last_modificator`, ";
        $query  .=  "`last_modification_date`, `archiver`, `archive_date`";
        
        $orderAttributesKeys = [];
        foreach( $this->attributes as $attributeName => $attribute )
        {
            $orderAttributesKeys[$attributeName] = [];
            foreach($attribute->tableColumns as $key => $tableColumn)
            {
                $orderAttributesKeys[$attribute->name][] = $key;  
                $query .= ", `".$tableColumn."`";
            }
        }
        $query  .=  " ) ";
        
        if( is_array($this->context) )
        {
            $buffer = array();
            foreach($this->context as $label => $value)
            {   $buffer[] = $label.":".$value;  }
            
            $contextString = implode(",", $buffer);
        }
        else
        {   $contextString = $this->context;    }
        
        $query  .=  "VALUES ( '".$this->id."', ";
        $query  .=  "'".$db->escape_string($this->name)."', ";
        $query  .=  "'".$contextString."', ";
        $query  .=  "'".$this->modificator->id."', "; 
        $query  .=  "'".$this->modification_date->sqlFormat()."', ";
        $query  .=  "'".$userID."', "; 
        $query  .=  "'".$currentDate."'";
        
        foreach( $orderAttributesKeys as $attributeName => $orderKeys )
        {   foreach( $orderKeys as $key )
            {
                $value  =   $this->attributes[$attributeName]->values[$key];
                $query  .=  ", '".$db->escape_string($value)."'";
        }   }
        
        $query  .=  " ) ";
        
        $archiveId = $db->insertQuery($query);
        
        $archive = new Archive($this->structure);
        $archive->fetch($archiveId);
        
        foreach( $this->attributes as $attribute )
        {   $attribute->create($archive);   }
        
        return $archiveId;
    }
    
    static function searchUserContentsData( $structureName, $userID, $offset=false, $limit=false )
    {
        global $db;
        global $localisation;
        
        $query  =   "SELECT content.*, loc.* ";
        $query  .=  "FROM `content_".$structureName."` AS content, ";
        $query  .=  "`localisation` AS loc ";
        
        $query  .=  "WHERE ( content.creator=".$userID." ";
        $query  .=      "OR content.modificator=".$userID." ) ";
        $query  .=  "AND loc.site='".$localisation->site."' ";
        $query  .=  "AND loc.target_table='content_".$structureName."' ";
        $query  .=  "AND loc.target_fk=content.id ";
        
        $query  .=  "ORDER BY content.modification_date DESC ";
        
        if( $offset !== false && is_int($offset)
            && $limit && is_int($limit)
        ){
            $query  .=  "LIMIT ".$offset.", ".$limit." ";
        }
        
        return $db->selectQuery($query);
    }

}

<?php

require_once 'system/classes/targets/Target.php';
require_once 'system/classes/Localisation.php';

class Archive extends Target {
    
    static $dbFields    =   array(
                                "`content_key` int(11) DEFAULT NULL",
                                "`last_modificator` int(11) DEFAULT NULL",
                                "`last_modification_date` datetime DEFAULT NULL",
                                "`archiver` int(11) DEFAULT NULL",
                                "`archive_date` datetime DEFAULT NULL",
                            );
    
    static $datatypes            =   array(
                                            'Signature'         =>  array(
                                                                        'last_modificator', 
                                                                        'archiver'
                                                                    ),
                                            'ExtendedDateTime'  =>  array(
                                                                        'last_modification_date', 
                                                                        'archive_date'
                                                                    )
                                        );
    function Archive($structure)
    {
        $this->type                     =   'archive';
        $this->structure                =   $structure;
        $this->content_key              =   0;
        $this->last_modificator         =   new Signature('', '', '');
        $this->last_modification_date   =   new ExtendedDateTime("0000-00-00 00:00:00");
        $this->archiver                 =   new Signature('', '', '');
        $this->archive_date             =   new ExtendedDateTime("0000-00-00 00:00:00");
        
        parent::__construct("archive_".$structure);
    }
    
    function fetch( $id )
    {
        return $this->fetchTarget( $id, self::$datatypes );
    }
    
    function set( $args )
    {
        return $this->setTarget( $args, self::$datatypes );
    }
    
    function delete()
    {
        global $log;
        global $db;
        
        $contentTable = "content_".$this->structure;
        $query = "SELECT * FROM `".$contentTable."` WHERE id = '".$this->content_key."' ";
        
        $result = $db->singleRowQuery($query);
        
        if( $result )
        {
            $query  =   "DELETE FROM `".$db->escape_string($this->table)."` ";
            $query  .=  "WHERE `id` = '".$db->escape_string($this->id)."' ";
            
            if( !$db->deleteQuery($query) )
            {
                $message = "Deletion archive ".$this->table." of ID ".$this->id." failed";
                $log->error($message);
                return false;
            }
            
            return true;
        }
        elseif( Localisation::deleteFromTarget($this->table, $this->id) )
        {
            $query  =   "DELETE FROM `".$db->escape_string($this->table)."` ";
            $query  .=  "WHERE content_key = '".$db->escape_string($this->content_key)."' ";
            
            if( !$db->deleteQuery($query) )
            {
                $message = "Localisation deleted but not archives ".$this->table." of content key ".$this->content_key;
                $log->error($message);
                return false;
            }
            
            return true;
        }
    }
    
    function restore()
    {
        global $log;
        global $db;
        
        $contentTable = "content_".$this->structure;
        $query = "SELECT * FROM `".$contentTable."` WHERE id = '".$this->content_key."' ";
        
        $contentData = $db->singleRowQuery($query);
        
        $userID = $_SESSION["user"]["connexionID"];
        
        if( !$userID )
        {
            $log->error( "Cannot get current user, SESSION var seems empty : aborting restoration" );
            return false;
        }
        
        $currentDate = date("Y-m-d H:i:s");
        
        if( $contentData )
        {
            require_once 'system/classes/targets/Content.php';
            require_once 'system/classes/targets/Draft.php';
            
            // Archive content
            $content = new Content( $this->structure );
            
            $content->set($contentData);
            $content->archive();
            
            // Archive & delete drafts
            $draftTable = "draft_".$this->structure;
            $query = "SELECT * FROM `".$draftTable."` WHERE content_key = '".$this->content_key."' ";
            
            $draftsData = $db->selectQuery($query);
            
            if( $draftsData )
            {
                $draft = new Draft( $this->structure );
                foreach( $draftsData as $draftsDataItem )
                {
                    $draft->set($draftsDataItem);
                    $draft->delete();
                }
            }
            
            // TODO Structure management
            
            
            // MAJ content
            $query  =   "UPDATE `".$db->escape_string("content_".$this->structure)."` ";
            $query  .=  "SET `modificator` = '".$userID."', ";
            
            $query  .=  "`modification_date` = '".$currentDate."'";
            
            foreach( $this->attributes as $attribute )
            {   foreach( $attribute->tableColumns as $key => $tableColumn  )
                {   if( strcmp($attribute->values[$key], "__last_value__") != 0 )
                    {
                        // TODO STRUCTURE !!!!!!!!!!
                        
                        $value  =   $db->escape_string($attribute->values[$key]);
                        $query  .=  ", `".$tableColumn."` = '".$value."'";
            }   }   }
            
            $query  .=  " WHERE `id` = '".$this->content_key."' ";
            
            if( !$db->updateQuery($query) )
            {
                $message    = "Cannot update content ".$content->name;
                $message   .= " with ID: ".$content->id;
                $message   .= ", aborting retoration ";
                $log->error($message);
                
                return false;
            }
            
            foreach( $content->attributes as $attribute )
            {
                $attribute->save($content);
            }
        }
        else
        {
            // Create new content
            $query  =   "INSERT INTO `".$db->escape_string($contentTable)."` ";
            $query  .=  "( `name`, `context`, `creator`, ";
            $query  .=  "`publication_date`, `modificator`, `modification_date`";
            
            foreach( $this->attributes as $attribute )
            {   foreach( $attribute->tableColumns as $key => $tableColumn  )
                {   if( strcmp($attribute->values[$key], "__last_value__") != 0 )
                    {   $query  .=  ", `".$tableColumn."`"; }
            }   }
            
            $query  .=  " ) ";
            
            $query  .=  "VALUES ( '".$db->escape_string($this->name)."', ";
            $query  .=  "'".$db->escape_string("")."', ";
            $query  .=  "'".$userID."', "; 
            $query  .=  "'".$currentDate."', ";
            $query  .=  "'".$userID."', "; 
            $query  .=  "'".$currentDate."'";

            foreach( $this->attributes as $attribute )
            {   foreach( $attribute->tableColumns as $key => $tableColumn  )
                {   if( strcmp($attribute->values[$key], "__last_value__") != 0 )
                    {
                        $value  =   $attribute->values[$key];
                        $query  .=  ", '".$db->escape_string($value)."'"; 
            }   }   }

            $query  .=  " ) ";            
            
            $newContentId = $db->insertQuery($query);

            if( !is_numeric($newContentId) )
            {
                $message    = "Cannot insert content ".$this->name;
                $message   .= ", aborting retoration ";
                $log->error($message);

                return false;
            }
            
            require_once 'system/classes/targets/Content.php';
            
            $content = new Content( $this->structure );
            
            $content->fetch($newContentId);
            
            foreach( $this->attributes as $attribute )
            {   $attribute->create($content);   }
            
            // Update locations
            $query  =   "UPDATE `localisation` ";
            $query  .=  "SET `target_table` = '".$db->escape_string($contentTable)."', ";
            $query  .=  "`target_fk` = '".$newContentId."', ";
            //$query  .=  "`name` = '".$db->escape_string($this->name)."', ";
            $query  .=  "`datetime` = '".$currentDate."' ";
            $query  .=  "WHERE `target_table` = '".$db->escape_string($this->table)."' ";
            $query  .=  "AND `target_fk` = '".$this->id."' ";
            
            /*$query  .=  "AND ( ";

            $first = true;
            foreach( $localisation->administratedSites() as $administratedSite )
            {
                if( $first )
                {   $first = false; }
                else
                {   $query  .=  "OR ";  }

                $query  .=  "`site` = '".$db->escape_string($administratedSite)."' ";
            }
            
            $query  .=  ") ";*/
            
            if( !$db->updateQuery($query) )
            {
                $message    = "Cannot update localisations for content ".$this->name;
                $message   .= ", aborting retoration ";
                $log->error($message);

                return false;
            }
            
            // MAJ linked archives
            $query  =   "UPDATE `".$db->escape_string($this->table)."` ";
            $query  .=  "SET content_key = '".$db->escape_string($newContentId)."' ";
            $query  .=  "WHERE content_key = '".$db->escape_string($this->content_key)."' ";
            
            if( !$db->updateQuery($query) )
            {
                $message    = "Cannot update linked archives ";
                $message   .= ", aborting retoration ";
                $log->error($message);
                
                return false;
            }
        }
        
        $query  =   "DELETE FROM `".$db->escape_string($this->table)."` ";
        $query  .=  "WHERE id = '".$db->escape_string($this->id)."' ";
        
        if( !$db->deleteQuery( $query ) )
        {
            $message    = "Cannot delete this archive ";
            $message   .= ", aborting retoration ";
            $log->error($message);
            
            return false;
        }
        
        return true;
    }
    
    function publish()
    {
        return $this->restore();
    }
}

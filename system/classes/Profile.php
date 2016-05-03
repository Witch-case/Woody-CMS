<?php

require_once 'system/classes/datatypes/ExtendedDateTime.php';
require_once 'system/classes/datatypes/Police.php';
require_once 'system/classes/Cache.php';

class Profile {
    
    function Profile( $args )
    {
        global $db;
        
        if( is_numeric($args) )
        {
            $this->id = $args;
            
            $query  =   "SELECT * FROM `user_profile` ";
            $query  .=  "LEFT JOIN `police` ";
            $query  .=  "ON police.fk_profile=user_profile.id ";
            $query  .=  "WHERE `user_profile`.id = '".$db->escape_string($this->id)."' ";
            
            $data   = $db->multipleRowsQuery($query);
            
            if( $data )
            {
                $this->name     = $data[0]['name'];
                $this->datetime = new ExtendedDateTime( $data[0]['datetime'] );

                $this->polices = [];
                foreach( $data as $dataPolice )
                {   if( $dataPolice['id'] )
                    {   $this->polices[] = new Police($dataPolice); }
                }
            }
        }
        else
        {
            $this->id       = $args['id'];
            $this->name     = $args['name'];
            $this->datetime = new ExtendedDateTime( $args['datetime'] );
            $this->polices  = false;
        }
    }
    
    function delete()
    {
        global $log;
        global $db;
        
        Cache::delete('profiles', $this->name);
        
        $query  =   "DELETE FROM `user_profile` ";
        $query  .=  "WHERE id='".$db->escape_string($this->id)."' ";
        
        if( !$db->deleteQuery( $query ) )
        {
            $log->error("Can't delete profile ".$this->name." with ID ".$this->id);
            return false;
        }
        
        $query  =   "DELETE FROM `police` ";
        $query  .=  "WHERE fk_profile='".$db->escape_string($this->id)."' ";
        
        if( !$db->deleteQuery( $query ) )
        {   $log->error("Can't delete polices for profile ".$this->name." with ID ".$this->id); }
        
        return true;
    }
    
    function addPolice( $policeModule, 
                        $policeAction, 
                        $localisationId, 
                        $policeInherit, 
                        $limitation, 
                        $policeLimitationProfile    )
    {
        global $db;
        
        Cache::delete('profiles', $this->name);
        
        $targetLocalisation = new Localisation($localisationId);
        
        $positionArray = $targetLocalisation->position;
        
        if( count($positionArray) == 0 
            ||  $policeInherit  
        ){
            $positionArray[] = '*';
        }
        
        $positionString = implode(',', $positionArray);
        
        $data = [
            'fk_profile'        =>  $this->id,
            'module'            =>  $policeModule, 
            'action'            =>  $policeAction, 
            'position'          =>  $positionString, 
            'rigths_limitation' =>  $limitation, 
            'group_fk_profile'  =>  $policeLimitationProfile
        ];
        
        $query  =   "INSERT INTO police ";
        $query  .=  "(fk_profile, module, action, position, ";
        $query  .=  "rigths_limitation, group_fk_profile) ";
        $query  .=  "VALUES ('".$this->id."', ";
        $query  .=  "'".$db->escape_string($data['module'])."', ";
        $query  .=  "'".$db->escape_string($data['action'])."', ";
        $query  .=  "'".$db->escape_string($data['position'])."', ";
        $query  .=  "'".$db->escape_string($data['rigths_limitation'])."', ";
        $query  .=  "'".$db->escape_string($data['group_fk_profile'])."') ";
        
        $policeID = $db->insertQuery($query);
        
        $data['id'] = $policeID;
        
        $this->polices[] = new Police($data);
        
        return $policeID;
    }
    
    static function create( $name )
    {
        global $db;
        
        $query =    "INSERT INTO user_profile (name, datetime) ";
        $query .=   "VALUES ('".$db->escape_string($name)."', '".date("Y-m-d H:i:s")."') ";
        
        $profileID = $db->insertQuery($query);
        
        if( $profileID )
        {   return new self($profileID);    }
        else
        {   return false;   }
    }
    
    static function listProfiles( $orders=[], $offset=0, $limit=false )
    {
        global $db;
        
        if( !isset($orders['name']) )
        {   $orders['name'] = 'asc';    }
        
        $query  =   "SELECT * FROM `user_profile` ";
        $query  .=  "ORDER BY ";
        
        $first  =   true;
        foreach( $orders as $field => $order )
        {
            if( $first )
            {   $first  =   false;   }
            else
            {   $query  .=  ", ";   }
            
            $query  .=  "`".$field."` ".$order; 
        }
        
        if( $limit )
        {   $query  .=  " LIMIT ".$offset.", ".$limit;  }
        
        $data   = $db->multipleRowsQuery($query);
        
        $profiles = [];
        foreach( $data as $profileArgs )
        {   $profiles[] = new self( $profileArgs ); }
        
        return $profiles;
    }
    
    static function countProfiles()
    {
        global $db;
        
        $query  =   "SELECT count(*) FROM `user_profile` ";
        $data   =   $db->singleRowQuery($query);
        
        return $data['count(*)'];
    }
}

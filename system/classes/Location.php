<?php

require_once 'system/classes/Localisation.php';

class Location {
    
    public $localisations   = [];
    public $id              = false;
    
    function Location($data)
    {
        if( !is_array($data) )
        {   return false;   }
        
        foreach( $data as $localisationData )
        {
            if( !$this->id )
            {   $this->id = $localisationData['location_id'];   }
            
            $site   = $localisationData['site'];
            $id     = $localisationData['id'];
            $this->localisations[ $site ] = new Localisation($id, $localisationData);
        }
    }
    
    static function getFromLocalisationID( $id )
    {
        global $db;
        
        $query  =   "SELECT results.* ";
        $query  .=  "FROM localisation AS base ";
        $query  .=  "LEFT JOIN localisation AS results ";
        $query  .=  "ON base.location_id=results.location_id ";
        $query  .=  "WHERE base.id='".$id."' ";
        
        return new self($db->multipleRowsQuery($query));
    }
    
    static function getFromLocationID( $id )
    {
        global $db;
        
        $query  =   "SELECT * ";
        $query  .=  "FROM localisation ";
        $query  .=  "WHERE location_id='".$id."' ";
        
        return new self($db->multipleRowsQuery($query));
    }
    
    static function create( $parentsIdArray, 
                            $name, 
                            $module='view', 
                            $target_table='', 
                            $target_fk=null, 
                            $description='', 
                            $customUrl=false, 
                            $newSite=false  )
    {
        global $localisation;
        global $configuration;
        
        $key        = implode('-', $parentsIdArray);
        $uniqueId   = md5( uniqid( $key, true ) );
        
        if( !$customUrl )
        {   $customUrl = $name; }
        
        $customUrl = Localisation::cleanupString($customUrl);
        
        if( !$customUrl && !$newSite )
        {   return false;   }
        
        if( $newSite )
        {
            $rootId         = $parentsIdArray[0];
            $newSitesArray  = [];
            $parentsIdArray = [];
            $urlsArray      = [];
            
            foreach( $configuration->sections as $section )
            {
                $adminForSites = $configuration->get( $section, 'adminForSites');
                
                if( is_array($adminForSites)
                    &&  (   in_array('*', $adminForSites)
                            || in_array($newSite, $parentsIdArray)
                        )
                ){
                    $parentsIdArray[]   = $rootId;
                    $newSitesArray[]    = $section;
                    $urlsArray[]        = Localisation::cleanupString($name);
                }
            }
            
            $parentsIdArray[]   = $rootId;
            $newSitesArray[]    = $newSite;
            $urlsArray[]        = $customUrl;
        }
        
        $return = false;
        foreach( $parentsIdArray as $key => $parentId )
        {
            $url = $customUrl;
            if( $newSite )
            {
                $newSite = $newSitesArray[$key];
                $url     = $urlsArray[$key];
            }
            $parentLocalisation = new Localisation($parentId);
            
            $newLocalisationID =    $parentLocalisation->addChild(
                                        $uniqueId, 
                                        $name, 
                                        $module, 
                                        $target_table, 
                                        $target_fk, 
                                        $description, 
                                        $url, 
                                        $newSite
                                    );
            
            if( strcmp($localisation->site, $parentLocalisation->site) == 0 )
            {   $return = $newLocalisationID;   }
        }
        
        return $return;
    }
    
    function addChild(  $name, 
                        $module='view', 
                        $target_table='', 
                        $target_fk=null, 
                        $description='', 
                        $customUrl=false )
    {
        $parentsIdArray = [];
        foreach( $this->localisations as $site => $linkedLocalisation )
        {   $parentsIdArray[] = $linkedLocalisation->id; }
        
        return  Location::create(   $parentsIdArray, 
                                    $name, 
                                    $module, 
                                    $target_table, 
                                    $target_fk, 
                                    $description, 
                                    $customUrl
                );
    }
    
    function delete()
    {
        $deletedLocalisationIds = [];
        
        foreach( $this->localisations as $linkedLocalisation )
        {
            if( !$linkedLocalisation->delete() )
            {   return false;  }
            
            $deletedLocalisationIds[] = $linkedLocalisation->id;
        }
        
        return $deletedLocalisationIds;
    }
    
    static function getAll()
    {
        global $db;
        global $localisation;
        
        $query =    "SELECT loc.*, link.*, link.url ";
        $query .=   "FROM localisation AS loc, ";
        $query .=   "localisation AS link ";
        $query .=   "WHERE loc.level_1 IS NOT NULL ";
        $query .=   "AND link.location_id=loc.location_id ";
        $query .=   "AND loc.site='".$localisation->site."' ";
        $query .=   "AND loc.target_table NOT LIKE 'draft_%' GROUP BY loc.location_id, ";
        
        $queryOrderBy = "ORDER BY ";
        
        for( $i=1; $i<=$localisation->maxDepth; $i++ )
        {
            if( $i > 1 )
            {
                $query .= ", ";
                $queryOrderBy .= ", ";
            }
            
            $query .= "loc.level_".$i." ";
            $queryOrderBy .= "loc.level_".$i." ASC ";
        }
        
        return $db->multipleRowsQuery($query.$queryOrderBy);
    }
    
    static function changePriority( $id, $priority )
    {
        global $db;
        
        if( !is_numeric( (int) $id) || !is_numeric( (int) $priority) )
        {   return false;   }
        
        $query  =   "UPDATE localisation ";
        $query  .=  "SET priority = '".$db->escape_string($priority)."' ";
        $query  .=  "WHERE location_id = '".$db->escape_string($id)."' ";
        
        return $db->updateQuery($query);
    }
}

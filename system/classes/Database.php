<?php

class Database
{
    function Database( $parameters )
    {
        global $log;
        
        if( $parameters['port'] )
        {
            $this->mysqli   =   new mysqli(   
                                        $parameters['server'], 
                                        $parameters['user'], 
                                        $parameters['password'], 
                                        $parameters['database'],
                                        $parameters['port']
                                    );
        }
        else
        {
            $this->mysqli   =   new mysqli(   
                                        $parameters['server'], 
                                        $parameters['user'], 
                                        $parameters['password'], 
                                        $parameters['database']
                                    );
        }
        
        if( $this->mysqli->connect_error ) 
        {
            $log->error(    'Database connexion failed (' . 
                            $this->mysqli->connect_errno . ') '. 
                            $this->mysqli->connect_error, 
                            true
            );
        }
    }
    
    function singleRowQuery( $query )
    {
        $result = $this->mysqli->query( $query );
        
        if( !$result )
        {   return false;   }
        
        if( $result->num_rows == 0 )
        {   $return  = 0;   }
        
        elseif( $result->num_rows > 1 )
        {   $return = $result->num_rows;    }
        
        else
        {   $return = $result->fetch_assoc();   }
        
        $result->free();
        
        return $return;
    }
    
    function multipleRowsQuery( $query )
    {
        $result = $this->mysqli->query( $query );
        
        if( !$result )
        {   return false;   }
        
        if( $result->num_rows == 0 )
        {   return array(); }
        
        $rows = array();
        while( $row = $result->fetch_assoc() )
        {   $rows[] = $row; }
        
        $result->free();
        
        return $rows;
    }
    
    function countQuery( $query )
    {
        $result = $this->mysqli->query( $query );
        
        if( !$result )
        {
            $result->free();
            return false;
        }
        
        $rows = $result->fetch_assoc();
        
        foreach( $rows as $value )
        {   $rowCount = $value; }
        
        $result->free();
        
        return $rowCount;
    }
    
    function selectQuery( $query )
    {
        return $this->multipleRowsQuery($query);
    }
    
    function insertQuery( $query )
    {
        $result = $this->mysqli->query( $query );
        
        if( !$result )
        {   return false;   }
        
        return $this->mysqli->insert_id;
    }
    
    function updateQuery( $query )
    {
        return $this->mysqli->query( $query );
    }
    
    
    function deleteQuery( $query )
    {
        return $this->mysqli->query( $query );
    }
    
    function alterQuery( $query )
    {
        return $this->mysqli->query( $query );
    }
    
    function createQuery( $query )
    {
        return $this->mysqli->query( $query );
    }
    
    function escape_string( $string )
    {
        return $this->mysqli->real_escape_string( $string );
    }
    
    function begin()
    {
        return $this->mysqli->query( "BEGIN" );
    }
    
    function savePoint( $savePointName )
    {
        return $this->mysqli->query( "SAVEPOINT ".$this->escape_string($savePointName) );
    }
    
    function rollback( $savePointName = false )
    {
        if( $savePointName )
        {
            $result = $this->mysqli->query( "ROLLBACK TO ".$this->escape_string($savePointName) );
            
            if($result)
            {   return $result; }
        }
        
        return $this->mysqli->query( "ROLLBACK" );
    }
    
    function commit()
    {
        return $this->mysqli->query( "COMMIT" );
    }
    
    function errno()
    {
        return $this->mysqli->errno;
    }
}
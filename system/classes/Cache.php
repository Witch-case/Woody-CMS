<?php

class Cache {
    
    const CACHEDIR      = 'cache';
    
    static function get( $folder, $filebasename )
    {
        global $configuration;
        global $log;
        
        if( strstr($folder, "/") !== false )
        {   $cacheFolder = dirname($folder);    }
        else
        {   $cacheFolder = $folder; }
        
        if( !in_array($cacheFolder, $configuration->get( 'system', 'cacheFolders' )) )
        {
            $log->error("Trying to access unmanaged cache folder : ".$folder);
            return false;
        }
        
        if( !is_dir(self::CACHEDIR.'/'.$folder) 
            && !mkdir( self::CACHEDIR.'/'.$folder,  octdec($configuration->get('system', 'createFolderRights')), true )
        )
        {
            $log->error("Can't create cache folder : ".$folder);
            return false;
        }
        
        $filename = self::CACHEDIR.'/'.$folder.'/'.$filebasename.".php";
        
        if( file_exists($filename) )
        {
            $limit = (int) $configuration->get('system', $cacheFolder.'CacheDuration');
            
            if( (time() - filemtime($filename)) > $limit )
            {   unlink($filename);  }
            else
            {   return $filename;   }
        }
        
        $method = 'create'.ucfirst($folder).'File';
        
        if( in_array( $method, get_class_methods(get_called_class()) ) )
        {   return call_user_func('self::'.$method, $filebasename );    }
        else
        {   return false;   }
    }
    
    static function delete( $folder, $filebasename )
    {
        global $configuration;
        global $log;
        
        if( strstr($folder, "/") !== false )
        {   $cacheFolder = dirname($folder);    }
        else
        {   $cacheFolder = $folder; }
        
        if( !in_array($cacheFolder, $configuration->get( 'system', 'cacheFolders' )) )
        {
            $log->error("Trying to delete unmanaged cache folder : ".$folder);
            return false;
        }
        
        $filename = self::CACHEDIR.'/'.$folder.'/'.$filebasename.".php";
        
        if( file_exists($filename) )
        {   unlink($filename);  }
        
        return true;
    }
    
    static function create( $folder, $filebasename, $value, $varname=false )
    {
        global $configuration;
        global $log;
        
        if( strstr($folder, "/") !== false )
        {   $cacheFolder = dirname($folder);    }
        else
        {   $cacheFolder = $folder; }
        
        if( !in_array($cacheFolder, $configuration->get( 'system', 'cacheFolders' )) )
        {
            $log->error("Trying to delete unmanaged cache folder : ".$folder);
            return false;
        }
        
        if( $varname == false )
        {   $varname = $filebasename;   }
        
        // Writing cache polices files (based on profile)
        $filename = self::CACHEDIR."/".$folder."/".$filebasename.".php";
        
        $cacheFileFP = fopen( $filename, 'a');
        fwrite($cacheFileFP, "<?php\n");
        fwrite($cacheFileFP, "$".$varname." = ");
        
        ob_start();
        var_export($value);
        $buffer = ob_get_contents();
        ob_end_clean();
        
        fwrite($cacheFileFP, $buffer);
        fwrite($cacheFileFP, ";\n?>\n");
        
        return $filename;
    }
    
    static function createProfilesFile( $filebasename )
    {
        global $db;
        global $log;
        
        // Get policies and create cache profile file
        $query  =   "SELECT * FROM `user_profile` ";
        $query  .=  "LEFT JOIN `police` ";
        $query  .=  "ON `police`.fk_profile=user_profile.id ";
        $query  .=  "WHERE fk_profile IS NOT NULL ";
        $query  .=  "AND `user_profile`.name LIKE '".$filebasename."' ";

        $dataPolices = $db->selectQuery($query);
        if( $dataPolices === false )
        {
            $log->error('Login failed : access police '.$filebasename.' impossible');
            return false;
        }
        else
        {
            // Setting policies
            $profileData = [];
            foreach( $dataPolices as $dataPolices_item )
            {
                $police = [];
                
                $police['module']           = $dataPolices_item['module'];
                $police['action']           = $dataPolices_item['action'];
                $police['rigths_limitation']= $dataPolices_item['rigths_limitation'];
                $police['group_fk_profile'] = $dataPolices_item['group_fk_profile'];

                $buffer = explode(',', $dataPolices_item['position']);

                $police['position'] = [];
                foreach( $buffer as $i => $value )
                {   $police['position'][$i+1] = $value; }

                if( $dataPolices_item['inherit_subtree'] )
                {   $police['position'][] = '*';    }

                $profileData[]    = $police;
            }
            
            return self::create("profiles", $filebasename, $profileData, "polices" );
        }
    }
    
}

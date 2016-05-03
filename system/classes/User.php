<?php

require_once 'system/classes/Cache.php';

class User 
{
    const CACHEPROFILEDIR = 'cache/profiles';
    
    var $name;
    var $profiles;
    var $policies;
    var $connexion = false;
    var $loginMessages;
    
    function User()
    {
        global $configuration;
        global $db;
        global $log;
        
        session_start();
        $this->loginMessages = [];
        
        // If previous page is login page
        if( filter_has_var(INPUT_POST, 'login') 
            && strcmp(filter_input(INPUT_POST, 'login'), 'login') == 0 
        ){
            $login = $db->escape_string( filter_input(INPUT_POST, 'username') );
            
            $query  =   "SELECT * FROM user_connexion ";
            $query  .=  "WHERE ( email='".$login."' OR login='".$login."' ) ";
            $query  .=  "AND target_table LIKE 'content_%' ";
            
            $connexion = $db->singleRowQuery($query);
            if( is_numeric($connexion) && $connexion > 0 )
            {
                $this->loginMessages[] = "Problem whith this username: multiple match ";
                $this->loginMessages[] = "Please contact administrator";
                $log->error('Login failed : multiple username match');
            }
            elseif( $connexion == 0 )
            {
                $this->loginMessages[] = "Unknon username";
                $log->debug('Login failed : unknown username');
            }
            else
            {
                $hash = crypt( filter_input(INPUT_POST, 'password'), $connexion['pass_hash'] );
                
                if( $hash === $connexion['pass_hash'] )
                {
                    $profiles = [];
                    foreach( explode(",", $connexion['profiles']) as $profile )
                    {   $profiles[] = trim($profile);   }
                    
                    $_SESSION['user']   =   [
                                                'connexionID'   => $connexion['id'],
                                                'signature'     => $connexion['name'],
                                                'profiles'      => $profiles,
                                                'connexionData' => $connexion
                                            ];
                }
                else
                {
                    $this->loginMessages[] = "Wrong password, please try again";
                    $log->debug('Login failed : wrong password for login: '.$login);
                }
            }
        }
        
        if( isset($_SESSION['user']) )
        {
            $this->id       = $_SESSION['user']['connexionID'];
            $this->name     = $_SESSION['user']['signature'];
            $this->profiles = $_SESSION['user']['profiles'];
        }
        else // No user log in, get default user (="public user") from configuration
        {
            $this->name     = $configuration->get('system', 'publicUser');
            $this->profiles = [ $configuration->get('system', 'publicUserProfile') ];
        }
        
        
        $this->policies = [];
        foreach( $this->profiles as $profile )
        {
            $cacheFile = Cache::get('profiles', $profile);
            
            if( $cacheFile )
            {
                include $cacheFile;
                
                foreach( $polices as $police )
                {   $this->policies[] = $police;    }
            }
        }
        
        if( count($this->policies) == 0 )
        {
            $this->loginMessages[] = "Problem whith this system: unable to log user";
            $this->loginMessages[] = "Please contact administrator";
            $log->error('Login failed : accessing policies impossible', true);
        }
    }
    
    function getLocalisationUri()
    {
        global $db;
        global $localisation;
        
        if( isset($this->uri) )
        {   return $this->uri;  }
        
        $connexion = $_SESSION['user']['connexionData'];
        
        $column = "@_".$connexion['target_attribute'];

        if( $connexion['target_attribute_var'] )
        {   $column .= "#".$connexion['target_attribute_var'];  }

        $column .= "__".$connexion['attribute_name'];

        $query  =   "SELECT localisation.* FROM ".$connexion['target_table']." ";
        $query  .=  "LEFT JOIN localisation ";
        $query  .=  "ON ( localisation.target_table = '".$connexion['target_table']."' ";
        $query  .=  "AND localisation.target_fk = ".$connexion['target_table'].".id ) ";
        $query  .=  "WHERE `".$column."`=".$connexion['id']." ";
        $query  .=  "AND localisation.site='".$localisation->site."' ";

        $userData = $db->singleRowQuery($query);
        
        if( is_numeric($userData) && $userData > 0 )
        {
            $this->loginMessages[] = "Problem whith this connexion: multiple content match";
            $this->loginMessages[] = "Please contact administrator";
        }
        elseif( $userData == 0 )
        {
            $this->loginMessages[] = "Unknon content for connexion: no profile can be loaded";
            $this->loginMessages[] = "Please contact administrator";
        }
        else
        {
            $this->uri = 'http://'.$localisation->siteAccess.$userData['url'];
            return $this->uri;
        }
        
        return false;
    }
}

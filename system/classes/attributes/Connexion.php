<?php

require_once 'system/classes/attributes/Attribute.php';

class Connexion extends Attribute {
    
    function Connexion( $attributeName, $params=[] )
    {
        $this->type     =   "connexion";
        $this->name     =   $attributeName;
        
        $this->tableColumns =   [
            'user_connexionId'  => "@_".$this->type."__".$attributeName,
        ];
        
        $this->values       =   [
            'user_connexionId'  => null,
            'name'              => "",
            'login'             => "",
            'email'             => "",
            'pass_hash'         => "",
            'pass_display'      => "",
            'profiles'          => [],

        ];
        
        $this->dbFields     =   [
            "`@_connexion__".$attributeName."` int(11) DEFAULT NULL", 
        ];
        
        $this->joinTables[] =   [
            'table' =>  "user_connexion",
            'alias' =>  $this->type."_".$this->name
            //'user_connexion AS `'.$this->type.'_'.$this->name.'`',
        ];
        
        /*$this->joinFields   =   [
            'name'      =>  '`'.$this->type.'_'.$this->name.'`.name '.
                            'AS `@_'.$this->type.'#name__'.$this->name.'`',
            'login'     =>  '`'.$this->type.'_'.$this->name.'`.email '.
                            'AS `@_'.$this->type.'#email__'.$this->name.'`',
            'email'     =>  '`'.$this->type.'_'.$this->name.'`.login '.
                            'AS `@_'.$this->type.'#login__'.$this->name.'`',
            'pass_hash' =>  '`'.$this->type.'_'.$this->name.'`.pass_hash '.
                            'AS `@_'.$this->type.'#pass_hash__'.$this->name.'`',
            'profiles'  =>  '`'.$this->type.'_'.$this->name.'`.profiles '.
                            'AS `@_'.$this->type.'#profiles__'.$this->name.'`',
        ];*/
        
        $this->joinFields   =   [
            'name'      =>  [
                                'field' =>  "`".$this->type."_".$this->name."`.name",
                                'alias' =>  "@_".$this->type."#name__".$this->name,
                            ],
            'email'     =>  [
                                'field' =>  "`".$this->type."_".$this->name."`.email",
                                'alias' =>  "@_".$this->type."#email__".$this->name,
                            ],
            'login'     =>  [
                                'field' =>  "`".$this->type."_".$this->name."`.login",
                                'alias' =>  "@_".$this->type."#login__".$this->name,
                            ],
            'pass_hash' =>  [
                                'field' =>  "`".$this->type."_".$this->name."`.pass_hash",
                                'alias' =>  "@_".$this->type."#pass_hash__".$this->name,
                            ],
            'profiles'  =>  [
                                'field' =>  "`".$this->type."_".$this->name."`.profiles",
                                'alias' =>  "@_".$this->type."#profiles__".$this->name,
                            ],
        ];
        
        $this->joinConditions = [
            '`'.$this->type.'_'.$this->name.'`.id = `@_'.$this->type."__".$attributeName."`",
        ];
    }
    
    function set( $args )
    {
        foreach( $args as $argColumn => $value )
        {   switch( $argColumn )
            {
                case $this->tableColumns['user_connexionId']:
                    
                    $this->values['user_connexionId'] = $value;
                    break;
                
                case '@_'.$this->type.'#name__'.$this->name:
                    
                    $this->values['name'] = $value;
                    break;
                
                case '@_'.$this->type.'#login__'.$this->name:
                    $this->values['login'] = $value;
                    break;
                
                case '@_'.$this->type.'#email__'.$this->name:
                    if( !filter_var($value, FILTER_VALIDATE_EMAIL) === false )
                    {   $this->values['email'] = $value;    }
                    break;
                
                case '@_'.$this->type.'#profiles__'.$this->name:
                    
                    if( is_array($value) )
                    {   $profilesArray = $value;    }
                    else
                    {
                        $profilesArray = [];
                        foreach( explode(",", $value) as $profileElement )
                        {   $profilesArray[] = trim($profileElement);   }
                    }
                    
                    $this->values['profiles'] = $profilesArray;
                    break;
                
                case '@_'.$this->type.'#password__'.$this->name:
                    $confirm = $args['@_'.$this->type.'#password_confirm__'.$this->name];
                    
                    if( strcmp($value, $confirm) == 0 
                        && strcmp($value, '__last_value__') != 0 
                    ){
                        $hash = $this->generate_hash($value);
                        $this->values['pass_hash']      = $hash;
                        $this->values['pass_display']   = '__last_value__';
                    }
                    
                    break;
                
                case '@_'.$this->type.'#pass_hash__'.$this->name:
                    $this->values['pass_hash']      = $value;
                    $this->values['pass_display']   = '__last_value__';
                    break;
        }   }
        
        return true;
    }
    
    function create( $target )
    {
        global $db;
        
        $profiles = implode(", ", $this->values['profiles']);
        
        $query  =   "INSERT INTO `user_connexion` ";
        $query .=   "( `name`, `email`, `login`, `pass_hash`, `profiles`, ";
        $query .=   "`target_table`, `target_attribute`, `target_attribute_var`, ";
        $query .=   "`attribute_name`, `datetime`) ";
        
        //$query .=   "VALUES ('', '', '', '', '', ";
        
        $query .=   "VALUES ('".$this->values['name']."', ";
        $query .=   "'".$this->values['email']."', ";
        $query .=   "'".$this->values['login']."', ";
        $query .=   "'".$this->values['pass_hash']."', ";
        $query .=   "'".$profiles."', ";
        
        $query .=   "'".$target->table."', '".$this->type."', '', ";
        $query .=   "'".$this->name."', '".date("Y-m-d H:i:s")."') ";
        
        $id = $db->insertQuery($query);
        
        if( !$id )
        {   return false;   }
        
        if( !$target->edit( [$this->tableColumns['user_connexionId'] => $id] ) )
        {   return false;   }
        
        return true;
    }
    
    function save( $target ) 
    {
        global $db;
        
        $profiles = implode(", ", $this->values['profiles']);
        
        $query  =   "UPDATE `user_connexion` ";
        $query  .=  "SET `name` = '".$db->escape_string($this->values['name'])."', ";
        $query  .=  "`email` = '".$db->escape_string($this->values['email'])."', ";
        $query  .=  "`login` = '".$db->escape_string($this->values['login'])."', ";
        $query  .=  "`pass_hash` = '".$db->escape_string($this->values['pass_hash'])."', ";
        $query  .=  "`profiles` = '".$db->escape_string($profiles)."', ";
        $query  .=  "`target_table` = '".$db->escape_string($target->table)."', ";
        $query  .=  "`target_attribute` = '".$db->escape_string($this->type)."', ";
        $query  .=  "`target_attribute_var` = '', ";
        $query  .=  "`attribute_name` = '".$db->escape_string($this->name)."', ";
        $query  .=  "`datetime` = '".date("Y-m-d H:i:s")."' ";
        
        $query  .=  " WHERE `id` = '".$db->escape_string($this->values['user_connexionId'])."' ";
        
        return $db->updateQuery($query);
    }
    
    function generate_hash($password, $cost=11)
    {
        /* To generate the salt, first generate enough random bytes. Because
         * base64 returns one character for each 6 bits, the we should generate
         * at least 22*6/8=16.5 bytes, so we generate 17. Then we get the first
         * 22 base64 characters
         */
        $salt = substr( base64_encode(openssl_random_pseudo_bytes( 17 )), 0, 22 );
        /* As blowfish takes a salt with the alphabet ./A-Za-z0-9 we have to
         * replace any '+' in the base64 string with '.'. We don't have to do
         * anything about the '=', as this only occurs when the b64 string is
         * padded, which is always after the first 22 characters.
         */
        $salt = str_replace( "+",".", $salt );
        /* Next, create a string that will be passed to crypt, containing all
         * of the settings, separated by dollar signs
         */
        $param = '$'.implode(   '$',
                                [
                                    "2y", //select the most secure version of blowfish (>=PHP 5.3.7)
                                    str_pad($cost, 2, "0", STR_PAD_LEFT), //add the cost in two digits
                                    $salt //add the salt
                                ]
                    );
       
        //now do the actual hashing
        return crypt( $password, $param );
    }
    
}
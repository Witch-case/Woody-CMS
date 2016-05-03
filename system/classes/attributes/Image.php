<?php

require_once 'system/classes/attributes/Attribute.php';

class Image extends Attribute {
    
    function Image( $attributeName, $params=[] )
    {
        $this->type         = "image";
        $this->name         = $attributeName;
        $this->directory    = "files/".$this->type."/".$attributeName;
        
        $this->dbFields     =   [
            "file"  =>  "`@_image#file__".$attributeName."` varchar(511) DEFAULT NULL",
            "title" =>  "`@_image#title__".$attributeName."` varchar(511) DEFAULT NULL",
            "link"  =>  "`@_image#link__".$attributeName."` varchar(511) DEFAULT NULL",
        ];
        
        $this->tableColumns =   [
            "file"  =>  "@_image#file__".$attributeName,
            "title" =>  "@_image#title__".$attributeName,
            "link"  =>  "@_image#link__".$attributeName,
        ];
        
        $this->values       =   [
            "file"  =>  "",
            "title" =>  "",
            "link"  =>  "",
        ];
        
        $this->parameters   =   [];
    }
    
    function set( $args )
    {
        if( !empty($_FILES['@_'.$this->type.'#fileupload__'.$this->name]["tmp_name"]) )
        {
            $tmpFileInfos = $_FILES['@_'.$this->type.'#fileupload__'.$this->name];
            
            $check = getimagesize($tmpFileInfos["tmp_name"]);

            if( $check !== false )
            {
                $directoryPath = "";
                foreach( explode('/', $this->directory) as $folder ) 
                {
                    $directoryPath .= $folder;
                    if( !is_dir($directoryPath) ) 
                    {   mkdir( $directoryPath, 0705 );  }

                    $directoryPath .= "/";
                }
                
                if( copy($tmpFileInfos["tmp_name"], $directoryPath.$tmpFileInfos["name"]) )
                {   $this->values['file'] = $tmpFileInfos["name"];  }
            }
        }
        
        if( isset($args['storeButton']) 
            && strcmp($args['storeButton'], '@_'.$this->type.'#filedelete__'.$this->name) == 0 )
        {   $this->values['file'] = ""; }
        
        parent::set($args);
        
        return true;
    }
    
    function content()
    {
        $filepath   = $this->getImageFile($this->values['file']);
        
        if( $filepath )
        {
            $content         = [];
            $content['file'] = $filepath;
            
            if( !empty($this->values['title']) )
            {   $content['title'] = $this->values['title'];  }
            else
            {
                $content['title'] = substr( $this->values['file'], 
                                            0, 
                                            strrpos($this->values['file'], ".") - strlen($this->values['file']) 
                                    );
            }
            
            if( isset($this->values['link']) )
            {   $content['link'] = $this->values['link'];   }
            else
            {   $content['link'] = false;    }
            return $content;
        }
        else
        {   return false;   }
    }
    
    function getImageFile()
    {
        global $module;
        
        $filepath = $this->directory.'/'.$this->values['file'];
        
        if( !is_file($filepath) )
        {   return false;   }
        
        return $module->getHost()."/".$filepath;
    }
    
    
}

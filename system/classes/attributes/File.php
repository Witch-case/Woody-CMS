<?php

require_once 'system/classes/attributes/Attribute.php';

class File extends Attribute {
    
    function File( $attributeName, $params=[] )
    {
        $this->type         = "file";
        $this->name         = $attributeName;
        $this->directory    = "files/".$this->type."/".$attributeName;
        
        $this->dbFields     =   [
            "file"  =>  "`@_".$this->type."#file__".$this->name."` varchar(511) DEFAULT NULL",
            "text"  =>  "`@_".$this->type."#text__".$this->name."` varchar(511) DEFAULT NULL",
        ];
        
        $this->tableColumns =   [
                                    "file"  =>  "@_".$this->type."#file__".$this->name,
                                    "text"  =>  "@_".$this->type."#text__".$this->name,
                                ];
        $this->values       =   [
                                    "file"  =>  "",
                                    "text"  =>  "",
                                ];
        $this->parameters   =   [];
    }
    
    function set( $args )
    {
        if( !empty($_FILES['@_'.$this->type.'#fileupload__'.$this->name]["tmp_name"]) )
        {
            $tmpFileInfos = $_FILES['@_'.$this->type.'#fileupload__'.$this->name];
            
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
        
        if( isset($args['storeButton']) 
            && strcmp($args['storeButton'], '@_'.$this->type.'#filedelete__'.$this->name) == 0 )
        {   $this->values['file'] = ""; }
        
        parent::set($args);
        
        return true;
    }
    
    function content()
    {
        $filepath   = $this->getFile($this->values['file']);
        
        if( $filepath )
        {
            $content         = [];
            $content['file'] = $filepath;
            
            if( !empty($this->values['text']) )
            {   $content['text'] = $this->values['text'];  }
            else
            {
                $content['text'] =  substr( $this->values['file'], 
                                            0, 
                                            strrpos($this->values['file'], ".") - strlen($this->values['file']) 
                                    );

            }
            
            return $content;
        }
        else
        {   return false;   }
    }
    
    function getFile()
    {
        global $module;
        
        $filepath = $this->directory.'/'.$this->values['file'];
        
        if( !is_file($filepath) )
        {   return false;   }
        
        return $module->getHost()."/".$filepath;
    }
}

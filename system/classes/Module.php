<?php

class Module {
    
    var $name;
    var $site;
    var $execFile;
    var $extensions;
    var $contextFile;
    var $host;
    
    function Module( $localisation )
    {
        global $log;
        
        $this->name         = $localisation->module;
        $this->site         = $localisation->site;
        $this->extensions   = false;
        $this->execFile     = $this->getControllerFile("modules/".$this->name.".php");
        
        if( !$this->execFile )
        {   $log->error("Can't access module file: modules/".$this->name.".php", true); }
        
        else
        {   $log->debug("Module file: ".$this->execFile);   }
    }
    
    function getFilePath( $filename )
    {
        global $configuration;
        
        // Looking in this site
        $filePath = "sites/".$this->site."/".$filename;
        
        if( is_file($filePath) )
        {   return $filePath;   }
        
        // Looking in herited sites
        $siteHeritages = $configuration->get( $this->site, "siteHeritages" );
        if( is_array($siteHeritages) )
        {   foreach( $siteHeritages as $heritedSite )
            {
                $filePath = "sites/".$heritedSite."/".$filename;
                
                if( file_exists($filePath) )
                {   return $filePath;   }
        }   }
        
        // Looking in extension files
        $extensions = $configuration->getExtensions($this->site);
        if( is_array($extensions) )
        {   foreach( $extensions as $extension )
            {
                $filePath = "extensions/".$extension."/".$filename;
                
                if( file_exists($filePath) )
                {   return $filePath;   }
        }   }
        
        // Looking in default site
        $filePath = "sites/default/".$filename;
        
        if( is_file($filePath) )
        {   return $filePath;   }
        
        // Looking in system files
        $filePath = "system/".$filename;
        
        if( is_file($filePath) )
        {   return $filePath;   }
        
        return false;
    }
    
    function getDesignFile( $filename=false, $mandatory=true )
    {
        global $log;
        
        if( !$filename )
        {   $filename = "modules/".$this->name.".php"; }
        
        $filename           = "design/".$filename;
        $visualisationFile  = $this->getFilePath($filename);
        
        if( !$visualisationFile )
        {   $log->error("Can't get visualisation file: ".$filename, $mandatory);  }
        
        return $visualisationFile;
    }
    
    function getControllerFile( $filename=false )
    {
        global $log;
        
        if( !$filename )
        {   $filename = $this->name.".php";  }
        
        $filename           = "controller/".$filename;
        $controllerFile  = $this->getFilePath($filename);
        
        if( !$controllerFile )
        {   $log->error("Can't get controller file: ".$filename, true);  }
        
        return $controllerFile;
    }
    
    function getImageFile( $filename )
    {
        $fullPath = $this->getFilePath("design/images/".$filename);
        
        if( !$fullPath )
        {   return false;   }
        
        return $this->getHost()."/".$fullPath;
    }
    
    function getClassFile( $filename )
    {
        global $configuration;
        
        $filename = "classes/".$filename;
        
        $extensions = $configuration->getExtensions($this->site);
        
        if(is_array($extensions) )
        {   foreach( $extensions as $extension )
            {
                $filePath = "extensions/".$extension."/".$filename;
                
                if( file_exists($filePath) )
                {   return $filePath;   }
        }   }
        
        $filePath = "system/".$filename;
        
        if( is_file($filePath) )
        {   return $filePath;   }
        
        return false;
    }
    
    function requireAttributeClass( $attributeClass )
    {
        return $this->getClassFile('attributes/'.$attributeClass.'.php');
    }
    
    function addCssFile( $cssFile )
    {
        if( !isset($this->css) )
        {   $this->css = [];    }
        
        $cssFilePath = $this->getDesignFile("css/".$cssFile, false);
        
        if( $cssFilePath 
            && !in_array($this->getHost()."/".$cssFilePath, $this->css)
        ){
            $this->css[] = $this->getHost()."/".$cssFilePath;
        }
        else
        {   return false;   }
        
        return true;
    }
    
    function getCssFiles()
    {
        if( !isset($this->css) )
        {   $this->css = [];    }
        
        return $this->css;
    }

    function addJsFile( $jsFile )
    {
        if( !isset($this->js) )
        {   $this->js = [];    }
        
        $jsFilePath = $this->getDesignFile("js/".$jsFile, false);
        
        if( $jsFilePath 
            && !in_array($this->getHost()."/".$jsFilePath, $this->js) 
        ){
            $this->js[] = $this->getHost()."/".$jsFilePath;
        }
        else
        {   return false;   }
        
        return true;
    }
    
    function getJsFiles()
    {
        if( !isset($this->js) )
        {   $this->js = [];    }
        
        return $this->js;
    }
    
    function getHost()
    {
        global $localisation;
        
        if( !$this->host )
        {
            $host = $localisation->siteAccess;
            
            if( strstr($host, '/') )
            {   $host = dirname($host); }
            
            $this->host = "http://".$host;
        }
        
        return $this->host;
    }
}
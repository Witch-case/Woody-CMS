<?php

class Configuration {
    
    var $filepath           = "configuration/configuration.ini";
    var $sections           = [];
    var $siteAccess         = [];
    var $heritedVariables   = [];
    
    function Configuration( $mandatory=true, $configurationFilePath=false )
    {
        if( $configurationFilePath )
        {   $this->filepath = $configurationFilePath;   }
        
        if( !file_exists($this->filepath) && $mandatory )
        {   die("Configuration file unreachable");  }
        
        $configuration = parse_ini_file("configuration/configuration.ini", true);
        
        foreach($configuration as $name => $array)
        {
            $this->$name        = $array;
            $this->sections[]   = $name;
        }
    }
    
    function get( $section=false, $variable=false )
    {
        if( !$section )
        {
            $returnArray = [];
            
            foreach( $this->sections as $section )
            {   $returnArray[$section] = $this->$section;   }
            
            return $returnArray;
        }
        
        if( !isset($this->$section) )
        {   return false;   }
        
        $sectionData = $this->$section;
        
        if( !$variable )
        {   return $sectionData;    }
        
        if( !isset($sectionData[$variable]) )
        {   return false;   }
        
        return $sectionData[$variable];
    }
    
    function getHeritedVariable( $variable, $site )
    {
        if( isset($this->heritedVariables[$variable][$site]) )
        {   return $this->heritedVariables[$variable][$site];   }
        
        if( !isset($this->heritedVariables[$variable]) )
        {   $this->heritedVariables[$variable] = [];    }
        
        $this->heritedVariables[$variable][$site] = [];
        
        $sections = $this->configurationAreas( $site );
        foreach( $sections as $section )
        {
            $sectionVariables = $this->get($section, $variable);
            
            if( is_array($sectionVariables) ) 
            {   foreach( $sectionVariables as $sectionVariable )
                {   $this->heritedVariables[$variable][$site][] = $sectionVariable; }
            }
        }
        
        return $this->heritedVariables[$variable][$site];
    }
    
    function addVariable( $varSection, $varName, $varValue, $isArray=false )
    {
        $filename           = basename($this->filepath);
        $dirname            = substr( $this->filepath, 0, -strlen($filename) );
        $filenameExtension  = pathinfo($filename, PATHINFO_EXTENSION);
        $baseFilename       = substr( $filename, 0, (-1 -strlen($filenameExtension)) );
        $workFilename       = $baseFilename."_tmp.".$filenameExtension;
        $workFilepath       = $dirname.$workFilename;
        
        if( file_exists($workFilepath) )
        {   unlink($workFilepath);  }
        
        if( is_array($varValue) )
        {   $isArray = true;    }
        
        elseif( $isArray )
        {   $varValue = [ $varValue ];  }
        
        
        if( isset($this->$varSection) )
        {
            $sectionContent = $this->$varSection;
            
            if( isset($sectionContent[$varName]) )
            {   if( is_array($sectionContent[$varName]) && $isArray )
                {   foreach( $varValue as $varValue_item )
                    {   if( !in_array($varValue_item, $sectionContent[$varName]) )
                        {
                            $sectionContent[$varName][] = $varValue_item;   
                }   }   }
                
                elseif( is_array($sectionContent[$varName]) )
                {   if( !in_array($varValue, $sectionContent[$varName]) )
                    {
                        $sectionContent[$varName][] = $varValue;
                }   }
                
                else 
                {   $sectionContent[$varName] = $varValue;  }
            }
            else
            {   $sectionContent[$varName]   = $varValue;    }
            
            $this->$varSection = $sectionContent;
        }
        else
        {
            $this->sections[]   = $varSection;
            $this->$varSection  = [ $varName => $varValue ];
        }
        
        $fileContent = "; this is the INI configuration file\n";
        foreach( $this->sections as $section )
        {
            $fileContent .= "\n[".$section."]\n";
            
            $sectionContent =   $this->$section;
            foreach( $sectionContent as $sectionVariableName => $sectionVariableValue )
            {   
                if( !is_array($sectionVariableValue) )
                {   $fileContent .= $sectionVariableName."=".$sectionVariableValue."\n";    }
                
                else
                {   foreach( $sectionVariableValue as $sectionVariableValue_item )
                    {   $fileContent .= $sectionVariableName."[]=".$sectionVariableValue_item."\n"; }
                }
            }
        }
        
        $fp = fopen($workFilepath, "w");
        fwrite($fp, $fileContent);
        fclose($fp);
        
        $saveFilename = $baseFilename."_".date("Y-m-d_H-i-s").".".$filenameExtension;
        
        if( !copy($this->filepath, $dirname.$saveFilename) 
            || !rename( $workFilepath , $this->filepath )
        ){
            return false;
        }
        
        return $saveFilename;
    }
    
    function getSitesAccess()
    {
        if( count($this->siteAccess) > 0 )
        {   return  $this->siteAccess;  }
        
        foreach( $this->global['sites'] as $site )
        {
            $this->siteAccess[ trim($site) ]    = [];
            $siteSection                        = $this->$site;
            
            foreach( $siteSection['access'] as $siteaccess )
            {   $this->siteAccess[$site][] = trim($siteaccess); }
        }
        
        return $this->siteAccess;
    }
    
    function administratedSites( $site )
    {
        global $log;
        
        if( isset($this->administratedSites[$site]) )
        {   return $this->administratedSites[$site];    }
        
        $this->administratedSites   = [];
        $section                    = $this->$site;
        foreach( $section['adminForSites'] as $administratedSite )
        {
            if(strcmp($administratedSite, '*') == 0 )
            {
                $this->administratedSites = $this->global['sites'];
                break;
            }
            
            if( !in_array($administratedSite, $this->global["sites"]) )
            {
                $message =  "Site ".$administratedSite;
                $message .= " declared to be administrated by site ".$site;
                $message .= " is no active site declared in the [global] part of configuration";
                $log->error($message);
                continue;
            }
            
            $this->administratedSites[] = $administratedSite;
        }
        
        return $this->administratedSites;
    }
    
    function configurationAreas( $site )
    {
        if( isset($this->configurationAreas[$site]) )
        {   return $this->configurationAreas[$site];    }
        
        if( !isset($this->configurationAreas) )
        {   $this->configurationAreas = []; }
        
        $this->configurationAreas[$site] = [ $site ];
        
        $siteHeritages  = $this->get($site, "siteHeritages");
        if( is_array($siteHeritages) )
        {   foreach( $siteHeritages as $heritedSite )
            {   $this->configurationAreas[$site][] = $heritedSite;  }
        }
        
        $this->configurationAreas[$site][] = 'global';
        
        return $this->configurationAreas[$site];

    }
    
    function getExtensions( $site )
    {
        return $this->getHeritedVariable( 'extensions', $site );
    }
    
    function getAllAttributes( $site )
    {
        return $this->getHeritedVariable( 'attributes', $site );
    }
    
    function getModules( $site )
    {
        return $this->getHeritedVariable( 'modules', $site );
    }
    
    function getModulesActions( $site )
    {
        if( isset($this->modulesActions[$site]) )
        {   return $this->modulesActions[$site];    }
        
        if( !isset($this->modulesActions) )
        {   $this->modulesActions = []; }
        
        $this->modulesActions[$site] = [];
        
        $modules    = $this->getModules($site);
        $sections   = $this->configurationAreas( $site );
        
        foreach( $modules as $module )
        {
            $this->modulesActions[$site][$module] = [ '*' ];
            
            foreach( $sections as $section )
            {
                $moduleActions = $this->get($section, $module.".actions");
                
                if( is_array($moduleActions) ) 
                {   foreach( $moduleActions as $action )
                    {   $this->modulesActions[$site][$module][] = $action;  }
                }
            }
        }
        
        return $this->modulesActions[$site];
    }
    
    function getModulesVariables( $site )
    {
        if( isset($this->modules[$site]) )
        {   return $this->modules[$site];    }
        
        if( !isset($this->modules) )
        {   $this->modules = []; }
        
        $this->modules[$site] = [];
        
        $modules    = $this->getModules($site);
        $sections   = $this->configurationAreas( $site );
        
        foreach( $modules as $module )
        {
            $this->modules[$site][$module] = [  ];
            
            foreach( $sections as $section )
            {   foreach( $this->$section as $varIniName => $varValue )
                {   if( strpos( $varIniName , $module."."  ) === 0 )
                    {
                        $varName = substr( $varIniName , strlen($module.".") );
                        $this->modules[$site][$module][$varName] = $varValue;
            }   }   }
        }
        
        return $this->modules[$site];
    }
    
    function rollback( $iniSave )
    {
        $filename           = basename($this->filepath);
        $dirname            = substr( $this->filepath, 0, -strlen($filename) );
        
        if( !file_exists($dirname.$iniSave) )
        {   return false;   }
        
        return copy($dirname.$iniSave, $this->filepath);
    }
}

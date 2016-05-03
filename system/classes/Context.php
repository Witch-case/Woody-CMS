<?php

class Context 
{
    var $execFile;
    var $visualisationFile;
    
    function Context( $contextFile=false )
    {
        global $log;
        global $module;
        
        $this->visualisationFile    = false;
        $this->execFile             = false;
        
        if( $contextFile )
        {
            $this->execFile = $module->getControllerFile( "contexts/".$contextFile );
            
            if( !$this->execFile )
            {   $log->error("Context File: ".$contextFile." summoned in module but not found"); }
        }
    }
    
    function getExecFile()
    {
        global $log;
        global $localisation;
        global $module;
        global $target;
        
        if( $this->execFile )
        {   return $this->execFile; }
        
        // Context is in Location record
        // =============================
        if( $localisation->context )
        {
            $this->execFile = $module->getControllerFile( "contexts/".$localisation->context );
            
            if( !$this->execFile )
            {
                $message    =   "Context File: ".$localisation->context;
                $message    .=  " specified in localisation but not found";
                $log->error($message);
            }
            else
            {   return $this->execFile; }
        }
        
        // Context is in Target record
        // ===========================
        if( isset($target->context[$localisation->site]) )
        {
            $this->execFile = $module->getControllerFile( "contexts/".$target->context[$localisation->site] );
            
            if( !$this->execFile )
            {
                $message    =   "Context File: ".$target->context[$localisation->site];
                $message    .=  " specified in target but not found";
                $log->error($message);
            }
            else
            {   return $this->execFile; }
        }
        
        // Context is ruled in configuration file
        // ======================================
        if( !$this->execFile )
        {
            $this->execFile = $this->contextRuleConf($localisation->site);

            if( $this->execFile )
            {   return $this->execFile; }

            $this->execFile = $this->contextRuleConf('global');

            if( $this->execFile )
            {   return $this->execFile; }
        }
        
        // Context is default
        // ==================
        $this->execFile = $module->getControllerFile( "contexts/default.php" );
        
        if( $this->execFile )
        {   return $this->execFile; }
        
        $log->error('No context file can be identified', true);
    }
    
    private function contextRuleConf( $confPart )
    {
        global $log;
        global $configuration;
        global $localisation;
        global $module;
        
        $contextRules   = $configuration->get( $confPart, 'contextRules' );
        $contextValues  = $configuration->get( $confPart, 'contextValues' );
        if( is_array($contextRules) && is_array($contextValues) ) 
        {
            if( count($contextRules) != count($contextValues)  )
            {
                $message    =   "Context rules and values don't match ";
                $message    .=  "(not same quantity) in the: ";
                $message    .=  $confPart." part of configuration file.";
                $log->error( $message );
            }
            else
            {   foreach( $contextRules as $key => $rule_value )
                {
                    $buffer = explode('.', $rule_value);
                    $rule   = $buffer[0];
                    $value  = $buffer[1];
                    
                    $match   = false;
                    $exclude = false;
                    switch( $rule )
                    {
                        case 'target_structure':
                            if( $localisation->has_target 
                                && ( strcmp($localisation->target_structure, $value) == 0 )
                            ){
                                $match = true;
                            }
                            break;
                            
                        case 'parent_target_structure':
                            $parents = $localisation->parents();
                            $parent_target_structure = "";
                            if( $parents[0]["target_table"] )
                            {
                                $buffer = explode('_', $parents[0]["target_table"]);
                                unset($buffer[0]);
                                $parent_target_structure = implode("_", $buffer);
                            }
                            
                            if( strcmp($parent_target_structure, $value) == 0 )
                            {   $match = true;  }
                            break;
                            
                        case 'status':
                            if( strcmp($localisation->status, $value) == 0 )
                            {   $match = true;  }
                            break;
                            
                        case 'target_type':
                            if( $localisation->has_target 
                                && ( strcmp($localisation->target_type, $value) == 0 )
                            ){
                                $match = true;
                            }
                            break;
                            
                        case 'depth':
                            if( $localisation->depth == $value )
                            {   $match = true;  }
                            break;
                            
                        case 'subposition_parent':
                            $exclude = true;
                        case 'subposition_parent_included':
                            $positionMatch = explode(',', $value);
                            
                            $match = true;
                            if( $exclude && count($localisation->position) <= count($positionMatch) )
                            {   $match = false; }
                            
                            elseif( count($localisation->position) < count($positionMatch) )
                            {   $match = false; }
                            
                            else
                            {   foreach( $localisation->position as $i => $positionID )
                                {   if( !isset($positionMatch[$i-1]) )
                                    {   break;  }
                                    
                                    elseif( $positionMatch[$i-1] != $positionID )
                                    {
                                        $match = false;
                                        break;
                            }   }   }
                            break;
                            
                        default:
                            $message    =   "the context rule in configuration part: ";
                            $message    .=  $confPart." is not accepted by the system.";
                            $log->error( $message );
                            break;
                    }
                    
                    if( $match )
                    {
                        $filename = "contexts/".$contextValues[$key];
                        $this->execFile = $module->getControllerFile( $filename );
                        
                        if( $this->execFile )
                        {   return $this->execFile; }
                    }
            }   }
        }
        
        return false;
    }
    
    function getDesignFile( $visulalisationName=false, $mandatory=true )
    {
        global $module;
        
        if( $this->visualisationFile )
        {   return $this->visualisationFile;    }
        
        if( !$visulalisationName )
        {
            if( !$this->execFile )
            {   $this->getExecFile();   }
            
            $visulalisationName = basename($this->execFile);
        }
        
        $filename = "contexts/".$visulalisationName;
        $this->visualisationFile = $module->getDesignFile($filename, $mandatory);
        
        return $this->visualisationFile;
    }
}
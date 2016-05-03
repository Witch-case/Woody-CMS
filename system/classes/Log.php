<?php

class Log 
{
    const MAXARCHIVEFILES   = 100;
    const FATALERRORMESSAGE = "System down\nPlease contact administrator";
    const LOGFILENAME       = 'log/error.log';
    
    var $maxLog;
    var $logFilename;
    var $currentIP;
    var $debug;
    var $errorLogFP;
    var $debugIpArray;
    
    function Log( $configuration, $logFilename=false )
    {
        $this->maxLog       = $configuration["maxLog"];
        $this->currentIP    = filter_input( INPUT_SERVER, 
                                            'REMOTE_ADDR', 
                                            FILTER_VALIDATE_IP );
        
        if( $logFilename )
        {   $this->logFilename = $logFilename;  }
        else
        {   $this->logFilename = self::LOGFILENAME; }
        
        // If log file is too big, renaming it
        if( is_file($this->logFilename)
            && filesize($this->logFilename) > $this->maxLog 
        ){
            unlink($this->logFilename.'.'.self::MAXARCHIVEFILES);
            
            for( $i=self::MAXARCHIVEFILES-1; $i>0; $i-- )
            {   if( file_exists($this->logFilename.'.'.$i) )
                {
                    $oldFilename = $this->logFilename.'.'.$i;
                    $newFilename = $this->logFilename.'.'.($i+1);
                    rename( $oldFilename, $newFilename ); 
            }   }
            
            rename( $this->logFilename, $this->logFilename.'.1' );
        }
        
        // Setting File pointers
        if( isset($configuration['debug']) 
            && in_array( $this->currentIP, $configuration['debug'] )
        ){
            $this->debug        = true;
        }
        else
        {   $this->debug = false;   }
        
        $this->errorLogFP = fopen( $this->logFilename, 'a' );
    }
    
    function debug( $variable, $userPrefix=false, $depth=0, $fromThis=false )
    {
        if( $this->debug )
        {
            echo "<pre>".$this->prefix(false, $fromThis)."\n";
            
            if( $userPrefix )
            {   echo $userPrefix."\n";  }
            
            if( $depth == 0 )
            {   var_dump($variable);    }
            else
            {   $this->debugPrint($variable, $depth);   }
            
            echo "\n</pre>";
        }
        
        return;
    }
    
    private function debugPrint($variable,$depth=10,$i=0,&$objects = array())
    {
        $search = array("\0", "\a", "\b", "\f", "\n", "\r", "\t", "\v");
        $replace = array('\0', '\a', '\b', '\f', '\n', '\r', '\t', '\v');
        
        $string = '';
        
        switch( gettype($variable) ) 
        {
            case 'boolean':
                $string.= $variable?'true':'false'; 
                break;
            
            case 'resource':
                $string.= '[resource]';
                break;
            
            case 'NULL':
                $string.= "null";
                break;
            
            case 'unknown type': 
                $string.= '???';
                break;
            
            case 'string':
                $len = strlen($variable);
                $variable = str_replace($search,$replace,$variable);
                $string.= '"'.$variable.'"';
                break;
            
            case 'array':
                $len = count($variable);
                if ($i==$depth) 
                {   $string.= 'array('.$len.') {...}';  }
                elseif(!$len) 
                {   $string.= 'array(0) {}';    }
                else 
                {
                    $keys = array_keys($variable);
                    $spaces = str_repeat(' ',$i*2);
                    $string.= "array($len)\n".$spaces.'{';
                    foreach($keys as $key) 
                    {
                        $string.= "\n".$spaces."  [".(is_numeric($key)?$key:'"'.$key.'"')."] => ";
                        $string.= $this->debugPrint($variable[$key],$depth,$i+1,$objects);
                    }
                    $string.="\n".$spaces.'}';
                }
                break;
                
            case 'object':
                $id = array_search($variable,$objects,true);
                if ($id!==false)
                {   $string.=get_class($variable).'#'.($id+1).' {...}'; }
                else if($i==$depth)
                {   $string.=get_class($variable).' {...}'; }
                else 
                {
                    $id = array_push($objects,$variable);
                    $array = (array)$variable;
                    $spaces = str_repeat(' ',$i*2);
                    $string.= get_class($variable)."#$id\n".$spaces.'{';
                    $properties = array_keys($array);
                    foreach($properties as $property) 
                    {
                        $name = str_replace("\0",':',trim($property));
                        $string.= "\n".$spaces."  [\"".$name."\"] => ";
                        $string.= $this->debugPrint($array[$property],$depth,$i+1,$objects);
                    }
                    $string.= "\n".$spaces.'}';
                }
                break;
            
            default :
                $string.= $variable;
                break;
        }
        
        if ($i>0) 
        {   return $string; }
        
        echo $string;
    }
    
    function error( $message, $fatal=false )
    {
        //$this->debug( $this->prefix($fatal).'ERROR : '.$message."\n" );
        
        $userprefix = "ERROR : ";
        
        if( $fatal )
        {   $userprefix = "FATAL ".$userprefix; }
        
        $this->debug( $message."\n", $userprefix, 0, true );
        
        fwrite( $this->errorLogFP, $this->prefix($fatal).$message."\n" );
        
        if( $fatal )
        {   die(self::FATALERRORMESSAGE);   }
        
        return;
    }
    
    function prefix( $fatal=false, $fromThis=false )
    {
        $prefix = "[ ".date(DATE_RFC2822)." ] [ ".$this->currentIP." ] ";
        
        if( !isset( $this->backtraceFileBegin ) )
        {   if( !filter_has_var(INPUT_SERVER, "DOCUMENT_ROOT") )
            {   $this->backtraceFileBegin = 0;  }
            else
            {
                $this->backtraceFileBegin = strlen( filter_input(INPUT_SERVER, "DOCUMENT_ROOT") ) + 1;
        }   }
        
        $backtraceDepth = 1;
        if($fromThis)
        {   $backtraceDepth++;   }
        
        $backtrace = debug_backtrace(DEBUG_BACKTRACE_IGNORE_ARGS, $backtraceDepth+1);
        
        $prefix .= "[ ".substr( $backtrace[$backtraceDepth]['file'], $this->backtraceFileBegin );
        $prefix .= " on line ".$backtrace[$backtraceDepth]["line"]." ] ";
        
        if($fatal)
        {   $prefix .= "FATAL ";    }
        
        return $prefix;
    }
}

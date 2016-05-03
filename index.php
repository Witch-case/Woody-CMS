<?php

if( !file_exists("configuration/configuration.ini") )
{   die("Configuration file unreachable");  }

require_once 'system/classes/Configuration.php';
require_once 'system/classes/Log.php';
require_once 'system/classes/Database.php';
require_once 'system/classes/User.php';
require_once 'system/classes/Localisation.php';
require_once 'system/classes/Context.php';

$configuration  =   new Configuration();
$log            =   new Log( $configuration->get('system') );
$db             =   new Database( $configuration->get('database') );
$user           =   new User();
$localisation   =   new Localisation();
$module         =   $localisation->getModule();
$target         =   $localisation->getTarget();

$contextFile = false;
if( is_file($module->execFile) )
{
    ob_start();
    include $module->execFile;
    $module_result = ob_get_contents();
    ob_end_clean();
}

$context = new Context( $contextFile );

include $context->getExecFile();
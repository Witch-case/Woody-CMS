<?php

global $log;

require_once 'system/classes/Profile.php';

$profiles = Profile::listProfiles();

include $module->getDesignFile('attributes/edit/connexion.php');

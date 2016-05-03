<?php

$maxLenght  = 255;
$indice     = strpos( substr($this->values['string'], $maxLenght), " " ) + $maxLenght;
$value      = substr($this->values['string'], 0, $indice);

if( strlen($this->values['string']) > strlen($value) )
{   $value .= " (...)"; }

include $module->getDesignFile('attributes/view/string.php');
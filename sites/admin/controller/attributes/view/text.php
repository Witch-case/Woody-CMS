<?php

$maxLenght  = 255;
$indice     = strpos( substr($this->values['text'], $maxLenght), " " ) + $maxLenght;
$value      = substr($this->values['text'], 0, $indice);

if( strlen($this->values['text']) > strlen($value) )
{   $value .= " (...)"; }

include $module->getDesignFile('attributes/view/text.php');
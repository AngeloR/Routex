<?php 

function Routex2Autoloader($className) {

	$className = ltrim($className, '\\');
    $pieces = explode(DIRECTORY_SEPARATOR, $className);
    $rootNamespace = array_shift($pieces);
    $className = join(DIRECTORY_SEPARATOR, $pieces);
    
    $fileName = $className.'.php';


	require_once($fileName);
}


spl_autoload_register('Routex2Autoloader');
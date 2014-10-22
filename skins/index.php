<?php

//tinyweb

session_start();
/*
if(isset($_SESSION['HTTP_USER_AGENT'])){
	if($_SESSION['HTTP_USER_AGENT'] != md5($_SERVER['HTTP_USER_AGENT'])){
		session_regenerate_id();
		$_SESSION['HTTP_USER_AGENT'] = mds($_SERVER['HTTP_USER_AGENT']);
	}
} else{
	$_SESSION['HTTP_USER_AGENT'] = md5($_SERVER['HTTP_USER_AGENT']);
}*/

define("APP_PATH", dirname(__FILE__)."/");

define("PCAFW", true);

function __autoload($class_name){
	require_once('controllers/'.$class_name.'/'.$class_name.'.php');
}

require_once('PCARegistry/pcaregistry.class.php');
$registry = PCARegistry::singleton();
$registry->storeObject('template', 'template');
$registry->getObject('template')->generateOutput();

print $registry->getFrameworkName();

exit();

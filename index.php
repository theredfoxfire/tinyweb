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

$registry->storeCoreObjects();

$registry->getObject->('db')->newConnection('localhost', 'root', '', 'pcaframework');
$registry->storeSetting('default', 'skin');
$registry->getObject('template')->buildFromTemplates('main.tpl.php');
$cache = $registry->getObject('db')->cacheQuery('SELECT * FROM members');
$registry->getObject('template')->getPage()->addTag('members', array('SQL', $cache) );
$registry->getObject('template')->getPage()->setTitle('Our members');
$registry->getFrameworkName();
$registry->getObject('template')->parseOutput();
print $registry->getObject('template')->getPage()->getContent();


exit();

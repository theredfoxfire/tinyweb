<?php

//tinyweb

class PCARegistry{
	private static $objects = array();
	private static $settings = array();
	private static $frameworkName = 'tinyweb version a.01';
	private static $instance;
	
	private function __construct(){
		
	}
	
	public static function singleton(){
		if(!isset(self::$instance)){
			$obj = __CLASS__;
			self::$instance = new $obj;
		}
		
		return self::$instance;
	}
	
	public function __clone(){
		trigger_error('Cloning the registry is not permitted', E_USER_ERROR);
	}
	
	public function storeObject($object, $key){
		require_once('objects/'.$object.'.class.php');
		self::$objects[$key] = new $object(self::$instance);
	}
	
	public function getObject($key){
		if(is_object(self::$objects[$key])){
			return self::$objects[$key];
		}
	}
	
	public function storeSetting($data, $key){
		self::$settings[$key] = $data;
	}
	
	public function getSetting($key){
		return self::$settings[$key];
	}
	
	public function getFrameworkName(){
		return self::$frameworkName;
	}
}

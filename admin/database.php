<?php

class database{
	private $connections = array();
	private $activeConnection = 0;
	private $queryCache = array();
	private $dataCache = array();
	private $last;
	
	public function __construct(){
		
	}
	
	public function newConnection($host, $user, $password, $database){
		$this->connections[] = new mysqli($host, $user, $password, $database);
		$connection_id = count($this->connections)-1;
		if (mysql_connect_erno()){
			trigger_error('Error connecting to host. '.$this->connections[$connection_id]->error, E_USER_ERROR);
		}
		
		return $connection_id;
	}
	
	public function closeConnection(){
		$this->connections[$this->activeConnection]->close();
	}
	
	public function setActiveConnection(int $new){
		$this->activeConnection = $new;
	}
	
	public function cacheQuery($queryStr){
		if(!$result = $this->connections[$this->activeConnection]->query($queryStr)){
			trigger_error('Error executing and caching query: '.$this->connections[$this->activeConnection]->error, E_USER_ERROR);
			return -1;
		} else {
			$this->queryCache[] = $result;
			return count($this->queryCache)-1;
		}
	}
	
	public function numRowsFromCache($cache_id){
		return $this->queryCache[$cache_id]->num_rows;
	}
	
	public function resultsFromCache($cache_id){
		return $this->queryCache[$cache_id]->fetch_array(MYSQLI_ASSOC);
	}
	
	public function cacheData($data){
		$this->dataCache[] = $data;
		return count($this->dataCache)-1;
	}
	
	public function dataFromCache($cache_id){
		return $this->dataCache[$cache_id];
	}
	
	public function deleteRecords($table, $condition, $limit){
		$limit = ($limit == '') ? '' : ' LIMIT '. $limit;
		$delete = "DELETE FROM {$table} WHERE {$condition} {$limit}";
		$this->executeQuery($delete);
	}
	
	public function updateRecords($table, $changes, $condition){
		$update = "UPDATE".$table."SET";
		foreach($changes as $fields => $value){
			$update.="`".$field."`='{$value}',";
		}
		
		$update = substr($update, 0, -1);
		if($condition != ''){
			$update .="WHERE". $condition;
		}
		
		$this->executeQuery($update);
		
		return true;
	}
	
	public function insertRecords($table, $data){
		$fields = "";
		$values = "";
		
		foreach($data as $f =>$v){
			$fields .="`$f`,";
			$values .=(is_numeric($v) && (intval($v) == $v)) ? $v.",": "'$v',";
		}
		
		$fields = substr($fields, 0, -1);
		$values = substr($values, 0, -1);
		
		$insert = "INSERT INTO $table ({$fields}) VALUES ({$values})";
		$this->executeQuery($insert);
		return true;
	}
	
	public function executeQuery($queryStr){
		if(!$result = $this->connections[$this->activeConnection]->query($queryStr)){
			trigger_error('Error executing query:'.$this->connections[$this->activeConnection]->error, E_USER_ERROR);
		} else {
			$this->last = $result;
		}
	}
	
	public function getRows(){
		return $this->last->fetch_array(MYSQLI_ASSOC);
	}
	
	public function affectedRows(){
		return $this->connections[$this->activeConnection]->affected_rows;
	}
	
	public function sanitizeData($data){
		return $this->connections[$this->activeConnection]->real_escape_string($data);
	}
	
	public function __deconstruct(){
		foreach($this->connections as $connection){
			$connection->close();
		}
	}
}

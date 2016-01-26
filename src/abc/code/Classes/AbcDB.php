<?php

class AbcDB extends PDO{

	protected static $instance = null;

	public function __construct($dsn = null, $username = null, $password = null, array $driver_options = null){

		// Get the System DB Config
		global $databaseConfig;

		// Make the config easier to work with
		$conf = (object) $databaseConfig;

		// fix for sqlite dbs
		$type = strtolower(str_replace('Database', '', $conf->type));
		if ($type == 'sqlitepdo') $type = 'sqlite';
		if ($type == 'mysqlpdo') $type = 'mysql';

		// DSN
		if (!$dsn) $dsn = $type . ':' . 'host='.$conf->server . ';' . 'dbname=' . $conf->database;

		// Authentication
		if (!$username) $username =	$conf->username;
		if (!$password) $password =	$conf->password;

		// Connect
		parent::__construct($dsn, $username, $password, $driver_options);

	}

	public static function getInstance(){
		if (empty(self::$instance)) self::$instance = new self;
		return self::$instance;
	}

}

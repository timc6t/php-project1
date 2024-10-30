<?php
/**
 * Database configuration script
 * 
 * This script handles the database configuration that is used during the project. For now, it
 * contains the loading configuration and a function to check the user's identity when
 * logging in.
 * 
 * @package database-configuration
 * @author Timothy Casiano Tamin <timothy.casiano@educa.madrid.org>
 * @version 1.0
 */

 /**
  * Loads configuration from and XML file and validates it against a given schema.
  * 
  * This function reads a configuration file, validates its structure using an XML schema,
  * and extracts database connection details (IP, database, name, user and password).
  *
  * @param mixed $name
  * @param mixed $schema
  * @throws \InvalidArgumentException
  * @return array
  */
 function load_config($name, $schema){
	$config = new DOMDocument();
	$config -> load($name);
	$res = $config->schemaValidate($schema);
	if ($res===FALSE) { 
	   throw new InvalidArgumentException("Check configuration file");
	} 		
	$data = simplexml_load_file($name);	
	$ip = $data -> xpath("//ip");
	$name = $data -> xpath("//name");
	$user = $data -> xpath("//user");
	$password = $data -> xpath("//password");	
	$db_connection = sprintf("mysql:dbname=%s;host=%s", $name[0], $ip[0]);
	$result = [];
	$result[] = $db_connection;
	$result[] = $user[0];
	$result[] = $password[0];
	return $result;
}

/**
 * Checks the provided email and password against the database for user authentication.
 * 
 * This function loads the database configuration from an XML file, establishes a PDO
 * connection, and verifies the user's credentials by checking the hashed password stored in
 * the database. 
 * 
 * @param mixed $email
 * @param mixed $password
 * @return array|bool
 */
function check_user($email, $password) {
	$result = load_config(dirname(__FILE__) . "/configuration.xml", dirname(__FILE__) . "/configuration.xsd");

	$db = new PDO($result[0], $result[1], $result[2]);
	$prepared = $db -> prepare("SELECT user_id, email, user_password FROM users WHERE email = ?");
	$prepared -> execute([$email]);

	$user = $prepared -> fetch(PDO::FETCH_ASSOC);

	if ($user && password_verify($password, $user['user_password'])) {
		return [
			'user_id' => $user['user_id'],
			'email' => $user['email']
		];
	} else {
		return false;
	}
}
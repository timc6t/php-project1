<?php

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
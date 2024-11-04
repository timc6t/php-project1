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
	$db_connection = sprintf("mysql:dbname=employee_expenses;host=127.0.0.1", $name[0], $ip[0]);
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

function add_expense($user_id, $date, $amount, $description, $category) {
	try {
		// Option 1 (should be out of the try block)
		$result = load_config(dirname(__FILE__) . "/configuration.xml", dirname(__FILE__) . "/configuration.xsd");

		$db = new PDO($result[0], $result[1], $result[2]);
		$db -> beginTransaction();
		$hour = date("d-m-Y H:i:s", time());
		$prepared = $db -> prepare("INSERT INTO expenses (user_id, date, amount, description, category) VALUES (?, ?, ?, ?, ?)");
		
		//$prepared -> execute(array($hour, $user_id));

		if (!$prepared) {
			return FALSE;
		}

		// Option 2: The one given by ChatGPT:
		$stmt = $db -> prepare("INSERT INTO expenses (user_id, date, amount, description, category) VALUES (:employee_id, :date, :amount, :description, :category)");
		$stmt->bindParam(':employee_id', $employee_id, PDO::PARAM_INT);
        $stmt->bindParam(':date', $date, PDO::PARAM_STR);
        $stmt->bindParam(':amount', $amount, PDO::PARAM_STR);
        $stmt->bindParam(':description', $description, PDO::PARAM_STR);
        $stmt->bindParam(':category', $category, PDO::PARAM_STR);

		return $stmt -> execute();
	} catch (PDOException $e) {
		echo "Database error: " . $e -> getMessage();
		return false;
	}
}
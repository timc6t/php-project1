<?php
/*if (session_start() === PHP_SESSION_NONE) {
	session_start();
}*/

function load_config($name, $schema){
	$config = new DOMDocument();
	$config->load($name);
	$res = $config->schemaValidate($schema);
	if ($res === FALSE){ 
	   throw new InvalidArgumentException("Check configuration file");
	} 		
	$data = simplexml_load_file($name);	
	$ip = $data -> xpath("//ip");
	$name = $data -> xpath("//name");
	$user = $data -> xpath("//user");
	$password = $data -> xpath("//password");	
	$conn_string = sprintf("mysql:dbname=%s;host=%s", $name[0], $ip[0]);
	$result = [];
	$result[] = $conn_string;
	$result[] = $user[0];
	$result[] = $password[0];
	return $result;
}

function check_user($email, $password){
	$res = load_config(dirname(__FILE__)."/configuration.xml", dirname(__FILE__)."/configuration.xsd");
	$db = new PDO($res[0], $res[1], $res[2]);

	$prepared = $db -> prepare(	"SELECT user_id, email, name, user_role FROM users
										WHERE email = ? AND user_password = ?");
	$prepared -> execute(array($email, $password));

	if ($prepared -> rowCount() === 1) {
		return $prepared -> fetch();
	} else {
		return FALSE;
	}

	/*if ($user) {
		// if (password_verify($password, $user['user_password'])){		añadir más tarde
		if ($password === $user['user_password']) { // Sustituir por la anterior línea más tarde
			return $user;		
		} else {
			echo "Password does not match";
		}
	} else {
		echo "User not found with that email";
	}
	return FALSE;*/
}

function add_report() {
	if ($_SERVER['REQUEST_METHOD'] == 'POST') {
		$user_id = $_POST['user_id'];
		$category = $_POST['category'];
		$description = $_POST['description'];
		$date = $_POST['date'];
		$amount = $_POST['amount'];
	
		if ($user_id && $category && $description && $date && $amount) {
	
			if (add_expense($user_id, $category, $description, $date, $amount)) {
				$message = 'Expense added successfully!';
			} else {
				$message = 'Failed to add expense.';
			}
	
		} else {
			$message = 'Please fill in all required fields.';
		}
	}
}

function getReports() {
	try {
		$res = load_config(dirname(__FILE__)."/configuration.xml", dirname(__FILE__)."/configuration.xsd");
		$db = new PDO($res[0], $res[1], $res[2]);

		if ($db) {
            echo "Conexión exitosa a la base de datos.<br>";
        } else {
            echo "Error de conexión a la base de datos.<br>";
        }

		$sql = 'SELECT expenses.*, users.name AS employee_name FROM expenses JOIN users ON expenses.user_id = users.user_id';
		$reports = $db->query($sql);

		$output = '';

		foreach ($reports as $report) {
			$rep_id = $report['report_id'];
			$user_id = $report['user_id'];
			$name = $report['employee_name'];
			$cat = $report['category'];
			$desc = $report['description'];
			$repDate = $report['report_date'];
			$amount = $report['amount'];
			$status = $report['status'];
			$created = $report['created_at'];
			$output .=  "<tr>
							<td>$name</td>
							<td>$cat</td>
							<td>$desc</td>
							<td>$repDate</td>
							<td>$amount</td>
							<td class='status-update'>
								<form method='POST' action=''>
									<input type='hidden' name='report_id' value='$rep_id'>
									<select name='status'>
										<option value='pending'" . ($status == 'pending' ? ' selected' : '') . ">Pending</option>
										<option value='approved'" . ($status == 'approved' ? ' selected' : '') . ">Approved</option>
										<option value='denied'" . ($status == 'denied' ? ' selected' : '') . ">Denied</option>
									</select>
									<button type='submit' name='update_status'>Update</button>
								</form>
							</td>
							<td>$created</td>
						</tr>";
		}
		return $output;
	} catch (PDOException $e) { 
		echo 'Database error: ' . $e -> getMessage(); 
	}
}

// The following function is to be used for sending the expenses through email
function get_email() {
	if (session_status() === PHP_SESSION_NONE) {
		session_start();
	}

	var_dump($_SESSION);

	if (!isset($_SESSION['user_id'])) {
		return 'User not logged in';
	}

	$user_id = $_SESSION['user_id'];

	$res = load_config(dirname(__FILE__)."/configuration.xml", dirname(__FILE__)."/configuration.xsd");
	$db = new PDO($res[0], $res[1], $res[2]);

	$prepared = $db -> prepare("SELECT email FROM users WHERE user_id = ?");
	$prepared -> execute([$user_id]);

	$user = $prepared -> fetch();

	if ($user) {
		return $user['email'];
	} else {
		return 'The presence of an unknown entity has been detected in your session. It is recommended that you DO NOT CONTINUE NAVIGATING THIS WEBSITE. Ignoring this warning may occur in strange events that are out of our control.'; // User unknown error
	}
}
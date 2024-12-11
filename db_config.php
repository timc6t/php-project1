<?php
/**
 * db_config.php
 * 
 * This file contains the database configuration and functions related to the connection.
 * It will be included in other files in order to do database operations.
 */
require_once 'fpdf/fpdf.php';

/**
 * Loads and validates the configuration XML file, then extracts the database connection
 * details.
 * 
 * This function loads the specified XML configuration file, validates it using an XML
 * schema, and then extracts the necessary database connection details (IP address, database,
 * name, user, and password) using XPath queries. If the XML validation fails, an
 * 'InvalidArgumentException' is thrown.
 *
 * @param string $name   The path to the configuration XML file.
 * @param string $schema The path to the XML schema file for validation.
 * 
 * @return array An array containing the following values:
 *               - Connection string for the database (`mysql:dbname=...;host=...`),
 *               - The database user,
 *               - The database password.
 * 
 * @throws InvalidArgumentException If the XML schema validation fails, an exception is
 * 									thrown.
 */
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

/**
 * Verifies the user's email and password against the database.
 * 
 * This function checks if a user exists in the database with the provided email and
 * password. It loads the database configuration, connects to the database using PDO, and
 * queries the `users` table to match the email and password. If a match is found, the user's
 * information is returned. If no match is found, the function returns `FALSE`.
 *
 * @param string $email    The email address of the user attempting to log in.
 * @param string $password The password of the user attempting to log in.
 * 
 * @return array|bool If a user with the provided email and password is found, an associative
 * 					  array containing the user's 'user_id', 'email', 'name', and
 *                    'user_role' is returned. If no matching user is found, the function
 *                    returns `FALSE`.
 * 
 * @throws PDOException If there is an error with the database connection or query execution,
 * an exception will be thrown.
 */
function check_user($email, $password){
	$res = load_config(
		dirname(__FILE__)."/configuration.xml",
		dirname(__FILE__)."/configuration.xsd"
	);
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

/**
 * Función para registrar un nuevo usuario en la base de datos.
 * 
 * Esta función recibe los datos del usuario (email, nombre, contraseña y rol), los inserta en la base de datos
 * y devuelve los detalles del nuevo usuario.
 * 
 * @param string $email    El correo electrónico del nuevo usuario.
 * @param string $name     El nombre del nuevo usuario.
 * @param string $password La contraseña del nuevo usuario (aún no cifrada en esta versión).
 * @param int    $role     El rol del nuevo usuario (0=empleado, 1=gerente, 2=soporte).
 * 
 * @return array Los detalles del nuevo usuario después de ser registrado.
 * 
 * @throws PDOException Si ocurre un error al conectar con la base de datos o al ejecutar la consulta.
 */
function register_user($email, $name, $password, $role) {
    try {
        $res = load_config(
			dirname(__FILE__)."/configuration.xml",
			dirname(__FILE__)."/configuration.xsd"
		);
		$db = new PDO($res[0], $res[1], $res[2]);

        // Cifrar la contraseña antes de almacenarla (descomentar más tarde)
        // $hashed_password = password_hash($password, PASSWORD_DEFAULT);
        $hashed_password = $password; // Eliminar después

        $prepared = $db->prepare("INSERT INTO users (email, name, user_password, user_role) VALUES (?, ?, ?, ?)");
        $prepared->execute([$email, $name, $hashed_password, $role]);

        $new_user_id = $db->lastInsertId();

        $stmt = $db->prepare("SELECT * FROM users WHERE user_id = ?");
        $stmt->execute([$new_user_id]);
        $new_user = $stmt->fetch(PDO::FETCH_ASSOC);

        return $new_user;

    } catch (PDOException $e) {
        echo "<p style='color: red;'>Error: " . $e->getMessage() . "</p>";
    }
}

/**
 * Handles the management of user roles and fetching of user data.
 * 
 * This function is responsible for updating a user's role in the database based on the
 * provided POST data, and for fetching all users from the database. It uses PDO for database 
 * interaction and handles any exceptions that may occur during the process.
 * 
 * @param PDO 		 $db 	   The database connection object, used to interact with the
 * 							   database.
 * @param string 	 $method   The request method ('POST' or 'GET'). Used to determine whether
 * 							   to handle a role update or just fetch the user data.
 * @param array|null $postData An associative array containing POST data. If the method is
 * 							   'POST' and contains an 'update_role' key, the function will 
 * 							   attempt to update the user's role.
 * 
 * @return array Returns an array with two elements:
 * 				 - A string containing a success of error message, HTML formatted.
 * 				 - An array of users, each represented as an associative array with 'user_id',
 * 				   'email', 'name', and 'user_role' keys.
 * 
 * @throws PDOException If an error occurs during database interactions, an exception will be
 * 						thrown and caught.
 */
function manageUsers($db, $method, $postData = null) {
	$message = '';
	$users = [];
	
	try {
		if ($method === "POST" && isset($postData['update_role'])) {
			$user_id = $postData['user_id'];
			$new_role = $postData['user_role'];

			$prepared = $db -> prepare("UPDATE users SET user_role = ? WHERE user_id ? ?");
			$prepared -> execute([$new_role, $user_id]);

			$message = "<p style='color: green;'>Role updated successfully.</p>";
		}

		$prepared = $db -> prepare("SELECT user_id, email, name, user_role FROM users");
		$prepared -> execute();
		$users = $prepared -> fetchAll(PDO::FETCH_ASSOC);
	} catch (PDOException $e) {
		$message = "<p style='color: red;'>Error: " . $e -> getMessage() . "</p>";
	}
	
	return [$message, $users];
}

/**
 * Adds a new expense report to the database.
 * 
 * This function inserts a new expense report into the `expenses` table in the database. 
 * Before inserting, it checks if the user exists in the `users` table. If the user doesn't
 * exist, an exception is thrown. The function handles database-related errors and throws
 * appropriate exceptions.
 *
 * @param int    $user_id     The ID of the user submitting the expense report.
 * @param string $cat         The category of the expense (e.g., Travel, Office Supplies).
 * @param string $desc        A description of the expense.
 * @param float  $amount      The amount of the expense.
 * @param string $created_at  The date the expense was created (in `Y-m-d` format).
 * @param string $status      The status of the expense report (default is 'pending').
 * 
 * @return string A message indicating whether the expense report was successfully created.
 * 
 * @throws Exception If the user does not exist in the database, or any database-related error occurs.
 */
function addExpense($user_id, $cat, $desc, $amount, $created_at, $status = 'pending') {
	try {
		$res = load_config(
			dirname(__FILE__)."/configuration.xml",
			dirname(__FILE__)."/configuration.xsd"
		);
		$db = new PDO($res[0], $res[1], $res[2]);

		$checkUser = $db->prepare("SELECT COUNT(*) FROM users WHERE user_id = ?");
		$checkUser->execute([$user_id]);

		if ($checkUser->fetchColumn() == 0) {
			throw new Exception("The user does not exist in the database.");
		}

		$prepared = $db -> prepare("INSERT INTO expenses (user_id, category, description, amount, report_date, status) VALUES (?, ?, ?, ?, ?, ?)");
		$prepared -> execute([$user_id, $cat, $desc, $amount, $created_at, $status]);

		return "Report succesfully created";
	} catch (PDOException $e) {
		throw new Exception("Database error: " . $e -> getMessage());
	} catch (Exception $e) {
		throw new Exception($e -> getMessage());
	}
}

/**
 * Retrieves expenses for the logged-in user. The expenses can be filtered based on their
 * status.
 * 
 * This function fetches the expense reports from the database for the currently logged-in
 * user with an optional status filter applied. It queries the `expenses` and `users` tables,
 * joining them to retrieve the necessary data, and formats the results into an HTML table
 * row format. If no status filter is provided, all reports for the user are returned. The
 * results are ordered by the report creation date in descending order.
 * 
 * @return string An HTML string representing the filtered expense reports, formatted as
 * 				  table rows.
 * 
 * @throws PDOException If there is an issue with the database connection or query execution.
 */
function getFilteredReports() {
	try {
		$res = load_config(
			dirname(__FILE__)."/configuration.xml",
			dirname(__FILE__)."/configuration.xsd"
		);
		$db = new PDO($res[0], $res[1], $res[2]);
		$user_id = $_SESSION['user']['user_id'];
		$status_filter = isset($_GET['status_filter']) ? $_GET['status_filter'] : '';

		echo "User ID: $user_id<br>";
		echo "Status filter: $status_filter<br>";
		$sql = 'SELECT expenses.*, users.name AS employee_name
				FROM expenses
				JOIN users ON expenses.user_id = users.user_id
				WHERE expenses.user_id = :user_id';
		
		if ($status_filter) {
			$sql .= ' AND expenses.status = :status';
		}

		$sql .= ' ORDER BY expenses.created_at DESC';
		$prepared = $db -> prepare($sql);
		$prepared -> bindParam(':user_id', $user_id);

		if ($status_filter) {
			$prepared -> bindParam(':status', $status_filter);
		}

		$prepared -> execute();
		$reports = $prepared -> fetchAll(PDO::FETCH_ASSOC);

		$output = '';

		foreach ($reports as $report) {
			$rep_id = $report['report_id'];
			$user_id = $report['user_id'];
			$name = $report['employee_name'];
			$cat = $report['category'];
			$desc = $report['description'];
			$repDate = $report['report_date'];
			$date = DateTime::createFromFormat('Y-m-d', $repDate);
			$formatted_repDate = $date -> format('d-m-Y');
			$amount = $report['amount'];
			$status = $report['status'];
			$created = $report['created_at'];
			$output .= "<tr>
                            <td>$name</td>
                            <td>$cat</td>
                            <td>$desc</td>
                            <td>$formatted_repDate</td>
                            <td>$amount</td>
                            <td>$status</td>
                            <td>$created</td>
                        </tr>";
		}
		return $output;
	} catch (PDOException $e) {
		echo 'Database error: ' . $e -> getMessage(); 
	}
}

/**
 * Retrieves and displays the status of expense reports, with the ability to update their
 * status.
 * 
 * This function retrieves all expense reports from the database, joining the 'expenses' and
 * 'users' tables to include the employee's name. The reports are ordered by the report
 * creation date in descending order. If a status update request is made through the 'POST'
 * method, the status of the specific report is updated in the database.
 *
 * @return string An HTML string representing the status of the expense reports, formatted as
 * 				  table rows with status update forms.
 * 
 * @throws PDOException If there is an issue with the database connection or query execution.
 */
function getStatusReports() {
	try {
		$res = load_config(
			dirname(__FILE__)."/configuration.xml",
			dirname(__FILE__)."/configuration.xsd"
		);
		$db = new PDO($res[0], $res[1], $res[2]);

		if (isset($_POST['update_status'])) {
            $report_id = $_POST['report_id'];
            $status = $_POST['status'];

            $updateStatusQuery = $db->prepare("UPDATE expenses SET status = ? WHERE report_id = ?");
            $updateStatusQuery->execute([$status, $report_id]);
        }

		$sql = 'SELECT expenses.*, users.name AS employee_name FROM expenses JOIN users ON expenses.user_id = users.user_id ORDER BY expenses.report_date DESC';
		$reports = $db->query($sql);

		$output = '';

		foreach ($reports as $report) {
			$rep_id = $report['report_id'];
			$user_id = $report['user_id'];
			$name = $report['employee_name'];
			$cat = $report['category'];
			$desc = $report['description'];
			$repDate = $report['report_date'];
			$date = DateTime::createFromFormat('Y-m-d', $repDate);
			$formatted_repDate = $date -> format('d-m-Y');
			$amount = $report['amount'];
			$status = $report['status'];
			$created = $report['created_at'];
			
			$output .=  "<tr>
							<td>$name</td>
							<td>$cat</td>
							<td>$desc</td>
							<td>$formatted_repDate</td>
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

/**
 * Generates a PDF document containing a list of expense reports.
 * 
 * This function creates a PDF file using the `FPDF` library. It includes a header and footer
 * on each page and then generates a table with the following columns: Employee, Category,
 * Description, Amount, Report date, Status, and Created at. The data for the table is
 * populated from the `$expenses` array. The PDF is then output as a downloadable file named
 * 'expense_reports.pdf'.
 *
 * @param array $expenses An array of expense reports, where each report is an associative
 * 						  array with the following keys:
 * 						  - 'employee_name': The name of the employee.
 * 						  - 'category': The category of the expense.
 * 						  - 'description': A description of the expense.
 * 						  - 'amount': The amount of the expense.
 * 						  - 'report_date': The date the expense report was created.
 * 						  - 'status': The status of the expense report (e.g., 'pending',
 * 									  'approved', 'denied').
 * 						  - 'created_at': The timestamp when the expense report was created.
 * 
 * @return void Outputs the PDF to the browser and exits the script. The PDF file is named `expense_reports.pdf`.
 */
function generatePDF($expenses) {
	class PDF extends FPDF {
		function Header() {
			$this -> SetFont('Arial', 'B', 14);
			$this -> Cell(0, 10, 'Expense reports', 0, 1, 'C');
			$this -> Ln(10);
		}

		function Footer() {
			$this -> SetY(-15);
			$this -> SetFont('Arial', 'I', 7);
			$this -> Cell(0, 10, 'Page '. $this -> PageNo(), 0, 0, 'C');
		}
	}

	$pdf = new PDF('L', 'mm', 'A4');
	$pdf -> SetAutoPageBreak(true, 10);
	$pdf -> AddPage();
	$pdf -> SetFont('Arial', 'B', 11);

	$pdf -> Cell(40, 7, 'Employee', 1);
	$pdf -> Cell(60, 7, 'Category', 1);
	$pdf -> Cell(60, 7, 'Description', 1);
	$pdf -> Cell(23, 7, 'Amount', 1);
	$pdf -> Cell(25, 7, 'Report date', 1);
	$pdf -> Cell(20, 7, 'Status', 1);
	$pdf -> Cell(35, 7, 'Created at', 1);
	$pdf -> Ln();

	$pdf -> SetFont('Arial', '', 10);

	foreach ($expenses as $expense) {
		$pdf -> Cell(40, 7, $expense['employee_name'], 1);
		$pdf -> Cell(60, 7, $expense['category'], 1);
		$pdf -> Cell(60, 7, $expense['description'], 1);
		$pdf -> Cell(23, 7, number_format($expense['amount']), 1);
		$pdf -> Cell(25, 7, $expense['report_date'], 1);
		$pdf -> Cell(20, 7, ucfirst($expense['status']), 1);
		$pdf -> Cell(35, 7, $expense['created_at'], 1);
		$pdf -> Ln();
	}

	$pdf -> Output('D', 'expense_reports.pdf');
	exit;
}

/**
 * Fetches all expense records from the database along with the employee name.
 * 
 * This function retrieves a list of expenses along with the associated employee name,
 * category, description, amount, report date, status, and creation date from the database.
 * It uses a join between the 'expenses' and 'users' tables to get the employee name. The
 * 'report_date' is formatted to a 'dd-mm-yyyy' format. The result is returned as an array of
 * associative arrays, each representing an expense report.
 * 
 * @return array An array of expense records. Each record is an associative array containing:
 * 				 - 'employee_name': The name of the employee who submitted the expense.
 * 				 - 'category': The category of the expense.
 * 				 - 'description': A description of the expense.
 * 				 - 'amount': The amount of the expense.
 * 				 - 'report_date': The formatted report date ('dd-mm-yyyy').
 * 				 - 'created_at': The timestamp when the expense report was created.
 * 				 - 'status': The status of the expense report (e.g., 'pending', 'approved',
 * 							 'denied').
 * 
 * @throws Exception If there is a database error, an exception is thrown with the error
 * 					 message.
 */
function fetchExpenses() {
	try {
		$res = load_config(
			dirname(__FILE__)."/configuration.xml",
			dirname(__FILE__)."/configuration.xsd"
		);
		$db = new PDO($res[0], $res[1], $res[2]);

		$prepared = $db -> prepare( "SELECT users.name AS employee_name, expenses.category, expenses.description, expenses.amount,
											DATE_FORMAT(expenses.report_date, '%d-%m-%Y') AS report_date,
											expenses.created_at, expenses.status
											FROM expenses
											JOIN users ON expenses.user_id = users.user_id");

		$prepared -> execute();
		return $prepared -> fetchAll(PDO::FETCH_ASSOC);
	} catch (PDOException $e) {
		throw new Exception("Database error: " . $e -> getMessage());
	}
}

// The following function is to be used for sending the expenses through email
/*function get_email() {
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
}*/
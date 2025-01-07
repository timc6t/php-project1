<?php
require_once 'fpdf/fpdf.php';

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
	try {
		$res = load_config(
			dirname(__FILE__)."/configuration.xml",
			dirname(__FILE__)."/configuration.xsd"
		);
		$db = new PDO($res[0], $res[1], $res[2]);

		$prepared = $db -> prepare(	"SELECT user_id, email, name, user_role FROM users
											WHERE email = ?");
		$prepared -> execute(array($email));

		if ($prepared -> rowCount() === 1) {
			return $prepared -> fetch();

			if (password_verify($password, $user['user_password'])) {
				return [
					'user_id' => $user['user_id'],
					'email' => $user['email'],
					'name' => $user['name']
				];
			} else {
				return false;
			}
		} else {
			return FALSE;
		}
	} catch (PDOException $e) {
		throw $e;
	}
}

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

function getEmployees() {
	try {
		$res = load_config(
			dirname(__FILE__)."/configuration.xml",
			dirname(__FILE__)."/configuration.xsd"
		);
		$db = new PDO($res[0], $res[1], $res[2]);

		$sql = "SELECT user_id, name FROM users WHERE user_role = 0 OR user_role = 1 OR user_role = 2";
		$prepared = $db -> prepare($sql);
		$prepared -> execute();
		return $prepared -> fetchAll();
	} catch (PDOException $e) {
		echo "Database error: " . $e -> getMessage();
	}
}

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

function generateExpense($employee_id, $start_date, $end_date){
	try {
		$res = load_config(
			dirname(__FILE__)."/configuration.xml",
			dirname(__FILE__)."/configuration.xsd"
		);
		$db = new PDO($res[0], $res[1], $res[2]);

		$sql = "SELECT expenses.*, users.name AS employee_name
				FROM expenses
				JOIN users ON expenses.user_id = users.user_id
				WHERE expenses.user_id = :employee_id
				AND expenses.report_date BETWEEN :start_date AND :end_date";
		$prepared = $db -> prepare($sql);
		$prepared -> bindParam(':employee_id', $employee_id);
		$prepared -> bindParam(':start_date', $start_date);
		$prepared -> bindParam(':end_date', $end_date);

		$prepared -> execute();
		$expenses = $prepared -> fetchAll();

		$output = "<table class='table-style'>
					<tr>
						<th>Employee</th>
						<th>Category</th>
						<th>Description</th>
						<th>Report date</th>
						<th>Amount</th>
						<th>Status</th>		
					</tr>";

		$total_amount = 0;
		foreach ($expenses as $expense) {
			$output .= "<tr>
							<td>{$expense['employee_name']}</td>
							<td>{$expense['category']}</td>
							<td>{$expense['description']}</td>
							<td>{$expense['report_date']}</td>
							<td>{$expense['amount']}</td>
							<td>{$expense['status']}</td>
						</tr>";
			
			$total_amount += $expense['amount'];
		}

		$output .= "<tr>
                        <td colspan='4'><strong>Total Expenses</strong></td>
                        <td><strong>{$total_amount}</strong></td>
                        <td><strong>" . count($expenses) . "</strong></td>
                    </tr>";

        $output .= "</table>";

        return $output;
	} catch (PDOException $e) {
		echo 'Database error: ' . $e->getMessage();
	}
}

function delete_expense($expense_id, $user_id) {
	try {
		$res = load_config(
			dirname(__FILE__)."/configuration.xml",
			dirname(__FILE__)."/configuration.xsd"
		);
		$db = new PDO($res[0], $res[1], $res[2]);

		$prepared = $db -> prepare("SELECT report_id FROM expenses WHERE report_id = ? AND user_id = ? AND status IN ('pending')");
		$prepared -> execute([$expense_id, $user_id]);

		if ($prepared -> rowCount() === 0) {
			return "Expense not found or cannot be deleted.";
		}

		$delete_exp = $db -> prepare("DELETE FROM expenses WHERE report_id = ?");
		$delete_exp -> execute([$expense_id]);

		return true;
	} catch (PDOException $e) {
		return "Error: " . $e -> getMessage();
	}
}

function getFilteredReports() {
	try {
		$res = load_config(
			dirname(__FILE__)."/configuration.xml",
			dirname(__FILE__)."/configuration.xsd"
		);
		$db = new PDO($res[0], $res[1], $res[2]);
		$user_id = $_SESSION['user']['user_id'];
		$status_filter = isset($_GET['status_filter']) ? $_GET['status_filter'] : '';

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
                            <td>$created</td>";
			
			if ($status === 'pending') {
				$output .="<td>
								<form method='POST' action='main.php'>
									<input type='hidden' name='expense_id' value='$rep_id'>
									<button type='submit' name='delete_expense'>Delete</button>
								</form>
						   </td>";
			} else {
				$output .= "<td></td>";
			}
			
        	$output .= "</tr>";
		}
		return $output;
	} catch (PDOException $e) {
		echo 'Database error: ' . $e -> getMessage(); 
	}
}

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

            $updateStatusQuery = $db->prepare("UPDATE expenses SET status = ?  WHERE report_id = ?");
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
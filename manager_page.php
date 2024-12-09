<?php
	require_once 'db_config.php';
	session_start();
	if (!isset($_SESSION['user'])) {
		header("Location: login.php?redirected=true");
		exit;
	} else {
		if ($_SESSION['user']['user_role'] !== 1) {
			header("Location: main.php?redirected=true");
			exit;
		}
	}

	$res = load_config(dirname(__FILE__) . "/configuration.xml", dirname(__FILE__) . "/configuration.xsd"); 
    $cadena_conexion = 'mysql:dbname=employee_expenses;host=127.0.0.1'; 
    $usuario = 'root'; 
    $clave = ''; 
	
?>
<!DOCTYPE html>
<html>
	<head>
		<meta charset="UTF-8">
    	<meta name="viewport" content="width=device-width, initial-scale=1.0">
		<title>Expenses</title>
		<link rel="stylesheet" href="styles.css">
	</head>
	<body>
		<?php require 'header.php'; ?>
		<h1> Expense management </h1>
		<?php echo "<h3>User: " . $_SESSION['user']['name'] . "</h3>"; ?>
		<table class="table-style">
		<tr>
            <th>Employee</th>
            <th>Category</th>
            <th>Description</th>
            <th>Report date</th>
            <th>Amount</th>
            <th>Status</th>
            <th>Created at</th>
        </tr>		
		<?php
            try { 
                $bd = new PDO($cadena_conexion, $usuario, $clave); 				
                $sql = 'SELECT expenses.*, users.name AS employee_name FROM expenses JOIN users ON expenses.user_id = users.user_id'; 
                $reports = $bd->query($sql); 				
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
                    echo "<tr>
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
            } catch (PDOException $e) { 
                echo 'Database error: ' . $e->getMessage(); 
            }
		?>
	</table>
		<br><a href="logout.php"> Logout <a>
	</body>
</html>
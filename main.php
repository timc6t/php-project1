<?php
    require 'sessions.php';
    require_once 'db_config.php';
    check_session();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Main page</title>
</head>
<body>
    <?php require 'header.php'; ?>
    <h1>Your expenses</h1>

    <!-- List here all of the expenses that the employee has -->

    <table>
		<tr><th>Category</th><th>Description</th><th>Report date</th><th>Amount</th><th>Status</th><th>Created at</th></tr>		
		<?php
            $res = load_config(dirname(__FILE__) . "/configuration.xml", dirname(__FILE__) . "/configuration.xsd"); 
            $cadena_conexion = 'mysql:dbname=employee_expenses;host=127.0.0.1'; 
            $usuario = 'root'; 
            $clave = ''; 
            try { 
                $bd = new PDO($cadena_conexion, $usuario, $clave); 				
                $sql = 'SELECT * FROM expenses'; 
                $users = $bd->query($sql); 				
                foreach ($users as $usu) {
                    //$rep_id = $usu['report_id'];
                    $user_id = $usu['user_id'];
                    $cat = $usu['category'];
                    $desc = $usu['description'];
                    $repDate = $usu['report_date'];
                    $amount = $usu['amount'];
                    $status = $usu['status'];
                    $created = $usu['created_at'];
                    echo "<tr><td>$cat</td><td>$desc</td><td>$repDate</td><td>$amount</td><td>$status</td><td>$created</td></tr>";
                }
            } catch (PDOException $e) { 
                echo 'Databse error: ' . $e->getMessage(); 
            }
		?>
	</table>

    <p><a href="new_expense.php">Add a new expense</a></p>
</body>
</html>
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

if ($_SERVER["REQUEST_METHOD"] == "GET" && isset($_GET['employee_id'], $_GET['start_date'], $_GET['end_date'])) {
    $employee_id = $_GET['employee_id'];
    $start_date = $_GET['start_date'];
    $end_date = $_GET['end_date'];

    $report = generateExpense($employee_id, $start_date, $end_date);
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manager Page</title>
    <link rel="stylesheet" href="styles.css">
</head>
<body>
    <?php require 'header.php'; ?>
    <h1>Generate an employee's report</h1>

    <form method="GET" action="generate_report.php">
        <label for="employee_id">Select Employee: </label>
        <select name="employee_id" id="employee_id">
            <?php
            $employees = getEmployees(); 
            foreach ($employees as $employee) {
                echo "<option value='{$employee['user_id']}'>{$employee['name']}</option>";
            }
            ?>
        </select><br><br>

        <label for="start_date">Start Date: </label>
        <input type="date" id="start_date" name="start_date" required><br><br>

        <label for="end_date">End Date: </label>
        <input type="date" id="end_date" name="end_date" required><br><br>

        <button type="submit">Generate Report</button>
    </form>

    <hr>

    <?php
    if (isset($report)) {
        echo "<h2>Expense Report for Employee ID: $employee_id</h2>";
        echo $report;
    }
    ?>

</body>
</html>
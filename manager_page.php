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
		<ul><a href="manage_reports.php">Manage all expense reports status</a></ul>
		<ul><a href="generate_report.php">Generate an employee's expense report</a></ul>
		<br><a href="logout.php"> Logout <a>
	</body>
</html>
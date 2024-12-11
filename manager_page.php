<?php
/**
 * Manages the expenses page for the manager user.
 * 
 * This script handles the expense reports page for users with admin privileges. It ensures 
 * that only logged-in users with an admin role (user role 1) can access this page. If the 
 * user is not logged in or does not have the appropriate role, they are redirected to 
 * the login page or the main page accordingly. The script also includes the functionality 
 * to download the expense reports in PDF format.
 * 
 * If the user requests to download the expenses in PDF, the script fetches all expenses 
 * and generates a PDF document.
 * 
 * @throws Exception If there is an error while generating the PDF (e.g., if fetching expenses fails).
 */
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

if (isset($_GET['download_pdf'])) {
	try {
		$expenses = fetchExpenses();
		generatePDF($expenses);
	} catch (Exception $e) {
		echo $e -> getMessage();
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
		<?php echo getStatusReports(); ?>
	</table>
	<form method="GET" action="manager_page.php">
		<button type="submit" name="download_pdf">Download expenses in PDF</button>
	</form>
		<br><a href="logout.php"> Logout <a>
	</body>
</html>
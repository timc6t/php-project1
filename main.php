<?php
/**
 * Main page for displaying user expenses and managing filters.
 * 
 * This script serves as the main page where users can view their expenses. The page provides
 * a dropdown filter to view expenses by their current status (Pending, Approved, Denied).
 * It also includes a link to add new expenses. The user session is checked at the start, and
 * only authenticated users can access this page.
 * 
 * The script uses the 'getFilteredReports()' function to fetch and display expenses based on
 * the selected status filter, if any.
 * 
 * @throws Exception If fetching the filtered reports fails (e.g., database connection issues).
 */
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
    <link rel="stylesheet" href="styles.css">
</head>
<body>
    <?php require 'header.php'; ?>
    <h1>Your expenses</h1>

    <form method="GET" action="main.php">
        <label for="status_filter">Filter by status: </label>
        <select name="status_filter" id="status_filter">
            <option value="">All</option>
            <option value="pending" <?php echo isset($_GET['status_filter']) && $_GET['status_filter'] == 'pending' ? 'selected' : ''; ?>>Pending</option>
            <option value="approved" <?php echo isset($_GET['status_filter']) && $_GET['status_filter'] == 'approved' ? 'selected' : ''; ?>>Approved</option>
            <option value="denied" <?php echo isset($_GET['status_filter']) && $_GET['status_filter'] == 'denied' ? 'selected' : ''; ?>>Denied</option>
        </select>
        <button type="submit">Apply filter</button>
    </form>

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
		<?php echo getFilteredReports(); ?>
	</table>

    <p><a href="new_expense.php">Add a new expense</a></p>
</body>
</html>
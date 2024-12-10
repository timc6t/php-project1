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

    <!-- TODO: List here all of the expenses that the employee has. -->
    <!-- For now it only shows all the expenses from all the employees. -->

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
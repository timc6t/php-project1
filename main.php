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
    <h1>Welcome, <?php echo htmlspecialchars($user['user_name']) ?></h1>
    <h2>Your expenses</h2>

<!-- Do a table of the expenses here -->

    <p><a href="new_expense.php">Add a new expense</a></p>
</body>
</html>
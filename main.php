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

    <p><a href="new_expense.php">Add a new expense</a></p>
</body>
</html>
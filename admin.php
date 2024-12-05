<?php
/**
 * Admin page script
 * 
 * This script handles the admin page. It checks the role of the user that is entering the
 * page. (To be described in depth later)
 */
    session_start();
    if (!isset($_SESSION['user'])) {
        header("Location: login.php?redirected=true");
        exit;
    } else {
        if ($_SESSION['user']['user_role'] !== 2) {
            header("Location: main.php?redirected=true");
            exit;
        }
    }
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Support and administration</title>
</head>
<body>
    <?php require 'header.php'; ?>
    <h1>Administration zone</h1>
    <?php echo '<h3>User: ' . $_SESSION['user']['name'] . '</h3>' ;?>
    <p>Manage the roles of the employees (normal employees and managers) in this page.</p>
    <?php echo '<br><a href="register.php">Register a new employee</a>'; ?>
    <!-- List all the users that are in the database in here. Add the option to edit their roles. -->
</body>
</html>
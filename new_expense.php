<?php
    require_once 'sessions.php';
    require 'db_config.php';
    check_session();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Add New Expense</title>
</head>
<body>
    <?php require 'header.php'; ?>
    
    <h1>Add New Expense</h1>

    <?php /*if ($message): ?>
        <p><?php echo $message; ?></p>
    <?php endif; */?>

    <form action="new_expense.php" method="POST">
        <!-- <label for=""></label><br>
        <input type="" id="" name="" required><br><br> -->

        <label for="category">Category:</label><br>
        <input type="text" id="category" name="category" required><br><br>

        <label for="description">Description:</label><br>
        <input type="text" id="description" name="description"><br><br>

        <label for="date">Date:</label><br>
        <input type="date" id="date" name="date" required><br><br>

        <label for="amount">Amount:</label><br>
        <input type="number" step="0.01" id="amount" name="amount" required><br><br>

        <input type="file" name="file"><br><br>

        <button type="submit">Add Expense</button>
    </form>
</body>
</html>

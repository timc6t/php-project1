<?php
require_once 'sessions.php';
require 'db_config.php';
check_session();

$user_id = $_SESSION['user']['user_id'] ?? null;

if ($user_id === null) {
    echo "Error: No user ID found. Please, log in.";
    exit;
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $cat = $_POST['category'];
    $desc = $_POST['description'];
    $amount = $_POST['amount'];
    $created_at = $_POST['report_date'];
    $status = 'pending';

    try {
        $message = addExpense($user_id, $cat, $desc, $amount, $created_at);
        echo $message;
    } catch (PDOException $e) {
        echo "Error: " . $e -> getMessage();
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Add New Expense</title>
</head>
<body>
    <?php require 'header.php'; ?>
    
    <h1>Write a new expense</h1>

    <form action="new_expense.php" method="POST">
        <label for="category">Category:</label><br>
        <input type="text" id="category" name="category" required><br><br>

        <label for="description">Description:</label><br>
        <input type="text" id="description" name="description"><br><br>

        <label for="amount">Amount:</label><br>
        <input type="number" step="0.01" id="amount" name="amount" required><br><br>

        <label for="report_date">Date:</label><br>
        <input type="date" id="report_date" name="report_date" required><br><br>

        <button type="submit">Add Expense</button>
    </form>
</body>
</html>

<?php
    require_once 'sessions.php';
    require 'db_config.php';
    check_session();

    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        $cat = $_POST['category'];
        $desc = $_POST['description'];
        $amount = $_POST['amount'];
        $created_at = $_POST['report_date'];
        // $status = $_POST['status'];

        try {
            list($dsn, $user, $db_password) = load_config(dirname(__FILE__) . "/configuration.xml", dirname(__FILE__) . "/configuration.xsd");
            $db = new PDO($dsn, $user, $db_password);
            $prepared = $db -> prepare("INSERT INTO expenses (category, description, amount, report_date) VALUES (?, ?, ?, ?)");
            $prepared -> execute([$cat, $desc, $amount, $created_at]);

            echo "Report succesfully created";
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

        <label for="amount">Amount:</label><br>
        <input type="number" step="0.01" id="amount" name="amount" required><br><br>

        <label for="report_date">Date:</label><br>
        <input type="report_date" id="report_date" name="report_date" required><br><br>

        <!-- <input type="file" name="file"><br><br> -->

        <button type="submit">Add Expense</button>
    </form>
</body>
</html>

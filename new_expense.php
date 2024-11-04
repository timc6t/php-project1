<?php
// MUST BE EDITED LATER. IT IS NOT COMPLETE.
require_once 'db_config.php';

$message = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Retrieve form data and sanitize it
    $employee_id = filter_input(INPUT_POST, 'employee_id', FILTER_VALIDATE_INT);
    $date = filter_input(INPUT_POST, 'date', FILTER_SANITIZE_STRING);
    $amount = filter_input(INPUT_POST, 'amount', FILTER_VALIDATE_FLOAT);
    $description = filter_input(INPUT_POST, 'description', FILTER_SANITIZE_STRING);
    $category = filter_input(INPUT_POST, 'category', FILTER_SANITIZE_STRING);

    // Validate the required fields
    if ($employee_id && $date && $amount && $category) {
        // Add the expense to the database
        if (add_expense($employee_id, $date, $amount, $description, $category)) {
            $message = 'Expense added successfully!';
        } else {
            $message = 'Failed to add expense.';
        }
    } else {
        $message = 'Please fill in all required fields.';
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
    <?php
        require 'header.php';
    ?>
    <h1>Add New Expense</h1>

    <?php if ($message): ?>
        <p><?php echo $message; ?></p>
    <?php endif; ?>

    <form action="new_expense.php" method="POST">
        <label for="employee_id">Employee ID:</label><br>
        <input type="number" id="employee_id" name="employee_id" required><br><br>

        <label for="date">Date:</label><br>
        <input type="date" id="date" name="date" required><br><br>

        <label for="amount">Amount:</label><br>
        <input type="number" step="0.01" id="amount" name="amount" required><br><br>

        <label for="description">Description:</label><br>
        <input type="text" id="description" name="description"><br><br>

        <label for="category">Category:</label><br>
        <input type="text" id="category" name="category" required><br><br>

        <button type="submit">Add Expense</button>
    </form>
</body>
</html>

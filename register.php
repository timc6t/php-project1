<?php
require_once 'db_config.php';
session_start();

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = $_POST['email'];
    $name = $_POST['name'];
    $password = $_POST['password'];
    $role = $_POST['role'];

    if (empty($email) || empty($name) || empty($password)) {
        echo "All fields are required.";
        exit;
    }
    
    $hashed_password = password_hash($password, PASSWORD_DEFAULT);

    $result = register_user($email, $name, $hashed_password, $role);

    if (is_array($result)) {
        $new_user = $result;
    } else {
        echo "Error: " . $result;
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register</title>
</head>
<body>
    <h2>Register</h2>
    <form method="post" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>">
        <label for="email">Email:</label><br>
        <input type="email" id="email" name="email" required><br><br>

        <label for="name">Name:</label><br>
        <input type="name" id="name" name="name" required><br><br>

        <label for="password">Password:</label><br>
        <input type="password" id="password" name="password" required><br><br>

        <label for="role">Role of the employee:</label><br>
        <select name="role" required>
            <option value="0">Employee</option>
            <option value="1">Manager</option>
            <option value="2">IT support</option>
        </select><br><br>
        
        <input type="submit" value="Register">
    </form><br><br>
    <?php if (!empty($new_user)): ?>
        <div>
            <p><strong>Registered correctly</strong></p>
            <p>ID: <?php echo htmlspecialchars($new_user['user_id']); ?></p>
            <p>Email: <?php echo htmlspecialchars($new_user['email']); ?></p>
            <p>Name: <?php echo htmlspecialchars($new_user['name']); ?></p>
            <p>Role: <?php echo htmlspecialchars($new_user['user_role']); ?></p>
        </div><br>
    <?php endif; ?>
    <a href="admin.php">Go back</a>
</body>
</html>
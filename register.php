<?php
/**
 * User Registration Script
 * 
 * This script handles the user registration process. It allows an administrator to create a
 * new user by providing their email, name, password, and role. The password is intended to
 * be hashed before storage (though currently the password is not hashed in this version).
 * After the user is registered, their details are displayed, including the user ID, email,
 * name, and role.
 * 
 * The form submits the user data via POST, and the backend inserts the new user's data into
 * the 'users' table in the database. If the registration is successful, the newly registered
 * user's details are displayed.
 * 
 * @throws PDOException If there is an error connecting to the database or executing the
 *                      queries.
 * 
 * @global PDO    $db          The database connection object.
 * @global string $dsn         The Data Source Name for the database connection.
 * @global string $user        The database username.
 * @global string $db_password The database password.
 * @global array  $new_user    The details of the newly registered user after successful 
 *                             registration.
 */
require_once 'db_config.php';
session_start();

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = $_POST['email'];
    $name = $_POST['name'];
    $password = $_POST['password'];
    $role = $_POST['role'];

    // Añadir la siguiente línea más tarde
    // $hashed_password = password_hash($password, PASSWORD_DEFAULT);
    // $hashed_password = $password;  Eliminar más tarde

    $new_user = register_user($email, $name, $password, $role);
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
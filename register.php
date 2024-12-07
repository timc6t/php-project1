<?php
    require_once 'db_config.php';
    session_start();

    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        $email = $_POST['email'];
        $name = $_POST['name'];
        $password = $_POST['password'];
        $role = $_POST['role'];

        // Añadir la siguiente línea más tarde
        // $hashed_password = password_hash($password, PASSWORD_DEFAULT);
        $hashed_password = $password; // Eliminar más tarde
        
        try {
            list($dsn, $user, $db_password) = load_config(
                dirname(__FILE__) . "/configuration.xml",
                dirname(__FILE__) . "/configuration.xsd"
            );
            $db = new PDO($dsn, $user);
            $prepared = $db -> prepare("INSERT INTO users (email, name, user_password, user_role) VALUES (?, ?, ?, ?)");
            $prepared -> execute([$email, $name, $hashed_password, $role]);

            $new_user_id = $db -> lastInsertId();

            $stmt = $db -> prepare("SELECT * FROM users WHERE user_id = ?");
            $stmt -> execute([$new_user_id]);
            $new_user = $stmt -> fetch(PDO::FETCH_ASSOC);
        } catch(PDOException $e) {
            echo "<p style = 'color: red;'>Error: " . $e -> getMessage() . "</p>";
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
    <!-- TODO: Show data from the recently registered user -->
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
<?php
    require_once 'db_config.php';
    session_start();

    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        $email = $_POST['email'];
        $password = $_POST['password'];

        // Añadir la siguiente línea más tarde
        // $hashed_password = password_hash($password, PASSWORD_DEFAULT);
        $hashed_password = $password; // Eliminar más tarde
        
        try {
            //$db = new PDO($result[0], $result[1], $result[2]);
            list($dsn, $user, $db_password) = load_config(dirname(__FILE__) . "/configuration.xml", dirname(__FILE__) . "/configuration.xsd");
            $db = new PDO($dsn, $user);
            $prepared = $db -> prepare("INSERT INTO users (email, user_password) VALUES (?, ?)");
            $prepared -> execute([$email, $hashed_password]);

            echo "Registration successful!";
        } catch(PDOException $e) {
            echo "Error: " . $e -> getMessage();
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
        <label for="email">Email: </label><br>
        <input type="email" id="email" name="email" required><br><br>

        <label for="password">Password: </label><br>
        <input type="password" id="password" name="password" required><br><br>

        <input type="submit" value="Register">
    </form>
    <p>Already have an account? <a href="login.php">Login here</a>.</p>
</body>
</html>
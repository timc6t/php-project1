<?php
    require_once 'db_config.php';
    session_start();

    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        $email = $_POST['email'];
        $password = $_POST['password'];

        $hashed_password = password_hash($password, PASSWORD_DEFAULT);
        
        try {
            $db = new PDO($result[0], $result[1], $result[2]);
            //$db = new PDO($dns, $username, $password);
            $prepared = $db -> prepare("INSERT INTO users (email, user_password) VALUES (?, ?)");
            $prepared -> execute([$email, $password]);

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

        <label for="password">Email: </label><br>
        <input type="password" id="password" name="password" required><br><br>

        <input type="submit" value="Register">
    </form>
</body>
</html>
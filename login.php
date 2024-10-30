<?php
/**
 * User login script
 * 
 * This script handles user login by checking the provided email and password against the
 * database. It initiates a session to store user information upon successful login and
 * redirects to the header page. If the login fails, appropiate error messages are displayed.
 * 
 * @throws PDOException If there is an error connecting to the database or executing the
 * query.
 * @throws InvalidArgumentException If the configuration file is invalid or cannot be loaded.
 */

    session_start(); 
    /* session_start() has been moved here due to it being undefined in the else block. */
    require_once 'db_config.php';

    if($_SERVER["REQUEST_METHOD"] == "POST") {
        try {
            $usu == check_user($_POST['user'], $_POST['user_password']);

            if (!$usu) {
                $err = true;
                $user = $_POST['user'];
            } else {
                $_SESSION['user'] = $usu;
                header("Location: header.php");
                exit;
            }
        } catch (PDOException $e) {
            echo 'Database error: ' . $e -> getMessage();
        } catch (InvalidArgumentException $e) {
            echo 'Configuration file error: ' . $e -> getMessage();
        }
    }
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login</title>
</head>
<body>
    <h1>Login</h1>
    <?php
        /**
         * Displays a message if the user was redirected after logging out.
         */
        if(isset($_GET["redirected"])) {
            echo "<p>Login to continue</p>";
        }
    ?>

    <?php
        /**
         * Displays an error message if the login attempt fails.
         */
        if(isset($err) and $err == TRUE) {
            echo "<p>Check user and password</p>";
        }
    ?>
    <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="POST">
        <label for="user">User: </label><br>
        <input value="<?php if(isset($user)) echo $user; ?>" id="user" name="user" type="text" placeholder="Email" required><br><br>

        <label for="password">Password: </label><br>
        <input id="password" name="password" type="password" placeholder="Password" required><br><br>

        <button type="submit">Login</button>
    </form>
</body>
</html>
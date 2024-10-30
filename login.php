<?php
    session_start(); //It has been moved here due to it being undefined in the else block.
    require_once 'db_config.php';

    if($_SERVER["REQUEST_METHOD"] == "POST") {
        try {
            $usu == check_user($_POST['user'], $_POST['user_password']);

            if (!$usu) {
                $err = true;
                $user = $_POST['user'];
            } else {
                //session_start()
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
    <!--PHP redirection-->
    <?php
        // When the user has logged out, it redirects to this with the following message:
        if(isset($_GET["redirected"])) {
            echo "<p>Login to continue</p>";
        }
    ?>

    <?php
        // If nothing has been typed into the inputs and the user clicks on Login, it will ask the user to check their email and password
        if(isset($err) and $err == TRUE) {
            echo "<p>Check user and password</p>";
        }
    ?>
    <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="POST">
        <label for="user">User: </label>
        <input value="<?php if(isset($user)) echo $user; ?>" id="user" name="user" type="text" placeholder="Email" required>

        <label for="password">Password: </label>
        <input id="password" name="password" type="password" placeholder="Password" required>

        <button type="submit">Login</button>
    </form>
</body>
</html>
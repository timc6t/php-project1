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

    require_once 'db_config.php';

    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        try {
            $usu = check_user($_POST['user'], $_POST['user_password']);

            if ($usu === false) {
                $err = true;
                $user = $_POST['user'];
            } else {
                session_start();
                // print_r($_SESSION['user']);

                $_SESSION['user'] = $usu;
                // $_SESSION['user_id']['email'] = $usu['email'];
                // var_dump(($_SESSION['user']['user_role']));
                
                switch ($_SESSION['user']['user_role']) {
                    case 2:
                        header("Location: admin.php");
                        exit;
                    case 1:
                        header("Location: manager_page.php");
                        exit;
                    default:
                        header("Location: main.php");
                        exit;
                }

                /*if ($_SESSION['user']['user_role'] == 2) {
                    header("Location: admin.php");
                } elseif ($_SESSION['user']['user_role'] == 1) {
                    header("Location: manager_page.php");
                } else {
                    header("Location: main.php");
                }
                exit;*/
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
        if(isset($_GET["redirected"])) {
            echo "<p>Login to continue</p>";
        }
    ?>

    <?php
        if(isset($err) and $err == TRUE) {
            echo "<p>Check user and password</p>";
        }
    ?>
    
    <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="POST">
        <label for="user">Email: </label><br>
        <input value="<?php if(isset($user)) echo $user; ?>" id="user" name="user" type="text" placeholder="Email" required><br><br>

        <label for="user_password">Password: </label><br>
        <input id="user_password" name="user_password" type="password" placeholder="Password" required><br><br>

        <button type="submit">Login</button>
    </form>
    <!-- There is a register.php file for entering new users but normal employees cannot access it -->
    <!-- <p>No account yet? Click <a href='register.php'>here</a> in order to get your account.</p> -->
</body>
</html>
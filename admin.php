<?php
/**
 * Admin page script
 * 
 * This script handles the admin page. It checks the role of the user that is entering the
 * page. (To be described in depth later)
 */
    require_once 'db_config.php';
    session_start();
    if (!isset($_SESSION['user'])) {
        header("Location: login.php?redirected=true");
        exit;
    } else {
        if ($_SESSION['user']['user_role'] !== 2) {
            header("Location: main.php?redirected=true");
            exit;
        }
    }

    try {
        list($dsn, $user, $db_password) = load_config(
            dirname(__FILE__) . "/configuration.xml",
            dirname(__FILE__) . "/configuration.xsd"
        );
        $db = new PDO($dsn, $user);

        if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST['update_role'])) {
            $user_id = $_POST['user_id'];
            $new_role = $_POST['user_role'];

            $prepared = $db -> prepare("UPDATE users SET user_role = ? WHERE user_id = ?");
            $prepared -> execute([$new_role, $user_id]);

            echo "<p style='color: green;'>User role updated successfully</p>";
        }

        $prepared = $db -> prepare("SELECT user_id, email, name, user_role FROM users");
        $prepared -> execute();
        $users = $prepared -> fetchAll(PDO::FETCH_ASSOC);
    } catch (PDOException $e) {
        echo "<p style = 'color: red;'>Error: " . $e -> getMessage() . "</p>";
    }
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Support and administration</title>
</head>
<body>
    <?php require 'header.php'; ?>
    <h1>Administration zone</h1>
    <?php echo '<h3>User: ' . $_SESSION['user']['name'] . '</h3>' ;?>
    <p>
        Manage the roles of the employees (normal employees and managers) in this page.<br><br>

        Notes:<br>
        1) Roles indicated with a 2 are for administrators while those with 1 are for managers. Roles with 0 are for the rest of the employees.<br>
        2) For this project only, all passwords are 12345. They will be hashed later but before that they might be changed.
    </p>
    <h2>List of employees</h2>
    <table class="table-style">
        <thead>
            <tr>
                <th>ID</th>
                <th>Email</th>
                <th>Name</th>
                <th>Role</th>
                <th>Change role</th>
            </tr>
        </thead>
        <tbody>
            <?php if (!empty($users)): ?>
                <?php foreach ($users as $user): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($user['user_id']); ?></td>
                        <td><?php echo htmlspecialchars($user['email']); ?></td>
                        <td><?php echo htmlspecialchars($user['name']); ?></td>
                        <td><?php echo htmlspecialchars($user['user_role']); ?></td>
                        <td>
                            <form method="POST" action="">
                                <input type="hidden" name="user_id" value="<?php echo htmlspecialchars($user['user_id']); ?>">
                                <select name="user_role">
                                    <option value="0" <?php echo $user['user_role'] == 0 ? 'selected' : ''; ?>>Employee</option>
                                    <option value="1" <?php echo $user['user_role'] == 1 ? 'selected' : ''; ?>>Manager</option>
                                    <option value="2" <?php echo $user['user_role'] == 2 ? 'selected' : ''; ?>>IT Support</option>
                                </select>
                                <button type="submit" name="update_role">Change role</button>
                            </form>
                        </td>
                    </tr>
                <?php endforeach; ?>
            <?php else: ?>
                <tr>
                    <td colspan="5">No users found.</td>
                </tr>
            <?php endif; ?>
        </tbody>
    </table>
    <?php echo '<br><a href="register.php">Register a new employee</a>'; ?>
    <!-- List all the users that are in the database in here. Add the option to edit their roles. -->
</body>
</html>
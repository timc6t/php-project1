<?php
/**
 * User Header Section
 * 
 * This header section displays user-specific navigation links based on their role. It shows
 * the user's email and provides links to manage expenses, add expenses, and log out. If the
 * user has a manager role (role 1), they will be given the option to manage expenses. If
 * they are an IT support user (role 2), they will see the admin panel link. The header is
 * dynamically generated based on the user's role, using session data.
 * 
 * @global array  $_SESSION['user']              Contains the session information for the 
 *                                               logged-in user.
 * @global string $_SESSION['user']['email']     The email of the logged-in user.
 * @global int    $_SESSION['user']['user_role'] The role of the user, where 1 is Manager, 
 *                                               2 is IT support, etc.
 * 
 * @return void Outputs the navigation links.
 */
?>

<header>
    User: <?php echo $_SESSION['user']['email']; ?>
    <a href="main.php">My expenses</a>
    <a href="new_expense.php">Add expenses</a>
    <?php
    if ($_SESSION['user']['user_role'] == 2) {
        echo '<a href="admin.php">Admin panel</a>';
    } elseif ($_SESSION['user']['user_role'] == 1) {
        echo '<a href="manager_page.php">Manage expenses</a>';
    }
    ?>
    <a href="logout.php">Log out</a>
</header>
<hr>
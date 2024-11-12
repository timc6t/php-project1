<header>
    User: <?php echo $_SESSION['user']['email']; ?>
    <a href="main.php">My expenses</a>
    <a href="new_expense.php">Add expenses</a>
    <?php
    if ($_SESSION['user']['user_role'] == 2) {
        echo '<a href="admin.php">Admin panel</a>';
    } elseif ($_SESSION['user']['user_role'] == 1) {
        echo '<a href="manager_page.php">Approve expenses</a>';
    }
    ?>
    <a href="logout.php">Log out</a>
</header>
<hr>
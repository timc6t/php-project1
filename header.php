<header>
    <?php
        require_once 'db_config.php';
        
        //$email = get_email();
    ?>
    <!-- User: <?php echo htmlspecialchars($email) ?> -->
    User: <?php echo get_email(); ?>
    <a href="main.php">My expenses</a>
    <a href="new_expense.php">Add expenses</a>
    <!-- <a href="">Expenses status</a> See how to do the expenses status-->
    <a href="logout.php">Log out</a>
</header>
<hr>
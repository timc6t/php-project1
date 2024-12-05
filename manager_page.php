<?php 
	session_start();
	if (!isset($_SESSION['user'])) {
		header("Location: login.php?redirected=true");
		exit;
	} else {
		if ($_SESSION['user']['user_role'] !== 1) {
			header("Location: main.php?redirected=true");
			exit;
		}
	}
	
?>
<!DOCTYPE html>
<html>
	<head>
		<title>Expenses</title>	
		<meta charset = "UTF-8">
	</head>
	<body>
		<?php require 'header.php'; ?>
		<h1> Expense management </h1>
		<?php echo "<h3>User: " . $_SESSION['user']['name'] . "</h3>"; ?>
		<br><a href="logout.php"> Logout <a>
	</body>
</html>
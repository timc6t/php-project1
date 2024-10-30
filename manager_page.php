<?php 
	session_start();
	if(!isset($_SESSION['user'])){	
		header("Location: login.php?redirected=true");
		return;
	}else{
		if($_SESSION['user']['role']==0){
			header("Location: main.php?redirected=true");
			return;
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
		<p> Expense manager zone </p>
		<?php echo "Welcome manager" . $_SESSION['user']['name'];?>
		<br><a href="logout.php"> Logout <a>
	</body>
</html>
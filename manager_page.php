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
		<title>Admin zone</title>	
		<meta charset = "UTF-8">
	</head>
	<body>	
		<p> Admin zone </p>
		<?php echo "Welcome admin".$_SESSION['user']['name'];?>
		<br><a href = "activity_3_5_sessions1_logout.php"> Logout <a>
	</body>
</html>
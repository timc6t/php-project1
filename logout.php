<?php
require_once 'sessions.php';	
check_session();
$_SESSION = array();
session_destroy();	
setcookie(session_name(), 123, time() - 1000); 
?>

<!DOCTYPE html>
<html>
	<head>
		<meta charset = "UTF-8">
		<title>Session closed</title>
	</head>
	<body>
		<?php header("Location:login.php"); ?>
	</body>
</html>
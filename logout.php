<?php
/**
 * Session logout script
 * 
 * This script handles the user logout process by first checking the current session and then
 * clearing the session data. It destroys the session and removes the session cookie,
 * effectively logging the user out. After the session is destroyed, the user is redirected
 * to the login page.
 * 
 * @throws Exception If there is an issue with the session destruction or cookie removal
 * 					 (though typically not thrown in this code).
 */

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
<?php

function check_session(){
	session_start();
	if(!isset($_SESSION['user'])){	
		header("Location: login.php?redirected=true");
		exit;
	}		
}
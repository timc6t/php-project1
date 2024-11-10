<?php

function check_session(){
	if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }

	if(!isset($_SESSION['user'])) {	
		header("Location: login.php?redirected=true");
		exit;
	}		
}
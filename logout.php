<?php

// when redirected to this file: unset the User ID session and redirect to index.php, exit the php scripts after that

	session_start();													// start a new session
	unset($_SESSION['userID']);											// unset the current userID session
	echo '<meta http-equiv="Refresh" content="0; url=index.php" />';	// redirect to index.php
	exit;	
?>
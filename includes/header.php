<?php

	session_start();						// start user session when header is included
	error_reporting(E_ALL ^ E_NOTICE);		// set the error report level(Report all errors except E_Notice) for best practice

	require_once('includes/class.user.php');
	require_once('includes/class.view.php');
	
	$loginUser = new User();					// Instantiate the new User Class and assignt to the variable $loginUser
	
	if(isset($_SESSION['userID'])) 				// if the userID exsits (for signed up users only)
	{
		$loginUser->load($_SESSION['userID']);	// get the userID from the session array
	}
	//print_r($_SESSION['userID']);
												//(::) Scope Resolution Operator is a token that allows access to static, constant, and overridden properties or methods of a class. 
	echo View::renderDoctype();					// echo the Doctype
	echo View::renderHeader();					// echo the header
	echo View::renderHeaderData($loginUser); 	// echo the header Data and send the User Parameter
?>
		
	
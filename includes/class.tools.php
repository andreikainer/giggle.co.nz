<?php
require_once('config.php');
// Tools class contains all information website tools
class Tools {
	
	public static function passwordEncrypt($sString) // password hashing function
	{
		return sha1(md5(sha1($sString.PASSWORD_SALT)));
	// or return password_hash($sString, PASSWORD_DEFAULT);
	}
	
	public static function redirect($url) // redirect function
	{
		echo '<meta http-equiv="Refresh" content="0; url='.$url.'" />';
		exit;
	}
}
?>
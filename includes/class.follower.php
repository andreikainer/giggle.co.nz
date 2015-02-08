<?php
require_once('mysqlPDOAdaptor.php');

class Follower {
														// the Follower class contains all the properties and methods to handle Followers and Timeline (Favorites)
	private $iID;										// create the properties
	private $iUserID;
	private $iFollowerUserID;
	private $database;
	
	public function __construct() {						// assign the default magic method __construct() values
		$this->iID = 0;
		$this->iUserID = 0;
		$this->iFollowerUserID = 0;
		$this->database = Database::getInstance();
	}
		
	public function load($iID)							// function to load the Followers where ever needed. @param = integer ID
	{
		$sQuery = "SELECT id, user_id, follower_user_id FROM tblFollowers WHERE id = ".$iID; // query statement
		$rsData = $this->database->query($sQuery);			// process the query
		$aData = $this->database->fetch_array($rsData);		// fetch the data as an array
		$this->iID = $aData['id'];							// assign the results of the query
		$this->iUserID = $aData['user_id'];
		$this->iFollowerUserID = $aData['follower_user_id'];
	}
															// this method is used to unfollow 
	public function loadByData($iUserID, $iFollowerUserID) // function to load the Followers with 2 parameters. @param1 = integer User ID, @param2 = integer Follower User ID
	{
		$sQuery = "SELECT id, user_id, follower_user_id FROM tblFollowers WHERE user_id = ".$iUserID." and follower_user_id = ".$iFollowerUserID; // query statement
		$rsData = $this->database->query($sQuery);			// process the query
		$aData = $this->database->fetch_array($rsData);		// fetch the data as an array
		$this->iID = $aData['id'];							// assign the results of the query
		$this->iUserID = $aData['user_id'];
		$this->iFollowerUserID = $aData['follower_user_id'];
	}
	
	public function save()									// function to UPDATE and INSERT INTO the Database
    {
        if($this->iID > 0)									// if the ID exists (if the integer ID is higher then 0)=> update the row data
        {
	        $sQuery = "UPDATE tblFollowers SET user_id = ".$this->iUserID.", follower_user_id = ".$this->iFollowerUserID." WHERE id = ".$this->iID.";"; // query statement
	        $bResult = $this->database->query($sQuery);		// process the query
	        
	        if($bResult == false)							// if the boolean Result returns 'false'
	        {
		        die("Could not save Follower data");		// display the error message and exit the script
	        }
	    }
	    else {
		    $sQuery = "INSERT INTO tblFollowers (user_id, follower_user_id) VALUES(".$this->iUserID.",".$this->iFollowerUserID.");"; // query statement
	        $bResult = $this->database->query($sQuery);		// process the query
	        
	        if($bResult == false)							// if the boolean Result returns 'false'
	        {
		        die("Could not save a new follower");		// display the error message and exit the script
	        }
	        else {
		        $this->iID = $this->database->get_last_id(); // otherwise (if the query was successfull) get the id of the last inserted item
	        }
	    }
    }
	
	public function delete()								// function to delete a row entry in tblFolowers
	{
		if($this->iID > 0)									// if the ID exists (if the integer ID is higher then 0)=> update the row data
        {
        	$sQuery = "DELETE FROM tblFollowers WHERE id = ".$this->iID.";"; // query statement
	        $bResult = $this->database->query($sQuery);		// process the query
	        
	        if($bResult == false)							// if the boolean Result returns 'false'
	        {
		        die("Could not delete the Follower data");	// display the error message and exit the script
	        }

        }
	}
	
	public function __get($sProperty)						// Get the property. This can be accessed outside the class via magic method __get(property=string)
	{														// http://php.net/manual/en/language.oop5.overloading.php
        switch($sProperty)
        {
        	case 'ID' :										// return the properties which are fetched from the database at top of this class
                return $this->iID;
                break;
            case 'user' :
                if($this->iUserID > 0)						// if a User ID exists (is higher then 0)
            	{
	            	$user = new User();						// instanciate a new User Class and return it
	            	$user->load($this->iUserID);
	            	return $user;
            	}
            	else
                	return array();							// otherwise return array() to create a new user
            	break;
            case 'followerUser' :
                if($this->iFollowerUserID > 0)				// if the iFollowerUserID exists (is higher then 0)
            	{
	            	$user = new User();						// instanciate a new User Class and return the iFollowerUserID
	            	$user->load($this->iFollowerUserID);
	            	return $user;
            	}
            	else
                	return array();							// otherwise return array() to create a new user
            	break;
            default:
                die($sProperty. ' for GET in Class FOLLOWER does not exist'); // if the property is not set display the error message
        } 
    }
    
    public function __set($sProperty,$value)				// set the values of the property. This can be accessed outside the class via magic method __set(propert, value)
    {    
        switch($sProperty)									// set (overload) the properties to create a new Follower
        {
        	case 'userID' :
                $this->iUserID = $value;
                break;
            case 'followerUserID' :
                $this->iFollowerUserID = $value;
                break;
            default:
                die($sProperty. ' for SET in Class FOLLOWER does not exist'); // if the property is not set display the error message
        }
    }
	
	public static function ifIFollowUser($iMyUserID, $iFollowerUserID) // Function to check if I already follow this user, receiving my User ID and the FollowerUSerID
	{
		$database = Database::getInstance();
	    $sQuery = "SELECT id FROM tblFollowers WHERE user_id = ".$iMyUserID." and follower_user_id = ".$iFollowerUserID; // query statement
    	$rsData = $database->query($sQuery);
    	if($database->num_rows($rsData)>0)					// if a datebase entry was found 
    	{
	    	return true;									// return true
    	}
    	else 
    	{
	    	return false;									// otherwise return false
    	}
	}


	
	public static function getFollowPosts($iUserID)    // Display all posts by the users I am following under the menu Favorites
	{
		$database = Database::getInstance();
	    $sQuery = "SELECT follower_user_id from tblFollowers WHERE user_id = ".$iUserID;	// select (only one at a time) the User Id I want to add to my favorites
    	$rsData = $database->query($sQuery);												// process the query
    	$aFoll = $database->fetch_array($rsData);											// save the ID into an array
    	$aPosts = array();
    	if($aFoll['follower_user_id']>0)													// if a User ID was received - execute this block - add it to my favorites
    	{	
	    	$sPostWhereString = " WHERE (user_id = ".$aFoll['follower_user_id'].")"; 		// injecting the WHERE clause into the tblPost query
	    	$bFirstLine = true;

	    	while($aData = $database->fetch_array($rsData))									// while there is more ID's to fetch, put them into an array
	    	{	
	    		// if($bFirstLine)
	    		// {	
		    		$sPostWhereString .= " OR (user_id = ".$aData['follower_user_id'].")";	// concatenate the OR clause for each entry 
		    		//$bFirstLine = false;
	    		//}
	    		// else
	    		// {	
		    	// 	$sPostWhereString .= " OR (user_id = ".$aData['follower_user_id'].")";
	    		// }
	    		
	    	}
	    	
	    	$sQuery = "SELECT id FROM tblPost".$sPostWhereString." ORDER BY date_created DESC"; // generate the tblPost query
	    	$rsData = $database->query($sQuery);												// process the query
	    	while($aData = $database->fetch_array($rsData))										// fetch the data from the DB as an array while there is something to fetch
	    	{
	    		$pPost = new Post();					
	    		$pPost->load($aData['id']);														// instanciate a new Post Object and send it off to load function with tblPost id param
	    		$aPosts[] = $pPost;
	    	} 
	    } 													// else return an empty array which displays (There are no posts to show)
	    return $aPosts;														// return the object data 
	}
	
	public static function followUser($iUserID, $iFollowerUserID) // Function to follow user
	{
		$follower = new Follower();									
		$follower->userID = $iUserID;								// __set() magic function => set the new values for the new object
		$follower->followerUserID = $iFollowerUserID;
		$follower->save();											// save it into the database
	}
	
	public static function unfollowUser($iUserID, $iFollowerUserID) // Function to unfollow user
	{
		$follower = new Follower();
		$follower->loadByData($iUserID, $iFollowerUserID);			// call this method by sending 2 params, my own user id and the id i would like to delete
		$follower->delete();										// delete the entry
	}
    
}	// EOF Class unfollowUser
?>
<?php
require_once('mysqlPDOAdaptor.php');

class Comment {
															// the Comment class contains all the properties and methods to handle Comments throughout the script
	private $iID;											// create the properties
	private $sText;
	private $sDateCreated;
	private $iUserID;
	private $iPostID;
	private $database;
	
	public function __construct() {							// assign the default magic method __construct() values
		$this->iID = 0;
		$this->sText = "";
		$this->sDateCreated = "";
		$this->iUserID = 0;
		$this->iPostID = 0;
		$this->database = Database::getInstance();
	}
	
	public function load($iID)								// function to load the Comments whereever needed. @param = integer ID
	{
		$sQuery = "SELECT id, date_created, text, user_id, post_id FROM tblComment WHERE id = ".$iID; // query statement
		$rsData = $this->database->query($sQuery);			// process the query
		$aData = $this->database->fetch_array($rsData);		// fetch the data as an array
		$this->iID = $aData['id'];							// assign the results of the query
		$this->sText = $aData['text'];
		$this->sDateCreated = $aData['date_created'];
		$this->iUserID = $aData['user_id'];
		$this->iPostID = $aData['post_id'];
	}
	
	public function save()									// function to UPDATE and INSERT INTO the Database
    {
        if($this->iID > 0) // if a comment with an id ($aData['id']) already exists then UPDATE that row 
        {
	        $sQuery = "UPDATE tblComment SET text = '".$this->sText."', date_created = '".$this->sDateCreated."', user_id = ".$this->iUserID.", post_id = ".$this->iPostID." WHERE id = ".$this->iID.";";
	        $bResult = $this->database->query($sQuery);
	        
	        if($bResult == false)
	        {
		        die("Could not change the comment"); // error message for failed UPDATE
	        }
	    }
	    else { // else if no comment row id exists create one (INSTER INTO) and give it an id with AUTO INCREMENT
		    $sQuery = "INSERT INTO tblComment (date_created, text, user_id, post_id) VALUES('".$this->sDateCreated."','".$this->sText."',".$this->iUserID.",".$this->iPostID.");";
	        $bResult = $this->database->query($sQuery);
	        
	        if($bResult == false)
	        {
		        die("Could not save a new comment"); // error message for failed INSERT
	        }
	        else {
		        $this->iID = $this->database->get_last_id();		// otherwise (if the query was successfull) get the id of the last inserted item
		        // echo $this->iID;
	        }
	    }
    }
	
	public function __get($sProperty)				// Get the property. This can be accessed outside the class via magic method __get(property=string)
	{												// http://php.net/manual/en/language.oop5.overloading.php
        switch($sProperty)
        {
        	case 'ID' :								// return the properties which are fetched from the database at top of this class
                return $this->iID;
                break;
            case 'text' :
                return $this->sText;
                break;
            case 'date' :
                return $this->sDateCreated;
                break;
            case 'user' :
            	if($this->iUserID > 0)				// if a User ID exists (is higher then 0)
            	{
	            	$user = new User();
	            	$user->load($this->iUserID);	// instanciate a new User Class and return it
	            	return $user;
            	}
            	else
                	return array();					// otherwise return array() to create a new user
            	break;
            default:
                die($sProperty. ' for GET in Class COMMENT does not exist'); // if the property is not set display the error message
        } 
    }
    
    public function __set($sProperty,$value)		// set the values of the property. This can be accessed outside the class via magic method __set(propert, value)
    {    
        switch($sProperty)							// set (overload) the properties to create a new Comment
        {
        	case 'text' :
                $this->sText = $value;
                break;
            case 'date' :
                $this->sDateCreated = $value;
                break;
        	case 'userID' :
                $this->iUserID = $value;
                break;
            case 'postID' :
                $this->iPostID = $value;
                break;
            default:
                die($sProperty. ' for SET in Class COMMENT does not exist'); // if the property is not set display the error message
        }
    }
    
    public static function getPostComments($iPostID)		// function to display the Post Comments
    {
    	$database = Database::getInstance();
	    $sQuery = "SELECT id FROM tblComment WHERE post_id = ".$iPostID.' ORDER BY date_created ASC'; // query statement 
    	$rsData = $database->query($sQuery);				// process the query
    	$aComments = array();
    	while($aData = $database->fetch_array($rsData))		// fetch the data from the DB as an array while there is something to fetch
    	{
    		$comment = new Comment();						// instanciate a new Post Class and return it into the array $aComments
    		$comment->load($aData['id']);
	    	$aComments[] = $comment;
    	}
    	return $aComments;
    }
} //EOF Class Comment

?>
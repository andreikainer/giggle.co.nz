<?php
require_once('mysqlPDOAdaptor.php');
require_once('class.blog.php');
                                                        // the User class contains all the properties and methods to handle User information
class User {
	
	private $iID;                                       // create the properties
	private $sLogin;
	private $sPassword;
	private $bActive;
	private $sName;
	private $sEmail;
	private $sPhoto;
	private $iBlogID;
	private $database;
	
	public function __construct() {                           // construct the Object USER and give it default values
		$this->iID = 0;                                       // User Id = integer
		$this->sLogin = "";                                   // Username = string
		$this->sPassword = "";                                // Password = string
		$this->bActive = true;                                // status = boolean
		$this->sName = "";                                    // Name = string
		$this->sEmail = "";                                   // Email = string
		$this->sPhoto = "";                                   // img path = string
		$this->iBlogID = 0;                                   // Table Blog ID = integer
		$this->database = Database::getInstance();            // call the function getInstance() in the class Database
	}
	
	public function load($iID) 
    {
		$sQuery = "SELECT id, login, password, active, name, email, photo FROM tblUser WHERE id = ".$iID; // the query statement
		$rsData = $this->database->query($sQuery);            // query the database
		$aData = $this->database->fetch_array($rsData);       // fetch the data as an assosiative Array
		$this->iID = $aData['id'];                            // assign the results of the query
		$this->sLogin = $aData['login'];
		$this->sPassword = $aData['password'];
		$this->bActive = $aData['active'];
		$this->sName = $aData['name'];
		$this->sEmail = $aData['email'];
		$this->sPhoto = $aData['photo'];
		
		$sQuery = "SELECT id FROM tblBlog WHERE user_id = ".$iID; // the second query
    	$rsBlogs = $this->database->query($sQuery);               // query the database
    	$aBlog = $this->database->fetch_array($rsBlogs);          // fetch the data as an assosiative Array
    	if($aBlog)
    	{
    		$this->iBlogID = $aBlog['id'];                        // 
    	}
		
	}
	
	public function loadByLogin($login)
    {
		$sQuery = "SELECT id FROM tblUser WHERE login = '".$login."';";       // the statement
        $rsUsers = $this->database->query($sQuery);                           // query the database
		$aUser = $this->database->fetch_array($rsUsers);                      // fetch the data as an assosiative Array
		if($aUser)
		{
			$this->load($aUser['id']);                                       // set the many to many relationship
		}
		return $aUser;
    }
	
	public function save()
    {
        if($this->iID > 0)                                                  // if the user id exist UPDATE the date
        {
	        $sQuery = "UPDATE tblUser SET login = '".$this->sLogin."', password = '".$this->sPassword."', active = ".$this->bActive.", name = '".$this->sName."', email = '".$this->sEmail."', photo = '".$this->sPhoto."' WHERE id = ".$this->iID.";";
	        $bResult = $this->database->query($sQuery);
	        
	        if($bResult == false)
	        {
		        die("Could not save the user data");
	        }
	    }
	    else {                                                             // if the user id does not exist add a row to table User
		    $sQuery = "INSERT INTO tblUser (login, password, active, name, email, photo) VALUES('".$this->sLogin."','".$this->sPassword."',".$this->active.",'".$this->sName."','".$this->sEmail."', '".$this->sPhoto."');";
	        $bResult = $this->database->query($sQuery);
	        
	        if($bResult == false)
	        {
		        die("Could not save a new user");
	        }
	        else {
		        $this->iID = $this->database->get_last_id();
	        }
	    }
    }
	// Use switch inside of magical functions __get and __set:
    // http://stackoverflow.com/questions/17704038/how-to-access-multiple-properties-with-magic-method-get-set

	public function __get($sProperty)
	{
        switch($sProperty)                              // get the properties and replace with default value
        {
        	case 'ID' :
                return $this->iID;
                break;
            case 'login' :
                return $this->sLogin;
                break;
            case 'password' :
                return $this->sPassword;
                break;
            case 'active' :
                return $this->bActive;
                break;
            case 'name' :
                return $this->sName;
                break;
            case 'email' :
                return $this->sEmail;
                break;
            case 'photo' :
                return $this->sPhoto;
                break;
            case 'followPosts' :
            	if($this->iID > 0)                                     // if a entry exist...
            	{
            		return Follower::getFollowPosts($this->iID);      // get the post ID's from the database
            	}
            	else 
            		return array();                                   // otherwise return as an array
            	break;
            case 'blog' :
                if($this->iBlogID > 0)                                  // if the Blog id exits (is higher then 0)
                {
		    		$bBlog = new Blog();                                // instanciate a new class Blog and return it
		    		$bBlog->load($this->iBlogID);
		    		return $bBlog;
                }
                else
                	return array();
                break;   
            default:
                die($sProperty. ' does not exist');
        } 
    }
    
    public function __set($sProperty,$value)    // __set takes 2 params: $Key and $value
    {    
        switch($sProperty)
        {
        	case 'login' :
                $this->sLogin = $value;
                break;
            case 'password' :
                $this->sPassword = $value;
                break;
            case 'active' :
                $this->bActive = $value;        // set the active value 
                break;
            case 'name' :
                $this->sName = $value;
                break;
            case 'email' :
                $this->sEmail = $value;
                break;
            case 'photo' :
                $this->sPhoto = $value;
                break;
            default:
                die($sProperty. 'does not exist');
        }
    }
    
}
?>
<?php
	require_once('mysqlPDOAdaptor.php');
	require_once('class.category.php');


	
class Blog {
																// The Blog class contains all information about the user's blog
	private $iID;												// create the properties of the class 
	private $iUserID;
	private $bActive;
	private $sName;
	private $database;
	
	public function __construct() 								// assign the default magic method __construct() values
	{								
		$this->iID = 0;
		$this->iUserID = 0;
		$this->bActive = true;
		$this->sName = "";
		$this->database = Database::getInstance();
	}
	
	public function load($iID) 									// function to display the blogs whereever needed. @param = integer ID
	{
		$sQuery = "SELECT id, user_id, name, active from tblBlog WHERE id = ".$iID; // query statement
		$rsData = $this->database->query($sQuery);				// process the query
		$aData = $this->database->fetch_array($rsData);			// fetch the data as an array
		$this->iID = $aData['id'];								// assign the results of the query
		$this->iUserID = $aData['user_id'];
		$this->sName = $aData['name'];
		$this->bActive = $aData['active'];
	}
	
	public function save()										// function to UPDATE and INSERT INTO the Database
    {
        if($this->iID > 0)										// if the ID exists (if the integer ID is higher then 0) => update the row data
        {
	        $sQuery = "UPDATE tblBlog set user_id = ".$this->iUserID.", name = '".$this->sName."', active = ".$this->bActive." where id = ".$this->iID.";"; // query statement
	        $bResult = $this->database->query($sQuery);			// process the query
	        
	        if($bResult == false)								// if the boolean Result returns 'false'
	        {
		        die("Could not save the blog data");			// display the error message and exit the script
	        }
	    }
	    else {													// otherwise create a new row (ID)
		    $sQuery = "INSERT INTO tblBlog (user_id, name, active) values(".$this->iUserID.",'".$this->sName."',".$this->active.");"; //query statement
	        $bResult = $this->database->query($sQuery);			// process the query
	        
	        if($bResult == false)								// if the boolean Result returns 'false'
	        {
		        die("Could not save a new blog");				// display the error message and exit the script
	        }
	        else {												// otherwise (if the query was successfull) get the id of the last inserted item
		        $this->iID = $this->database->get_last_id();
	        }
	    }
    }
	
	public function __get($sProperty)							// Get the property. This can be accessed outside the class via magic method __get(property=string)
	{															// http://php.net/manual/en/language.oop5.overloading.php
        switch($sProperty)
        {
        	case 'ID' :											// return the properties which are fetched from the database at top of this class
                return $this->iID;
                break;
            case 'user' :
                if($this->iUserID > 0)							// if a User ID exists (is higher then 0)
            	{
	            	$user = new User();							// instanciate a new User Class and return it
	            	$user->load($this->iUserID);
	            	return $user;
            	}
            	else
                	return false;								// otherwise return false cause the user does not exist
                break;
            case 'name' :
                return $this->sName;
                break;
            case 'active' :
                return $this->bActive;
                break;
            case 'categories' :
            	if($this->iID > 0)								// if the blog Id exists (is higher then 0)
            	{
	            	return Category::getBlogCategories($this->iID); // call the method and send @param1 = blog id
            	}
            	else 
            		return false;								// otherwise return false cause the Blog does not exist
            	break;
            case 'posts' :
            	if($this->iID > 0)								// if the blog Id exists (is higher then 0) 
            	{
	            	return Post::getBlogPosts($this->iID);		// call the method and send @param1 = blog id
            	}
            	else 
            		return false;								// otherwise return false cause the Blog does not exist
            	break; 
            default:
                die($sProperty. ' for the GET method in CLass BLOG does not exist'); // if the property is not set display the error message
        } 
    }
    
    public function __set($sProperty,$value)					// set the values of the property. This can be accessed outside the class via magic method __set(propert, value)
    {    
        switch($sProperty)										// set (overload) the properties to create a new blog
        {
        	case 'userID' :
                $this->iUserID = $value;
                break;
            case 'name' :
                $this->sName = $value;
                break;
            case 'active' :
                $this->bActive = $value;
                break;
            default:
                die($sProperty. 'for the SET method in CLass BLOG does not exist'); // if the property is not set display the error message
        }
    }
    
} // EOF Class Blog
?>
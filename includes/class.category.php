<?php
    require_once('mysqlPDOAdaptor.php');
	require_once('class.post.php');
	
class Category {                                        
	                                                   // the Category class contains all the properties and methods to handle Categories
	private $iID;                                      // create the properties
	private $iBlogID;
	private $sName;
	private $iActive;
	private $database;
	
	public function __construct() {                    // assign the default magic method __construct() values
		$this->iID = 0;
		$this->iBlogID = 0;
		$this->iActive = 1;
		$this->sName = "";
		$this->database = Database::getInstance();
	}
	
	public function load($iID) {                       // function to display the categories whereever needed. @param = integer ID 
		$sQuery = "SELECT id, blog_id, name, active from tblCategory WHERE id = ".$iID; // query statement
		$rsData = $this->database->query($sQuery);        // process the query
		$aData = $this->database->fetch_array($rsData);   // fetch the result data as an array
		$this->iID = $aData['id'];                        // assign the results of the query
		$this->iBlogID = $aData['blog_id'];
		$this->sName = $aData['name'];
		$this->iActive = $aData['active'];
	}
	
	public function save()                             // function to UPDATE and INSERT INTO the Database
    {
        if($this->iID > 0)                              // if the ID exists (if the integer ID is higher then 0)
        {
	        $sQuery = "UPDATE tblCategory set blog_id = ".$this->iBlogID.", name = '".$this->sName."', active = ".$this->iActive." where id = ".$this->iID.";"; //query statement
	        $bResult = $this->database->query($sQuery); // process the query 
	        
	        if($bResult == false)                      // if the boolean Result returns 'false'
	        {
		        die("Could not save the category data"); // display the error message and exit the script   
	        }
	    }
	    else {                                         // otherwise create a new row (ID)
		    $sQuery = "INSERT INTO tblCategory (blog_id, name, active) values(".$this->iBlogID.",'".$this->sName."',".$this->iActive.");";
	        $bResult = $this->database->query($sQuery);    // process the query
	        
	        if($bResult == false)                          // if the boolean $bResult returns 'false'
	        {
		        die("Could not save a new category");     // display the error message and exit the script
	        }
	        else {                                         // otherwise (if the query was successfull) get the id of the last inserted item
		        $this->iID = $this->database->get_last_id();
	        }
	    }
    }
	
	public function __get($sProperty)                      // Get the property. This can be accessed outside the class via magic method __get(property=string)
	{                                                      // http://php.net/manual/en/language.oop5.overloading.php
        switch($sProperty)
        {
        	case 'ID' :                                    // return the properties which are fetched from the database at top of this class
                return $this->iID;
                break;
            case 'name' :
                return $this->sName;
                break;
            case 'active' :
                return $this->iActive;
                break;
            case 'blog' :                                   
            	if($this->iBlogID > 0)                     // if a Blog ID exists (is higher then 0)
            	{
	            	$bBlog = new Blog();                    // instanciate a new Blog Class and return it
	            	$bBlog->load($this->iBlogID);
	            	return $bBlog;
            	}
            	else
            		return array();
            	break;
            case 'posts' :                          
            	if($this->iID > 0)                         // if the ID exists (if the integer ID is higher then 0) 
            	{
	            	return Post::getCategoryPosts($this->iID); // call the method and send param1 = categoryID integer
            	}
            	else 
            		return '';                            // otherwise return an empty string. This will display: No Posts to show
            	break;
            case 'postCount' :
            	if($this->iID > 0)                         // if the ID exists (if the integer ID is higher then 0)
            	{
	            	return Post::getCategoryPostsNumber($this->iID);      // call the method with param1 = categoryID integer
            	}                                                          // this will return an iteger of count(categorieposts);
            	else 
            		return false;                             // return false if there are is no category ID
            	break;
            default:
                die($sProperty. ' for the GET Method in Class CATEGORY does not exist'); // if the property is not set display the error message
        } 
    }
    
    public function __set($sProperty,$value)                    // set the values of the property. This can be accessed outside the class via magic method __set(propert, value)
    {    
        switch($sProperty)                                      // set (overload) the properties to create a new categorie
        {
        	case 'blogID' :
                $this->iBlogID = $value;
                break;
            case 'name' :
                $this->sName = $value;
                break;
            case 'active' :
                $this->iActive = $value;
                break;
            default:
                die($sProperty. 'for the SET Method in Class CATEGORY does not exist'); // if the property is not set dispaly the error message
        }
    }
    
    public static function getBlogCategories($iBlogID)          // function to get the Blog Categories fromt the tblCategory, @param1 = BlogId
    {
    	$database = Database::getInstance();
	    $sQuery = "SELECT id from tblCategory WHERE blog_id = ".$iBlogID; // query statement
    	$rsData = $database->query($sQuery);                   // process the query
    	$aCategories = array();
    	while($aData = $database->fetch_array($rsData))        // fetch the data from the DB as an array while there is something to fetch
    	{
    		$category = new Category();                        // instanciate a new Category Class and return it
    		$category->load($aData['id']);
	    	$aCategories[] = $category;
    	}
    	return $aCategories;
    }
       
    public static function getCategoryNames($iBlogID)           // function to get the Category Names, @param1 = BlogId
    {
    	$database = Database::getInstance();
	    $sQuery = "SELECT name from tblCategory WHERE blog_id = ".$iBlogID; // query statement
    	$rsData = $database->query($sQuery);                   // process the query
    	$aCategories = array();
    	while($aData = $database->fetch_array($rsData))        // fetch the data from the DB as an array while there is something to fetch
    	{
	    	$aCategories[] = $aData['name'];                   // assign the arrays and return the $aCategories array
    	}
    	return $aCategories;
    }
    
    public static function getCategoryIDs($iBlogID)             // function to getCategoryIDs, @param1 = BlogId
    {
    	$database = Database::getInstance();
	    $sQuery = "SELECT id from tblCategory WHERE blog_id = ".$iBlogID; // query statement
    	$rsData = $database->query($sQuery);                   // process the query
    	$aCategories = array();
    	while($aData = $database->fetch_array($rsData))        // fetch the data from the DB as an array while there is something to fetch
    	{
	    	$aCategories[] = $aData['id'];                    // assign the arrays and return the $aCategories array
    	}
    	return $aCategories;
    }
    
} // EOF Class Category

?>
<?php
    require_once('mysqlPDOAdaptor.php');
    require("class.pdo.bind.php");
	require_once('class.comment.php');


class Post {
	                                                   // the Post class contains all the properties and methods to handle posts
	private $iID;                                      // create the properties
	private $sTitle;
	private $sFirstText;
	private $sSecondText;
	private $sDateCreated;
	private $iUserID;
	private $iCategoryID;
	private $iActive;
	private $database;
	
	public function __construct()                      // assign the default magic method __construct() values
    {                    
		$this->iID = 0;
		$this->sTitle = "";
		$this->sFirstText = "";
		$this->sSecondText = "";
		$this->sDateCreated = "";
		$this->iUserID = 0;
		$this->iCategoryID = 0;
		$this->iActive = 1;
		$this->database = Database::getInstance();
	}
	
    public function load($iID)                         // function to display the posts whereever needed. @param = integer ID 
	{   $db = new Bind();                              // instanciate the new Bind class
        $db->bind("id", $iID);                         // bind the values for security reasons
		$aData = $db->row("SELECT id, title, first_text, second_text, date_created, user_id, category_id, active FROM tblPost WHERE id = :id AND active = 1");
		$this->iID = $aData['id'];                     // assign the results of the query
		$this->sTitle = $aData['title'];
		$this->sFirstText = $aData['first_text'];
		$this->sSecondText = $aData['second_text'];
		$this->sDateCreated = $aData['date_created'];
		$this->iUserID = $aData['user_id'];
		$this->iCategoryID = $aData['category_id'];
		$this->iActive = $aData['active'];
	}
    	
	public function save()                             // function to UPDATE and INSERT INTO the Database
    {
        if($this->iID > 0)                             // if the ID exists (if the integer ID is higher then 0)
        {
	        $sQuery = "UPDATE tblPost set title = '".$this->sTitle."', first_text = '".$this->sFirstText."', second_text = '".$this->sSecondText."', date_created = '".$this->sDateCreated."', user_id = ".$this->iUserID.", category_id = ".$this->iCategoryID.", active = ".$this->iActive." where id = ".$this->iID.";";
	        $bResult = $this->database->query($sQuery); // process the query
	        
	        if($bResult == false)                      // if the boolean Result returns 'false'
	        {
		        die("Could not save the post data");   // display the error message and exit the script
	        }
	    }
	    else {                                         // otherwise create a new row (ID)
		    $sQuery = "INSERT INTO tblPost (title, first_text, second_text, date_created, user_id, category_id, active) values('".$this->sTitle."','".$this->sFirstText."','".$this->sSecondText."','".$this->sDateCreated."',".$this->iUserID.",".$this->iCategoryID.", ".$this->iActive.");";
	        $bResult = $this->database->query($sQuery);    // process the query
	        
	        if($bResult == false)                      // if the boolean Result returns 'false'
	        {
		        die("Could not save a new post");     // display the error message and exit the script
	        }
	        else {                                     // otherwise (if the query was successfull) get the id of the last inserted item 
		        $this->iID = $this->database->get_last_id();  
	        }
	    }
    }
	
	public function __get($sProperty)                              // overload default values. This can be accessed outside the class via magic method __get()
	{                                                              // http://php.net/manual/en/language.oop5.overloading.php
        switch($sProperty)
        {
        	case 'ID' :
                return $this->iID;                                  // return the properties which are fetched from the database at top of this class
                break;
            case 'title' :
                return $this->sTitle;
                break;
            case 'firstText' :
                return $this->sFirstText;
                break;  
            case 'secondText' :
                return $this->sSecondText;
                break;
            case 'active' :
            	return $this->iActive;
            	break;
            case 'date' :                                           
                return date("d.m.Y H:i",strtotime($this->sDateCreated)); // convert the sting into time format
                break;
            case 'category' :
            	if($this->iCategoryID > 0)                             // if a category ID exists (is higher then 0)
            	{
	            	$cCategory = new Category();                      // instanciate a new Category Class and return it
	            	$cCategory->load($this->iCategoryID);
	            	return $cCategory;
            	}
            	else 
            		return array();                                   // otherwise create an array
            	break;
            case 'user' :
                if($this->iUserID > 0)                                  // if a category ID exists
            	{
	            	$user = new User();                               // instanciate a new User Class and return it
	            	$user->load($this->iUserID);
	            	return $user;
            	}
            	else
                	return array();
                break;
            case 'comments' :
            	if($this->iID > 0)                                     // if a post row exists 
            	{
	            	return Comment::getPostComments($this->iID);         // display the according comments
            	}
            	else   
            		return false;                                     // otherwise display none
            	break;
            default:
                die($sProperty. ' for the GET method in Class POST does not exist');  // if the property is not set display the error message
        } 
    }
    
    public function __set($sProperty,$value)
    {    
        switch($sProperty)
        {
        	case 'title' :                                             // set (overload) the properties to create a new post
                $this->sTitle = $value;
                break;
            case 'firstText' :
                $this->sFirstText = $value;
                break;
            case 'secondText' :
                $this->sSecondText = $value;
                break;
            case 'date' :
                $this->sDateCreated = $value;
                break;
            case 'active' :
            	$this->iActive = $value;
            	break;
            case 'userID' :
                $this->iUserID = $value;
                break;
            case 'categoryID' :
                $this->iCategoryID = $value;
                break;
            default:
                die($sProperty. 'for the SET method in class SET does not exist');  // if the property is not set display the error message
        }
    }
    
    public static function getLatestPosts()                            // function to get the latest posts in the DB table Post
    {
	    $database = Database::getInstance();                           // get the instance
	    $sQuery = "SELECT id from tblPost where active = 1 order by date_created DESC";   // statement query
	    $rsData = $database->query($sQuery);                           // process the query
    	$aPosts = array();
    	while($aData = $database->fetch_array($rsData))                // fetch the data from the DB as an array while there is something to fetch
    	{
    		$post = new Post();                                          // instanciate a new Post Class and return it
    		$post->load($aData['id']);
	    	$aPosts[] = $post;
    	}
    	return $aPosts;
    }
    
    public static function getCategoryPosts($iCategoryID)                   // function the get posts for the specific category in a while loop 
    {
    	$database = Database::getInstance();
	    $sQuery = "SELECT id from tblPost WHERE active = 1 AND category_id = ".$iCategoryID; // statement query
    	$rsData = $database->query($sQuery);                               // process the query
    	$aPosts = array();
    	while($aData = $database->fetch_array($rsData))                     // fetch the data from the DB as an array while there is something to fetch
    	{
    		$post = new Post();                                               // instanciate a new Post Class and return it
    		$post->load($aData['id']);
	    	$aPosts[] = $post;
    	}
    	return $aPosts;
    }
    
    public static function getSearchPosts($sSearchString)                   // function to get posts for search result page in a while loop, (AGAINST Query string)
    {
    	$database = Database::getInstance();
	    $sQuery = "SELECT id FROM tblPost WHERE MATCH(first_text, second_text, title) AGAINST('".$sSearchString."' IN BOOLEAN MODE) ORDER BY MATCH(first_text, second_text, title) AGAINST('".$sSearchString."' IN BOOLEAN MODE) DESC;";
    	$rsData = $database->query($sQuery);                               // process the query
    	$aPosts = array();
    	while($aData = $database->fetch_array($rsData))                    // fetch the data from the DB as an array while there is something to fetch
    	{
    		$post = new Post();                                            // instanciate a new Post Class and return it
    		$post->load($aData['id']);
	    	$aPosts[] = $post;
    	}
    	return $aPosts;
    }
    
    public static function getBlogPosts($iBlogID)                           // function to get posts for a specific user's blog in a while loop
    {
	    $database = Database::getInstance();
	    $sQuery = "SELECT tblPost.id as PostID FROM tblCategory INNER JOIN tblBlog ON tblCategory.blog_id = tblBlog.id INNER JOIN tblPost ON tblPost.category_id = tblCategory.id WHERE tblPost.active = 1 AND tblBlog.id = ".$iBlogID." ORDER BY tblPost.date_created DESC";
    	$rsData = $database->query($sQuery);                               // process the query
    	$aPosts = array();
    	while($aData = $database->fetch_array($rsData))                    // fetch the data from the DB as an array while there is something to fetch
    	{
    		$post = new Post();                                           // instanciate a new Post Class and return it
    		$post->load($aData['PostID']);
	    	$aPosts[] = $post;
    	}
    	return $aPosts;
    }
    
    public static function getCategoryPostsNumber($iCategoryID)             // function to get the number of posts in the category
    {
	    $database = Database::getInstance();
	    $sQuery = "SELECT id FROM tblPost WHERE active = 1 AND category_id = ".$iCategoryID;
    	$rsData = $database->query($sQuery);                               // process the query
    	$aCats = $database->fetch_array($rsData); //resultData                  // fetch the result as a nummeric array
        //print_r($aCats);
        return count($aCats[id]);                                               // and return the count
    }
}  //END OF CLASS POST

?> 
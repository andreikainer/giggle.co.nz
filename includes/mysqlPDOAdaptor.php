<?php
define("HOST", "");
define("USER_NAME", "");
define("PASSWORD", "");
define("DB_NAME", "");

require_once('class.tools.php');

class Database{
                                                            // the class Database is used to connect and execute the database queries
	private $PDOConnection;                                    // set the class properties
    private static $dbInstance = null;

    private function __construct()
    {
		try
		{
	     $this->PDOConnection = new PDO('mysql:host='.HOST.';dbname='.DB_NAME, USER_NAME, PASSWORD); // connection info
		 $this->PDOConnection -> setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);             // set the PDO ERROR MODE
	    }
		catch(PDOException $e)
		{
		 echo 'Connection error: ' . $e->getMessage();
  	    }
    }

    public static function getInstance()                    // this function makes sure that there is only one instance of a class: http://stackoverflow.com/questions/1449362/origin-explanation-of-classgetinstance
    {
        if (is_null(self::$dbInstance))                     // if no instance exists then create one
        {
            self::$dbInstance = new Database();
        }
        return self::$dbInstance;                           // self is like $this but used specific for classes to instantiate a new class
    }



    public function close_connection()                      // function closes the PDO connection
    {
        $this->PDOConnection = null;
    }



    public function query($sSQL)                                // function queries the datebase, if there is an error echo it out
    {
        // echo $sSQL.'<br/>';
        try
        {
            $resResult = $this->PDOConnection->query($sSQL);    // return the results of the query, or 'false' on failure
            return $resResult;                                  // http://php.net/manual/en/pdo.query.php
        }
        catch(PDOException $e) {
            echo 'ERROR: ' . $e->getMessage();                  // display the error message if the function execution fails
        }

    }


    public function fetch_array($resResult)                     // function fetches the data as an associative array
    {
        return $resResult->fetch(PDO::FETCH_ASSOC);
    }



    public function num_rows($resResult)                        // function fetches the data as an numeric array
    {
        return $resResult->fetch(PDO::FETCH_NUM);
    }



    public function get_last_id()                               // Returns the last inserted id as a string
    {
        return $this->PDOConnection->lastInsertId();
    }

} // EOF Class Database

?>

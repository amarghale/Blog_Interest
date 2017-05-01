<?php
class Database
{   
    private $host = "mysql02.comp.dkit.ie";
    private $db_name = "D00172325";
    private $username = "D00172325";
    private $password = "IG&!uiH#";
    public $conn;
    public function dbConnection()
	{
     
	    $this->conn = null;    
        try
		{
            $this->conn = new PDO("mysql:host=" . $this->host . ";dbname=" . $this->db_name, $this->username, $this->password);
			$this->conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);	
        }
		catch(PDOException $exception)
		{
            echo "Connection error: " . $exception->getMessage();
        }
        return $this->conn;
    }
}
?>
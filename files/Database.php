<?php
class Database
{
    private $host = "localhost";
    private $user = "root";
    private $password = "";
    private $dbname = "pwd";
    public $conn;

    public function __construct()
    {
        $this->connectDB();
    }

    private function connectDB()
    {
        $this->conn = new mysqli($this->host, $this->user, $this->password, $this->dbname);

        if ($this->conn->connect_error) {
            die("Connection failed: " . $this->conn->connect_error);
        }
    }

    public function getConnection()
    {
        return $this->conn;
    }
}
?>
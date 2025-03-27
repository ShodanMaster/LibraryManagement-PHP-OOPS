<?php

class Dbconfig{
    private $host = "localhost" ;
    private $dbname = "librarymanagement";
    private $username = "root";
    private $password = "";
    private $conn;
    
    protected function connect() {
        try {
            mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);
            $this->conn = new mysqli($this->host, $this->username, $this->password, $this->dbname);
            return $this->conn;
        } catch (mysqli_sql_exception $e) {
            die("Connection Failed: " . $e->getMessage());
        }
    }
    
}
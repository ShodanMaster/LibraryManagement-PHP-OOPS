<?php
// session_start();
require_once("../config/dbconfig.php");

class Librarian extends Dbconfig{

    public function __construct(){
        if(isset($_SESSION["role"]) && !$_SESSION["role"] === "admin"){
            header("index.php");
        }
    }

    public function librarianCreate($username, $password){
        try{
            $conn = $this->connect();
            $conn->begin_transaction();
    
            $query = "INSERT INTO users (username, password) VALUES (?, ?)";
            $stmt = $conn->prepare($query);

            $hashed_password = password_hash($password, PASSWORD_DEFAULT);
            $stmt->bind_param("ss", $username, $hashed_password);

            if ($stmt->execute()) {
                $conn->commit();
                return ["status" => 200, "message" => "Librarian Created successful!"];
            } else {
                $conn->rollback();
                return ["status" => 500, "message" => "Librarian Create failed!"];
            }
        } catch (mysqli_sql_exception $e) {
            $conn->rollback();
            if (strpos($e->getMessage(), 'Duplicate entry') !== false) {
                return ["status" => 409, "message" => "Username already exists!"];
            }
            return ["status" => 500, "message" => "Database error: " . $e->getMessage()];
        }
        
    }
}
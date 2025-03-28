<?php
// session_start();
require_once("../config/dbconfig.php");

class Librarian extends Dbconfig{

    public function __construct(){
        if(isset($_SESSION["role"]) && !$_SESSION["role"] === "admin"){
            header("index.php");
        }
    }

    protected function librariansGet(){
        try {
            $conn = $this->connect();
            
            $draw = $_GET['draw'] ?? 1;
            $start = $_GET['start'] ?? 0;
            $length = $_GET['length'] ?? 10;
            $searchValue = $_GET['search']['value'] ?? '';
                        
            $stmt = $conn->prepare("SELECT COUNT(*) as count FROM Users WHERE role <> 'admin'");
            $stmt->execute();
            $totalRecords = $stmt->get_result()->fetch_assoc()['count'];
            
            $query = "SELECT id, username FROM users WHERE role <> 'admin'";
            $params = [];
            $types = '';
            
            if (!empty($searchValue)) {
                $searchValue = "%$searchValue%";
                $query .= " AND username LIKE ?";
                $params[] = $searchValue;
                $types .= "s";
            }
            
            $filterQuery = "SELECT COUNT(*) as count FROM users WHERE role <> 'admin'";
            if (!empty($searchValue)) {
                $filterQuery .= " AND username LIKE ?";
            }
            
            $stmt = $conn->prepare($filterQuery);
            if (!empty($searchValue)) {
                $stmt->bind_param($types, ...$params);
            }
            $stmt->execute();
            $recordsFiltered = $stmt->get_result()->fetch_assoc()['count'];
            
            $query .= " ORDER BY id DESC LIMIT ?, ?";
            $params[] = (int)$start;
            $params[] = (int)$length;
            $types .= "ii";
            
            $stmt = $conn->prepare($query);
            $stmt->bind_param($types, ...$params);
            $stmt->execute();
            $result = $stmt->get_result();
            
            $data = [];
            while ($row = $result->fetch_assoc()) {
                $data[] = $row;
            }
            
            return json_encode([
                "draw" => intval($draw),
                "recordsTotal" => $totalRecords,
                "recordsFiltered" => $recordsFiltered,
                "data" => $data
            ]);
    
        } catch (Exception $e) {
            return json_encode([
                "draw" => intval($draw),
                "recordsTotal" => 0,
                "recordsFiltered" => 0,
                "data" => [],
                "error" => $e->getMessage()
            ]);
        }
    }    

    protected function librarianCreate($username, $password){
        // echo $username;exit;
        try{
            $conn = $this->connect();
            // print_r($conn);exit;
            $conn->begin_transaction();
    
            $query = "INSERT INTO users (username, password) VALUES (?, ?)";
            // echo $query;exit;
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

    protected function librarianUpdate($id, $username, $password = null) {
        try {
            $conn = $this->connect();
            $conn->begin_transaction();
    
            $query = "SELECT id FROM users WHERE id = ?";
            $stmt = $conn->prepare($query);
            $stmt->bind_param("i", $id);
            $stmt->execute();
            $result = $stmt->get_result();
    
            if ($result->num_rows === 0) {
                return ["status" => 404, "message" => "User Not Found"];
            }
    
            if (!empty($password)) {
                $sql = "UPDATE users SET username = ?, password = ? WHERE id = ?";
                $stmt = $conn->prepare($sql);
                $stmt->bind_param("ssi", $username, $password, $id);
            } else {
                $sql = "UPDATE users SET username = ? WHERE id = ?";
                $stmt = $conn->prepare($sql);
                $stmt->bind_param("si", $username, $id);
            }
    
            if ($stmt->execute()) {
                $conn->commit();
                return [
                    'status' => 200,
                    'message' => !empty($password) 
                        ? 'Username and Password Updated Successfully' 
                        : 'Username Updated Successfully'
                ];
            }
    
        } catch (mysqli_sql_exception $e) {
            $conn->rollback();
            if (strpos($e->getMessage(), 'Duplicate entry') !== false) {
                return ["status" => 409, "message" => "Username already exists!"];
            }
        }
    }
    
}
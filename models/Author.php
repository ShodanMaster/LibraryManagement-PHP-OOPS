<?php
// session_start();
require_once("../config/Dbconfig.php");

class Author extends Dbconfig{

    public function __construct(){
        if(isset($_SESSION["role"]) && !$_SESSION["role"] === "admin"){
            header("index.php");
        }
    }

    protected function authorsGet() {
        try {
            $conn = $this->connect();
    
            $draw = $_GET['draw'] ?? 1;
            $start = $_GET['start'] ?? 0;
            $length = $_GET['length'] ?? 10;
            $searchValue = $_GET['search']['value'] ?? '';
            
            $stmt = $conn->prepare("SELECT COUNT(*) as count FROM authors");
            $stmt->execute();
            $totalRecords = $stmt->get_result()->fetch_assoc()['count'];
            
            $query = "SELECT id, name FROM authors WHERE 1";
            $params = [];
            $types = '';
    
            if (!empty($searchValue)) {
                $query .= " AND (name LIKE ?)";
                $searchValue = "%$searchValue%";
                $params = [$searchValue];
                $types = "s";
            }
            
            $filterQuery = "SELECT COUNT(*) as count FROM authors WHERE 1";
            if (!empty($searchValue)) {
                $filterQuery .= " AND (name LIKE ?)";
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

    protected function authorCreate($name){
        // echo $username;exit;
        try{
            $conn = $this->connect();
            // print_r($conn);exit;
            $conn->begin_transaction();
    
            $srlno = $this->makeSerialNo();

            $query = "INSERT INTO authors (name) VALUES (?)";
            // echo $query;exit;
            $stmt = $conn->prepare($query);
            
            $stmt->bind_param("s",$name);

            if ($stmt->execute()) {
                $conn->commit();
                return ["status" => 200, "message" => "Author Created successful!"];
            } else {
                $conn->rollback();
                return ["status" => 500, "message" => "Author Create failed!"];
            }
        } catch (mysqli_sql_exception $e) {
            $conn->rollback();
            if (strpos($e->getMessage(), 'Duplicate entry') !== false && strpos($e->getMessage(), 'for key \'name\'') !== false) {
                return ["status" => 409, "message" => "Author already exists!"];
            }
            return ["status" => 500, "message" => "Database error: " . $e->getMessage()];
        }        
        
    }

    protected function authorUpdate($id, $name) {
        try {
            $conn = $this->connect();
            $conn->begin_transaction();
    
            $query = "SELECT id FROM authors WHERE id = ?";
            $stmt = $conn->prepare($query);
            $stmt->bind_param("i", $id);
            $stmt->execute();
            $result = $stmt->get_result();
    
            if ($result->num_rows === 0) {
                return ["status" => 404, "message" => "Author Not Found"];
            }
            
            $sql = "UPDATE authors SET name = ? WHERE id = ?";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("si", $name, $id);
    
            if ($stmt->execute()) {
                $conn->commit();
                return [
                    'status' => 200,
                    'message' =>'Author Updated Successfully'
                ];
            }
    
        } catch (mysqli_sql_exception $e) {
            $conn->rollback();
            if (strpos($e->getMessage(), 'Duplicate entry') !== false && strpos($e->getMessage(), 'for key \'name\'') !== false) {
                return ["status" => 409, "message" => "Author already exists!"];
            }
            return ["status" => 500, "message" => "Database error: " . $e->getMessage()];
        }
    }
    
    public function authorDelete($id){
        try{
            $conn = $this->connect();
            $conn->begin_transaction();

            $sql = "SELECT id FROM authors WHERE id = ?";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("i", $id);
            $stmt->execute();
            $result = $stmt->get_result();

            if ($result->num_rows > 0) {
                $sql = "DELETE FROM books WHERE author_id = ?";
                $stmt = $conn->prepare($sql);
                $stmt->bind_param("i", $id);
                $stmt->execute();

                $sql = 'DELETE FROM authors WHERE id = ?';
                $stmt = $conn->prepare($sql);
                $stmt->bind_param('i', $id);

                if ($stmt->execute()) {
                    $conn->commit();
                    return ['status' => 200, 'message' => 'Author and releated books Deleted Successfully'];
                } else {
                    $conn->rollback();
                    return ["status" => 500, "message" => "Author Delete Failed"];
                }
            }

        } catch (mysqli_sql_exception $e) {
            $conn->rollback();
            return ["status" => 500, "message" => "Database Error: " . $e->getMessage()];
        }
    }

    private function makeSerialNo() {
        $conn = $this->connect();
    
        $query = "SELECT serial_no FROM members ORDER BY id DESC LIMIT 1";
        $result = $conn->query($query);
    
        if ($result && $row = $result->fetch_assoc()) {
            $lastSerial = $row['serial_no'];
            $number = (int)substr($lastSerial, -6) + 1;
        } else {
            $number = 1;
        }
    
        return "MemLib-" . str_pad($number, 6, "0", STR_PAD_LEFT);
    }
    
}
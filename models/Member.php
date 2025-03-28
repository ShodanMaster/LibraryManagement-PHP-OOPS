<?php
// session_start();
require_once("../config/Dbconfig.php");

class Member extends Dbconfig{

    public function __construct(){
        if(isset($_SESSION["role"]) && !$_SESSION["role"] === "admin"){
            header("index.php");
        }
    }

    protected function membersGet() {
        try {
            $conn = $this->connect();
    
            $draw = $_GET['draw'] ?? 1;
            $start = $_GET['start'] ?? 0;
            $length = $_GET['length'] ?? 10;
            $searchValue = $_GET['search']['value'] ?? '';
    
            $stmt = $conn->prepare("SELECT COUNT(*) as count FROM members");
            $stmt->execute();
            $totalRecords = $stmt->get_result()->fetch_assoc()['count'];
    
            $query = "SELECT id, serial_no, name, phone FROM members WHERE 1";
            $params = [];
            $types = '';
    
            if (!empty($searchValue)) {
                $query .= " AND (serial_no LIKE ? OR name LIKE ? OR phone LIKE ?)";
                $searchValue = "%$searchValue%";
                $params = [$searchValue, $searchValue, $searchValue];
                $types = "sss";
            }
    
            $filterQuery = "SELECT COUNT(*) as count FROM members WHERE 1";
            if (!empty($searchValue)) {
                $filterQuery .= " AND (serial_no LIKE ? OR name LIKE ? OR phone LIKE ?)";
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

    protected function memberCreate($name, $phone){
        // echo $username;exit;
        try{
            $conn = $this->connect();
            // print_r($conn);exit;
            $conn->begin_transaction();
    
            $srlno = $this->makeSerialNo();

            $query = "INSERT INTO members (serial_no, name, phone) VALUES (?, ?, ?)";
            // echo $query;exit;
            $stmt = $conn->prepare($query);
            
            $stmt->bind_param("sss", $srlno, $name, $phone);

            if ($stmt->execute()) {
                $conn->commit();
                return ["status" => 200, "message" => "Member Created successful!"];
            } else {
                $conn->rollback();
                return ["status" => 500, "message" => "Member Create failed!"];
            }
        } catch (mysqli_sql_exception $e) {
            $conn->rollback();
            if (strpos($e->getMessage(), 'Duplicate entry') !== false && strpos($e->getMessage(), 'for key \'phone\'') !== false) {
                return ["status" => 409, "message" => "Phone number already exists!"];
            }
            return ["status" => 500, "message" => "Database error: " . $e->getMessage()];
        }        
        
    }

    protected function memberUpdate($id, $name, $phone) {
        try {
            $conn = $this->connect();
            $conn->begin_transaction();
    
            $query = "SELECT id FROM members WHERE id = ?";
            $stmt = $conn->prepare($query);
            $stmt->bind_param("i", $id);
            $stmt->execute();
            $result = $stmt->get_result();
    
            if ($result->num_rows === 0) {
                return ["status" => 404, "message" => "Member Not Found"];
            }
            
            $sql = "UPDATE members SET name = ?, phone = ? WHERE id = ?";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("ssi", $name, $phone, $id);
    
            if ($stmt->execute()) {
                $conn->commit();
                return [
                    'status' => 200,
                    'message' =>'Member Updated Successfully'
                ];
            }
    
        } catch (mysqli_sql_exception $e) {
            $conn->rollback();
            if (strpos($e->getMessage(), 'Duplicate entry') !== false && strpos($e->getMessage(), 'for key \'phone\'') !== false) {
                return ["status" => 409, "message" => "Phone number already exists!"];
            }
            return ["status" => 500, "message" => "Database error: " . $e->getMessage()];
        }
    }
    
    public function librarianDelete($id){
        try{
            $conn = $this->connect();
            $conn->begin_transaction();

            $sql = "SELECT id FROM users WHERE id = ?";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("i", $id);
            $stmt->execute();
            $result = $stmt->get_result();

            if ($result->num_rows > 0) {
                $sql = 'DELETE FROM users WHERE id = ?';
                $stmt = $conn->prepare($sql);
                $stmt->bind_param('i', $id);

                if ($stmt->execute()) {
                    $conn->commit();
                    return ['status' => 200, 'message' => 'User Deleted Successfully'];
                } else {
                    $conn->rollback();
                    return ["status" => 500, "message" => "User Delete Failed"];
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
<?php
// session_start();
require_once("../config/Dbconfig.php");

class Book extends Dbconfig{

    public function __construct(){
        if(isset($_SESSION["role"]) && !$_SESSION["role"] === "admin"){
            header("index.php");
        }
    }

    protected function booksGet() {
        try {
            $conn = $this->connect();
    
            $draw = $_GET['draw'] ?? 1;
            $start = $_GET['start'] ?? 0;
            $length = $_GET['length'] ?? 10;
            $searchValue = $_GET['search']['value'] ?? '';
    
            $stmt = $conn->prepare("SELECT COUNT(*) as count FROM books");
            $stmt->execute();
            $totalRecords = $stmt->get_result()->fetch_assoc()['count'];
    
            $query = "SELECT books.id, books.serial_no, books.author_id, books.title, authors.name AS author 
                      FROM books 
                      INNER JOIN authors ON books.author_id = authors.id 
                      WHERE 1";
    
            $filterQuery = "SELECT COUNT(*) as count 
                            FROM books 
                            INNER JOIN authors ON books.author_id = authors.id 
                            WHERE 1";
    
            $params = [];
            $types = "";
    
            if (!empty($searchValue)) {
                $query .= " AND (books.serial_no LIKE ? OR books.title LIKE ? OR authors.name LIKE ?)";
                $filterQuery .= " AND (books.serial_no LIKE ? OR books.title LIKE ? OR authors.name LIKE ?)";
                $searchValue = "%$searchValue%";
                $params = [$searchValue, $searchValue, $searchValue];
                $types = "sss";
            }
    
            $stmt = $conn->prepare($filterQuery);
            if (!empty($params)) {
                $stmt->bind_param($types, ...$params);
            }
            $stmt->execute();
            $recordsFiltered = $stmt->get_result()->fetch_assoc()['count'];
    
            $query .= " ORDER BY books.id DESC LIMIT ?, ?";
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
          

    protected function bookCreate($title, $author){
        // echo $author;exit;
        try{

            $conn = $this->connect();
            // print_r($conn);exit;
            $conn->begin_transaction();
    
            $srlno = $this->makeSerialNo();

            $authorId = $this->fetchAuthorId($author);

            if(!$authorId){
                return ["status" => 404, "message" => "Author Not Found"];
            }

            $query = "INSERT INTO books (serial_no, author_id, title) VALUES (?, ?, ?)";
            // echo $query;exit;
            $stmt = $conn->prepare($query);
            
            $stmt->bind_param("sis", $srlno, $authorId,$title);

            if ($stmt->execute()) {
                $conn->commit();
                return ["status" => 200, "message" => "Book Created successful!"];
            } else {
                $conn->rollback();
                return ["status" => 500, "message" => "Book Create failed!"];
            }
        } catch (mysqli_sql_exception $e) {
            $conn->rollback();
            return ["status" => 500, "message" => "Database error: " . $e->getMessage()];
        }        
        
    }

    protected function authorUpdate($id, $name) {
        try {
            $conn = $this->connect();
            $conn->begin_transaction();
    
            $query = "SELECT id FROM books WHERE id = ?";
            $stmt = $conn->prepare($query);
            $stmt->bind_param("i", $id);
            $stmt->execute();
            $result = $stmt->get_result();
    
            if ($result->num_rows === 0) {
                return ["status" => 404, "message" => "Author Not Found"];
            }
            
            $sql = "UPDATE books SET name = ? WHERE id = ?";
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

            $sql = "SELECT id FROM books WHERE id = ?";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("i", $id);
            $stmt->execute();
            $result = $stmt->get_result();

            if ($result->num_rows > 0) {
                $sql = 'DELETE FROM authors WHERE id = ?';
                $stmt = $conn->prepare($sql);
                $stmt->bind_param('i', $id);

                if ($stmt->execute()) {
                    $conn->commit();
                    return ['status' => 200, 'message' => 'Author Deleted Successfully'];
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
    
        $query = "SELECT serial_no FROM books ORDER BY id DESC LIMIT 1";
        $result = $conn->query($query);
    
        if ($result && $row = $result->fetch_assoc()) {
            $lastSerial = $row['serial_no'];
            $number = (int)substr($lastSerial, -6) + 1;
        } else {
            $number = 1;
        }
    
        return "BkLib-" . str_pad($number, 6, "0", STR_PAD_LEFT);
    }

    private function fetchAuthorId($author) {
        $conn = $this->connect();
    
        $query = "SELECT id FROM authors WHERE name = ?";
        $stmt = $conn->prepare($query);
        $stmt->bind_param("s", $author);
        $stmt->execute();
        $result = $stmt->get_result();
    
        $authorId = null;
        if ($result && $row = $result->fetch_assoc()) {
            $authorId = $row['id'];
        }
    
        $stmt->close();
        return $authorId;
    }
    
    
}
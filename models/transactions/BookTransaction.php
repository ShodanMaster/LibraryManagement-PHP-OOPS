<?php
// session_start();
require_once("../../config/Dbconfig.php");

class BookTransaction extends Dbconfig{

    public function __construct(){
        if(isset($_SESSION["role"]) && !$_SESSION["role"] === "admin"){
            header("index.php");
        }
    }

    protected function bookTransaction($member, $book){

    }

    protected function dataFetch($member, $book){
        try {
            $conn = $this->connect();
            $conn->begin_transaction();
    
            $memberSql = "SELECT id, name FROM members WHERE serial_no = ?";
            $memberStmt = $conn->prepare($memberSql);
            $memberStmt->bind_param("s", $member);
            $memberStmt->execute();
            $memberResult = $memberStmt->get_result();
            
            $bookSql = "SELECT id, title FROM books WHERE serial_no = ?";
            $bookStmt = $conn->prepare($bookSql);
            $bookStmt->bind_param("s", $book);
            $bookStmt->execute();
            $bookResult = $bookStmt->get_result();
    
            $conn->commit();
            return [
                "status" => 200,
                'member' => $memberResult->fetch_assoc(),
                'book'=> $bookResult->fetch_assoc(),
                ];
        } catch (mysqli_sql_exception $e) {
            $conn->rollback();
            return ["status" => 500, "message" => "Database Error: " . $e->getMessage()];
        }
    }

    protected function transactionSave($memberId, $booksIds) {
        try {
            $conn = $this->connect();
            $conn->begin_transaction();
    
            $transactionDate = date("Y-m-d");
            $dueDate = date("Y-m-d", strtotime("+1 month"));
            $returnDate = null;
            $status = 'issued';
    
            foreach ($booksIds as $id) {
                // Check if the book exists
                $checkSql = "SELECT id FROM books WHERE id = ?";
                $checkStmt = $conn->prepare($checkSql);
                $checkStmt->bind_param("i", $id);
                $checkStmt->execute();
                $checkStmt->store_result();
    
                if ($checkStmt->num_rows === 0) {
                    $conn->rollback();
                    return ["status" => 500, "message" => "Book ID $id does not exist in the books table."];
                }
    
                // Insert transaction
                $sql = "INSERT INTO booktransactions (book_id, member_id, transaction_date, due_date, return_date, status) 
                        VALUES (?, ?, ?, ?, ?, ?)";
                $stmt = $conn->prepare($sql);
                $stmt->bind_param("iissss", $id, $memberId, $transactionDate, $dueDate, $returnDate, $status);
                $stmt->execute();
            }
    
            $conn->commit();
            return ["status" => 200, "message" => "Transaction saved successfully."];
    
        } catch (mysqli_sql_exception $e) {
            $conn->rollback();
            return ["status" => 500, "message" => "Database Error: " . $e->getMessage()];
        }
    }
      
    
}
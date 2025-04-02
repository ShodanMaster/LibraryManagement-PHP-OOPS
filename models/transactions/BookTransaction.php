<?php
// Start the session at the top
// session_start();
require_once("../../config/Dbconfig.php");

class BookTransaction extends Dbconfig {

    public function __construct() {
        // Corrected the role check condition
        if (!isset($_SESSION["role"]) || $_SESSION["role"] !== "admin") {
            header("Location: index.php");
            exit();
        }
    }

    protected function bookTransaction($member, $book) {
        $data = $this->dataFetch($member, $book);
        if ($data['status'] === 200) {
            return $this->bookIssue($data['member']['id'], [$data['book']['id']]);
        }
        return $data;
    }

    protected function dataFetch($member, $book) {
        try {
            $conn = $this->connect();
            $conn->begin_transaction();

            // Fetch member
            $memberSql = "SELECT id, name FROM members WHERE serial_no = ?";
            $memberStmt = $conn->prepare($memberSql);
            $memberStmt->bind_param("s", $member);
            $memberStmt->execute();
            $memberResult = $memberStmt->get_result();
            $memberData = $memberResult->fetch_assoc();

            if (!$memberData) {
                return ["status" => 404, "message" => "Member not found."];
            }

            // Fetch book
            $bookSql = "SELECT id, title FROM books WHERE serial_no = ?";
            $bookStmt = $conn->prepare($bookSql);
            $bookStmt->bind_param("s", $book);
            $bookStmt->execute();
            $bookResult = $bookStmt->get_result();
            $bookData = $bookResult->fetch_assoc();

            if (!$bookData) {
                return ["status" => 404, "message" => "Book not found."];
            }

            // Validate book
            $bookValidation = $this->validateBook($bookData['id']);
            if ($bookValidation['status'] !== 200) {
                return $bookValidation;
            }

            $conn->commit();
            return [
                "status" => 200,
                'member' => $memberData,
                'book' => $bookData,
            ];

        } catch (mysqli_sql_exception $e) {
            $conn->rollback();
            return ["status" => 500, "message" => "Database Error: " . $e->getMessage()];
        }
    }

    protected function bookIssue($memberId, $booksIds) {
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
                $sql = "INSERT INTO booktransactions (book_id, member_id, transaction_date, due_date, return_date, status, transaction_type) 
                        VALUES (?, ?, ?, ?, ?, ?, 'issued')";
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

    protected function booksFetch($memberSerailNo){
        try{
            $conn = $this->connect();
            $conn->begin_transaction();

            $sql = "SELECT id FROM members WHERE serial_no = ?";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("s", $memberSerailNo);
            $stmt->execute();
            $result = $stmt->get_result();
            $row = $result->fetch_assoc();

            if ($result->num_rows > 0) {
                $bookSql = "SELECT 
                                books.id AS bookID, 
                                books.title AS bookTitle, 
                                books.serial_no AS bookSNO 
                            FROM booktransactions 
                            JOIN books ON booktransactions.book_id = books.id 
                            WHERE booktransactions.member_id = ? 
                            AND booktransactions.status = 'issued'";
                $stmt = $conn->prepare($bookSql);
                $stmt->bind_param("i", $row['id']);
                $stmt->execute();
                $result = $stmt->get_result();

                $books = [];
                while ($row = $result->fetch_assoc()) {
                    $books[] = $row;
                }

                if (!empty($books)) {
                    return ['status' => 200, 'message' => 'Books Found', 'data' => $books];
                }
            }
                        
            return ['status' => 404, 'message' => 'No books found'];
                
        } catch (mysqli_sql_exception $e) {
            return ["status" => 500, "message" => "Database Error: " . $e->getMessage()];
        }
    }

    protected function scanReturn($memberSerialNo, $bookSerialNo){
        
        try{

            $validateMemberBook = $this->validateMemberBook( $memberSerialNo, $bookSerialNo);

            if($validateMemberBook['status'] !== 200){
                return $validateMemberBook;
            }

            $conn = $this->connect();
            $conn->begin_transaction();

            $returnDate = date("Y-m-d");
            $status = 'returned';

            $updateSql = "UPDATE booktransactions SET return_date = ?, status = ? WHERE member_id = 
                        (SELECT id FROM members WHERE serial_no = ?) 
                        AND book_id = (SELECT id FROM books WHERE serial_no = ?) 
                        AND transaction_type = 'issued' LIMIT 1";
            $stmt = $conn->prepare($updateSql);
            $stmt->bind_param("ssss", $returnDate, $status, $memberSerialNo, $bookSerialNo);

            $stmt->execute();

            if ($stmt->affected_rows > 0) {
                $conn->commit();
                return ["status" => 200, "message" => "Book returned successfully."];
            } else {
                $conn->rollback();
                return ["status" => 400, "message" => "Failed to return book."];
            }


            } catch (mysqli_sql_exception $e) {
                return ["status" => 500, "message" => "Database Error: " . $e->getMessage()];
            }
        
    }

    private function validateBook($id) {
        try {
            $conn = $this->connect(); 

            $sql = "SELECT status FROM booktransactions WHERE book_id = ? ORDER BY id DESC LIMIT 1";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("i", $id);
            $stmt->execute();
            $result = $stmt->get_result();
            
            if ($result->num_rows > 0) {
                $row = $result->fetch_assoc();
                if ($row['status'] === 'issued') {
                    $booksql = "SELECT title FROM books WHERE id = ?";
                    $bookStmt = $conn->prepare($booksql);
                    $bookStmt->bind_param("i", $id);
                    $bookStmt->execute();
                    $bookResult = $bookStmt->get_result();
                    
                    if ($bookResult->num_rows > 0) {
                        $bookRow = $bookResult->fetch_assoc();
                        return ["status" => 400, "message" => "The book '{$bookRow['title']}' is already issued."];
                    }
                }
            }

            return ["status" => 200, "message" => "The book is available."];

        } catch (mysqli_sql_exception $e) {
            return ["status" => 500, "message" => "Database Error: " . $e->getMessage()];
        }
    }

    private function validateMemberBook($memberSerialNo, $bookSerialNo) {
        try {
            $conn = $this->connect();

            $memberSql = "SELECT id FROM members WHERE serial_no = ?";
            $memberStmt = $conn->prepare($memberSql);
            $memberStmt->bind_param("s", $memberSerialNo);
            $memberStmt->execute();
            $memberResult = $memberStmt->get_result();
            $memberRow = $memberResult->fetch_assoc();

            $bookSql = "SELECT id FROM books WHERE serial_no = ?";
            $bookStmt = $conn->prepare($bookSql);
            $bookStmt->bind_param("s", $bookSerialNo);
            $bookStmt->execute();
            $bookResult = $bookStmt->get_result();
            $bookRow = $bookResult->fetch_assoc();

            if ($bookResult->num_rows > 0 && $memberResult->num_rows > 0) {
                $sql = "SELECT * FROM booktransactions WHERE member_id = ? AND book_id = ? AND transaction_type = 'issued'";
                $stmt = $conn->prepare($sql);
                $stmt->bind_param("ii", $memberRow['id'], $bookRow['id']);
                $stmt->execute();
                $result = $stmt->get_result();

                if ($result->num_rows > 0) {
                    return ['status' => 200, 'message' => 'Member Took this Book', 'book_id' => $bookRow['id'], 'member_id' => $memberRow['id']];
                }
                return ['status' => 400, 'message' => 'Member Did Not Take this Book'];
            }

            return ['status' => 400, 'message' => 'Invalid Member or Book Serial Number'];

        } catch (mysqli_sql_exception $e) {
            return ["status" => 500, "message" => "Database Error: " . $e->getMessage()];
        }
    }

}
?>

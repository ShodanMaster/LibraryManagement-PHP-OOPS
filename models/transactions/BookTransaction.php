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
            $memberSql = "SELECT id, status, membership_updated, membership_type, name FROM members WHERE serial_no = ?";
            $memberStmt = $conn->prepare($memberSql);
            $memberStmt->bind_param("s", $member);
            $memberStmt->execute();
            $memberResult = $memberStmt->get_result();
            $memberData = $memberResult->fetch_assoc();

            if (!$memberData) {
                return ["status" => 404, "message" => "Member not found."];
            }

            $validateMember = $this->validateMember($memberData);

            if ($validateMember['status'] !== 200) {
                return $validateMember;
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
                                books.serial_no AS bookSNO,
                                booktransactions.due_date,
                                CASE 
                                    WHEN booktransactions.due_date < CURDATE() THEN 'Yes' 
                                    ELSE 'No' 
                                END AS isDue
                            FROM booktransactions 
                            JOIN books ON booktransactions.book_id = books.id 
                            WHERE booktransactions.member_id = ? 
                            AND booktransactions.transaction_type = 'issued'";

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

    protected function booksFine($memberSerialNo) {
        try {
            $conn = $this->connect();
    
            $sql = "SELECT id FROM members WHERE serial_no = ?";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("s", $memberSerialNo);
            $stmt->execute();
            $result = $stmt->get_result();
    
            if ($result->num_rows === 0) {
                return ['status' => 404, 'message' => 'Member not found'];
            }
    
            $member = $result->fetch_assoc();
    
            $bookSql = "SELECT 
                            books.id AS bookID, 
                            books.title AS bookTitle, 
                            books.serial_no AS bookSNO,
                            booktransactions.due_date,
                            CASE 
                                WHEN booktransactions.due_date < CURDATE() THEN 'Yes' 
                                ELSE 'No' 
                            END AS isDue
                        FROM booktransactions 
                        JOIN books ON booktransactions.book_id = books.id 
                        WHERE booktransactions.member_id = ? 
                        AND booktransactions.status = 'overdue'";
    
            $stmt = $conn->prepare($bookSql);
            $stmt->bind_param("i", $member['id']);
            $stmt->execute();
            $result = $stmt->get_result();
    
            $books = [];
            while ($bookRow = $result->fetch_assoc()) {
                $books[] = $bookRow;
            }
    
            if (!empty($books)) {
                return ['status' => 200, 'message' => 'Books Found', 'data' => $books];
            }
    
            return ['status' => 404, 'message' => 'No fined books found'];
    
        } catch (mysqli_sql_exception $e) {
            return ["status" => 500, "message" => "Database Error: " . $e->getMessage()];
        }
    }

    protected function scanFine($memberSerialNo, $bookSerialNo){
        try{

            $validateMemberBook = $this->validateMemberBook( $memberSerialNo, $bookSerialNo);

            if($validateMemberBook['status'] !== 200){
                return $validateMemberBook;
            }

            $validateDueDate = $this->validateDueDate($bookSerialNo);

            if($validateDueDate['status'] !== 200){
                
                $conn = $this->connect();
                $conn->begin_transaction();
                
                $status = 'issue';
                $date = date('Y-m-d');
    
                $updateSql = "UPDATE booktransactions SET status = ?, due_date = ? WHERE member_id = 
                            (SELECT id FROM members WHERE serial_no = ?) 
                            AND book_id = (SELECT id FROM books WHERE serial_no = ?) 
                            AND transaction_type = 'issued' LIMIT 1";
                $stmt = $conn->prepare($updateSql);
                $stmt->bind_param("ssss", $status, $date,$memberSerialNo, $bookSerialNo);
    
                $stmt->execute();
    
                if ($stmt->affected_rows > 0) {
                    $conn->commit();
                    return ["status" => 200, "message" => "Book Fined successfully."];
                } else {
                    $conn->rollback();
                    return ["status" => 400, "message" => "Failed to fined book."];
                }
            }

            return $validateDueDate;

        } catch (mysqli_sql_exception $e) {
            return ["status" => 500, "message" => "Database Error: " . $e->getMessage()];
        }
    }
    

    protected function scanReturn($memberSerialNo, $bookSerialNo) {
        try {
            // Validate if the member has issued this book
            $validateMemberBook = $this->validateMemberBook($memberSerialNo, $bookSerialNo);
            if ($validateMemberBook['status'] !== 200) {
                return $validateMemberBook;
            }
    
            // Validate if book is not overdue (optional: remove this if you still want to allow return even if overdue)
            $validateDueDate = $this->validateDueDate($bookSerialNo);
            if ($validateDueDate['status'] !== 200) {
                return $validateDueDate;
            }
    
            $conn = $this->connect();
            $conn->begin_transaction();
    
            $returnDate = date("Y-m-d");
            $status = 'returned';
    
            $updateSql = "UPDATE booktransactions 
                          SET return_date = ?, status = ? 
                          WHERE member_id = (SELECT id FROM members WHERE serial_no = ?) 
                          AND book_id = (SELECT id FROM books WHERE serial_no = ?) 
                          AND transaction_type = 'issued' 
                          LIMIT 1";
    
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
    

    protected function scanRenew($memberSerialNo, $bookSerialNo){
        try{
            $validateMemberBook = $this->validateMemberBook($memberSerialNo, $bookSerialNo);
    
            if ($validateMemberBook['status'] !== 200) {
                return $validateMemberBook;
            }
    
            $validateDueDate = $this->validateDueDate($bookSerialNo);
    
            if ($validateDueDate['status'] !== 200) {
                return $validateDueDate;
            }
    
            $conn = $this->connect();
            $conn->begin_transaction();
    
            // Retrieve the current due date
            $selectSql = "SELECT due_date FROM booktransactions 
                          WHERE member_id = (SELECT id FROM members WHERE serial_no = ?) 
                          AND book_id = (SELECT id FROM books WHERE serial_no = ?) 
                          AND transaction_type = 'issued' LIMIT 1";
            $stmt = $conn->prepare($selectSql);
            $stmt->bind_param("ss", $memberSerialNo, $bookSerialNo);
            $stmt->execute();
            $stmt->store_result();
            
            if ($stmt->num_rows === 0) {
                return ["status" => 400, "message" => "No active transaction found for renewal."];
            }
    
            $stmt->bind_result($currentDueDate);
            $stmt->fetch();
            $stmt->close();
    
            // Calculate the new due date (current due date + 1 month)
            $newDueDate = date("Y-m-d", strtotime($currentDueDate . " +1 month"));
    
            // Update the due date
            $updateSql = "UPDATE booktransactions SET due_date = ? 
                          WHERE member_id = (SELECT id FROM members WHERE serial_no = ?) 
                          AND book_id = (SELECT id FROM books WHERE serial_no = ?) 
                          AND transaction_type = 'issued' LIMIT 1";
            $stmt = $conn->prepare($updateSql);
            $stmt->bind_param("sss", $newDueDate, $memberSerialNo, $bookSerialNo);
            $stmt->execute();
    
            if ($stmt->affected_rows > 0) {
                $conn->commit();
                return ["status" => 200, "message" => "Book renewal successful. New due date: $newDueDate"];
            } else {
                $conn->rollback();
                return ["status" => 400, "message" => "Failed to renew book."];
            }
    
        } catch (mysqli_sql_exception $e) {
            return ["status" => 500, "message" => "Database Error: " . $e->getMessage()];
        }
    }
    
    private function validateMember($memberData) {
        $memberId = $memberData["id"];
        $membershipType = $memberData["membership_type"];
        $membershipUpdated = $memberData["membership_updated"];
        $membershipStatus = $memberData["status"];
    
        if ($membershipStatus === 'expired') {
            return ['status' => 401, 'message' => 'Membership Expired'];
        }
    
        $currentDate = date('Y-m-d');
        $daysDifference = (strtotime($currentDate) - strtotime($membershipUpdated)) / (60 * 60 * 24);
    
        if (($membershipType === 'monthly' && $daysDifference > 30) ||
            ($membershipType === 'yearly' && $daysDifference > 365)) {
            
            $conn = $this->connect();
            $sql = 'UPDATE members SET status = "expired" WHERE id = ?';
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("i", $memberId);
            $stmt->execute();
    
            return ['status' => 401, 'message' => 'Membership Expired'];
        }
    
        return ['status' => 200, 'message' => 'Membership Valid'];
    }
    
    private function validateDueDate($bookSerialNo) {
        try {
            $conn = $this->connect();
            
            $bookSql = "SELECT id FROM books WHERE serial_no = ?";
            $bookStmt = $conn->prepare($bookSql);
            $bookStmt->bind_param("s", $bookSerialNo);
            $bookStmt->execute();
            $bookResult = $bookStmt->get_result();
            $bookRow = $bookResult->fetch_assoc();
    
            if (!$bookRow) {
                return ['status' => 400, 'message' => 'Invalid Book Serial Number'];
            }
            
            $sql = "SELECT id, status, due_date FROM booktransactions 
                    WHERE book_id = ? AND transaction_type = 'issued' LIMIT 1";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("i", $bookRow['id']);
            $stmt->execute();
            $result = $stmt->get_result();
            $transactionRow = $result->fetch_assoc();
    
            if (!$transactionRow) {
                return ['status' => 400, 'message' => 'No active transaction found for this book'];
            }
            
            if ($transactionRow['status'] === 'overdue') {
                return ['status' => 400, 'message' => 'Book is overdue'];
            }
            
            if ($transactionRow['due_date'] < date("Y-m-d")) {
                $updateSql = "UPDATE booktransactions SET status = 'overdue' WHERE id = ?";
                $updateStmt = $conn->prepare($updateSql);
                $updateStmt->bind_param("i", $transactionRow['id']);
                $updateStmt->execute();
    
                return ['status' => 400, 'message' => 'Book overdue'];
            }
    
            return ['status' => 200, 'message' => 'Book is not overdue'];
    
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

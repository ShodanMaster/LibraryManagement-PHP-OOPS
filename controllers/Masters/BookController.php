<?php
session_start();
require_once("../../models/masters/Book.php");

class BookController extends Book{
    
    private $userId;

    public function __construct(){

        if(isset($_SESSION["role"]) && !$_SESSION["role"] === "admin"){
            header("index.php");
        }
    }

    public function getBooks(){
        $booksJson = $this->booksGet();

        $books = json_decode($booksJson, true);
        
        if ($books === null || !isset($books['data'])) {
            return json_encode([
                "status" => 500,
                "message" => "Invalid JSON response from getBooks()",
                "debug" => $booksJson
            ]);
        }

        return json_encode($books);
    }

    public function createBook($post){
        // print_r($post);exit;    
        $category = $post['category'];
        $author = $post['author'];
        $title = $post['title'];
        
        $validation = $this->validate($title, $author, $category);
        
        if ($validation['status'] !== 200) {
            return json_encode($validation);
        }

        $createBook = $this->bookCreate($title, $author, $category);
        return json_encode($createBook);
    }

    public function updateBook($post){
        // print_r($post);
        $id = $post['id'];
        $title = $post['title'];
        $category = $post['category'];
        $author = $post['author'];

        $validation = $this->validate($title, $author, $category);
        
        if ($validation['status'] !== 200) {
            return json_encode($validation);
        }
            
        $updateBook = $this->bookUpdate($id, $author, $title, $category);
        return json_encode($updateBook);
    }

    public function deleteBook($post){
        $id = $post['bookId'];
        
        $deleteAuthor = $this->bookDelete($id);
        return json_encode($deleteAuthor);
    }

    private function validate($title, $author, $category) {
        if (empty($title) || empty($author) || empty($category)) {
            return ["status" => 400, "message" => "Category, Title and Author are required!"];
        }
    
        if (!preg_match('/^[a-zA-Z\s]{3,200}$/', $title)) {
            return ["status" => 400, "message" => "Title must be 3-20 characters long and contain only letters and spaces!"];
        }
    
        return ["status" => 200, "message" => "Validation passed!"];
    }

}
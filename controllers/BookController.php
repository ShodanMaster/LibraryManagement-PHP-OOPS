<?php
session_start();
require_once("../models/Book.php");

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

    public function createAuthor($post){
        // print_r($post);exit;    
        $name = $post['name'];
        
        $validation = $this->validate($name);
        
        if ($validation['status'] !== 200) {
            return json_encode($validation);
        }
        // print_r($post);exit;    

        $createAuthor = $this->authorCreate($name);
        return json_encode($createAuthor);
    }

    public function updateAuthor($post){
        // print_r($post);
        $id = $post['id'];
        $name = $post['name'];

        $validation = $this->validate($name);
        
        if ($validation['status'] !== 200) {
            return json_encode($validation);
        }
            
        $updateAuthor = $this->authorUpdate($id, $name);
        return json_encode($updateAuthor);
    }

    public function deleteAuthor($post){
        $id = $post['authorId'];
        
        $deleteAuthor = $this->authorDelete($id);
        return json_encode($deleteAuthor);
    }

    private function validate($name) {
        if (empty($name)) {
            return ["status" => 400, "message" => "Name Type are required!"];
        }
    
        if (!preg_match('/^[a-zA-Z\s]{3,20}$/', $name)) {
            return ["status" => 400, "message" => "Name must be 3-20 characters long and contain only letters and spaces!"];
        }
    
        return ["status" => 200, "message" => "Validation passed!"];
    }

}
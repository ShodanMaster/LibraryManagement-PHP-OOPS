<?php
session_start();
require_once("../models/Category.php");

class CategoryController extends Category{
    
    private $userId;

    public function __construct(){

        if(isset($_SESSION["role"]) && !$_SESSION["role"] === "admin"){
            header("index.php");
        }
    }

    public function getCategories(){
        $categoriesJson = $this->categoriesGet();

        $categories = json_decode($categoriesJson, true);
        
        if ($categories === null || !isset($categories['data'])) {
            return json_encode([
                "status" => 500,
                "message" => "Invalid JSON response from getCategories()",
                "debug" => $categoriesJson
            ]);
        }

        return json_encode($categories);
    }

    public function createCategory($post){
        // print_r($post);exit;    
        $name = $post['name'];
        
        $validation = $this->validate($name);
        
        if ($validation['status'] !== 200) {
            return json_encode($validation);
        }
        // print_r($post);exit;    

        $createCategory = $this->categoryCreate($name);
        return json_encode($createCategory);
    }

    public function updateCaregory($post){
        // print_r($post);
        $id = $post['id'];
        $name = $post['name'];

        $validation = $this->validate($name);
        
        if ($validation['status'] !== 200) {
            return json_encode($validation);
        }
            
        $updateCategory = $this->categoryUpdate($id, $name);
        return json_encode($updateCategory);
    }

    public function deleteCategory($post){
        $id = $post['categoryId'];
        
        $deleteCategory = $this->categoryDelete($id);
        return json_encode($deleteCategory);
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
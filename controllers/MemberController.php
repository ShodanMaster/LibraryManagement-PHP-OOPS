<?php
session_start();
require_once("../models/Member.php");

class MemberController extends Member{
    
    private $userId;

    public function __construct(){

        if(isset($_SESSION["role"]) && !$_SESSION["role"] === "admin"){
            header("index.php");
        }
    }

    public function getMembers(){
        $membersJson = $this->membersGet();

        $members = json_decode($membersJson, true);
        
        if ($members === null || !isset($members['data'])) {
            return json_encode([
                "status" => 500,
                "message" => "Invalid JSON response from getMembers()",
                "debug" => $membersJson
            ]);
        }

        return json_encode($members);
    }

    public function createMember($post){
        // print_r($post);exit;    
        $name = $post['name'];
        $phone = $post['phone'];
        
        $validation = $this->validate($name, $phone);
        
        if ($validation['status'] !== 200) {
            return json_encode($validation);
        }
        // print_r($post);exit;    

        $createMember = $this->memberCreate($name, $phone);
        return json_encode($createMember);
    }

    public function updateMember($post){
        // print_r($post);
        $id = $post['id'];
        $name = $post['name'];
        $phone = $post['phone'];

        $validation = $this->validate($name, $phone);
        
        if ($validation['status'] !== 200) {
            return json_encode($validation);
        }
            
        $updateMember = $this->memberUpdate($id, $name, $phone);
        return json_encode($updateMember);
    }

    public function deleteLibrarian($post){
        $id = $post['userId'];
        
        $deleteUser = $this->librarianDelete($id);
        return json_encode($deleteUser);
    }
    private function validate($name, $phone) {
        if (empty($name) || empty($phone)) {
            return ["status" => 400, "message" => "Name and Phone are required!"];
        }
    
        if (!preg_match('/^[a-zA-Z\s]{3,20}$/', $name)) {
            return ["status" => 400, "message" => "Name must be 3-20 characters long and contain only letters and spaces!"];
        }        
    
        if (!preg_match('/^[6-9]\d{9}$/', $phone)) {
            return ["status" => 400, "message" => "Phone number must be a valid 10-digit Indian mobile number starting with 6-9!"];
        }    
    
        return ["status" => 200, "message" => "Validation passed!"];
    }

}
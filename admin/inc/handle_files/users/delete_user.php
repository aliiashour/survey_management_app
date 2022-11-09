<?php

    if($_SERVER['REQUEST_METHOD'] == "POST"){
        include_once "../../../config/connect_database.php" ; 
        extract($_REQUEST) ; 
        $q = "DELETE FROM users WHERE user_id = :user_id" ; 
        $stmt = $con->prepare($q) ; 
        $res = $stmt->execute(array(':user_id' => $user_id));
        $data = '' ; 
        if($res){
            $data = array('status' => 'success') ; 
        }else{
            $data = array('status' => 'failed') ; 
        }
        
        echo json_encode($data) ; 

        
    }
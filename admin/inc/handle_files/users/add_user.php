<?php

    if($_SERVER['REQUEST_METHOD'] == "POST"){
        include_once  "../../../config/connect_database.php" ; 
        extract($_REQUEST) ; 
        if(strlen($user_password) > 0){
            $q = "INSERT INTO users SET 
                user_name = :user_name,
                user_email = :user_email,
                user_status = :user_status,
                user_password = :user_password" ; 
            $stmt = $con->prepare($q) ; 
            $res = $stmt->execute(array(
                ':user_name' => $user_name,
                ':user_email' => $user_email,
                ':user_status' => $user_status,
                ':user_password' => sha1($user_password)
            ));
            $data = '' ; 
            if($res){
                $data = array('status' => 'success', 'msg'=>'user successfully added') ; 
            }else{
                $data = array('status' => 'failed', 'msg'=>'can not add user') ; 
            }
            
            echo json_encode($data) ; 
        }else{
            $data = array('status' => 'failed', 'msg'=>'fill all fieds') ; 
            
            echo json_encode($data) ; 
        }
    }
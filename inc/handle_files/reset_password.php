<?php
    session_start() ; 
    include_once "../../config/connect_database.php" ; 
    $data = '' ; 
    if($_SERVER['REQUEST_METHOD'] == "POST"){
        if(isset($_POST['old_password']) && isset($_POST['new_password'])){
            // first i should ensure the old password
            $q = "SELECT user_password FROM USERS WHERE user_id=:user_id";
            $stmt = $con->prepare($q) ; 
            $stmt->execute(array(
                ':user_id' => $_SESSION['user_id']
            )) ;
            $res = $stmt->fetch(); 
            if($res['user_password'] == sha1($_POST['old_password'])){
                // yes i am the real user want to change password
    
                // now reset password
                $q = "UPDATE USERS SET user_password = :user_password WHERE user_id=:user_id";
                $stmt = $con->prepare($q) ; 
                $stmt->execute(array(
                    ':user_password' => sha1($_POST['new_password']),
                    ':user_id' => $_SESSION['user_id']
                )) ;
                $data = array(
                    "status" => 'true',
                    'error' => 'no error'
                ) ;
                echo json_encode($data) ; 
            }else{
                $data = array(
                    "status" => 'false',
                    'error' => 'old password does not match'
                ) ;
                echo json_encode($data) ; 
            }
    
        }
    }
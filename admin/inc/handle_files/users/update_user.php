
<?php

    if($_SERVER['REQUEST_METHOD'] == "POST"){
        include_once "../../../config/connect_database.php" ; 
        extract($_REQUEST) ; 
        $q = "UPDATE users SET 
                user_name = :user_name,
                user_email = :user_email,
                user_status = :user_status";
           if(strlen($user_password) > 0 ) {
            $q .= ", user_password=:user_password" ; 
           }
            $q .= " WHERE user_id = :user_id" ; 
        $stmt = $con->prepare($q) ; 
        $res = false ; 
        if(strlen($user_password) > 0 ) {
            $res = $stmt->execute(array(
                ':user_id' => $user_id,
                ':user_name' => $user_name,
                ':user_email' => $user_email,
                ':user_status' => $user_status,
                ':user_password' => sha1($user_password)
            ));
        }else{
            $res = $stmt->execute(array(
                ':user_id' => $user_id,
                ':user_name' => $user_name,
                ':user_email' => $user_email,
                ':user_status' => $user_status
            ));
        }
        
        if($res){
            $data = array('status' => 'success', 'msg'=>'user successfully edited') ; 
        }else{
            $data = array('status' => 'failed', 'msg'=>'can not edit user') ; 
        }
        
        echo json_encode($data) ; 

        
    }
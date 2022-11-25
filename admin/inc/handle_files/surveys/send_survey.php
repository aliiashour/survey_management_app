<?php

    if($_SERVER['REQUEST_METHOD'] == "POST"){
        include_once "../../../config/connect_database.php" ; 
        extract($_REQUEST) ; 
        // first get id of user using email
        $q = "SELECT user_id FROM users WHERE user_email = :user_email" ; 
        $stmt = $con->prepare($q) ; 
        $res = $stmt->execute(array(':user_email' => $user_email));
        $count = $stmt->rowCount() ; 
        $data = '' ; 
        if($count){
            $res = $stmt->fetch() ;
            $user_id = $res['user_id'] ; 
            // if the user found i should check if he active
            $q = "SELECT * FROM users WHERE user_id = :user_id AND user_status='active'" ; 
            $stmt = $con->prepare($q) ; 
            $res = $stmt->execute(array(':user_id' => $user_id));
            $count = $stmt->rowCount() ; 
            if($count){
                // if the user found and active i should check if survey active not pended
                $q = "SELECT * FROM survey WHERE survey_id = :survey_id AND survey_status='active'" ; 
                $stmt = $con->prepare($q) ; 
                $res = $stmt->execute(array(':survey_id' => $survey_id));
                $count = $stmt->rowCount() ; 
                if($count){
                    // check if it send before
                    $q = "SELECT * FROM survey_info WHERE user_id = :user_id AND survey_id= :survey_id" ; 
                    $stmt = $con->prepare($q) ; 
                    $res = $stmt->execute(array(
                        ':user_id' => $user_id,
                        ':survey_id' => $survey_id,
                    ));
                    $count = $stmt->rowCount() ; 
                    if(!$count){
                        $q = "INSERT INTO survey_info SET
                            user_id = :user_id,
                            survey_id = :survey_id 
                        " ; 
                        $stmt = $con->prepare($q) ; 
                        $res = $stmt->execute(array(
                            ':survey_id' => $survey_id,
                            ':user_id' => $user_id
                            )
                        );
                        $data = array('status' => 'success', 'msg'=>'Survey Successfully sended') ; 
                    }else{
                        $data = array('status' => 'failed', 'msg'=>'this survey sent already once before') ;     
                    }
                }else{
                    $data = array('status' => 'failed', 'msg'=>'this survey not active yet') ; 
                }
            }else{
                $data = array('status' => 'failed', 'msg'=>'this user is pended') ; 
            }
        }else{
            $data = array('status' => 'failed', 'msg'=>'this user email not defined') ; 
        }
        echo json_encode($data) ;
    }
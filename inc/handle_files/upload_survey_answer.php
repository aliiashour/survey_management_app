<?php

    if($_SERVER['REQUEST_METHOD'] == "POST"){
        include_once  "../../config/connect_database.php" ; 
        extract($_REQUEST) ; 
        $q = "INSERT INTO answers SET 
            user_id = :user_id,
            question_id = :question_id,
            survey_id = :survey_id,
            question_type = :question_type,
            question_answer = :question_answer" ; 
        $stmt = $con->prepare($q) ; 
        $res = $stmt->execute(array(
            ':user_id' => $user_id,
            ':question_id' => $question_id,
            ':survey_id' => $survey_id,
            ':question_type' => $question_type,
            ':question_answer' => $question_answer
        ));
        $data = '' ; 
        if($res){
            // update survey status
            // where survey id  = $id and user_id  = $user_id

            $q = "UPDATE survey_info SET 
            status = :status,
            complete_date = :complete_date WHERE user_id = :user_id AND survey_id = :survey_id" ;
            $stmt = $con->prepare($q) ; 
            $res = $stmt->execute(array(
                ':user_id' => $user_id,
                ':survey_id' => $survey_id,
                ':complete_date' => date("Y-m-d",time()),
                ':status' => 'Completed'

            ));
            if($res){
                $data = array('status' => 'success', 'msg'=>'successfully modifiyed') ; 
            }else{
                $data = array('status' => 'failed', 'msg'=>'can not modify survey info') ; 
            }
        }else{
            $data = array('status' => 'failed', 'msg'=>'can not upload answer') ; 
        }
        
        echo json_encode($data) ; 
    }
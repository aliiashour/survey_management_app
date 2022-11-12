<?php

    if($_SERVER['REQUEST_METHOD'] == "POST"){
        include_once  "../../../config/connect_database.php" ; 
        extract($_REQUEST) ; 
        $q = "INSERT INTO questions SET 
            question_title = :question_title,
            question_survey_id = :question_survey_id" ; 
        $stmt = $con->prepare($q) ; 
        $res = $stmt->execute(array(
            ':question_title' => $question_title,
            ':question_survey_id' => $survey_id
        ));
        $data = '' ; 
        if($res){
            $data = array('status' => 'success', 'msg'=>'question successfully added') ; 
        }else{
            $data = array('status' => 'failed', 'msg'=>'can not add question') ; 
        }
        
        echo json_encode($data) ; 
    }
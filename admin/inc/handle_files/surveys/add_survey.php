<?php
// i need here to ensure dates ! garet me!!
    if($_SERVER['REQUEST_METHOD'] == "POST"){
        include_once  "../../../config/connect_database.php" ; 
        extract($_REQUEST) ; 
        
        $q = "INSERT INTO survey SET 
            survey_title = :survey_title,
            survey_start_date = :survey_start_date,
            survey_expire_date = :survey_expire_date,
            survey_status = :survey_status" ; 
        $stmt = $con->prepare($q) ; 
        $res = $stmt->execute(array(
            ':survey_title' => $survey_title,
            ':survey_start_date' => $survey_start_date,
            ':survey_expire_date' => $survey_expire_date,
            ':survey_status' => $survey_status
        ));
        $data = '' ; 
        if($res){
            $data = array('status' => 'success', 'msg'=>'survey successfully added') ; 
        }else{
            $data = array('status' => 'failed', 'msg'=>'can not add survey') ; 
        }
        
        echo json_encode($data) ; 
    }
    
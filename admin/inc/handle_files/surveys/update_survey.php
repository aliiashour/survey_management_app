
<?php
//here we should check dates also
    if($_SERVER['REQUEST_METHOD'] == "POST"){
        include_once "../../../config/connect_database.php" ; 
        extract($_REQUEST) ; 
        if($survey_expire_date > $survey_start_date){
            $q = "UPDATE survey SET 
                    survey_title = :survey_title,
                    survey_start_date = :survey_start_date,
                    survey_expire_date = :survey_expire_date,
                    survey_status = :survey_status
                    WHERE survey_id = :survey_id";
            $stmt = $con->prepare($q) ; 
    
            $res = $stmt->execute(array(
                ':survey_id' => $survey_id,
                ':survey_title' => $survey_title,
                ':survey_start_date' => $survey_start_date,
                ':survey_expire_date' => $survey_expire_date,
                ':survey_status' => $survey_status
            ));
            
            if($res){
                $data = array('status' => 'success', 'msg'=>'survey successfully edited') ; 
            }
        }else{
            $data = array('status' => 'failed', 'msg'=>'Enter right sequence of dates') ; 
        }
        

        echo json_encode($data) ; 
    }
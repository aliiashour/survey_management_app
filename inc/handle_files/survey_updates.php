<?php
    include_once "../../config/connect_database.php" ; 
    // get the wanted survey_ids that should be closed
    
    $q = "SELECT user_id, survey_id FROM survey_info WHERE complete_date is NULL AND CURRENT_DATE > get_expire_date(survey_id);" ; 
    $stmt = $con->prepare($q) ; 
    $res = $stmt -> execute() ; 
    $ids = [] ; 
    if($res){
        $rows = $stmt->fetchAll() ; 
        foreach ($rows as $row) {
            array_push($ids, $row['survey_id']) ; 
        }
    }
    $q = "UPDATE survey_info SET status = 'closed' WHERE complete_date is NULL AND survey_id IN(". join(', ', $ids) .")" ; 
    $stmt = $con->prepare($q) ; 
    $res = $stmt -> execute() ; 


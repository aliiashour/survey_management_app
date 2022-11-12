<?php

    if($_SERVER['REQUEST_METHOD'] == "POST"){
        include_once "../../../config/connect_database.php" ; 
        extract($_REQUEST) ; 
        $q = "DELETE FROM questions WHERE question_id = :question_id" ; 
        $stmt = $con->prepare($q) ; 
        $res = $stmt->execute(array(':question_id' => $question_id));
        $data = '' ; 
        if($res){
            $data = array('status' => 'success') ; 
        }else{
            $data = array('status' => 'failed') ; 
        }
        
        echo json_encode($data) ; 

        
    }
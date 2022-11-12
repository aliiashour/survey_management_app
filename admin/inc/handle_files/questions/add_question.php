<?php
// data:{survey_id:survey_id, question_title:question_title, question_type:input_type, 
    // answer_values:input_value, answer_count:input_num},

    if($_SERVER['REQUEST_METHOD'] == "POST"){
        include_once  "../../../config/connect_database.php" ; 
        extract($_REQUEST) ; 
        
        $q = "INSERT INTO questions SET 
            question_title = :question_title,
            question_survey_id = :question_survey_id" ;
        $stmt = $con->prepare($q) ; 
        $stmt->execute(array(
            ':question_title' => $question_title,
            ':question_survey_id' => $survey_id
        ));

        // here i toutally need the last question id i just inserted 
        $question_id = $con->lastInsertId() ; 
        $res = false ; 
        if($question_type == 'text'){
            // for normal question
            // check value for answer_values
            $q = "INSERT INTO question_details SET
                question_id = :question_id,
                question_type = :question_type
                " ;
            $stmt = $con->prepare($q) ; 
            $res = $stmt->execute(array(
                'question_id' => $question_id,
                'question_type' => strtoupper($question_type)
            ));
            
        }elseif($question_type=='radio'){
            // for normal question
            // check value for answer_values
            $q = "INSERT INTO question_details SET
                question_id = :question_id,
                question_type = :question_type,
                question_radio_num = :answer_count,
                question_radio_value = :answer_values
                " ;
            $stmt = $con->prepare($q) ; 
            $res = $stmt->execute(array(
                'question_id' => $question_id,
                'question_type' => strtoupper($question_type),
                'answer_count' => $answer_count,
                'answer_values' => strtoupper($answer_values)
            ));
        }else{
            // for check box question
            $q = "INSERT INTO question_details SET
                question_id = :question_id,
                question_type = :question_type,
                question_check_num = :answer_count,
                question_check_value = :answer_values
                " ;
            $stmt = $con->prepare($q) ; 
            $res = $stmt->execute(array(
                'question_id' => $question_id,
                'question_type' => strtoupper($question_type),
                'answer_count' => $answer_count,
                'answer_values' => strtoupper($answer_values)
            ));
        }

        $data = '' ; 
        if($res){
            $data = array('status' => 'success', 'msg'=>'question successfully added') ; 
        }else{
            $data = array('status' => 'failed', 'msg'=>'can not add question') ; 
        }
        
        echo json_encode($data) ; 
    }
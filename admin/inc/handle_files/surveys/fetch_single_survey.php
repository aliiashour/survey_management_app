<?php
    if($_SERVER['REQUEST_METHOD'] == "POST"){
        include_once "../../../config/connect_database.php" ;
        extract($_REQUEST) ; 

        $q = "SELECT * FROM survey WHERE survey_id = ?" ; 
        $stmt = $con->prepare($q) ; 
        $stmt->execute(array($survey_id)) ; 
        $data = '' ; 
        if($stmt->rowCount()){
            $res = $stmt->fetch() ; 
            $data = array(
                'status' => 'found',
                'data'=>array(
                    'survey_id' => $res['survey_id'],
                    'survey_title' => $res['survey_title'],
                    'survey_start_date' => $res['survey_start_date'],
                    'survey_expire_date' => $res['survey_expire_date'],
                    'survey_status' => $res['survey_status']
                )
            ) ; 
        }else{
            $data = array(
                'status' => 'notfound',
                'data'=>''
            ) ; 
        }
        echo json_encode($data) ; 
        
    }

?>
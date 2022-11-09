<?php
    session_start() ; 
    include_once "../../../config/connect_database.php" ;  

    $q = "SELECT survey.survey_id, survey.survey_title, survey.survey_start_date, survey.survey_expire_date, 
            COUNT(survey_info.survey_id) as sent, 
            get_survey_completed_num(survey.survey_id) as participat 
            FROM survey INNER JOIN survey_info ON
            survey.survey_id = survey_info.survey_id
            GROUP BY survey.survey_title " ; 
    $stmt = $con->prepare($q) ; 
    $stmt->execute() ; 
    $count_all_rows = $stmt->rowCount() ; 

    if(isset($_POST['search']['value'])){
        $search_value = $_POST['search']['value'] ; 
        $q .= "HAVING survey.survey_title LIKE '%" . $search_value . "%'" ; 
        $q .= " OR survey.survey_start_date LIKE '%" . $search_value . "%'" ; 
        $q .= " OR survey.survey_expire_date LIKE '%" . $search_value . "%'" ; 
        $q .= " OR sent LIKE '%" . $search_value . "%'" ; 
        $q .= " OR participat LIKE '%" . $search_value . "%'" ; 
        $q .= " OR participat LIKE '%" . $search_value . "%'" ; 
         
    }

    if(isset($_POST['order'])){
        $column = $_POST['order'][0]['column'] ; 
        $order = $_POST['order'][0]['dir'] ; 
        $q .= " order by " . $column . " " . $order ; 
    }else{
        $q .= " order by survey_title ASC" ; 
    }

    if(isset($_POST['length']) && $_POST['length'] != -1){
        $start = $_POST['start'] ; 
        $length = $_POST['length'];
        $q .= ' LIMIT ' . $start . ', ' . $length ; 
    }

    $data =array() ; 
    $stmt = $con->prepare($q) ; 
    $stmt->execute() ; 
    $filtered_rows = $stmt->rowCount() ; 
    $counter= 1 ; 
    if($stmt->rowCount()){
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $sub_arr = array() ; 
            $sub_arr[] = $counter; 
            $sub_arr[] = $row['survey_title'] ; 
            $sub_arr[] = $row['survey_start_date'] ; 
            $sub_arr[] = $row['survey_expire_date'] ; 
            $sub_arr[] = $row['sent'] ; 
            $sub_arr[] = $row['participat'] ; 
            $sub_arr[] = '<button data-title="review_survey" data-action="review" id="review_button" class="btn btn-info" data-survey_id="' . $row['survey_id'] .  '"><i class="fa-solid fa-eye"></i></button>' ; 
            $data[] = $sub_arr ; 
            $counter +=1 ; 
        }
    }
   

    $output = array(
        'data'=>$data,
        'draw'=>intval($_POST['draw']),
        'recordsTotal'=>$filtered_rows, // 10
        'recordsFiltered'=>$count_all_rows, //14
    ) ; 
    echo json_encode($output) ; 
<?php
    session_start() ; 
    include_once "../../config/connect_database.php" ;  
    $q = "SELECT survey.survey_id, survey_title, survey_info.sent_date, survey.survey_expire_date, survey_info.status 
    FROM `survey_info` INNER JOIN users ON
    survey_info.user_id = users.user_id 
    INNER JOIN survey ON 
    survey.survey_id = survey_info.survey_id WHERE users.user_id = :user_id AND status='pending' " ; 
    $stmt = $con->prepare($q) ; 
    $stmt->execute(array(
        ':user_id' => $_SESSION['user_id']
    )) ; 
    $count_all_rows = $stmt->rowCount() ; 

    if(isset($_POST['search']['value'])){
        $search_value = $_POST['search']['value'] ; 
        $q .= "AND (survey.survey_title LIKE '%" . $search_value . "%'" ; 
        $q .= " OR survey_info.sent_date LIKE '%" . $search_value . "%'" ; 
        $q .= " OR survey.survey_expire_date LIKE '%" . $search_value . "%'" ; 
        $q .= " OR survey_info.status LIKE '%" . $search_value . "%')" ; 
         
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
    $stmt->execute(array(
        ':user_id' => $_SESSION['user_id']
    )) ; 
    $filtered_rows = $stmt->rowCount() ; 
    $counter= 1 ; 
    if($stmt->rowCount()){
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $sub_arr = array() ; 
            $sub_arr[] = $counter; 
            $sub_arr[] = $row['survey_title'] ; 
            $sub_arr[] = $row['sent_date'] ; 
            $sub_arr[] = $row['survey_expire_date'] ; 
            $sub_arr[] = '<span class="btn btn-sm bg-danger text-light">'.$row['status'].'</span>' ; 
            $sub_arr[] = '<a class="btn btn-md btn-info" href="../takeSurvey/index.php?survey_id='.$row['survey_id'].'" target="_blank"><i class="fa-solid fa-list"></i></a>' ; 
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
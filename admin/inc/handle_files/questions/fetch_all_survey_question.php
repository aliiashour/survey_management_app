<?php
    session_start() ; 
    include_once "../../../config/connect_database.php" ;  
    $survey_id = $_GET['survey_id'] ; 

    $q = "SELECT * FROM questions WHERE question_survey_id =:survey_id"; 
    $stmt = $con->prepare($q) ; 
    $stmt->execute(array(
        ':survey_id'=>$survey_id
    )) ; 
    $count_all_rows = $stmt->rowCount() ; 

    if(isset($_POST['search']['value'])){
        $search_value = $_POST['search']['value'] ; 
        $q .= " AND ( question_title LIKE '%" . $search_value . "%')" ; 
         
    }

    if(isset($_POST['order'])){
        $column = $_POST['order'][0]['column'] ; 
        $order = $_POST['order'][0]['dir'] ; 
        $q .= " order by " . $column . " " . $order ; 
    }else{
        $q .= " order by question_title ASC" ; 
    }

    if(isset($_POST['length']) && $_POST['length'] != -1){
        $start = $_POST['start'] ; 
        $length = $_POST['length'];
        $q .= ' LIMIT ' . $start . ', ' . $length ; 
    }

    $data =array() ; 
    $stmt = $con->prepare($q) ;  
    $stmt->execute(array(
        ':survey_id'=>$survey_id
    )) ; 
    $filtered_rows = $stmt->rowCount() ; 
    $counter= 1 ; 
    if($stmt->rowCount()){
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $sub_arr = array() ; 
            $sub_arr[] = $counter; 
            $sub_arr[] = $row['question_title'] ; 
            $sub_arr[] = '
                            <button data-title="review_survey" data-action="review" id="review_button" class="btn btn-info" data-survey_id="' . $row['question_survey_id'] .  '"><i class="fa-solid fa-eye"></i></button>
                            <button id="delete_button" class="btn btn-danger" data-survey_id="' . $row['question_survey_id'] . '"><i class="fa-sharp fa-solid fa-trash"></i></button>
                        ' ; 
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
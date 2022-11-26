<?php
    session_start() ; 
    include_once "../../../config/connect_database.php" ;  
    $q = "SELECT * FROM survey " ; 
    $stmt = $con->prepare($q) ; 
    $stmt->execute() ; 
    $count_all_rows = $stmt->rowCount() ; 

    if(isset($_POST['search']['value'])){
        $search_value = $_POST['search']['value'] ; 
        $q .= "WHERE survey.survey_title LIKE '%" . $search_value . "%'" ; 
        $q .= " OR survey.survey_start_date LIKE '%" . $search_value . "%'" ; 
        $q .= " OR survey.survey_start_date LIKE '%" . $search_value . "%'" ; 
        $q .= " OR survey.survey_status LIKE '%" . $search_value . "%'" ; 
         
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
            $send_button = '<button';
            if(date("Y-m-d", time()) > $row['survey_expire_date']){
                $send_button .= ' disabled title="can\'t send expired survey"';
            }
            $send_button .= '  id="send_button" class="btn btn-info" data-survey_id="' . $row['survey_id'] . '"><i class="fa-solid fa-paper-plane-top"></i>SEND</button>' ;
            $sub_arr[] =  $send_button; 
            $sub_arr[] = $row['survey_title'] ; 
            $sub_arr[] = $row['survey_start_date'] ; 
            $sub_arr[] = $row['survey_expire_date'] ; 
            if($row['survey_status'] == 'active'){
                $sub_arr[] = '<span class="btn btn-md bg-primary text-light">' . $row['survey_status'] . '</span>' ; 
            }else{
                $sub_arr[] = '<span class="btn btn-md bg-danger text-light">' . $row['survey_status'] . '</span>' ; 
            }
            $sub_arr[] = '
                            <button data-title="review_survey" data-action="review" id="review_button" class="btn btn-info" data-survey_id="' . $row['survey_id'] .  '"><i class="fa-solid fa-eye"></i></button>
                            <a style="text-decoration:none" href="../questions/index.php?survey_id='. $row['survey_id'] .'">
                                <button class="btn btn-secondary" data-survey_id="' . $row['survey_id'] .  '">
                                    <i class="fa-solid fa-list"></i>
                                </button>
                            </a>
                            <button data-title="Edit Survey" data-action="update" id="edit_button" class="btn btn-warning" data-survey_id="' . $row['survey_id'] . '"><i class="fa-solid fa-pen-to-square"></i></button>
                            <button id="delete_button" class="btn btn-danger" data-survey_id="' . $row['survey_id'] . '"><i class="fa-sharp fa-solid fa-trash"></i></button>
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
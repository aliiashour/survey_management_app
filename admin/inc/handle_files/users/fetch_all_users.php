<?php
    session_start() ; 
    include_once "../../../config/connect_database.php" ;  
    $q = "SELECT * FROM users WHERE user_id != ? " ; 
    $stmt = $con->prepare($q) ; 
    $stmt->execute(array($_SESSION['user_id'])) ; 
    $count_all_rows = $stmt->rowCount() ; 

    if(isset($_POST['search']['value'])){
        $search_value = $_POST['search']['value'] ; 
        $q .= "AND(users.user_name LIKE '%" . $search_value . "%'" ; 
        $q .= " OR users.user_email LIKE '%" . $search_value . "%'" ; 
        $q .= " OR users.user_created_at LIKE '%" . $search_value . "%'" ; 
        $q .= " OR users.user_status LIKE '%" . $search_value . "%')" ; 
         
    }

    if(isset($_POST['order'])){
        $column = $_POST['order'][0]['column'] ; 
        $order = $_POST['order'][0]['dir'] ; 
        $q .= " order by " . $column . " " . $order ; 
    }else{
        $q .= " order by user_name ASC" ; 
    }

    if(isset($_POST['length']) && $_POST['length'] != -1){
        $start = $_POST['start'] ; 
        $length = $_POST['length'];
        $q .= ' LIMIT ' . $start . ', ' . $length ; 
    }

    $data =array() ; 
    $stmt = $con->prepare($q) ; 
    $stmt->execute(array($_SESSION['user_id'])) ; 
    $filtered_rows = $stmt->rowCount() ; 
    $counter= 1 ; 
    if($stmt->rowCount()){
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $sub_arr = array() ; 
            $sub_arr[] = $counter; 
            $sub_arr[] = $row['user_name'] ; 
            $sub_arr[] = $row['user_email'] ; 
            $sub_arr[] = $row['user_created_at'] ; 

            if($row['user_status'] == 'pended'){
                $sub_arr[] = '<span class="btn btn-md bg-danger text-light" style="pointer-events:none">'.$row['user_status'].'</span>' ; 
            }else{
                $sub_arr[] = '<span class="btn btn-md bg-primary text-light" style="pointer-events:none">'.$row['user_status'].'</span>' ; 
            }

            $sub_arr[] = '
                            <button data-title="Edit User" data-action="update" id="edit_button" class="btn btn-warning" data-user_id="' . $row['user_id'] . '"><i class="fa-solid fa-pen-to-square"></i></button>
                            <button id="delete_button" class="btn btn-danger" data-user_id="' . $row['user_id'] . '"><i class="fa-sharp fa-solid fa-trash"></i></button>
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
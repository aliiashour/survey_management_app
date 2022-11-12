<?php 
    $page_title = "Survey management system"  ; 
    include_once "../init.php" ; 

    if(!isset($_SESSION['user_id'])){
        header('location:../../login.php') ; 
    }
    $q = "SELECT * FROM USERS WHERE user_id = ?" ; 
    $stmt = $con->prepare($q) ; 
    $stmt->execute(array($_SESSION['user_id'])) ; 
    if($stmt->rowCount()): 
        $res = $stmt->fetch() ; 
    ?>
        <div class="container-fluid">
            <div class="row">
                <nav class="navbar navbar-expand-lg bg-dark">
                    <div class="container-fluid">
                        <a class="navbar-brand text-light" href="./">SurveyBuilder</a>
                        
                        <div class="collapse navbar-collapse justify-content-end" id="navbarSupportedContent">
                            <ul class="navbar-nav mb-2 mb-lg-0">
                                <li class="nav-item">
                                    <div class="dropdown">
                                        <span class="text-light dropdown-toggle" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                                            <?php echo $res['user_email']?>
                                        </span>
                                        <ul class="dropdown-menu">
                                            <li><a class="dropdown-item" href="./report.php">surveys Report</a></li>
                                            <li><a class="dropdown-item" href="../users/">Users</a></li>
                                            <hr>
                                            <li><span style="cursor:pointer" id="change_password_button" class="dropdown-item" href="" data-modal="reset_password_modal">change password</span></li>
                                            <li><a class="dropdown-item" href="../../logout.php">logout</a></li>
                                        </ul>
                                    </div>
                                </li>
                            </ul>
                        </div>
                    </div>
                </nav>
            </div>
            <div class="row">
                <div class="col-12 text-end">
                    <button data-title="Add Survey" data-action="add" id ="add_survey_button" class="mt-3 mb-3 btn btn-lg btn-primary"><i class="fa-solid fa-plus"></i> Add Survey</button>
                </div>
                <div class="col-12">
                    <!-- 
                        get all surveys i submit
                        surveys report
                    -->                    
                    <div id="response"></div>
                    <table id="datatable" class="table hover">
                        <thead>
                            <th>S.N</th>
                            <th>Selection</th>
                            <th>Name</th>
                            <th>Start date</th>
                            <th>Expire date</th>
                            <th>Status</th>
                            <th>Action</th>
                        </thead>
                        <tbody>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <?php include_once $templates. "footer.php" ?>
        <script>
            // setting datatable
            $("#datatable").DataTable({
                "pagingType": 'full_numbers',
                // "processing":true,
                "reponsive":true,
                "language":{
                    "search":"_INPUT_",
                    "searchPlaceholder":"Search..."
                },
                "serverSide" : true,
                "select" : true,
                "lengthChange":true,
                "paging":true,
                "order":[],
                "ajax":{
                    "url":"../inc/handle_files/surveys/fetch_all_surveys.php",
                    "type":"post",
                },
                "fnCreateRow":function(nRow, aData, iDataIndex){
                    $(nRow).attr('id', aData[0]) ; 
                },
                "columnDefs":[{
                    "target":[0,6],
                    "orderable":false,
                }]
            }) ; 

/////////////////////////////////////////////////////////////////////////

            $("#add_survey_button").on('click', function(){
                $modal_title = $(this).data("title") ; 
                $modal_action = $(this).data("action") ; 
                $("#title").html($modal_title) ;
                $("#action").html($modal_action) ;
                var survey_id = $("#survey_id").val('') ; 
                var survey_title = $("#survey_title").val('') ; 
                var survey_start_date = $("#survey_start_date").val('') ; 
                var survey_expire_date = $("#survey_expire_date").val('') ; 
                var survey_status = $("#survey_status").val('active');
                $("#survey_modal").modal('show') ; 
            })

            // submit the modal 
            $(document).on("submit", "#survey_modal", function(event){
                event.preventDefault() ;
                var survey_id = $("#survey_id").val() ; 
                var survey_title = $("#survey_title").val() ; 
                var survey_start_date = $("#survey_start_date").val() ; 
                var survey_expire_date = $("#survey_expire_date").val() ; 
                var survey_status = $("#survey_status").val();
                var url = "../inc/handle_files/surveys/" + $("#action").html() + "_survey.php" ; 
                if(survey_title != ''  && survey_start_date != ''  && survey_expire_date != ''){
                    $.ajax({
                        url:url,
                        method:"post",
                        data:{survey_id:survey_id, survey_title:survey_title, survey_start_date:survey_start_date, survey_expire_date:survey_expire_date, survey_status:survey_status},
                        success:function(data){
                            var json = JSON.parse(data) ; 
                            if(json.status == "success"){
                                $("#datatable").DataTable().draw() ; 
                                $("#response-form").html('<div class="alert alert-success">' + json.msg + '</div>') ; 
                                $("#survey_title").val('') ; 
                                $("#survey_start_date").val('') ; 
                                $("#survey_expire_date").val('') ; 
                                $("#survey_status").val('') ; 
                            }else{
                                $("#response-form").html('<div class="alert alert-danger">' +json.msg + '</div>') ; 
                            }
                            setTimeout(function(){
                                $("#response").html('');
                                $("#response-form").html('');
                            }, 2000);
                        }
                    }) ; 
                }else{
                    $("#response-form").html('<div class="alert alert-danger">fill all fieds</div>') ; 
                    setTimeout(function(){
                        $("#response-form").html('') ; 
                    }, 2000) ; 
                
                } 
            }) ;
            
            // click to edit button
            $(document).on('click', '#edit_button', function(){
                var survey_id = $(this).data('survey_id') ; 
                $modal_title = $(this).data("title") ; 
                $modal_action = $(this).data("action") ; 
                $("#title").html($modal_title) ;
                $("#action").html($modal_action) ;
                if(survey_id != ''){
                    // now fetch user data
                    $.ajax({
                        url:"../inc/handle_files/surveys/fetch_single_survey.php",
                        method:"POST",
                        data:{survey_id:survey_id},
                        success:function(data){
                            var json = JSON.parse(data) ;
                            if(json.status =='found'){
                                // exist user
                                $("#survey_id").val(json['data']['survey_id']) ; 
                                $("#survey_title").val(json['data']['survey_title']) ; 
                                $("#survey_start_date").val(json['data']['survey_start_date']) ; 
                                $("#survey_expire_date").val(json['data']['survey_expire_date']) ; 
                                $("#survey_status").val(json['data']['survey_status']) ;
                                $("#survey_modal").modal('show') ; 
                            }
                        }
                    }) ; 
                }
            }) ;

            // delete user
            $(document).on('click', '#delete_button', function(){
                var survey_id = $(this).data("survey_id") ; 
                // now delete user directly
                if(confirm('are you sure?')){
                    $.ajax({
                        url:"../inc/handle_files/surveys/delete_survey.php",
                        method:"POST",
                        data:{survey_id:survey_id},
                        success:function(data){
                            json = JSON.parse(data) ; 
                            if(json['status'] == 'success'){
                                $("#datatable").DataTable().draw() ; 
                                $("#response").html('<div class="alert alert-success text-start">survey deleted</div>') ; 
                                setTimeout(function(){
                                    $("#response").html('') ; 
                                }, 2000) ; 
                            }
                        }
                    });
                }
            }) ; 
            //////////////////////////////


            // open reset_password_modal when clicking the change_password_button
            $("#change_password_button").on('click', function(){
                $("#reset_password_modal").modal('show') ; 
            })

            // submit the modal 
            $(document).on("submit", "#reset_password_modal", function(event){
                event.preventDefault() ; 
                var old_password = $("#old_password").val() ; 
                var new_password = $("#new_password").val() ;
                if(old_password != '' && new_password != ''){
                    // what id the old == new 
                    if(old_password==new_password){
                        $("#response_form").html('<div class="alert alert-danger">can not use the old one as the new password</div>') ; 
                    }else{
                        $.ajax({
                            url:"../inc/handle_files/users/reset_password.php",
                            method:"POST",
                            data:{old_password:old_password, new_password:new_password},
                            beforeSend:function(){
                                $("#reset_button").html('wait..');
                                $("#reset_button").attr('disabled', 'disabled');
                            },
                            success:function(data){
                                var json = JSON.parse(data) ; 
                                if(json.status == 'true'){
                                    // successfully change password
                                    $("#response").html('<div class="alert alert-success">password succefully changed</div>') ;
                                    var old_password = $("#old_password").val('') ; 
                                    var new_password = $("#new_password").val('') ; 
                                    $("#reset_password_modal").modal('hide') ; 
                                }else{
                                    // the old one is diffrent
                                    $("#response_form").html('<div class="alert alert-danger">'+ json.error +'</div>') ; 
                                }
                                $("#reset_button").attr('disabled', false);
                                $("#reset_button").html('Reset');
                            }
                        });
                    }
                }else{
                    $("#response_form").html('<div class="alert alert-danger">fill all record</div>') ; 
                }
                setTimeout(function(){
                    $("#response_form").html('');
                    $("#response").html('');
                }, 2000);

            });
            
            // send survey to user
            $(document).on('click', '#send_button', function(){
                var survey_id = $(this).data("survey_id") ; 
                // now delete user directly
                if(confirm('are you sure?')){
                    $.ajax({
                        url:"../inc/handle_files/surveys/send_survey.php",
                        method:"POST",
                        data:{survey_id:survey_id, user_email:localStorage.getItem("email").trim()},
                        success:function(data){
                            console.log(localStorage.getItem("email").trim()); 
                            json = JSON.parse(data) ; 
                            if(json['status'] == 'success'){
                                $("#datatable").DataTable().draw() ; 
                                $("#response").html('<div class="alert alert-success text-start">'+json['msg']+'</div>') ;
                            }else{
                                $("#response").html('<div class="alert alert-danger text-start">'+json['msg']+'</div>') ; 
                            }
                        }
                    });
                    setTimeout(function(){
                        $("#response").html('') ; 
                    }, 2000) ; 
                }
            }) ; 
        </script>
    <?php else: ?>
        <p>you should not to be here</p>
    <?php endif ?>
    <div id="survey_modal" class="modal" tabindex="-1">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="title"></h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form class="row g-3" method="POST">
                    <input type="hidden" id="survey_id">
                    <div id="response-form"></div>
                    <div class="col-md-6">
                        <label for="inputName" class="form-label">Survey Title</label>
                        <input type="text" class="form-control" id="survey_title" name="survey_title">
                    </div>
                    <div class="col-md-6">
                        <label for="inputEmail4" class="form-label">Survey Start Date</label>
                        <input type="date" class="form-control" id="survey_start_date" name="survey_start_date">
                    </div>
                    <div class="col-md-6">
                        <label for="inputPassword4" class="form-label">Survey Expire Date</label>
                        <input type="date" class="form-control" id="survey_expire_date" name="survey_expire_date">
                    </div>
                    <div class="col-md-4">
                        <label for="inputState" class="form-label">Status</label>
                        <select name="survey_status" id="survey_status" class="form-select">
                            <option selected>Choose...</option>
                            <option value="pended">pended</option>
                            <option value="active" selected>active</option>
                        </select>
                    </div>
            </div>
            <div class="modal-footer">
                    <div class="col-12 text-end">
                        <button type="submit" class="btn btn-primary" id="action"></button>
                    </div>
                </form>
            </div>
            </div>
        </div>
    </div>

    <!-- Modal to reset password -->
    <div id="reset_password_modal" class="modal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Reset Password</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form>
                    <div id="response_form"></div>
                    <div class="mb-3">
                        <label for="exampleInputPassword1" class="form-label">Old Password</label>
                        <input type="password" class="form-control" name="old_password" id="old_password">
                    </div>
                    <div class="mb-3">
                        <label for="exampleInputPassword1" class="form-label">New Password</label>
                        <input type="password" class="form-control" name="new_password" id="new_password">
                    </div>
            </div>
            <div class="modal-footer">
                    <button type="submit" class="btn btn-primary bg-danger" id="reset_button">Reset</button>
                </form>
            </div>
            </div>
        </div>
    </div>
    
</body>
</html>

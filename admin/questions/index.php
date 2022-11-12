<?php 
    
    $page_title = "Survey questions"  ; 
    include_once "../init.php" ; 

    if(!isset($_SESSION['user_id'])){
        header('location:../../login.php') ; 
    }
    
    $survey_id = $_GET['survey_id'] ; 

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
                        <a class="navbar-brand text-light" href="../surveys/">SurveyBuilder</a>
                        
                        <div class="collapse navbar-collapse justify-content-end" id="navbarSupportedContent">
                            <ul class="navbar-nav mb-2 mb-lg-0">
                                <li class="nav-item">
                                    <div class="dropdown">
                                        <span class="text-light dropdown-toggle" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                                            <?php echo $res['user_email']?>
                                        </span>
                                        <input type="hidden" id="survey_id" value="<?php echo $survey_id?>">
                                        <ul class="dropdown-menu">
                                            <li><a class="dropdown-item" href="../surveys/report.php">surveys Report</a></li>
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
                    <button data-title="Add Question" data-action="add" id ="add_question_button" class="mt-3 mb-3 btn btn-lg btn-primary">
                        <i class="fa-solid fa-plus"></i> 
                        Add Question
                    </button>
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
                            <th>Question</th>
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
            var survey_id = $("#survey_id").val() ;
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
                    "url":"../inc/handle_files/questions/fetch_all_survey_question.php?survey_id="+survey_id,
                    "type":"post",
                },
                "fnCreateRow":function(nRow, aData, iDataIndex){
                    $(nRow).attr('id', aData[0]) ; 
                },
                "columnDefs":[{
                    "target":[0,2],
                    "orderable":false,
                }]
            }) ; 


            $("#add_question_button").on('click', function(){
                $modal_title = $(this).data("title") ; 
                $modal_action = $(this).data("action") ; 
                $("#title").html($modal_title) ;
                $("#action").html($modal_action) ;
                var question_title = $("#question_title").val('') ; 
                $("#question_modal").modal('show') ; 
                $("#question_type").click(function(){
                    $("#tbl_result").html("") ; 
                    var type = $("#question_type option:selected" ).val() ; 
                    if(type =="RADIO" || type =="CHECK"){
                        if(type == "RADIO"){
                            type = 'radio'  ; 
                        }else{
                            type = 'checkbox'  ; 
                        }
                        var indx = 0 ; 
                        var html = '<tr><td><div class="row"><div id="response-form" class="mb-1"></div><div class="col-md-8"><input type="text" name="value" class="form-control" data-create="'+type+'" id="input_type"></div><div class="col-md-4"><span id="add_it" class="btn btn-sm btn-primary">add</span></div></td></tr></div>';

                        $("#tbl_result").prepend(html) ; 
                        $("#add_it").on('click', function(){
                            indx++ ; 
                            var option = '<tr><td><div class="row"><div id="response-form" class="mb-1"></div><div class="col-md-8"><input type="text" name="value" class="form-control" data-create="'+type+'"></div><div class="col-md-4"><span id="delete_it" class="btn btn-sm btn-danger">delete</span></div></td></tr></div>';
                            $("#tbl_result").append(option) ; 
                        }) ; 

                        $("#tbl_result").on('click','#delete_it', function(){
                            $("#tbl_result tr:last").remove() ; 
                            indx-- ; 
                        }) ; 
                    }else{
                        type = 'text'  ; 
                    }

                });
            })

            // submit the modal 
            $(document).on("submit", "#question_modal", function(event){

                event.preventDefault() ;
                var survey_id = $("#survey_id").val() ; 


                var question_title = $("#question_title").val() ; 
                // 01
                var input_type = $("#input_type").data('create')?$("#input_type").data('create'):'text' ; 
                var values = [] ; 
                $('[name="value"]').each(function(i){
                    values[i] = $(this).val().trim() ; 
                }) ;
                // 02
                var input_value = values.join(", ") ;
                input_value = input_value?input_value:"" ; 
                // 03
                var input_num = values.length ; 
                var url = "../inc/handle_files/questions/" + $("#action").html() + "_question.php" ; 
                if(question_title != '' ){
                    $.ajax({
                        url:url,
                        method:"post",
                        data:{survey_id:survey_id, question_title:question_title, question_type:input_type, answer_values:input_value, answer_count:input_num},
                        success:function(data){
                            var json = JSON.parse(data) ; 
                            if(json.status == "success"){
                                $("#datatable").DataTable().draw() ; 
                                $("#response-form").html('<div class="alert alert-success">' + json.msg + '</div>') ; 
                                $("#question_title").val('') ;  
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
            
            // delete question
            $(document).on('click', '#delete_button', function(){
                var question_id = $(this).data("question_id") ; 
                // now delete user directly
                if(confirm('are you sure?')){
                    $.ajax({
                        url:"../inc/handle_files/questions/delete_question.php",
                        method:"POST",
                        data:{question_id:question_id},
                        success:function(data){
                            json = JSON.parse(data) ; 
                            if(json['status'] == 'success'){
                                $("#datatable").DataTable().draw() ; 
                                $("#response").html('<div class="alert alert-success text-start">question deleted</div>') ; 
                                setTimeout(function(){
                                    $("#response").html('') ; 
                                }, 2000) ; 
                            }
                        }
                    });
                }
            }) ; 

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


            // set in result the question_type


        </script>
    <?php else: ?>
        <p>you should not to be here</p>
    <?php endif ?>
    <div id="question_modal" class="modal" tabindex="-1">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="title"></h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form class="row g-3" method="POST">
                    <div id="response-form"></div>
                    <div class="col-md-8">
                        <label for="inputName" class="form-label">Enter question </label>
                        <input type="text" class="form-control" id="question_title" name="question_title">
                    </div>
                    <div class="col-md-4">
                        <label for="inputState" class="form-label">Type</label>
                        <select name="question_type" id="question_type" class="form-select">
                            <option value="TEXT" selected>Text Box</option>
                            <option value="RADIO">Choose One</option>
                            <option value="CHECK">Choose More</option>
                        </select>
                    </div>
                    <div class="col-md-12">
                        <div class="row" id="result">
                            <!-- here just what i want is when click in selection box -->
                            <!-- the result appear in front of me -->
                            <table id="tbl_result"></table>
                        </div>
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

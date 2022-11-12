<?php 
    $page_title = "Users"  ; 
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
                        <a class="navbar-brand text-light" href="../surveys">SurveyBuilder</a>
                        
                        <div class="collapse navbar-collapse justify-content-end" id="navbarSupportedContent">
                            <ul class="navbar-nav mb-2 mb-lg-0">
                                <li class="nav-item">
                                    <div class="dropdown">
                                        <span class="text-light dropdown-toggle" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                                            <?php echo $res['user_email']?>
                                        </span>
                                        <ul class="dropdown-menu">
                                            <li><a class="dropdown-item" href="../surveys/report.php">surveys Report</a></li>
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
                    <button data-action="add" data-title="Add User" dtat-action = "Create" id ="add_user_button" class="mt-3 mb-3 btn btn-lg btn-primary"><i class="fa-solid fa-plus"></i> Add User</button>
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
                            <th>Name</th>
                            <th>Email</th>
                            <th>Created at</th>
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
                    "url":"../inc/handle_files/users/fetch_all_users.php",
                    "type":"post",
                },
                "fnCreateRow":function(nRow, aData, iDataIndex){
                    $(nRow).attr('id', aData[0]) ; 
                },
                "columnDefs":[{
                    "target":[0,5],
                    "orderable":false,
                }]
            }) ; 


            // open reset_password_modal when clicking the change_password_button
            $("#add_user_button").on('click', function(){
                $modal_title = $(this).data("title") ; 
                $modal_action = $(this).data("action") ; 
                $("#title").html($modal_title) ;
                $("#action").html($modal_action) ;
                $("#user_modal").modal('show') ; 
            })

            // submit the modal 
            $(document).on("submit", "#user_modal", function(event){
                event.preventDefault() ;
                var user_id = $("#user_id").val() ; 
                var user_name = $("#user_name").val() ; 
                var user_email = $("#user_email").val() ; 
                var user_password = $("#user_password").val() ; 
                var user_status = $("#user_status").val();
                var url = "../inc/handle_files/users/" + $("#action").html() + "_user.php" ; 
                if(user_name != ''  && user_email != ''  && user_status != ''){
                    $.ajax({
                        url:url,
                        method:"post",
                        data:{user_id:user_id, user_name:user_name, user_email:user_email, user_password:user_password, user_status:user_status},
                        success:function(data){
                            var json = JSON.parse(data) ; 
                            if(json.status == "success"){
                                $("#datatable").DataTable().draw() ; 
                                $("#response-form").html('<div class="alert alert-success">' + json.msg + '</div>') ; 
                                $("#user_name").val('') ; 
                                $("#user_email").val('') ; 
                                $("#user_password").val('') ; 
                                $("#user_status").val('') ; 
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
                var user_id = $(this).data('user_id') ; 
                $modal_title = $(this).data("title") ; 
                $modal_action = $(this).data("action") ; 
                $("#title").html($modal_title) ;
                $("#action").html($modal_action) ;
                if(user_id != ''){
                    // now fetch user data
                    $.ajax({
                        url:"../inc/handle_files/users/fetch_single_user.php",
                        method:"POST",
                        data:{user_id:user_id},
                        success:function(data){
                            var json = JSON.parse(data) ;
                            if(json.status =='found'){
                                // exist user
                                $("#user_id").val(json['data']['user_id']) ; 
                                $("#user_name").val(json['data']['user_name']) ; 
                                $("#user_email").val(json['data']['user_email']) ; 
                                $("#user_status").val(json['data']['user_status']) ;
                                $("#user_password").val('') ;
                                $("#user_modal").modal('show') ; 
                            }
                        }
                    }) ; 
                }
            }) ;

            // delete user
            $(document).on('click', '#delete_button', function(){
                var user_id = $(this).data("user_id") ; 
                // now delete user directly
                if(confirm('are you sure?')){
                    $.ajax({
                        url:"../inc/handle_files/users/delete_user.php",
                        method:"POST",
                        data:{user_id:user_id},
                        success:function(data){
                            json = JSON.parse(data) ; 
                            if(json['status'] == 'success'){
                                $("#datatable").DataTable().draw() ; 
                                $("#response").html('<div class="alert alert-success text-start">user deleted</div>') ; 
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

            // get selection
            function getSelectionText() {
                var text = "";
                if (window.getSelection) {
                    text = window.getSelection().toString();
                } else if (document.selection && document.selection.type != "Control") {
                    text = document.selection.createRange().text;
                }
                    return text;
            }
            setInterval(function(){
                localStorage.setItem("email", getSelectionText()) ;  
            }, 1000);

        </script>
    <?php else: ?>
        <p>you should not to be here</p>
    <?php endif ?>
    <div id="user_modal" class="modal" tabindex="-1">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="title"></h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form class="row g-3" method="POST">
                    <input type="hidden" id="user_id">
                    <div id="response-form"></div>
                    <div class="col-md-6">
                        <label for="inputName" class="form-label">User name</label>
                        <input type="text" class="form-control" id="user_name" name="user_name">
                    </div>
                    <div class="col-md-6">
                        <label for="inputEmail4" class="form-label">Email</label>
                        <input type="email" class="form-control" id="user_email" name="user_email">
                    </div>
                    <div class="col-md-6">
                        <label for="inputPassword4" class="form-label">Password</label>
                        <input type="password" class="form-control" id="user_password" name="user_password">
                    </div>
                    <div class="col-md-4">
                        <label for="inputState" class="form-label">Status</label>
                        <select name="user_status" id="user_status" class="form-select">
                            <option selected>Choose...</option>
                            <option value="pended">pended</option>
                            <option value="active">active</option>
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

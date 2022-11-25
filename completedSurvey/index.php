<?php 
    $page_title = "Survey management system"  ; 
    include_once "./init.php" ; 

    if(!isset($_SESSION['user_id'])){
        header('location:../login.php') ; 
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
                        <a class="navbar-brand text-light" href="../pendingSurvey/">SurveyBuilder</a>
                        
                        <div class="collapse navbar-collapse justify-content-end" id="navbarSupportedContent">
                            <ul class="navbar-nav mb-2 mb-lg-0">
                                <li class="nav-item">
                                    <div class="dropdown">
                                        <span class="text-light dropdown-toggle" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                                            <?php echo $res['user_email']?>
                                        </span>
                                        <ul class="dropdown-menu">
                                            <li><span id="change_password_button" class="dropdown-item" href="" data-modal="reset_password_modal">change password</span></li>
                                            <hr>
                                            <li><a class="dropdown-item" href="../logout.php">logout</a></li>
                                        </ul>
                                    </div>
                                    <!-- <a class="nav-link text-light" href="../logout.php">logout</a> -->
                                </li>
                            </ul>
                        </div>
                    </div>
                </nav>
            </div>
            <div class="row">
                <div class="col-12 text-end">
                    <button onclick="change_dir()" class="mt-3 mb-3 btn btn-lg btn-danger"><i class="fa-solid fa-pen-to-square"></i> Pending Surveys</button>
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
                            <th>Complete date</th>
                            <th>Sent date</th>
                            <th>Status</th>
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
                    "url":"../inc/handle_files/fetch_complete_surveys.php",
                    "type":"post",
                },
                "fnCreateRow":function(nRow, aData, iDataIndex){
                    $(nRow).attr('id', aData[0]) ; 
                },
                "columnDefs":[{
                    "target":[0,4],
                    "orderable":false,
                }]
            }) ; 

            // change header direction to pending page
            function change_dir(){
                var current_location = window.location.href ; 
                new_location = current_location.replace('/completedSurvey/', '/pendingSurvey/');
                window.location.href = new_location ; 
            }

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
                            url:"../inc/handle_files/reset_password.php",
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

        </script>

    <?php else: ?>
        <p>you should not to be here</p>
    <?php endif ?>
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

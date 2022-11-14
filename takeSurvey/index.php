<?php 
    $page_title = "Take Survey"  ; 
    include_once "./init.php" ; 

    if(!isset($_SESSION['user_id'])){
        header('location:../login.php') ; 
    }
    if(isset($_GET['survey_id']) && !empty($_GET['survey_id'])){
        $survey_id = is_numeric($_GET['survey_id'])? $_GET['survey_id'] : 0;
        if($survey_id == 0){
            // totally error
            echo "Enter valid Survey Id" ; 
        }else{
            // search for this survey first
            $q = "SELECT * FROM survey WHERE survey_id = ?" ; 
            $stmt = $con->prepare($q) ; 
            $stmt->execute(array($survey_id)) ; 
            if($stmt->rowCount()){
                // found survey 
                // find out if it is aleady taken from this sessioned user
                $q = "SELECT status FROM survey_info WHERE survey_id =:survey_id AND user_id = :user_id" ; 
                $stmt = $con->prepare($q) ; 
                $stmt->execute(array(
                    ':survey_id' => $survey_id,
                    ':user_id' => $_SESSION['user_id']
                )) ; 
                $res = $stmt->fetch() ; 
                if($res['status'] =='pending'){
                    // ok, now take it
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
                                        <a class="navbar-brand text-light" href="#">SurveyBuilder</a>
                                        
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
                                                </li>
                                            </ul>
                                        </div>
                                    </div>
                                </nav>
                            </div>
                        </div>
                        <div class="container mt-5">
                            <div class="row mt-5 justify-content-center">
                                <div class="col-8" style="background-color: #f3f3f3;padding-top:10px;padding-bottom:10px">
                                    <!-- need to select the survey questions here -->
                                    <?php
                                        $q = "SELECT question_id, question_title FROM questions WHERE question_survey_id = ?" ; 
                                        $stmt = $con->prepare($q) ; 
                                        $stmt->execute(array($survey_id)) ; 
                                        if($stmt->rowCount()){
                                            $rows = $stmt->fetchAll() ; 
                                            foreach ($rows as $row){
                                                $question_id = $row['question_id'];
                                                $question_title = $row['question_title'];
                                                // now geet the question details
                                                $q = "SELECT * FROM question_details WHERE question_id = ?" ; 
                                                $stmt = $con->prepare($q) ; 
                                                $stmt->execute(array($question_id)) ; 
                                                if($stmt->rowCount()){
                                                    $res = $stmt->fetch() ; 
                                                    if($res['question_type'] == 'TEXT'){
                                                        // textArea
                                                        // two divs question, answer
                                                        ?>
                                                            <div class="col-12">
                                                                <div class="row">
                                                                    <form id="form_<?php echo $question_id?>">
                                                                        <div class="mb-3">
                                                                            <input type="text" class="form-control" id="question_<?php echo $question_id?>" value="<?php echo $question_title ?>" disabled>
                                                                        </div>
                                                                        <div class="mb-3">
                                                                            <textarea class="form-control" id="textarea_<?php echo $question_id?>" rows="3"></textarea>
                                                                        </div>
                                                                        <div class="mb-3 text-end">
                                                                            <button type="submit" class="btn btn-primary">Send</button>
                                                                        </div>
                                                                    </form>
                                                                </div>
                                                            </div>
                                                            <hr>
                                                        <?php
                                                    }elseif($res['question_type'] == 'RADIO'){
                                                        $radio_num = $res['question_radio_num'] ; 
                                                        $radio_values = explode(", ", $res['question_radio_value']) ; 
                                                        // radio
                                                        // two divs question, answer
                                                        ?>
                                                        <div class="col-12">
                                                            <div class="row">
                                                                <form id="form_<?php echo $question_id?>">
                                                                    <div class="mb-3">
                                                                        <input type="text" class="form-control" id="question_<?php echo $question_id?>" value="<?php echo $question_title ?>" disabled>
                                                                    </div>
                                                                    <?php 
                                                                        // loop through radio num
                                                                        $r = '' ; 
                                                                        for ($i=0; $i < $radio_num; $i++) { 
                                                                            $r .= '<div class="form-check mb-3">
                                                                                        <input class="form-check-input" type="radio" name="answer[]" id="radio_'.$question_id.'_'.$i.'"';
                                                                                        if($i==0)
                                                                                            $r .= ' checked' ; 
                                                                                        $r .= '>
                                                                                        <label class="form-check-label" for="radio_'.$question_id.'_'.$i.'">
                                                                                            ';
                                                                            $r .= $radio_values[$i] ;
                                                                            $r .=       '</label>
                                                                                    </div>' ; 
                                                                        }
                                                                        echo $r ; 
                                                                    
                                                                    ?>
                                                                    <div class="mb-3 text-end">
                                                                        <button type="submit" class="btn btn-primary">Send</button>
                                                                    </div>
                                                                </form>
                                                            </div>
                                                        </div>
                                                        <hr>
                                                    <?php
                                                    }else{
                                                        // checkbox
                                                        $check_num = $res['question_check_num'] ; 
                                                        $check_values = explode(", ", $res['question_check_value']) ; 
                                                        // radio
                                                        // two divs question, answer
                                                        ?>
                                                        <div class="col-12">
                                                            <div class="row">
                                                                <form id="form_<?php echo $question_id?>">
                                                                    <div class="mb-3">
                                                                        <input type="text" class="form-control" id="question_<?php echo $question_id?>" value="<?php echo $question_title ?>" disabled>
                                                                    </div>
                                                                    <?php 
                                                                        // loop through radio num
                                                                        $r = '' ; 
                                                                        for ($i=0; $i < $check_num; $i++) { 
                                                                            $r .= '<div class="form-check mb-3">
                                                                                        <input class="form-check-input" type="checkbox" name="answer[]" id="radio_'.$question_id.'_'.$i.'"';
                                                                                        if($i==0)
                                                                                            $r .= ' checked' ; 
                                                                                        $r .= '>
                                                                                        <label class="form-check-label" for="radio_'.$question_id.'_'.$i.'">
                                                                                            ';
                                                                            $r .= $check_values[$i] ;
                                                                            $r .=       '</label>
                                                                                    </div>' ; 
                                                                        }
                                                                        echo $r ; 
                                                                    
                                                                    ?>
                                                                    <div class="mb-3 text-end">
                                                                        <button type="submit" class="btn btn-primary">Send</button>
                                                                    </div>
                                                                </form>
                                                            </div>
                                                        </div>
                                                        <hr>
                                                    <?php
                                                    }
                                                }
                                                // echo "$question_id => $question_title<br>" ; 
                                            }
                                        }else{
                                            echo "there are no question fro this survey yet" ; 
                                        }
                                    ?>
                                    <!-- the question design -->
                                </div>
                            </div>
                        </div>

                        <?php include_once $templates. "footer.php" ?>
                        <script>
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
            <?php
                }else{
                    echo '<div class="alert alert-success mt-5">you already complete this survey</div>' ; 
                }
            }else{
                echo "This survey not found" ; 
            }
        }
    }

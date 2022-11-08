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
                    <a class="navbar-brand text-light" href="#">SurveyBuilder</a>
                    
                    <div class="collapse navbar-collapse justify-content-end" id="navbarSupportedContent">
                        <ul class="navbar-nav mb-2 mb-lg-0">
                            <li class="nav-item">
                                <a class="nav-link text-light" href="../logout.php">logout</a>
                            </li>
                        </ul>
                    </div>
                    </div>
                </nav>
            </div>
            <div class="row">
                <div class="col-12 text-end">
                    <button onclick="change_dir()" class="mt-3 mb-3 btn btn-lg btn-primary"><i class="fa-solid fa-pen-to-square"></i> Pending Surveys</button>
                </div>
                <div class="col-12">
                    <!-- 
                        get all surveys i submit
                        surveys report
                    -->
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
        </script>
    <?php else: ?>
        <p>you should not to be here</p>
    <?php endif ?>
</body>
</html>

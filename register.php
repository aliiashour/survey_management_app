<?php
    $page_title = "register user" ; 
    include_once "./init.php" ; 
    $response = '' ; 
    if(isset($_POST['submit']) && $_POST['submit']='submit'){
        if($_REQUEST['user_name']){
            if($_REQUEST['user_email']){
                $q = 'SELECT * FROM users WHERE user_email = :user_email' ; 
                $stmt = $con->prepare($q) ; 
                $stmt->execute(array(
                    ':user_email' => $_REQUEST['user_email']
                )) ; 
                $count = $stmt->rowCount() ; 
                if(!$count){
                    $res = $stmt->fetch() ; 
                    if (strlen($_REQUEST['user_password']) > 0){
                        $q = 'INSERT INTO users(user_name, user_email, user_password) VALUES(:user_name, :user_email, :user_password)' ; 
                        $stmt = $con->prepare($q) ; 
                        $stmt->execute(array(
                            ':user_name' => $_REQUEST['user_name'],
                            ':user_email' => $_REQUEST['user_email'],
                            ':user_password' => sha1($_REQUEST['user_password'])
                        )) ; 
                        header('location:login.php') ; 
                    }else{
                        $response = '<div class="alert alert-danger"><strong>password</strong> is empty</div>' ;     
                    }
    
                }else{
                    $response = '<div class="alert alert-danger">this <strong>useremail</strong> is duplicate</div>' ; 
                }
    
            }else{
                $response = '<div class="alert alert-danger"><strong>email</strong> is empty</div>' ; 
            }    
        }else{
            $response = '<div class="alert alert-danger"><strong>name</strong> is empty</div>' ; 
        }
    }
?>

<div class="container">
    <div class="row justify-content-center">
        <div class="col-12 col-sm-10 col-md-8  register_form_container">
            <span id="response"><?php echo $response?></span>
            <form method="POST" id="user_register_form">
                <div class="mb-3">
                    <label for="user_name" class="form-label">Enter your name</label>
                    <input type="text" class="form-control" name = "user_name" id="user_name">
                </div>
                <div class="mb-3">
                    <label for="user_email" class="form-label">Enter your email</label>
                    <input type="text" class="form-control" name = "user_email" id="user_email">
                </div>
                <div class="mb-3">
                    <label for="user_password" class="form-label">Enter your apssword</label>
                    <input type="password" class="form-control" name = "user_password" id="user_password">
                </div>
                <div class="mb-3 form-check text-center">
                    <span>have already account?<a href="./login.php">signin</a></span>
                </div>
                <button type="submit" name ="submit" class="btn btn-primary" id="submit_button">signup</button>
            </form>
        </div>
    </div>
</div>



<?php

    include_once $templates."footer.php" ; 
?>
</body>
</html>

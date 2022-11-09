<?php 
    $page_title = "login" ; 
    include_once "./init.php" ; 
    $response = '' ;  
    if(isset($_SESSION['user_id']) && isset($_SESSION['user_type']) && $_SESSION['user_type'] == 'USER'){
        header('location:pendingSurvey/') ; 
    }  
    if(isset($_SESSION['user_id']) && isset($_SESSION['user_type']) && $_SESSION['user_type'] == 'ADMIN'){
        header('location:admin/') ; 
    }
    if(isset($_POST['login']) && $_POST['login']='login'){
        if($_POST['user_email']){
            $q = 'SELECT * FROM users WHERE user_email = :user_email' ; 
            $stmt = $con->prepare($q) ; 
            $stmt->execute(array(
                ':user_email' => $_POST['user_email']
            )) ; 
            $count = $stmt->rowCount() ; 
            if($count){
                $res = $stmt->fetch();
                if(sha1($_POST["password"])==$res['user_password']){
                    $_SESSION['user_id'] = $res['user_id'] ; 
                    $_SESSION['user_name'] = $res['user_name'] ; 
                    if($res['user_type'] == 'USER'){
                        $_SESSION['user_type'] = 'USER' ; 
                        header('location:pendingSurvey/') ; 
                    }else{
                        $_SESSION['user_type'] = 'ADMIN' ; 
                        header('location:admin/') ; 
                    }
                }else{
                    $response = '<div class="alert alert-danger">wrong password</div>' ;     
                }
            }else{
                $response = '<div class="alert alert-danger">this email not found</div>' ; 
            }

        }else{
            $response = '<div class="alert alert-danger">Enter your email</div>' ; 
        }
    }



?>


<div class="container">
    <div class="row justify-content-center mt-5">
        <div class="col-12 col-sm-10 col-md-8 mt-5">
            <div id="response"><?php echo $response ; ?></div>
            <form method="POST">
                <div class="mb-3">
                    <label for="user_email" class="form-label">Enter your Email</label>
                    <input type="email" class="form-control" name="user_email" required>
                </div>
                <div class="mb-3">
                    <label for="user_password" class="form-label">Enter you password</label>
                    <input type="password" class="form-control" name="password">
                </div>
                <div class="mb-3 form-check text-center">
                    <span>you have no account?<a href="./register.php">register</a></span>
                </div>
                
                <div class="mb-3">
                    <input type="submit" class="btn btn-primary" name="login" value="login">
                </div>
            </form>
        </div>
    </div>
</div>


<?php include_once $templates. "footer.php" ?>
    
</body>
</html>


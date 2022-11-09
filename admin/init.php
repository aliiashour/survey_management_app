<?php
    session_start() ; 
    ini_set('display_errors', 'On') ;
    error_reporting(E_ALL) ; 
    include_once "../config/connect_database.php" ; 

    $css = "../layout/css/" ; 
    $js = "../layout/js/" ; 
    $images = "../layout/images/" ; 

    
    $functions = "../inc/functions/" ; 
    $templates = "../inc/templates/" ; 
    // $handle_files = "./inc/handle_files/" ; 
    // $database_file = "./config/connect_database.php" ; 


    // if(!isset($_SESSION['user_id']) || empty($_SESSION['user_id'])){
    //     header('location:login.php') ; 
    // }else{
    //     header('location:index.php') ; 
    // }


    include_once $templates . 'header.php' ; 
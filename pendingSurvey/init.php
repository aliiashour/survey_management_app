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



    include_once $templates . 'header.php' ; 
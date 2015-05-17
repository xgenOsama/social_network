<?php
    session_start();
/// SET session data to empty array
   $_SESSION = array();
 if(isset($_COOKIE['id']) && $_COOKIE['user'] && $_COOKIE['pass']){
     setcookie('id','',strtotime('-5 days'),'/');
     setcookie('user','',strtotime('-5 days'),'/');
     setcookie('pass','',strtotime('-5 days'),'/');
 }
/// Destroy the session variables
session_destroy();
/// double check to see if their session exists
if(isset($_SESSION['username'])){
    header('Location: message.php?msg=Error:logout_failed');
}else{
    header('location: signup.php');
    exit();
}
?>
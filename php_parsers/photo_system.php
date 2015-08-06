<?php
   include_once('../root/includesFiles/check_login_status.php');
   include_once('../root/includesFiles/db_connection.php');
   if ($user_ok != true || $log_username == "") {
   	exit();
   }
?><?php 
	if (isset($_FILES['avatar']['name']) && $_FILES['avatar']['tmp_name'] != "") {
		$fileName = $_FILES['avatar']['name'];
		$fileTmpLoc = $_FILES['avatar']['tmp_name'];
		$fileType = $_FILES['avatar']['type'];
		$fileSize = $_FILES['avatar']['size'];
		$fileErrorMsg = $_FILES['avatar']['error'];
		$kaboom = explode(".",$fileName);
        $fileExt = end($kaboom);
        list($width,$height) = getimagesize($fileTmpLoc);
        if($width < 10 || $height < 10){
        	header("Location: ../message.php?msg=ERROR: that image has no dimensions");
        	exit();
        }
        $db_file_name =rand(100000000000,99999999999).".".$fileExt;
        if($fileSize > 1048576){
        	header("Location: ../message.php?msg=ERROR: Your image size is larger than 1mb");
        	exit();
        }else if(!preg_match("/\.(gif|jpg|png)$/i", $fileName)){
        	header('Location: ../message.php?msg=ERROR Your image file was not jpg,gif or png type');
        	exit();
        }else if($fileErrorMsg == 1){
        	header('Location: ../message.php?msg=ERROR an unknown erroe occured.');
        	exit();
        }
        $sql = "SELECT avatar FROM users WHERE username='$log_username' LIMIT 1";
        $query = mysqli_query($db_conn,$sql);
        $row = mysqli_fetch_row($query);
        $avatar = $row[0];
        if ($avatar != "") {
        	$picurl = "../user/$log_username/$avatar";
        	if(file_exists($picurl)){unlink($picurl);}
        }
        $moveResult = move_uploaded_file($fileTmpLoc,"../user/$log_username/$db_file_name");
        if($moveResult != true){
        	header("Location: ../message.php?msg=Error: file uploader filed");
        	exit();
        }
        include_once("../root/includesFiles/image_resize.php");
        $target_file = "../user/$log_username/$db_file_name";
        $resize_file = "../user/$log_username/$db_file_name";
        $wmax = 200;
        $hmax = 300;
        img_resize($target_file,$resize_file,$wmax,$hmax,$fileExt);
        $sql = "UPDATE users SET avatar ='$db_file_name' WHERE username='$log_username' LIMIT 1";
        $query = mysqli_query($db_conn,$sql);
        mysqli_close($db_conn);
        header("Location: ../user.php?u=$log_username");
        exit();
    }
?>

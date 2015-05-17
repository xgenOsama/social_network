<?php
    include_once '../root/includesFiles/db_connection.php';
    if(isset($_POST['loguser'])&& isset($_POST['user'])){
        $loguser= $_POST['loguser'];
        $user = $_POST['user'];
        $sql = "DELETE  FROM friends WHERE user1='$user' AND user2='$loguser' LIMIT 1";
        $query = mysqli_query($db_conn ,$sql);
            echo 'refuse_ok';
            exit();
    }
?>
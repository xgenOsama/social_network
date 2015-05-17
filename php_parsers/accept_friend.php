<?php
    include_once '../root/includesFiles/db_connection.php';
    /// to be continued later ....
    if(isset($_POST['loguser']) && isset($_POST['user'])){
        $log_user = $_POST['loguser'];
        $user = $_POST['user'];
        $sql = "UPDATE  friends SET accepted='1' WHERE user1='$user' AND user2='$log_user' LIMIT 1";
         mysqli_query($db_conn,$sql);
         echo 'friend_ok';
         exit();
    }
?>
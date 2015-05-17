<?php
include_once '../root/includesFiles/db_connection.php';
include_once '../root/includesFiles/check_login_status.php';
if($user_ok != true || $log_username == ""){
    exit();
}
?><?php
if(isset($_POST['type'])&& isset($_POST['user'])){
    $user = preg_replace('#[^a-z0-9]#i','',$_POST['user']);
    $sql = "SELECT COUNT(id) FROM users WHERE username='$user' AND activated ='1' LIMIT 1";
    $query = mysqli_query($db_conn,$sql);
    $exist_count = mysqli_fetch_row($query);
    if($exist_count < 1){
        mysqli_close($db_conn);
        echo "$user does not exist." .$exist_count;
        exit();
    }
    if($_POST['type'] == 'friend'){
        $sql = "SELECT COUNT(id) FROM friends WHERE user1='$user' AND accepted='1'
OR user2='$user' AND accepted='1'";
        $query = mysqli_query($db_conn,$sql);
        $friend_count = mysqli_fetch_row($query);
        $sql = "SELECT COUNT(id) FROM blockedusers WHERE blocker='$user' AND blockee='$log_username' LIMIT 1";
        $query = mysqli_query($db_conn,$sql);
        $blockcount1 = mysqli_fetch_row($query);
        $sql = "SELECT COUNT(id) FROM blockedusers WHERE blocker='$log_username' AND blockee='$user' LIMIT 1";
        $query = mysqli_query($db_conn,$sql);
        $blockcount2 = mysqli_fetch_row($query);
        $sql = "SELECT COUNT(id) FROM friends WHERE user1='$log_username' AND user2='$user' AND accepted='1'LIMIT 1";
        $query = mysqli_query($db_conn,$sql);
        $row_count1 = mysqli_fetch_row($query);
        $sql = "SELECT COUNT(id) FROM friends WHERE user1='$user' AND user2='$log_username' AND accepted='1'LIMIT 1";
        $query = mysqli_query($db_conn,$sql);
        $row_count2 = mysqli_fetch_row($query);
        $sql = "SELECT COUNT(id) FROM friends WHERE user1='$log_username' AND user2='$user' AND  accepted='0' LIMIT 1";
        $query = mysqli_query($db_conn,$sql);
        $row_count3 = mysqli_fetch_row($query);
        $sql ="SELECT COUNT(id) FROM friends WHERE user1='$user' AND user2='$log_username' AND accepted='0'";
        $query = mysqli_query($db_conn,$sql);
        $row_count4 = mysqli_fetch_row($query);
        if($friend_count[0] > 99){
            mysqli_close($db_conn);
            echo 'this user have the maximum number of friends ... ';
            exit();
        }else if ($blockcount1[0] > 0){
            mysqli_close($db_conn);
            echo "$user has you blocked ,we can't processed ...";
            exit();
        }else if($blockcount2[0] > 0){
            mysqli_close($db_conn);
            echo "you must unblock user first, to process this operation";
            exit();
        }else if($row_count1[0] > 0 || $row_count2[0] > 0){
            mysqli_close($db_conn);
            echo 'you are already friend with this user';
            exit();
        }else if($row_count3[0] > 0){
            mysqli_close($db_conn);
            echo "you have pending friend request already sent to this $user ";
            exit();
        }else if ($row_count4[0] > 0){
            mysqli_close($db_conn);
            echo "$user has first send a friend request to you ";
            exit();
        }else{
            $sql = "INSERT INTO friends (user1,user2,datemade) VALUES ('$log_username','$user',now())";
            $query = mysqli_query($db_conn,$sql);
            mysqli_close($db_conn);
            echo "friend_request_sent";
            exit();
        }


    }else if($_POST['type'] == "unfriend"){
         /////////////////////to be continued later ...
        $sql = "SELECT COUNT(id) FROM friends WHERE user1='$log_username' AND  user2='$user' AND accepted='1' LIMIT 1";
        $query = mysqli_query($db_conn,$sql);
        $row_count1 = mysql_fetch_row($query);
        $sql = "SELECT COUNT(id) FROM friends WHERE user1='$user'AND user2='$log_username' AND  accepted='1' LIMIT 1";
        $query = mysqli_query($db_conn,$sql);
        $row_count2 = mysqli_fetch_row($query);
        if($row_count1[0] > 0 ){
            $sql = "DELETE FROM friends WHERE user1='$log_username' AND  user2='$user' AND accepted='1' LIMIT 1";
            $query = mysqli_query($db_conn,$sql);
            mysqli_close($db_conn);
            echo "unfriend_ok";
            exit();
        }else if($row_count2[0] > 0){
            $sql = "DELETE FROM friends WHERE user1='$user' AND  user2='$log_username' AND accepted='1' LIMIT 1";
            $query = mysqli_query($db_conn,$sql);
            mysqli_close($db_conn);
            echo "unfriend_ok";
            exit();
        }else{
            mysqli_close($db_conn);
            echo "no friendship can't be found between you and $user therefore we can't unfriend you";
            exit();
        }
    }
    }
?><?php
    if(isset($_POST['action']) && isset($_POST['reqid']) && isset($_POST['user1'])){
        $reqid = preg_replace("#[0-9]#",'',$_POST['reqid']);
        $user = preg_replace('#[^a-z0-9]#i','',$_POST['user1']);
        $sql = "SELECT COUNT(id) FROM users WHERE username='$user' AND activated='1' LIMIT 1";
        $query = mysqli_query($db_conn , $sql);
        $exist_count = mysqli_fetch_row($query);
        if($exist_count[0] < 1){
            mysqli_close($db_conn);
            echo "$user does not exist.";
            exit();
        }
        if($_POST['action'] == 'accept'){
            $sql = "SELECT COUNT(id) FROM friends WHERE user1='$log_username' AND  user2='$user' AND accepted='1' limit 1";
            $query = mysqli_query($db_conn,$sql);
            $row_count1 = mysqli_fetch_row($query);
            $sql = "SELECT COUNT(id) FROM friends WHERE  user1='$user' AND user2='$log_username' AND accepted='1' limit 1";
            $query = mysqli_query($db_conn ,$sql);
            $row_count2 = mysqli_fetch_row($query);
            if($row_count1[0] >0 || $row_count2[0] > 0){
                mysqli_close($db_conn);
                echo "You are already friend with $user";
                exit();
            }else{
                $sql = "UPDATE friends SET accepted='1' WHERE  id='$reqid' AND user1='$user' AND user2='$log_username' LIMIT 1";
                $query= mysqli_query($db_conn, $sql);
                mysqli_close($db_conn);
                echo "accept_ok";
                exit();
            }
        }else if($_POST['action'] == 'reject'){
            $sql = "DELETE FROM  friends WHERE id='$reqid' AND user1='$user' AND user2='$log_username' AND accepted='0' LIMIT 1";
            $query = mysqli_query($db_conn ,$sql);
            mysqli_close($db_conn);
            echo 'reject_ok';
            exit();
        }
    }
?>
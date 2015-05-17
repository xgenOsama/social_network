<?php
    include_once '../root/includesFiles/check_login_status.php';
    include_once '../root/includesFiles/db_connection.php';
if($user_ok != true || $log_username == ""){
exit();
}
?><?php
if(isset($_POST['type']) && isset($_POST['blockee'])){
    $blockee = preg_replace('#[^a-z0-9]#i','',$_POST['blockee']);
    $sql = "SELECT id FROM users WHERE username='$blockee' AND activated ='1' LIMIT 1";
    $query = mysqli_query($db_conn,$sql);
    $exist_count = mysqli_num_rows($query);
    if($exist_count < 1){
        mysqli_close($db_conn);
        echo "$blockee does not exists";
        exit();
    }
    $sql = "SELECT id FROM blockedusers WHERE blocker='$log_username' AND blockee='$blockee'";
    $query = mysqli_query($db_conn , $sql);
    /////////////////// continue later ......................
    $numrows = mysqli_num_rows($query);
    if($_POST['type'] == 'block'){
        if($numrows > 0){
            mysqli_close($db_conn);
            echo 'you have already this member blocked';
            exit();
        }else {
            $sql = "INSERT INTO blockedusers(blocker,blockee,blockdate) VALUES ('$log_username','$blockee',now())";
            $query = mysqli_query($db_conn,$sql);
            echo 'block_ok';
            exit();
        }
    }else if($_POST['type'] == 'unblock'){
        if($numrows == 0){
            mysqli_close($db_conn);
            echo "you don't have this user blocked , therefore we can't unblock then";
            exit();
        }else {
            $sql = "DELETE FROM blockedusers WHERE blocker='$log_username' AND blockee='$blockee' LIMIT 1";
            $query = mysqli_query($db_conn,$sql);
            mysqli_close($db_conn);
            echo 'unblock_ok';
            exit();
        }
    }
}
?>
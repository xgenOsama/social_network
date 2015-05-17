<?php
include_once('root/includesFiles/check_login_status.php');
if($user_ok != true || $log_username=""){
    header("Location: signup.php");
    exit();
}
$notification_list='';
$sql = "SELECT * FROM notifications WHERE  username LIKE BINARY '$log_username' OR BY date_time DESC";
$query = mysqli_query($db_conn,$sql);
$numrows = mysqli_num_rows($query);
if($numrows < 1){
    $notification_list = "You don't have any notifivations ";
}else{
    while($row = mysqli_fetch_array($query,MYSQL_ASSOC)){
        $noteid = $row['id'];
        $initiator = $row['initiator'];
        $app = $row['app'];
        $note = $row['note'];
        $date_time = $row['date_time'];
        $date_time = strftime("%b %d %Y",strtotime($date_time));
        $notification_list .="<p><a href='user.php?u=$initiator'>$initiator</a> | $app<br />$note</p>";
    }
    mysqli_query($db_conn,"UPDATE users SET notecheck=now() WHERE username='$log_username' LIMIT 1");
}
?><?php

?>

<!DOCTYPE html>
    <html>
<head>
    <title>

    </title>
    <script src="root/js/ajax.js"></script>
    <script src="root/js/main.js"></script>
    <?php include_once('root/includesFiles/style_js.php');?>
    <script type="text/javascript">
        function friendRequestHandler(action,reqid,user1,elem){
            var conf = confirm('Press ok to "'+action+' this friend request');
            if(conf != true){
                return false;
            }
            _(elem).innerHTML = "processing ... ";
            var ajax = ajaxObj('POST',"php_parsers/friend_system.php");
            ajax.onreadystatechange = function(){
                if(ajaxReturn(ajax) == true){
                    if(ajax.responseText == 'accept_ok'){
                        _(elem).innerHTML ="<b>Request accepted</b> </br> you are now friends";
                    }else if(ajax.responseText == 'reject_ok'){
                        _(elem).innerHTML = "<b>Request rejected</b><br/> you chose to reject friendship with this user";
                    }else{
                        _(elem).innerHTML = ajax.responseText;
                    }
                }
            };
            ajax.send("action="+action+"$reqid="+reqid+"$user1="+user1);
        }
    </script>
</head>
<body>
    <?php include_once('nav.php');?>
    <div id="noteBox"><h2>Notifications</h2><?php echo $notification_list; ?></div>
    <div id="friendReqBox"><h2>Friend Requests</h2><?php echo $friend_requests; ?></div>
</body>
</html>
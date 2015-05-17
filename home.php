<?php
 include_once("root/includesFiles/check_login_status.php");
include_once("root/includesFiles/db_connection.php");
include_once('root/includesFiles/style_js.php');
include_once('nav.php');
$sql = "SELECT username,avatar FROM users WHERE  avatar  IS NOT NULL AND activated='1' ORDER BY RAND() LIMIT 32";
/* echo $sql ;
exit(); */
$query = mysqli_query($db_conn,$sql);
$userlist = "";
while($row = mysqli_fetch_array($query,MYSQL_ASSOC)){
    $u = $row['username'];
    $avatar = $row['avatar'];
    $profile_pic = 'user/'.$u.'/'.$avatar;
    $userlist .="<a href='user.php?u=".$u."' title='".$u."'><img src='".$profile_pic."' alt='".$u."' style='width:150px; height:180px;margin:5px;'/></a>";
}
$sql = "SELECT COUNT(id) FROM users WHERE activated='1'";
$query = mysqli_query($db_conn,$sql);
$row = mysqli_fetch_row($query);
$usercount = $row[0];
?>

<!DOCTYPE html>
    <html>
<head>
    <title>

    </title>
</head>
<body>
    <div style='margin-top: 50px;'>
        <!--this is the home page logic to display users-->
        <?php echo $userlist; ?>
    </div>
</body>
</html>
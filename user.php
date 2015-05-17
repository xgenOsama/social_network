<?php
include_once("root/includesFiles/check_login_status.php");
include_once("root/includesFiles/db_connection.php");
include_once('root/includesFiles/style_js.php');
// initialize any variables that the page might echo
$u = "";
$sex = "Male";
$userlevel = "";
$profile_pic = "";
$profile_pic_btn = "";
$country = "";
$joindate = "";
$lastsession = "";
/// make sure the _GET username is set , sanitize it
if (isset($_GET['u'])) {
    $u = preg_replace('#[^a-z0-9]#i', '', $_GET['u']);
} else {
    header('Location: signup.php');
    exit();
}
// Select the member form the users table
$sql = "SELECT * FROM users WHERE username='$u' AND  activated='1' LIMIT 1";
$user_query = mysqli_query($db_conn, $sql);
/// now make sure that user exists int the table
$numrows = mysqli_num_rows($user_query);
if ($numrows < 1) {
    echo "That user does not exist or is not yet activated , press back";
    exit();
}
$isOwner = 'no';
if ($u == $log_username && $user_ok == true) {
    $isOwner = 'yes';
    $sql = "SELECT avatar FROM users WHERE username='$log_username'";
    $query = mysqli_query($db_conn,$sql);
    $profile_pic = "user/$log_username/".mysqli_fetch_row($query)[0];
    /*$profile_pic_btn = '<a href="#" onclick="return false;" 
    onmousedown="toggleElement(\'avatar_form\')">Toggle Avatar</a>';*/
    $profile_pic_btn .='<form id="avatar_form" enctype="multipart/form-data" method="POST"
    action="php_parsers/photo_system.php">';
    $profile_pic_btn .= '<h4>Change your avatar</h4>';
    $profile_pic_btn .= '<input type="file" name="avatar" required="required"/>';
    $profile_pic_btn .= '<p><input class="btn btn-primary" type="submit" value="Upload"/></p>';
    $profile_pic_btn .= '</form>';
}
/// fetch the user row from the query above
while ($row = mysqli_fetch_array($user_query, MYSQLI_ASSOC)) {
    $profile_id = $row['id'];
    $gender = $row['gender'];
    $country = $row['country'];
    $userlevel = $row['userlevel'];
    $signup = $row['signup'];
    $lastlogin = $row['lastlogin'];
    $joindate = strftime("%b %d, %Y", strtotime($signup));
    $lastsession = strftime("%b %d, %Y", strtotime($lastlogin));
    if ($gender == 'f') {
        $sex = "Female";
    }
}
?>
<?php
$isFriend = false;
$OwnerBlockViewer = false;
$viewerBlockOwner = false;
////To be continued ...... queries
if ($u != $log_username && $user_ok == true) {
    $sql = "SELECT avatar FROM users WHERE username='$u'";
    $query = mysqli_query($db_conn,$sql);
    $profile_pic = "user/$u/".mysqli_fetch_row($query)[0];
    $friend_check = "SELECT id FROM friends WHERE accepted='1' AND user1='$log_username' AND user2='$u'  OR user1='$u' AND user2='$log_username'LIMIT 1";
    $query = mysqli_query($db_conn, $friend_check);

    if (mysqli_num_rows($query) > 0) {
        $isFriend = true;
    }
    $block_check1 = "SELECT id FROM blockedusers WHERE blocker='$u' AND blockee='$log_username' LIMIT 1";
    $query = mysqli_query($db_conn, $block_check1);
    if (mysqli_num_rows($query) > 0) {
        $OwnerBlockViewer = true;
    }
    $block_check2 = "SELECT id FROM blockedusers WHERE blocker='$log_username' AND blockee='$u' LIMIT 1";
    $query = mysqli_query($db_conn, $block_check2);
    if (mysqli_num_rows($query) > 0) {
        $viewerBlockOwner = true;
    }
}
?><?php
$friend_button = '<button class="btn btn-success" disabled="disabled">Request As Friend</button>';
$block_button = '<button  class="btn btn-danger" disabled="disabled">Block user</button>';
/// logic for friend button
/* echo "<br/>$isFriend";
  echo "<br/>$OwnerBlockViewer+2";
  echo "<br/>$viewerBlockOwner";
  exit(); */
/*    echo '1'; */
$sql = "SELECT COUNT(id) FROM friends WHERE  user1='$log_username' AND user2='$u' AND accepted='0' LIMIT 1";
$query = mysqli_query($db_conn, $sql);
$accepted_check1 = mysqli_fetch_row($query);
$sql = "SELECT COUNT(id) FROM friends WHERE  user1='$u' AND user2='$log_username' AND accepted='0' LIMIT 1";
$query = mysqli_query($db_conn, $sql);
$accepted_check2 = mysqli_fetch_row($query);
if ($isFriend == true) {

    $friend_button = '<button class="btn btn-danger"
     onclick="friendToggle(\'unfriend\',\'' . $u . '\',\'friendBtn\')">Unfriend</button>';
} else if ($user_ok == true && $u != $log_username && $OwnerBlockViewer == false) {
    /* echo '2'; */
    // $friend_button = '<button onclick="friendToggle(\'friend\',\''.$u.'\',\'friendBtn\')">Request As friend</button>';
    $friend_button = '<button class="btn btn-success" 
    onclick="friendToggle(\'friend\',\'' . $u . '\',\'friendBtn\')">Request As friend</button>';
}
////logic ....
if ($accepted_check1[0] > 0) {
    $friend_button = '<button class="btn btn-success" disabled>waiting the response</button>';
} else if ($accepted_check2[0] > 0) {
    $friend_button = '<button class="btn btn-primary" 
    onclick="acceptFriend(\'' . $log_username . '\',\'' . $u . '\',\'friendBtn\');">accept as friend</button>';
}
/////// logic for friend button
if ($viewerBlockOwner == true) {
    /*  echo '3'; */
    $block_button = '<button class="btn btn-success" 
    onclick="blockToggle(\'unblock\',\'' . $u . '\',\'blockBtn\')">unblock</button>';
} else if ($user_ok == true && $u != $log_username) {
    /* echo '4';
      exit(); */
    $block_button = '<button class="btn btn-danger"
     onclick="blockToggle(\'block\',\'' . $u . '\',\'blockBtn\')">Block User</button>';
}

/* echo "<br/>".htmlspecialchars($friend_button);
  echo "<br/>".htmlspecialchars($block_button);
  exit(); */
?><?php
    $friendHTML = '';
$friend_view_all_link='';
$sql = "SELECT COUNT(id) FROM friends WHERE user1='$u' AND accepted='1' OR user2='$u' AND accepted='1'";
$query = mysqli_query($db_conn,$sql);
$query_count= mysqli_fetch_row($query);
$friend_count = $query_count[0];
if($friend_count < 1){
    $friendHTML = $u." has no friend yet";
}else{
    $max= 18 ;
    $all_friends = array();
    $sql = "SELECT user1 FROM friends WHERE user2='$u' AND accepted='1' ORDER BY RAND() LIMIT $max";
    $query = mysqli_query($db_conn , $sql);
    print_r($query);
    while($row = mysqli_fetch_array($query,MYSQL_ASSOC)){
        array_push($all_friends,$row['user1']);
    }
    $sql = "SELECT user2 FROM friends WHERE user1='$u' AND accepted='1' ORDER  BY  RAND() LIMIT $max";
    $query = mysqli_query($db_conn,$sql);
    while($row = mysqli_fetch_array($query,MYSQLI_ASSOC)){
        array_push($all_friends,$row['user2']);
    }
    $friendArrayCount = count($all_friends);
    if($friendArrayCount > $max){
        array_splice($all_friends,$max);
        $friendArrayCount = '<a href="view_friends.php?u='.$u.'">view all</a>';
    }
    $orlogic = '';
    foreach($all_friends as $key => $user){
        $orlogic .="username='$user' OR ";
    }
    $orlogic = chop($orlogic," OR ");
    $sql = "SELECT username,avatar FROM users WHERE $orlogic";
    $query = mysqli_query($db_conn,$sql);
    while($row = mysqli_fetch_array($query,MYSQL_ASSOC)){
        $frined_username = $row['username'];
        $friend_avatar = $row['avatar'];
        if($friend_avatar != ""){
            $friend_pic = 'user/'.$frined_username.'/'.$friend_avatar.'';
         }else{
            $friend_pic = 'root/images/avatardefault.png';
        }
        $friendHTML .='<a href="user.php?u='.$frined_username.'"><img 
         style="width:40px;height:50px;"class="friendpics" src="'.$friend_pic.'"
        title="'.$frined_username.'"/></a>';
    }
}
?>
<!DOCTYPE html>
<html>
    <head>
        <meta charset="UTF-8">
        <title><?php echo $u; ?></title>
        <link rel="icon" href="favico.ico" type="image/x-icon">
        <script src="root/js/main.js"></script>
        <script src="root/js/ajax.js"></script>
<?php include_once("root/includesFiles/style_js.php"); ?>
        <style>

            div p{
                font-family: sans-serif;
                margin-left: 35px;
                color: #f7f7f7;
            }
            h3{
                text-align: center;
                text-decoration: blink underline #000000;
            }
            .friendpics{
                margin:2px;
                border-radius: 5px;
            }
            #pagemiddle{
                border-radius: 30px;
                padding: 20px;
            }
            #blockBtn{
                color: #0f0f0f;
            }
            #friendBtn{
                color:#0f0f0f ;
            }
            #listFriends{
                border-radius: 25px;
            }
            #listFriends a {
                font-size: 18px;
            }
            #listFriendsRequests{
                border-radius: 25px;
            }
            #listFriendsRequests a{
                font-size: 18px;
            }
            .friendView{
                margin-top: 2px;
            }
        </style>
        <script type="text/javascript">
            function logout() {
                window.location = 'logout.php';
            }
            window.friendToggle = function (type, user, elem) {
                alert('hacked');
                var conf1 = confirm("Press ok to confirm the '" + type + "' action for user <?php echo $u; ?>");
                if (conf1 != true) {
                    return false;
                }
                var elem1 = document.getElementById(elem);
                elem1.innerHTML = 'please wait';
                var ajax = ajaxObj('POST', 'php_parsers/friend_system.php');
                ajax.onreadystatechange = function () {
                    if (ajaxReturn(ajax) == true) {
                        if (ajax.responseText === 'friend_request_sent') {
                            elem1.innerHTML = "ok friend request sent";
                        } else if (ajax.responseText === "unfriend_ok") {
                            elem1.innerHTML = '<button class="btn btn-success onclick="friendToggle(\'friend\',\'<?php echo $u; ?>\',\'friendBtn\')">Request As a friend</button>';
                        } else {
                            alert(ajax.responseText);
                            elem1.innerHTML = "Try again later";
                        }
                    }
                };
                ajax.send("type=" + type + "&user=" + user);
            };
            window.blockToggle = function (type, blockee, elem) {
                var conf2 = confirm("press ok to confirm the '" + type + "' action on user <?php echo $u; ?> ");
                if (conf2 != true) {
                    return false;
                }
                var elem2 = document.getElementById(elem);
                elem2.innerHTML = "please wait ... ";
                var ajax = ajaxObj("POST", 'php_parsers/block_system.php');
                ajax.onreadystatechange = function () {
                    if (ajaxReturn(ajax) == true) {
                        if (ajax.responseText === "block_ok") {
                            elem2.innerHTML = '<button class="btn btn-success onclick="blockToggle(\'unblock\',\'<?php echo $u; ?>\',\'blockBtn\');">Unblock</button>';
                        } else if (ajax.responseText === "unblock_ok") {
                            elem2.innerHTML = '<button class="btn btn-danger" onclick="blockToggle(\'block\',\'<?php echo $u; ?>\',\'blockBtn\');">Block user</button>';
                        } else {
                            elem2.innerHTML = "Try again later";
                        }
                    }
                };
                ajax.send("type=" + type + "&blockee=" + blockee);
            };
            window.acceptFriend = function (loguser, user, elem) {
                var conf3 = confirm("are you sure to accept '" + user + "'");
                if (conf3 != true) {
                    return false;
                }
                var elem3 = document.getElementById(elem);
                elem3.innerHTML = 'please wait ....';
                var ajax = ajaxObj("POST", "php_parsers/accept_friend.php");
                ajax.onreadystatechange = function () {
                    if (ajaxReturn(ajax) == true) {
                        if (ajax.responseText === "friend_ok") {
                            elem3.innerHTML = '<button class="btn btn-danger" onclick="friendToggle(\'unfriend\',\'' + user + '\',\'' + elem + '\')">unfriend</button>';
                        } else {
                            elem3.innerHTML = "we cann't do this operation now ";
                        }
                    }
                };
                ajax.send("loguser=" + loguser + "&user=" + user);
            };
            window.refuseFriend = function (loguser, user, elem) {
                var conf4 = confirm("are you sure to refuse '" + user + "' ?!");
                if (conf4 != true) {
                    return false;
                }
                var elem4 = document.getElementById(elem);
                var ajax = ajaxObj("POST", "php_parsers/refused_friend.php");
                ajax.onreadystatechange = function () {
                    if (ajaxReturn(ajax) == true) {
                        if (ajax.responseText === "refuse_ok") {
                            elem4.innerHTML = "Refused successfuly";
                        } else {
                            alert("we can't do this now try again later");
                        }
                    }
                };
                ajax.send("loguser=" + loguser + "&user=" + user);
            };
        </script>
    </head>
    <body style="background-color: #262626">
<?php
include_once 'nav.php';
?>
        <div id="contaner" width="100%">

            <div id="pagemiddle" style="background-color:  #5bc0de;width: 650px;height:auto;margin-top: 100px;margin-left: 15px;float: left;">
                <h3><?php echo "profile, $u" ?></h3>
                <div class="info" width="70%">
                    <div id="profile_image_box" class="imageAvatar" width="30%" style="float: right;">
                        <img src="<?php echo $profile_pic; ?>" width="150" height="180">
                        <div><?php echo $profile_pic_btn; ?></div>
                    </div>
                <p>Is the viewer the page owner , logged in and varified :<b><?php if ($isOwner == 'no') {
            echo "<span style='color: red'> " . " " . $isOwner . "</span>";
        } else {
            echo "<span style='color: blue'>" . " " . $isOwner . "</span>";
        }
?></b></p>
                <p>Gender: <?php echo "<span style='color:#000000'>" . $sex . "</span>"; ?></p>
                <p>Country: <?php echo "<span style='color:  #000000'>" . $country . "</span>"; ?></p>
                <p>User Level: <?php echo "<span style='color:  #000000'>" . $userlevel . "</span>"; ?></p>
                <p>Join Date: <?php echo "<span style='color:  #000000'>" . $joindate . "</span>"; ?></p>
                <p>Last Session:<?php echo "<span style='color:  #000000'>" . $lastsession . "</span>"; ?>
                <hr/>
                                                            <br/><br/><br/>
                <span><?php echo $u." has ".$friend_count." friends" ?> <?php echo $friend_view_all_link; ?></span>

                <?php if ($isOwner == "no"): ?>
                    <p>Friend button: <span id="friendBtn" ><?php echo $friend_button; ?></span></p>
                    <p>Block button: <span id="blockBtn" ><?php echo $block_button; ?></span></p>
                <?php endif ?>
                <div style="border:solid 1px #000000;border-radius: 5px;margin-bottom: 20px;width: 600px;height: auto;">
                    <div class="friendView"><?php echo $friendHTML ; ?></div>
                </div>
                <?php if ($isOwner == "yes"): ?>
                    <p><button class="btn btn-primary" onclick="logout()">logout</button></p>
                <?php endif ?>
            </div>
            </div>
            <div id="lists" style='float: right;width: 650px;margin-top: 100px;margin-right: 30px;'>
                <div id="listFriends" style="background-color: palegreen;width:300px;float: left;">
                    <?php
                    if ($isOwner == 'yes') {
                        ?>
                        <h3>
                            list Friends
                        </h3>
                        <?php
                        $sql = "SELECT user1,user2 FROM friends  WHERE accepted='1' AND user1='$log_username' OR user2='$log_username' AND accepted='1'";
                        $query = mysqli_query($db_conn, $sql);
                        while ($result = mysqli_fetch_array($query)):
                            if ($result['user1'] == $log_username) {
                                $friend = $result['user2'];
                            } else {
                                $friend = $result['user1'];
                            }
                            ?>
                            <p><a href="user.php?u=<?php echo $friend; ?>"><?php echo $friend; ?></a></p>
                        <?php endwhile;
                    } ?>
                </div>

<?php
if ($isOwner == 'yes'):
    ?>
    <div id="listFriendsRequests" style="background-color: #c0c0c0;width:300px;float: right;margin-left: 10px;padding: 8px;">
                        <h3>
                            list Requests
                        </h3>
                        <?php
                        $sql = "SELECT user1 FROM friends  WHERE  user2='$log_username' AND accepted='0'";
                        $query = mysqli_query($db_conn, $sql);
                        while ($result = mysqli_fetch_array($query)):
                            $friend = $result[0];
                            /*        $btnAccept= "<button class=\"btn btn-primary" id="$friend"
                              onclick=\"acceptFriend('$log_username','$u','$friend')\"> accept</button>"; */
                            $btnAccept = '<button style="float:right;margin:0 1px;" class="btn btn-primary" 
                             onclick="acceptFriend(\'' . $log_username . '\',\'' . $friend . '\',\'' . $friend . '\')"> Accept</button>';
                            $btnRefuse = '<button style="float: right;margin:0 1px;" class="btn btn-danger" 
                              onclick="refuseFriend(\'' . $log_username . '\',\'' . $friend . '\',\'' . $friend . '\')"> Refuse</button>';
                            ?>
                            <p><a href="user.php?u=<?php echo $friend; ?>"><?php echo $friend; ?></a> <span style="float: right; margin-right: 10px;" id="<?php echo $friend; ?>"><?php echo $btnAccept . ' ' . $btnRefuse; ?></span></p>
                <?php endwhile;
 endif; ?>
    </div>
            </div>
        </div>
    </body>
</html>

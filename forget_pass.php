<?php
    include_once 'root/includesFiles/check_login_status.php';
    if($user_ok == true){
        header('Location: user.php?u='.$_SESSION['username']);
        exit();
    }
?>
<?php
    /// Ajax Calls this code to execute
    if(isset($_POST['e'])){
        $e = mysqli_real_escape_string($db_conn ,$_POST['e']);
        /// include database object to execute querys
        include_once 'root/includesFiles/db_connection.php';
        $sql = "SELECT id,username FROM users WHERE email='$e' AND  activated='1' LIMIT 1";
        $query = mysqli_query($db_conn,$sql);
        $numrows = mysqli_num_rows($query);
        if($numrows > 0){
            while($row = mysqli_fetch_array($query ,MYSQL_ASSOC)){
                $id = $row['id'];
                $u = $row['username'];
            }
        $emailcut = substr($e,0,4);
        $randNum = rand(10000,99999);
        $tempPass = "$emailcut$randNum";
        $hashTempPass = md5($tempPass);
        $sql = "UPDATE useroptions SET temp_pass='$hashTempPass' WHERE username='$u' LIMIT 1" ;
        $query = mysqli_query($db_conn,$sql);
        $to = "$e";
        $from ="http://127.0.0.1/Social_net";
        $headers ="Form : $form\n";
        $headers .= "MIME-Version: 1.0\n";
        $headers .= "Content-type: text/html; charset=iso-8859-1 \n";
        $subject = "Web intersection temporary password";
            $msg ="
<h3>Hello $e </h3><p>This is an automated message from web social net. if you don't recenty initiate the forget password
 process, please discard this email.</p><p>you indicated that you forget your login password we can generate a temporary password in with, then once logged in you can change your password to anything you like.</p><p>After click the link below <br/>your password temporary be:<b>$tempPass</b></p><p><a href='www.webintersect.com/forget_pass.php?u=$u&p=$hashTempPass'>Click here to temporary password shown below to your account</a></p><p>if you do not click the link in this email , no changes will be made to the temporary password</p>
            ";
            if(mail($to,$subject,$msg,$headers)){
                echo 'success';
                exit();
            }else{
                echo 'mail_send_failed';
                exit();
            }
        }else{
            echo 'no_exist';
        }
        exit();
    }
?>
<?php
    //// EMAIL LINK CLICK THIS CODE TO EXECUTE
    if(isset($_GET['u']) && isset($_GET['p'])){
        $u = preg_replace('#[^a-z0-9]#i','',$_GET['u']);
        $temppasshash = preg_replace('#[^a-z0-9]#i','',$_GET['p']);
        if(strlen($temppasshash) < 10 ){
            exit();
        }
        $sql = "SELECT id FROM useroptions WHERE u='$u' AND temp_pass ='$temppasshash' LIMIT 1";
        $query = mysql_query($db_conn,$sql);
        $numrows = mysqli_num_rows($query);
        if($numrows == 0){
            header("Location: message.php?msg=There is an error");
            exit();
        }else{
            $row = mysqli_fetch_array($query);
            $id = $row[0];
            $sql = "UPDATE users SET password='$temppasshash' WHERE id='$id' AND username='$u'LIMIT 1";
            $query = mysqli_query($db_conn,$sql);
            $sql = "UPDATE useroptions SET temp_pass=''WHERE username='$u' LIMIT 1";
            $query = mysqli_query($db_conn,$sql);
            header('Location: login.php');
            exit();
        }
    }
?>
<!DOCTYPE html>
<html>
    <head>
        <meta charset="UTF-8">
        <title>
            Forget password
        </title>
        <link rel="icon" href="favicon.ico" type="image/s-icon">
        <?php include_once 'root/includesFiles/style_js.php' ?>
        <script type="application/javascript" src="root/js/main.js"></script>
        <script type="application/javascript" src="root/js/ajax.js"></script>
        <script>
            function forgetpass(){
                var e = _('email').value;
                if(e == ""){
                    _('status').innerHTML = "type in your email address";
                }else{
                    _('forgetpassbtn').style.display = "none";
                    _('status').style.innerHTML ='please wait ....';
                    var ajax = ajaxObj('POST','forget_pass.php');
                    ajax.onreadystatechange = function (){
                        if(ajaxReturn(ajax)){
                            var response = ajax.responseText;
                            if(response == 'success'){
                                _('forgetpassform').innerHTML = "<h3>Step 2 . check your email adress : "+e+"</h3>";
                            }else if (response == 'not_exist'){
                                _('status').innerHTML = "Sorry that email address is not in our system";
                            }else if(response == 'email_send_failed'){
                                _('status').innerHTML = 'Mail function failed to execute';
                            }else{
                                _('status').innerHTML ='An unkown error occurred';
                            }
                            _('forgetpassbtn').style.display = '';
                        }
                    }
                    ajax.send('e='+e);
                }
            }
        </script>
        <style>
            #pagemodel{
                background-color: #5cb85c;
                width: 600px;
                align-content: center;
                padding: 20px;
                text-align: center;
                margin-top: 70px;
                margin-left: 330px;
            }
        </style>
    </head>
    <body>
    <?php include_once 'nav.php' ;?>
    <div id="pagemodel">
        <h3>Generate a temporary log in password</h3>
        <form id="forgetpassform" onsubmit="return false ;">
            <div>
                Step1 :Enter your email address
            </div>
            <input id="email" type="text" onfocus="_('status').innerHTML='';" maxlength="88"/>
            <br/><br/>
            <button id="forgetpassbtn" onclick="forgetpass();">Generate</button>
            <p id="status"></p>
        </form>
    </div>
    </body>
</html>
<?PHP
 session_start();
if (isset($_SESSION['username'])){
    header('Location: user.php?u='.$_SESSION['username']);
    exit();
}
?><?php
    // AJAX CALLS THIS LOGIN CODE TO EXECUTE
if(isset($_POST['e'])){
    // CONNECT TO THE DATABASE
    include_once('root/includesFiles/db_connection.php');
    /// GATER THE POSTED DATA INTO LOCAL VARIABLES AND SANTIZE
    $e = mysqli_real_escape_string($db_conn,$_POST['e']);
    $p = md5($_POST['p']);
    /// GET user id address
    $ip = preg_replace('#[^0-9.]#',"",getenv('REMOTE_ADDR'));
    /// FORM DATE ERROR HANDLING
    if($e == "" || $p == ""){
        echo  "login_failed";
        exit();
    }else{
        /// END FORM DATA HANDLING
        $sql = "SELECT id,username,password FROM users WHERE email='$e' LIMIT 1";;
        $query = mysqli_query($db_conn,$sql);
        $row = mysqli_fetch_array($query);
        $db_id = $row[0];
        $db_username = $row[1];
        $db_pass_str = $row[2];
        if($p != $db_pass_str){
            echo "login_failed";
            exit();
        }else{
            $_SESSION['userid'] = $db_id;
            $_SESSION['username']=$db_username;
            $_SESSION['password']=$db_pass_str;
            setcookie('id',$db_id,strtotime('+30 days'),'/','','',true);
            setcookie('user',$db_username,strtotime('+30 days'),'/','','',true);
            setcookie('pass',$db_pass_str,strtotime('+30 days'),'/','','',true);
            //// update THERE 'IP' AND 'LASTLOGIN' FIELDS
            $sql = "UPDATE users SET ip='$ip',lastlogin=now() WHERE username='$db_username' LIMIT 1";
            $query = mysqli_query($db_conn,$sql);
            if(isset($_POST['submit'])){
                header('Location: user.php?u='.$_SESSION['username']);
            }
            echo $db_username;
            exit();
         }
    }
    exit();
}

?>
<html>
    <head>
        <title>

        </title>
        <?php
        include './root/includesFiles/style_js.php';
        include_once './nav.php';
        ?>
        <style>
            .form-group{
                margin: 10px;
            }
            .form-control{
                margin-left:5px;
            }
        </style>
        <script type="application/javascript" src="root/js/main.js"></script>
        <script type="application/javascript" src="root/js/ajax.js"></script>
        <script>
            function emptyElement(x){
                _(x).innerHTML = "";
            }
            function login(){
                var  e = _('email').value;
                var p = _('password').value;
                if( e == "" || p == ""){
                    _('status').innerHTML = "Fill all of the form data";
                }else{
                    _('loginbtn').style.display = "none";
                    _('status').innerHTML = "please wait.." ;
                    var ajax = ajaxObj('POST','login.php');
                    ajax.onreadystatechange = function(){
                        if(ajaxReturn(ajax) == true){
                            if(ajax.responseText == 'login_failed'){
                                _('status').innerHTML = 'login unsccessfuly please try again';
                                _('loginbtn').style.display = "block";
                            }else{
                                window.location = "user.php?u="+ajax.responseText;
                            }
                        }
                    }
                }
                ajax.send("e="+e+"&p="+p);
            }
        </script>
    </head>
    <body style="background-color: #f2d978">
        <form id="loginform" onsubmit="return false;" class="form-horizontal col-lg-4"
              style="margin: 90px 25px;border-radius: 25px;background-color: #ca9eaf;opacity: 0.95;">
            <h3>Login Here</h3>
            <div class="form-group">
                <label for="email" class="col-sm-2 control-label">Email: </label>
                <div class="col-sm-8">
                    <input id="email" class="form-control" type="text" placeholder="username ..." 
                           name="username" required="required" onfocus="emptyElement('status')" maxlength="88"/>
                </div>
            </div>
            <div class="form-group">
                <label for="password" class="col-sm-2 control-label">Password: </label>
                <div class="col-sm-8">
                    <input id="password" class="form-control" type="password" placeholder="password ..." 
                           name="password" required="required" onfocus="emptyElement('status')" maxlength="100"/>
                </div>
            </div>
            <div class="form-group">
                <a href="forget_pass.php" style="margin-left: 25px;">Forget your password</a>
                <button id="loginbtn" class="btn btn-primary" onclick="login()" style="float: right; margin-right: 100px;">Log In</button>
            </div>
            <p id="status"></p>
        </form>
    </body>
</html>

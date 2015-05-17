<?php
session_start();
//if user is logged in , header them away
if (isset($_SESSION['username'])) {
    header('Location: message.php?msg=NO to that weenis');
    exit();
}
?><?php
//Ajax calls this Name check code to execute
if (isset($_POST['usernamecheck'])) {
    include_once ("root/includesFiles/db_connection.php");
    $username = preg_replace('#[^a-z0-9]#i', '', $_POST['usernamecheck']);
    $sql = "SELECT id from users WHERE username='$username' LIMIT 1";
    $query = mysqli_query($db_conn, $sql);
    $uname_check = mysqli_num_rows($query);
    if (strlen($username) < 3 || strlen($username) > 16) {
        echo '<strong style="color:#F00;">3 - 16 characters please</strong>';
        exit();
    }
    if (is_numeric($username[0])) {
        echo '<strong style="color:#F00;">Username must begin with a letter</strong>';
        exit();
    }	
    if ($uname_check < 1) {
        echo '<strong style="color:#68dc70;">' . $username . ' is OK</strong>';
        exit();
    } else {
        echo '<strong style="color:#F00;">' . $username . ' is taken</strong>';
        exit();
    }
}
?><?php
// Ajax calls this registartion code to execute
if (isset($_POST['u'])) {
    /// connect to database
    include_once ("./root/includesFiles/db_connection.php");
    /// cather the posted data into local variables 
    $u = preg_replace('#[^a-z0-9]#i', '', $_POST['u']);
    $e = mysqli_real_escape_string($db_conn, $_POST['e']);
    $p = $_POST['p'];
    $g = preg_replace('#[^a-z]#', '', $_POST['g']);
    $c = preg_replace('#[^a-z0-9]#i', '', $_POST['c']);
    /// Get users ip address
    $ip = preg_replace('#[^a-z0-9]#', '', getenv('ROMOTE_ADDR'));
    /// DUPLICATE DATA CHECS FOR USER NAME AND EMAIL
    $sql = "SELECT id FROM users WHERE username='$u' LIMIT 1";
    $query = mysqli_query($db_conn, $sql);
    $u_check = mysqli_num_rows($query);
    //// ____________________________________________
    $sql = "SELECT id FROM users WHERE email='$e' LIMIT 1";
    $query = mysqli_query($db_conn, $sql);
    $e_check = mysqli_num_rows($query);
    //// FORM DATA ERROR HANDLING
    if ($u == "" || $e == "" || $p == "" || $g == "" || $c == "") {
        echo "The form submission is missing values $u , $e , $p , $g , $c";
        exit();
    } else if ($u_check > 0) {
        echo "The username you enter is already taken";
        exit();
    } else if ($e_check > 0) {
        echo "The email address is already in use in the system";
        exit();
    } else if (strlen($u) < 3 || strlen($u) > 16) {
        echo "username must between 3 and 16 characters";
        exit();
    } else if (is_numeric($u[0])) {
        echo "Username cannot begin with a number";
        exit();
    } else {
        /// End form data error handling
        /// begin insertion of the data into the database 
        /// hash password and apply you own mysterious
        //$cryptpass = crypt($p);
        ////+++++++++++++ 
        //include_once ("./root/includesFiles/randStrGen.php");
       // $p_hash = randStrGen(20) . "$cryptpass" . randStrGen(20);
        $p_hash = md5($p);
        //// add user info to the main table ...... 
        $sql = "INSERT INTO users (username,email,password,gender,country,ip,signup,lastlogin,notescheck)
VALUES ('$u','$e','$p_hash','$g','$c','$ip',now(),now(),now())";
        $query = mysqli_query($db_conn, $sql);
        //// this return the last id insert in the auto increment last query .
        $uid = mysqli_insert_id($db_conn);
        //// establish their row in useroptions table 
        $sql = "INSERT INTO useroptions (id,username,background) VALUES"
                . "('$uid','$u','original')";
        $query = mysqli_query($db_conn, $sql);
        /////// create dir (folder to hold user's files (pics, mp3s , etc .)
        if (!file_exists("user/$u")) {
            mkdir("user/$u", 0755);
        }
        /*
        /// Email the user their activation link 
        $to = "$e";
        $from = "http://localhost/Social_net/";
        $subject = "Web intersect Account activation";
        $message = '<!DOCTYPE html>
<html>
	<head>
	<meta charset="UTF-8">
		<title>
			web intersect message
		</title>
	</head>
	<body style="font-family:Tahoma,Geneva,sans-serif;">
		<div style="padding:10px;background:#333;font-size:24px;color:#ccc;">
			<a href="www.example.com"><img src="/root/images/logo.png" width="36px"
			height="30px" alt="image not fond" style="border:none;float:left;" /></a>web intersection account activation ...
		</div><div style="padding:24px;font-size:17px;">
			hello ' . $u . ' ,<br><br>
			Click the link below to activate your account now 
			<a href="http://localhost/Social_net/activation.php?id=' . $uid . '&u=' . $u . '&
			e=' . $e . '&p=' . $p_hash . '">Click to activate your account now</a><br/><br/>
			* login after activation using your<br>username :<b>' . $u . '</b>
			<br/>password :<b>' . $e . '</b>
		</div>
	</body>
</html>';
        $headers = "Form: $form\n";
        $headers .= "MIME-Varsion 1.0\n";
        $headers .= "content-type: text/html; charset=iso-8859";
        mail($to, $subject, $message, $headers);
        exit();*/
    }
        echo "signup_success";
        exit();
}
?>
<!DOCTYPE html>
<html>
    <head>
        <?php include './root/includesFiles/style_js.php'; ?>
        <?php include './root/includesFiles/db_connection.php'; ?>
        <meta charset="UTF-8">
        <title>Sing up</title>
        <script src="root/js/main.js" type="text/javascript"></script>
        <script src="root/js/ajax.js" type="text/javascript"></script>
        <script>
            $(function () {
                function reposition() {
                    var modal = $(this),
                            dialog = modal.find('.modal-dialog');
                    modal.css('display', 'block');
                    dialog.css("margin-top", Math.max(0, ($(window).height() - dialog.height()) / 3));
                    dialog.css("margin-left", Math.max(0, ($(window).width() - dialog.width()) / 1.5));

                }
                $('.modal').on('show.bs.modal', reposition);
                $(window).on('resize', function () {
                    $('.modal:visible').each(reposition);
                });
            });
            function appearFormUp() {
                document.getElementById('appear').style.opacity = 1;
            }
            function appearFormDown() {
                document.getElementById('appear').style.opacity = 0.8;
            }
        </script>
        <script>
            function restrict(elem) {
                var tf = _(elem);
                var rx = new RegExp;
                if (elem == 'email') {
                    rx = /[' "]/gi;
                } else if (elem == 'username') {
                    rx = /[^a-z0-9]/gi;
                }
                tf.value = tf.value.replace(rx, "");
            }
            function emptyElement(x) {
                _(x).innerHTML = "";
            }
            function checkusername() {
                var u = _("username").value;
                if (u != "") {
                    _("unamestatus").innerHTML = 'checking ... ';
                    var ajax = ajaxObj("POST", "signup.php");
                    ajax.onreadystatechange = function () {
                        if (ajaxReturn(ajax) == true) {
                            _("unamestatus").innerHTML = ajax.responseText;
                        }
                    };
                    ajax.send('usernamecheck=' + u);
                }
                if (u == "") {
                    _("unamestatus").innerHTML = "";
                }
            }
            function signup() {
                var u = _('username').value;
                var e = _('email').value;
                var p1 = _('pass1').value;
                var p2 = _('pass2').value;
                var c = _('country').value;
                var g = _('gender').value;
                var status = _("status");
                if (u == "" || e == "" || p1 == "" || p2 == "" || c == "" || g == "") {
                    status.innerHTML = "fill out all of form data .. ";
                } else if (p1 != p2) {
                    status.innerHTML = "your password fiels do not match";
                }else if (_('terms').style.display == "none") {
                    status.innerHTML = "please view the terms of use";
                }
                else {
                    _("signupbtn").style.display = "none";
                    status.innerHTML = "please wait ... ";
                    var ajax = ajaxObj('POST', 'signup.php');
                    ajax.onreadystatechange = function () {
                        if (ajaxReturn(ajax) == true) {
                            if (ajax.responseText != 'signup_success') {
                                status.innerHTML = ajax.responseText;
                            } else {
                                window.scroll(0, 0);
                                _('signupform').innerHTML = 'ok' + u + "check your email inbox and junk mail box at <u><a href='#'>" + e + "</a></u>" +
                                        " in sign up process by activating your account . you willl not be able to do antrhing on the site\n\
                                until you successfuly activate it ";
                            }
                        }
                    };
                    ajax.send("u=" + u + "&e=" + e + "&p=" + p1 + "&c=" + c + "&g=" + g);
                }
            }
            function openTerms() {
                _('terms').style.display = "block";
                emptyElement('status');
            }
//            function addEvents(){
//                _("elemID").addEventListener("click",func,false);
//            }
//            window.onload = addEvents;
        </script>
        <script>
            
        </script>
    </head>
    <body style="background-image: url('root/images/background.jpeg');background-repeat: no-repeat;background-position:top center;
          background-size: 100%;" >
        <div class="mainContainer">
         <?php include_once './nav.php';?>
            <!-- begin of model -->
            <div class="modal fade bs-example-modal-lg" tabindex="-1" role="dialog" aria-labelledby="myLargeModalLabel"
                 aria-hidden="true">
                <div class="modal-dialog modal-lg">
                    <div class="modal-content col-sm-8 center-block col-sm-offset-2 " style="background-color: aquamarine;
                         opacity: 0.8;
                         " onmouseover="this.style.opacity = 1;" onmouseout="this.style.opacity = 0.8;">
                        <div class="login" style="margin-top: 20px;">
                            <form class="form-horizontal" action="login.php" method="POST">
                                <div class="form-group">
                                    <label for="inputEmail3" class="col-sm-2 control-label">Email</label>
                                    <div class="col-sm-8">
                                        <input type="text" name="e" class="form-control" id="inputEmail3" placeholder="username ..">
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label for="inputPassword3" class="col-sm-2 control-label">Password</label>
                                    <div class="col-sm-8">
                                        <input type="password" name="p" class="form-control" id="inputPassword3" placeholder="Password">
                                    </div>
                                </div>
                                <div class="form-group">
                                    <div class="col-sm-offset-2 col-sm-10">
                                        <button type="submit" name="submit" class="btn btn-info">login</button>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <div class="col-sm-offset-2 col-lg-10">
                                        <a href="forget_pass.php" style="margin-left: 25px;">Forget your password</a>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
            <!-- End of the model -->
            <div id="appear" class="mySignupForm  col-lg-3" style="float: right; opacity: 0.8;font-weight: 100;
                 border-radius: 30px; background-color: #6889af;margin-right: 25px;margin-top: 80px;" 
                 onmouseover="appearFormUp()" onmouseout="appearFormDown()">
                <h3>Sign up Here</h3>
                <form id="signupform" class="form-group" name="signupform" onsubmit="return false;" style="opacity: 0.9;font-size: 17px;" > 
                    <div>Username :</div>
                    <input id="username"  type="text" onblur="checkusername()" onkeyup="restrict('username')" maxlength="16" />
                    <span id="unamestatus"></span>
                    <div>Email Address :</div>
                    <input id="email" type="text" onfocus="emptyElement('status')" onkeyup="restrict('email')" maxlength="88"/>
                    <div>Create password :</div>
                    <input id="pass1" type="password" onfocus="emptyElement('status')" maxlength="100">
                    <div>Confirm password</div>
                    <input id="pass2" type="password" onfocus="emptyElement('status')" maxlength="100">
                    <div>Gender:</div>
                    <select id="gender" onfocus="emptyElement('status')">
                        <option value=""></option> 
                        <option value="m">Male</option> 
                        <option value="f">Female</option> 
                    </select>
                    <div>Country:</div>
                    <select id="country" onfocus="emptyElement('status')">
                        <option value=""></option>
                        <option value="Egypt">Egypt</option>
                        <option value="USA">USA</option>
                        <option value="UK">UK</option>
                    </select>
                    <div>
                        <a href="#" onclick="return false;" onmousedown="openTerms()" style="color: white;">
                            View the Terms of Use 
                        </a>
                    </div>
                    <div id="terms" style="display: none;">
                        <h3>Web intersect Terms of Use</h3>
                        <p> . don't distarb other people here.</p>
                        <p>. use it well.</p>
                        <p>. contact with other people .</p>
                    </div>
                    <br/><br/>
                    <button class=" btn btn-success" id="signupbtn" onclick="signup()">create Account</button>
                    <span id="status"></span>
                </form>
            </div>
        </div>
    </body>
</html>

<?php

if (isset($_GET['id']) && isset($_GET['u']) && isset($_GET['e']) && isset($_GET['['])) {
    //// connect to database and sad sanitize incoming $_GET variablse
    include_once './root/includesFiles/db_connection.php';
    $id = preg_replace('#[^0-9]#i', '', $_GET['id']);
    $u = preg_replace('#[^a-z0-9]#i', '', $_GET['u']);
    $e = mysqli_real_escape_string($db_conn, $_GET['e']);
    $p = mysqli_real_escape_string($db_conn, $_GET['p']);
    /// Evaluate the lengths of the incoming $_GET variable
    if ($id == "" || strlen($u) < 3 || strlen($e) < 5 || /*strlen($p) != 74*/ strlen($p) == "") {
        /// log this header location 
        $sql = "SELECT * FROM users WHERE id='$id' AND username='$u' AND email='$e' AND password='$p' LIMIT 1";
        $query = mysqli_query($db_conn, $sql);
        $numrows = mysqli_num_rows($query);
        /// Evaluate for a match in the system (0 = no match , 1 = match)
        if ($numrows == 0) {
            // log this potential hack attempt to next file and email details to yourself 
            header("Location: message.php?msg=your credentials are not  matching anything in our system");
            exit();
        }
        // match was found , you can activate them 
        $sql = "UPDATE users SET activated='1' WHERE id ='$id' LIMIT 1";
        $query = mysqli_query($db_conn, $sql);
        /// optional double check to see if activated in fact now - 1
        $sql = "SELECT * FROM users WHERE id='$id' AND activated ='0' limit 1";
        $query = mysqli_query($db_conn, $sql);
        $numrows = mysqli_num_rows($query);
        // Evaluate the double check 
        if ($numrows == 0) {
            /// log this issue of no switch of activation field to 1 
            header("Location: message.php?msg=activation_failure");
            exit();
        }else if($numrows == 1){
            /// Great everything went fine activation
            header("Location: message.php?msg=activation_success");
            exit();
            }
    }  else {
        /// log this issue of missing initial $_get variable 
        header("Location: message.php?msg=missing_GET_variables");
        exit();
    }
}
?>
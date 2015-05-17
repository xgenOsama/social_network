<?php
$message = "No message";
$msg = preg_replace('#[^a-z 0-9.:_()]#i', '', $_GET['msg']);
if ($msg == "activation_failure") {
    $message = "";
} else if ($msg == "activation_success") {
    $message = "";
} else {
    $message = $msg;
}
?>
<div>
    <?php echo $message; ?>
</div>
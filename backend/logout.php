<?php
session_start();
//session_unset();
$_SESSION = [];
session_destroy();

header('Location: ../frontend/landing_page/landing_page.php');
exit;
?>

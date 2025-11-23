<?php
session_start();
session_unset();
session_destroy();

header('Location: ../frontend/landing_page/landing_page.html');
exit;
?>

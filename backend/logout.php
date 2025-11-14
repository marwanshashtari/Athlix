<?php
require_once __DIR__ . '/config.php';
session_start();

// Destroy the session
session_unset();
session_destroy();

// Redirect to the landing page
header('Location: ../frontend/landing_page/landing_page.html');
exit;
?>

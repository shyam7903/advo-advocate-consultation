<?php
session_start();
session_unset();
session_destroy();

// Redirect to logout confirmation
header("Location: logout_success.php");
exit;

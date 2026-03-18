<?php

session_start();

$_SESSION = [];
setcookie(session_name(), '', time() - 3600, '/');
session_unset();
session_destroy();

header('Location: login-page.php');

exit;

?>
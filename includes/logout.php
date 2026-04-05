<?php

/* Logout script, destroys the session and redirects to the login page */
require_once 'auth.php';
auth::destroy();
header('Location: ../login.php');
exit();
?>
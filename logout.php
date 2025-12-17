<?php
session_start();

session_unset();
session_destroy();

header("Location: landing_page.php");
exit;
?>

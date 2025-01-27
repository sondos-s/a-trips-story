<?php

session_start();
session_destroy();

header("Location: Splash.php");
exit;

?>
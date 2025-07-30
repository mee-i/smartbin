<?php
session_start();
// $_SESSION['IsAdmin'] = false;
session_destroy();
header("Location: /");
?>
<?php

require 'usermanager.php';


if (!$_SESSION["IsAdmin"]) {
    http_response_code(403);
    exit;
}

// function countData() {
//   return count(explode("\n", $fileContents));
// }

// $userCount = countData();

$AdminUsers = getUsers();

if ($_SERVER['REQUEST_METHOD'] == 'GET') {
    $data = $_GET['data'];
    $cmd = $_GET['command'];
    if ($cmd == 'deletemessage') {
        $filepath = "../../UserMessages/";
        unlink($filepath . $data);
    }
}
?>
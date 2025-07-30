<?php
require 'usermanager.php';

if (!$_SESSION['IsAdmin']) {
    http_response_code(403);
    exit;
}

function get_file_type($file) {
    preg_match('/\.([^.]+)$/', $file, $matches);
    $file_list = array(
        'js' => 'text/javascript',
        'css' => 'text/css',
        'html' => 'text/html'
    );
    return $file_list[$matches[1]];
}

if (isset($_GET['file'])) {
    $filename = $_GET['file'];
    header("Content-type: " . get_file_type($filename));
    readfile($filename);
    exit;
}
?>
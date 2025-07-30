<?php
if ($_SERVER['REQUEST_METHOD'] == 'GET') {
    $file_device = "../.Data/.data_week";
    echo file_get_contents($file_device);
}
?>
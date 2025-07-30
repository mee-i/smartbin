<?php
if ($_SERVER['REQUEST_METHOD'] == 'GET') {
    $file_device = "../.Data/.device_data";
    $data = json_decode(file_get_contents($file_device));
    $organik = $data->organic;
    $anorganik = $data->anorganic;
    $status = new stdClass();
    $status->organic = $organik;
    $status->anorganic = $anorganik;
    echo json_encode($status);
}
?>
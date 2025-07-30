<?php
// $smartbin_dir = '../../';
// $data_dir = $smartbin_dir.'.Data/';
    
// // Auto start socket if inactive
// $ws_data_dir = $data_dir.'.process_socket_device';
// $ws_pid = "";
// if (!file_exists($ws_data_dir)) {
//     file_put_contents($ws_data_dir, "");
//     $code = -1;
// }
// else {
//     $ws_pid = file_get_contents($ws_data_dir);
//     exec('ps -p ' . $ws_pid, $out, $code);
// }

// if ($code != 0 && $ws_pid != "WS_STP") {
//     exec('python3.11 '.$smartbin_dir.'websocket/device.py >> '.$smartbin_dir.'.logs/socket_device.log 2>&1 & echo $!', $output);
//     $output = implode('', $output);
//     $ws_pid = (int)$output;
//     file_put_contents($ws_data_dir, (string) $ws_pid);
// }
$smartbin_dir = '../../';
$data_dir = $smartbin_dir.'.Data/';
    
// Auto start socket if inactive
$ws_data_dir = $data_dir.'.process_socket';
$ws_pid = "";
if (!file_exists($ws_data_dir)) {
    file_put_contents($ws_data_dir, "");
    $code = -1;
}
else {
    $ws_pid = file_get_contents($ws_data_dir);
    exec('ps -p ' . $ws_pid, $out, $code);
}

if ($code != 0 && $ws_pid != "WS_STP") {
    exec('python3.11 '.$smartbin_dir.'websocket/ws.py >> '.$smartbin_dir.'.logs/socket.log 2>&1 & echo $!', $output);
    $output = implode('', $output);
    $ws_pid = (int)$output;
    file_put_contents($ws_data_dir, (string) $ws_pid);
}
?>
OK
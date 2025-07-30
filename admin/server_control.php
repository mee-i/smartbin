<?php

    require 'usermanager.php';
    
    if (!$_SESSION["IsAdmin"]) {
        http_response_code(403);
        echo "Anda tidak memiliki akses";
        exit;
    }

    if (!check_min_level('DEVELOPER_ADMIN')) {
        http_response_code(403);
        echo "Akses ditolak";
        exit;
    }
    
    if ($_SERVER["REQUEST_METHOD"] == "GET") {
        $command = $_GET["control"];
        $access_public = "../.htaccess";
        $ws_file = "../../.Data/.process_socket";
        $ws_file_device = "../../.Data/.process_socket_device";
        $device_command_file = "../../.Data/.device_cmd";
        if ($command == "WS_RST") {
            $ws_pid = file_get_contents($ws_file);
            if ($ws_pid != "WS_STP") exec("kill ".$ws_pid);
            file_put_contents($ws_file, "");
        }
        else if ($command == "WS_STP") {
            $ws_pid = file_get_contents($ws_file);
            exec("kill ".$ws_pid);
            file_put_contents($ws_file, "WS_STP");
        }
        else if ($command == "WS_DEVICE_RST") {
            $ws_pid = file_get_contents($ws_file_device);
            if ($ws_pid != "WS_STP") exec("kill ".$ws_pid);
            file_put_contents($ws_file_device, "");
        }
        else if ($command == "WS_DEVICE_STP") {
            $ws_pid = file_get_contents($ws_file_device);
            exec("kill ".$ws_pid);
            file_put_contents($ws_file_device, "WS_STP");
        }
        else if ($command == "ACCESS_CLOSE") {
            file_put_contents($access_public, "Require all denied");
        }
        else if ($command == "ACCESS_OPEN") {
            file_put_contents($access_public, "");
        }
        else if ($command == "digitalWrite") {
            $pin = $_GET['pin'];
            $state = $_GET['state'];
            file_put_contents($device_command_file, "pin_write $pin $state");
            chmod($device_command_file, 0777);
        }
    }
?>
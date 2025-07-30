<?php
    // Set file mime type event-stream
    session_start();
    if (!$_SESSION["IsAdmin"]) {
        http_response_code(403);
        exit;
    }
    $isSSE = false;
    if (!isset($_GET['issse'])) {
        header('Content-Type: text/plain');
    }
    else {
        header('Content-Type: text/event-stream');
        header('Cache-Control: no-cache');
        $isSSE = true;
    }

    $os = "unknown";

    if (strtoupper(substr(PHP_OS, 0, 3)) === 'WIN') {
        $os = "windows";
    }
    else if (strtoupper(substr(PHP_OS, 0, 5)) === 'LINUX') {
        $os = "linux";
    }
    
    $load = 0;
    $mem = 0;
    $total_mem = 0;
    $storage = 0;
    $total_storage = 0;

    if ($os == "windows") {
        $cmd = "wmic cpu get loadpercentage /all";
        @exec($cmd, $output);
        
        if ($output)
        {
            foreach ($output as $line)
            {
                if ($line && preg_match("/^[0-9]+\$/", $line))
                {
                    $load = (int)$line + $load;
                    break;
                }
            }
        }
        $output = "";
        $cmd = "wmic os get freephysicalmemory";
        @exec($cmd, $output);
    
        if ($output)
        {
            foreach ($output as $line)
            {
                if ($line && preg_match("/^[0-9]+\$/", $line))
                {
                    $mem = (int) $line;
                    break;
                }
            }
        }
    
        $output = "";
        $cmd = "wmic computersystem get totalphysicalmemory";
        @exec($cmd, $output);
    
    
        if ($output)
        {
            foreach ($output as $line)
            {
                if ($line && preg_match("/^[0-9]+\$/", $line))
                {
                    $total_mem = (int)$line / 1024;
                    break;
                }
            }
        }
        $storage = disk_free_space("C:") / (1024 * 1024 * 1024);
        $total_storage = disk_total_space("C:") / (1024 * 1024 * 1024);
    }
    else if ($os == "linux") {
        $load_arr = sys_getloadavg();
        $load = $load_arr[0];
        // $fh = fopen('/proc/meminfo','r');
        // while ($line = fgets($fh)) {
        //     $pieces = array();
        //     if (preg_match('/^MemFree:\s+(\d+)\skB$/', $line, $pieces)) {
        //         $mem = $pieces[1];
        //         break;
        //     }
        //     else if (preg_match('/^MemTotal:\s+(\d+)\skB$/', $line, $pieces)) {
        //         $total_mem = $pieces[1];
        //         break;
        //     }
        // }
        // fclose($fh);
        $output = "";
        $cmd = "free | awk '/Mem/ {print $2}'";
        @exec($cmd, $output);
        $total_mem = (int) $output[0];

        $output = "";
        $cmd = "free | awk '/Mem/ {print $4}'";
        @exec($cmd, $output);
        $mem = (int) $output[0];

        $output = "";
        $cmd = "df -BG / | awk 'NR==2 {gsub(\"G\", \"\", $4); print $4}'";
        @exec($cmd, $output);
        $storage = (int) $output[0];

        $output = "";
        $cmd = "df -BG --total | awk 'NR==2 {gsub(\"G\", \"\", $2); print $2}'";
        @exec($cmd, $output);
        $total_storage = (int) $output[0];
    }

    // $loadarr = sys_getloadavg();
    // $load = $load + $loadarr[0];
    // $load = $load + $loadarr[1];
    // $load = $load + $loadarr[2];


    $server_data = new stdClass();
    $server_data->cpu = round($load);
    // $mem = str_replace(",", "", $mem);
    // $total_mem = str_replace(",", "", $total_mem);
    $server_data->mem = ($total_mem - $mem) / 1024;
    $server_data->mem_max = $total_mem / 1024;
    $server_data->disk = $total_storage - $storage;
    $server_data->disk_max = $total_storage;
    $info = json_encode($server_data);
    echo ($isSSE? "data: " : "") . $info . "\n\n";
    if ($isSSE) {
        flush();
    }
?>
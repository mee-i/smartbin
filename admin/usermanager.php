<?php
session_start();

if (isset($_SESSION['RememberSession'])) {
    session_cache_expire(1440);
}

define("DATA_USERNAME", 0);
define("DATA_NAME", 1);
define("DATA_PASSWORD", 2);
define("DATA_LEVEL", 3);
define("DATA_LOGINCOUNT", 4);
define("DATA_LASTLOGIN", 5);

date_default_timezone_set("Asia/Jakarta");

$fileUserPath = "../../Users/Admins.txt";

if (isset($relative_path)) {
    $fileUserPath = $relative_path . $fileUserPath;
}
else {
    $relative_path = "";
}

$fileContents = "";

if (file_exists($fileUserPath))
$fileContents = file_get_contents($fileUserPath);

// var_dump($fileContents);

// var_dump($fileContents);
// else if (strtoupper(substr(PHP_OS, 0, 5)) === 'LINUX') {
    //    if (file_exists("/server_data/SmartBin/data/user.txt"))
    //        rename("/server_data/SmartBin/data/user.txt", $linuxPath);
    //    else if (file_exists($userFileSystem))
    //        file_put_contents($linuxPath, file_get_contents($userFileSystem));
    //    $fileContents = file_get_contents($fileUserPath);
    //}

// echo var_dump($fileContents);

function randStr($len = 15) {
    $chars = "0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz";
    $output = "";
    for ($i = 0; $i < $len; $i++) {
        $output .= $chars[rand(0, strlen($chars) - 1)];
    }
    return $output;
}

function getPass($username) {
    // Pecah isi file menjadi array berdasarkan baris
    $lines = explode("\n", $GLOBALS['fileContents']);
    
    // Iterasi melalui setiap baris
    foreach ($lines as $line) {
        // Pecah baris menjadi array berdasarkan karakter '|'
        $data = explode('|', $line);
        // echo $data[0];
        
        // Cek apakah username pada baris saat ini sama dengan username yang dicari
        if ($data[0] == $username) {
            // echo "Username cocok";
            // Jika cocok, kembalikan password
            // echo substr($data[2], 0, 128);
            // return $data[2];
            return substr($data[2], 0, 128);
        }
    }
    return null;
}

function getName($username) {
    // Pecah isi file menjadi array berdasarkan baris
    $lines = explode("\n", $GLOBALS['fileContents']);
    
    // Iterasi melalui setiap baris
    foreach ($lines as $line) {
        // Pecah baris menjadi array berdasarkan karakter '|'
        $data = explode('|', $line);
        
        // Cek apakah username pada baris saat ini sama dengan username yang dicari
        if ($data[0] == $username) {
            // Jika cocok, kembalikan [nama]
            return $data[1];
        }
    }
    return null;
}

function getData($username, $index) {
    // Pecah isi file menjadi array berdasarkan baris
    $lines = explode("\n", $GLOBALS['fileContents']);
    // Iterasi melalui setiap baris
    foreach ($lines as $line) {
        // Pecah baris menjadi array berdasarkan karakter '|'
        $data = explode('|', $line);
        // echo $data[0];

        // Cek apakah username pada baris saat ini sama dengan username yang dicari
        if ($data[0] == $username) {
            // echo "Username cocok";
            // Jika cocok, kembalikan data
            // echo substr($data[2], 0, 128);
            // return $data[2];
            return $data[$index];
        }
    }
    return null;
}

function setData($user, $index, $replacement) {
    $lines = explode("\n", $GLOBALS['fileContents']);
    $output = "";
    for ($i = 0; $i < count($lines); $i++) {
        $isUser = false;
        if ($i > 0) {
            $output .= "\n";
        }
        $data = explode("|", $lines[$i]);
        if ($data[0] == $user) {
            $isUser = true;
        }
        for ($j = 0; $j < count($data); $j++) {
            if ($j > 0) {
                $output .= "|";
            }
            if ($isUser && $j == $index) {
                $output .= $replacement;
                continue;
            }
            $output .= $data[$j];
        }
    }
    $GLOBALS['fileContents'] = $output;
    return $output;
}

function updateUserFile() {
    file_put_contents($GLOBALS['fileUserPath'], $GLOBALS['fileContents']);
}

function getUsers() {
    $lines = explode("\n", $GLOBALS['fileContents']);
    $Users = array();
    foreach ($lines as $line) {
        $data = explode('|', $line);
      array_push($Users, $data[0]);
    }
    return $Users;
}

function time_scandir($dir) {
    $ignored = array('.', '..', '.svn', '.htaccess', 'desktop.ini');
  
    $files = array();    
    foreach (scandir($dir) as $file) {
        if (in_array($file, $ignored)) continue;
        $files[$file] = filemtime($dir . '/' . $file);
    }
  
    arsort($files);
    $files = array_keys($files);
  
    return $files;
    
}

function check_min_level($minlevel) {
    $user_level = getData($_SESSION['Username'], DATA_LEVEL);
    $level_order = array("BASIC_ADMIN", "SUPER_ADMIN", "DEVELOPER_ADMIN");
    $user_level_number = array_search($user_level, $level_order);
    $minlevel_number = array_search($minlevel, $level_order);
    if ($user_level_number < $minlevel_number) {
        return false;
    }
    else {
        return true;
    }
    return false;
}

// if (!$_SESSION['IsAdmin']) {
//     http_response_code(403);
// }

$admin_manage_tokens = scandir($relative_path.'../../.Data/');

if ($admin_manage_tokens) {
    foreach ($admin_manage_tokens as $file) {
        $filename = $relative_path.'../../.Data/'.$file;
        if (is_dir($filename)) continue;
        else if (!preg_match("/^.admin_/", basename($filename))) continue;
        if (filemtime($filename) + 900 < time()) {
            unlink($filename);
        }
    }
}

$smartbin_dir = $relative_path.'../../';
$data_dir = $smartbin_dir.'.Data/';
$token_filedir = $data_dir.'.admin_';
if ($_SESSION['IsAdmin']) {
    $chfileadmin = false;
    if (isset($_COOKIE['adminid']) && filemtime($token_filedir.$_COOKIE['adminid']) + 450 < time()) {
        unlink($token_filedir.$_COOKIE['adminid']);
        $chfileadmin = true;
    }
    if (!isset($_COOKIE['adminid']) || $chfileadmin) {
        $new_id = randStr(24);
        $new_token = randStr(256);
        setcookie('adminid', $new_id, time() + 900, "/admin/"); // 86400 = 1 day
        setcookie('admintoken', $new_token, time() + 900, "/admin/"); // 86400 = 1 day
        $admin_data = array();
        $admin_data['admintoken'] = $new_token;
        $admin_data['adminlevel'] = getData($_SESSION['Username'], DATA_LEVEL);
        file_put_contents($relative_path.'../../.Data/.admin_'.$new_id, json_encode($admin_data));
    }

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
        if ($code == 0 && filemtime($ws_data_dir) + (3600 * 24) < time()) {
            if ($ws_pid != "WS_STP") exec("kill ".$ws_pid);
            file_put_contents($ws_file, "");
            $code = -1;
        }
    }

    if ($code != 0 && $ws_pid != "WS_STP") {
        exec('python3.11 '.$smartbin_dir.'websocket/ws.py > '.$smartbin_dir.'.logs/websocket/socket'.date("Ymd_His").'.log 2>&1 & echo $!', $output);
        $output = implode('', $output);
        $ws_pid = (int)$output;
        file_put_contents($ws_data_dir, (string) $ws_pid);
    }
}
?>
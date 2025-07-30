<?php
require 'usermanager.php';

if (!$_SESSION["IsAdmin"]) {
    http_response_code(403);
    exit;
}

if (getData($_SESSION["Username"], 3) == 'BASIC_ADMIN') {
    http_response_code(403);
    exit;
}

// function generateSignature($str) {
//     $sub_signature = 0;
//     $signature = "";
//     $char = "hjrncdiyqsmbfvgteazwxulkpoUJFPNBEVGYRTCMXKLSIOWQAZDH ~`!@#$%^&*()-_=+[{]}\\|;:'\",<.>/?";
//     for ($i = 0; $i < 100; $i++) {
//         $sub_signature = 0;
//         for ($j = 0; $j < 20; $j++) {
//             $index = ($i + $j) % strlen($str);
//             $sub_signature += strpos($char, substr($str, $index, 1)) * 1044 * $i * $j;
//         }
//         $sub_signature *= $i * 365;
//         $signature .= base64_encode($sub_signature);
//     }
//     // $signature = strlen($str);
//     return $signature;
//     // return $signature;
// }

chdir('../../');
$default_dir = getcwd();

if (!isset($_SESSION['terminal-dir'])) {
    $_SESSION['terminal-dir'] = $default_dir;
}
if (!isset($_SESSION['cmd_output'])) {
    $_SESSION['cmd_output'] = randStr(20);
}
if (!isset($_SESSION['current_cmd'])) {
    $_SESSION['current_cmd'] = false;
}

$current_dir = $_SESSION['terminal-dir'];

// $smartbin_dir = getcwd();
chdir($current_dir);

if (isset($_GET['update'])) {
    $file_out = $default_dir.'/.Data/.'.$_SESSION['cmd_output'].'.tmp';
    $control = $_GET['update'];
    
    if ($_SESSION['current_cmd'] !== false) {
        exec('ps -p ' . $_SESSION['current_cmd'], $out, $code);
    }
    else {
        $response = array();
        $response['out'] = "";
        $response['run'] = false;
        echo json_encode($response);
        exit;
    }
    
    if ($control == 'SIGKILL') {
        exec("kill " . $_SESSION['current_cmd']);
    }
    else if ($control == 'SIGINT') {
        exec("kill -2 " . $_SESSION['current_cmd']);
    }
    
    $response = array();
    $response['run'] = false;
    $response['out'] = file_get_contents($file_out);
    file_put_contents($file_out, "");
    if ($code == 0) {
        $response['run'] = true;
    }
    else {
        $response['run'] = false;
        $_SESSION['current_cmd'] = false;
        $dir = getcwd();
        $dir = str_replace($default_dir, "~", $dir);
        
        $response['out'] .= "\n<span class='terminal-user'>".get_current_user()."@orangepi3-lts</span>";
        $response['out'] .= ':<span class="terminal-dir">'.$dir.'</span>$ ';
        unlink($file_out);
    }
    
    echo json_encode($response);
    
    exit;
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $command = $_POST["command"];
    
    // $dir = getcwd();
    // $dir = preg_replace("/^".str_replace("/", "\\/", $smartbin_dir)."/", "~", $dir);
    
    if (hash("sha512", $command) != $_POST["signature"]) {
        echo "Invalid signature: ".hash("sha512", $command);
        exit;
    }
    $response = "";
    $response2 = "";
    if ($command != "") {
        $file_out = $default_dir.'/.Data/.'.$_SESSION['cmd_output'].'.tmp';
        
        exec(escapeshellcmd($command) . ' > ' . $file_out . ' 2>&1 & echo $!', $output);
        $_SESSION['current_cmd'] = (int) implode('', $output);
        // $response = implode("\n", $output) . "\n";
        // $response = "";
        // foreach ($output as $line) {
        //     $response = $response . $line . "\n";
        // }
        if (preg_match("/^\s*cd\s+(.*[\S]+)\s*$/", $command, $matches)) {
            chdir($matches[1]);
        }
        else if (preg_match("/^\s*cd\s*$/", $command)) {
            chdir($default_dir);
        }
    }
    $_SESSION['terminal-dir'] = getcwd();
    
    echo "CMD_RUN";
    exit;
}
?>
<html>
    <head>
        <title>Server Terminal</title>
        <meta name="viewport" content="width=device-width, height=device-height, interactive-widget=resizes-content, user-scalable=no">
        <link href="getfile.php?file=terminal.css" rel="stylesheet">
    </head>
    <body>
        <header>
            <span class="title">Server Terminal v1.2</span>
        </header>
        <div style="user-select: all; margin: 0px; border-radius: 0;" id="server-terminal"><div id="history"></div><div id="out"></div><span autocapitalize="off" contenteditable id="input-terminal"></span>
        </div>
        <footer>
            <div class="control" id="ctrl-c">CTRL-C</div>
            <div class="control" id="ctrl-z">CTRL-Z</div>
            <div class="control" id="arr-up">UP</div>
            <div class="control" id="arr-down">DOWN</div>
            <div class="control" id="scale-up">SCALE+</div>
            <div class="control" id="scale-down">SCALE-</div>
        </footer>
        <?php
        echo "<script>";
        if ($_SESSION['current_cmd'] !== false) {
            echo "window.isRunning = true;";
        }
        echo "const TERMINAL_TOKEN = ";
        echo "</script>";
        ?>
        <script src="/scripts/sha512.min.js"></script>
        <script src="getfile.php?file=terminal.js"></script>
    </body>
</html>
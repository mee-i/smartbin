<?php
require 'usermanager.php';

header("Content-Type: text/plain");

if (!$_SESSION["IsAdmin"]) {
    http_response_code(403);
    exit;
}

// function countData() {
//   return count(explode("\n", $fileContents));
// }

// $userCount = countData();

$AdminUsers = getUsers();
// echo var_dump($AdminUsers);

function getMessages() {
    $filepath = "../../UserMessages/";
    $fileArr = time_scandir($filepath);
    if (count($fileArr) <= 0) {
        echo "<div class='section-card'>";
        echo "<div class='box-title'> There is no message </div>";
        echo "</div>";
    }
    foreach ($fileArr as $filename) {
        $fileData = file_get_contents($filepath . $filename);
        echo "<div class='section-card'>";
        echo "<div class='box-title'>". str_replace(".txt", "", $filename) . 
            "<a class='delete-msg' href='javascript:' onclick='delMsg(\"/admin/setdata.php?data=".$filename."&command=deletemessage\")'>" .
            "Delete" . "</a>" .
            "</div>";
        echo "<table class='table1'>";
        if (preg_match('/Sender Name:\s+(.+)\n/', $fileData, $pieces)) {
            echo "<tr>";
            echo '<td>From :</td>';
            echo '<td>' . $pieces[1] . '</td>';
            echo "</tr>";
        }
        if (preg_match('/Email:\s+(.+)\n/', $fileData, $pieces)) {
            echo "<tr>";
            echo '<td>Email :</td>';
            echo '<td>' . $pieces[1] . '</td>';
            echo "</tr>";
        }
        if (preg_match('/Phone Number:\s+(.+)\n/', $fileData, $pieces)) {
            echo "<tr>";
            echo '<td>Phone Number :</td>';
            echo '<td>' . $pieces[1] . '</td>';
            echo "</tr>";
        }
        if (preg_match('/Message:\s([\w\W\d\D\n]+)$/', $fileData, $pieces)) {
            echo "<tr>";
            echo '<td>Message :</td>';
            echo '<td>' . $pieces[1] . '</td>';
            echo "</tr>";
        }
        echo "</table></div>";
    }
}

if ($_SERVER['REQUEST_METHOD'] == 'GET') {
    $data = $_GET['data'];
    if ($data == 'usersdata') {
        if (getData($_SESSION['Username'], 3) == 'BASIC_ADMIN') {
            http_response_code(403);
            echo "Forbidden access";
            exit;
        }
        $Users = array();
        foreach ($AdminUsers as $user) {
            $response = new stdClass();
            $response->name = getData($user, 1);
            $response->username = $user;
            $response->level = getData($user, 3) . "</td>";
            $response->lastlogin = getData($user, 5) . "</td>";
            $response->logincount = getData($user, 4) . "</td>";
            array_push($Users, $response);
        }
        echo json_encode($Users);
    }
    else if ($data == 'messages') {
        getMessages();
    }
    else if ($data == 'accesslog') {
        $cleanMode = true;
        if (isset($_GET['cleanmode'])) {
            if ($_GET['cleanmode'] == 'true') {
                $cleanMode = true;
            }
        }
        $maxFileLine = 50;
        $filepath = "";
        $logfile = "";
        if (strtoupper(substr(PHP_OS, 0, 3)) === 'WIN') {
            $filepath = "C:/xampp/apache/logs/access.log";
        }
        else if (strtoupper(substr(PHP_OS, 0, 5)) === 'LINUX') {
            $filepath = "/var/log/apache2/access.log";
        }
        $logfile = file_get_contents($filepath);
        $accesslog = explode("\n", $logfile);
        $dataCount = count($accesslog);
        if ($dataCount > 2000) {
            file_put_contents($filepath, "");
        }
        $responseArr = array();
        $response = "";
        if ($dataCount > $maxFileLine) {
            for ($i = 0; $i < $maxFileLine; $i++) {
                if (preg_match('/(update_data|getdata|ping)/', $accesslog[$dataCount - $maxFileLine + $i]) && $cleanMode) {
                    // $maxFileLine++;
                    continue;
                }
                array_push($responseArr, $accesslog[$dataCount - $maxFileLine + $i]);
            }
            $response = implode("\n", $responseArr);
        }
        else {
            $response = $logfile;
        }
        echo $response;
    }
    exit;
}
?>
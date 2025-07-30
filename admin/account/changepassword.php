<?php
$relative_path = "../";
require '../usermanager.php';

// // Baca isi file user.txt
// $fileUserPath = '../../../Users/user.txt';
// $fileContents = file_get_contents($fileUserPath);

// function getPass($username) {
//     // Pecah isi file menjadi array berdasarkan baris
//     $lines = explode("\n", $GLOBALS['fileContents']);

//     // Iterasi melalui setiap baris
//     foreach ($lines as $line) {
//         // Pecah baris menjadi array berdasarkan karakter '|'
//         $data = explode('|', $line);
//         // echo $data[0];

//         // Cek apakah username pada baris saat ini sama dengan username yang dicari
//         if ($data[0] == $username) {
//             // echo "Username cocok";
//             // Jika cocok, kembalikan password
//             // echo substr($data[2], 0, 128);
//             // return $data[2];
//             return substr($data[2], 0, 128);
//         }
//     }
//     return null;
// }

// function setData($user, $index, $replacement) {
//     $lines = explode("\n", $GLOBALS['fileContents']);
//     $output = "";
//     for ($i = 0; $i < count($lines); $i++) {
//         $isUser = false;
//         if ($i > 0) {
//             $output .= "\n";
//         }
//         $data = explode("|", $lines[$i]);
//         if ($data[0] == $user) {
//             $isUser = true;
//         }
//         for ($j = 0; $j < count($data); $j++) {
//             if ($j > 0) {
//                 $output .= "|";
//             }
//             if ($isUser && $j == $index) {
//                 $output .= $replacement;
//                 continue;
//             }
//             $output .= $data[$j];
//         }
//     }
//     $GLOBALS['fileContents'] = $output;
//     return $output;
// }

// function updateUserFile() {
//     file_put_contents($fileUserPath, $fileContents);
// }

if (!isset($_SESSION['Username']) || !isset($_SESSION['IsLogin'])) {
    exit;
}

$isProcessFailed = 'false';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $fileData = file_get_contents('../../../Users/user.txt');

    $firstpassword = hash("sha512", $_POST['firstpass']);
    $secondpassword = hash("sha512", $_POST['secondpass']);
    $thirdpassword = hash("sha512", $_POST['thirdpass']);

    $passwordOriginal = getPass($_SESSION['Username']);

    $isProcessFailed = false;

    if ($secondpassword != $thirdpassword) {
        $isProcessFailed = 'true_confirm';
    }
    else if ($passwordOriginal != $firstpassword) {
        $isProcessFailed = 'true_old';
    }
    else {
        setData($_SESSION['Username'], 2, $secondpassword);
        updateUserFile();
        header("Location: /admin/");
    }
    // $filecontent = 
}
?>
<!DOCTYPE html>
<html>
    <head>
        <title>SmartBin - Account</title>
        <meta name="viewport" content="width=device-width,initial-scale=1">
        <link rel="stylesheet" href="../getfile.php?file=account/style.css">
    </head>
    <body class="theme-background">
        <div class="container-flex flex-center">
            <div class="login-box container white-background">
                <form method="post" class="container-flex flex-center flex-column">
                    <h3 class="logo">SmartBin</h3>
                    <h2>Ubah Password</h2>
                    <p class="username">
                        <?php
                        if (!isset($_SESSION["Username"])) {
                            echo "Unknown";
                        }
                        else {
                            echo $_SESSION["Username"];
                        }
                        ?>
                    </p>
                    <div class="input-box">
                        <span class="input-label">Masukkan sandi lama</span>
                        <input name="firstpass" type="password">
                    </div>
                    <div class="input-box">
                        <span class="input-label">Masukkan sandi baru</span>
                        <input name="secondpass" type="password">
                    </div>
                    <div class="input-box">
                        <span class="input-label">Konfirmasi sandi baru</span>
                        <input name="thirdpass" type="password">
                    </div>
                    <?php
                    if ($isProcessFailed == 'true_old')
                        echo "<p style='color: red;'>Password lama salah</p>";
                    else if ($isProcessFailed == 'true_confirm')
                        echo "<p style='color: red;'>Password konfirmasi salah</p>";
                    ?>
                    <div class="input-box">
                        <input type="submit" value="          Ubah          ">
                    </div>
                </form>
            </div>
        </div>
    </body>
</html>
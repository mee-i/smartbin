<?php

require '../admin/usermanager.php';

// function getData($username, $index) {
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
//             // Jika cocok, kembalikan data
//             // echo substr($data[2], 0, 128);
//             // return $data[2];
//             return $data[$index];
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

if (isset($_GET['path'])) {
    $_SESSION['RedirectPath'] = $_GET['path'];
}

if (!isset($_SESSION["IsLoginSuccess"])) {
    $_SESSION["IsLoginSuccess"] = false;
}
if (!isset($_SESSION["IsLoginFailed"])) {
    $_SESSION["IsLoginFailed"] = false;
}
if (!isset($_SESSION["IsAdmin"])) {
    $_SESSION["IsAdmin"] = false;
}
if (!isset($_SESSION["Username"])) {
    $_SESSION["Username"] = "";
}
if (!isset($_SESSION["Name"])) {
    $_SESSION["Name"] = "";
}
if (!isset($_SESSION["IsLogin"])) {
    $_SESSION["IsLogin"] = false;
}

$adminName = getUsers();

if ($_SESSION["IsAdmin"] == true) {
    header("Location: /admin");
}

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

// function getName($username) {
//     // Pecah isi file menjadi array berdasarkan baris
//     $lines = explode("\n", $GLOBALS['fileContents']);

//     // Iterasi melalui setiap baris
//     foreach ($lines as $line) {
//         // Pecah baris menjadi array berdasarkan karakter '|'
//         $data = explode('|', $line);

//         // Cek apakah username pada baris saat ini sama dengan username yang dicari
//         if ($data[0] == $username) {
//             // Jika cocok, kembalikan [nama]
//             return $data[1];
//         }
//     }
//     return null;
// }

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $UsernameInput = $_POST["Username"];
    $PasswordInput = $_POST["Password"];
    if (!empty($UsernameInput) && !empty($PasswordInput)) {
        $hashedPassword = hash("sha512", $PasswordInput);
        $AccountPassword = getPass($UsernameInput);

        if ($AccountPassword == $hashedPassword) {
            if (in_array($UsernameInput, $adminName)) {
                // $usersData = file_get_contents('../../Users/user.txt');
                $_SESSION["IsAdmin"] = true;
                $_SESSION["IsLogin"] = true;
                $_SESSION["Username"] = $UsernameInput;
                $_SESSION["Name"] = getName($UsernameInput);
                if (isset($_POST['Remember'])) {
                    $_SESSION['RememberSession'] = '';
                }
                setData($UsernameInput, 4, (int)getData($UsernameInput, 4) + 1);
                setData($UsernameInput, 5, date("d/m/Y H:i:s"));
                updateUserFile();

                $redirect = "/admin";
                if (isset($_SESSION['RedirectPath'])) {
                    $redirect = $_SESSION['RedirectPath'];
                    unset($_SESSION['RedirectPath']);
                }
                header("Location: ".$redirect);
            } else {
                // header("Location: guest/dashboard.php");
            }
            // $_SESSION["IsLoginSuccess"] = true;
        } else {
            $_SESSION["IsLoginFailed"] = true;
        }
    }
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="login.css">
    <meta name="theme-color" content="#595be0">
    <title>SmartBin - Login</title>
</head>
<body>
    <div class="center">
        <h1>SmartBin</h1>
        <form method="post">
            <div class="animated-border">
                <div class="txt_field">
                    <input type="text" name="Username" required>
                    <span></span>
                    <label>Username</label>
                </div>
            </div>
        <div class="txt_field">
            <input type="password" name="Password" required>
            <span></span>
            <label>Password</label>
        </div>
        <div class="remember">
            <input type="checkbox" name="Remember" id="login-remember"/>
            <label for="login-remember">Remember login</label>
        </div>
        <?php
        if ($_SESSION["IsLoginFailed"]) {
            ?>
            <div class="wrong-password" style="
            margin-left: 20px; 
            color: #ce0000;
            ">
            Username or Password is wrong!</div>
            <?php
        }
        ?>
        <!-- <div class="pass">Forgot password?</div> -->
        <input type="submit" value="Login">
        <!-- <div class="signaupin_link">
            Not a member ? <a href="#">Sign up</a>
        </div> -->
        </form>
        
    </div>
</body>
</html>
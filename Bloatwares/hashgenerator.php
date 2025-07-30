<html>
    <head>
        <title> Hash Generator </title>
    </head>
    <body>
<?php

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $UsernameInput = $_POST["Username"];
    $PasswordInput = $_POST["Password"];
    if (!empty($UsernameInput) && !empty($PasswordInput)) {
        $hashedPassword = hash("sha512", $PasswordInput);
        echo $hashedPassword;
    }
}

?>
</body>
</html>
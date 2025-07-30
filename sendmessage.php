<?php
session_start();
if ($_SERVER["REQUEST_METHOD"] != 'POST') {
  exit;
}
$name = $_POST['name'];
$email = $_POST['email'];
$phone = $_POST['phone'];
$message = $_POST['message'];

$path = "../UserMessages/";
/* if (strtoupper(substr(PHP_OS, 0, 3)) === 'WIN') {
  $path = "../UserMessages/";
}
else if (strtoupper(substr(PHP_OS, 0, 5)) === 'LINUX') {
  $path = "/server_data/SmartBin/UserMessages/";
} */
$file = fopen($path.'Message from '.$name.'.txt', 'w');
fwrite($file, 'Sender Name: '.$name."\n");
fwrite($file, 'Email: '.$email."\n");
fwrite($file, 'Phone Number: '.$phone."\n");
fwrite($file, "\nMessage:\n".$message);
$_SESSION["LastMessageSubmit"] = true;
fclose($file);
// echo "Pesan Anda telah tersampaikan!";
?>
<!DOCTYPE html>
<html>
  <head>
    <title>Arduino IoT</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link href="style.css" rel="stylesheet">
    <style>
      body, html {
        height: 100%;
      }
      body {
        align-content: center;
        justify-content: center;
      }
      .left, .right, .card-container {
        height: 100%;
      }
      .right {
        height: 100%;
      }
      .right-container {
        position: relative;
        /* top: 50%;
        transform: translateY(-50%); */
        height: 75%;
      }
      .goto-btn {
        position: absolute;
        bottom: 20px;
        right: 20px;
      }
      .container {
        position: relative;
      }
      .home-section {
        left: 0 !important;
      }
    </style>
  </head>
  <body>
    <section id="messages" class="home-section tab">
      <div style="position: relative;">
        <div style="position: absolute;left: -29px; top: 25px;">
          <i class='bx bx-menu btn-phone sidebar-btn'></i>
        </div>
      </div>
      <div class="container" style="display:block; margin-top: 50px; height: 80%;">
        <div class="card-container">
          <div class="left">
            </div>
            <div class="right" style="overflow: hidden;">
              <div class="right-container">
                <h2>Pesan Anda telah Tersampaikan</h2>
                <p>Umpan balik yang Anda berikan dapat membantu kami untuk meyempurnakan SmartBin</p>
                <br>
                <a class="goto-btn" href="/">Ke halaman utama &gt;</a>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </body>
</html>

<?php
require 'usermanager.php';

if (!$_SESSION["IsAdmin"]) {
  if ($_GET['redirect'] == 'no') {
    header("Location: /login/?url=/admin/");
  }
  else {
    header("Location: /");
  }
}

// function countData() {
//   return count(explode("\n", $fileContents));
// }

// $userCount = countData();

// Mengambil daftar user
$AdminUsers = getUsers();

?>

<!-- 
  CPU Update: #CPU-stat (.value -> %(000%), meter -> %(0.00))
  Mem Update: #Mem-stat
  Recent Log: #recent-log
  Full stat: .warn
-->


<!DOCTYPE html>
<!-- Created by CodingLab |www.youtube.com/CodingLabYT-->
<html lang="en" dir="ltr">
  <head>
    <meta charset="UTF-8">
    <title>SmartBin - Admin</title>
    <link rel="stylesheet" href="/PoppinsFont/stylesheet.css">
    <link rel="stylesheet" href="getfile.php?file=style.css">
    <link rel="stylesheet" href="getfile.php?file=animations.css">
    <!-- Boxicons CDN Link -->
    <!-- <link href='https://unpkg.com/boxicons@2.0.7/css/boxicons.min.css' rel='stylesheet'> -->
    <link href='/icon/icon.css' rel='stylesheet'>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <script>
      window.onerror = function(event, source, lineno, colno, error) {
        alert(`${event}: ${source} at ${lineno}:${colno}\n${error}`);
      }
    </script>
  </head>
  <body class="admin-mode" hidden>
    <div class="sidebar">
      <div class="logo-details">
        <div class="logo_name">SMART BIN <span class="status">Admin Mode</span></div>
        <i class="bx bx-menu sidebar-button" id="btn-desktop"></i>
      </div>
      <ul class="nav-list">
        <li id="link-home">
          <a onclick="openTab('home')">
            <i class='bx bxs-home'></i>
            <span class="links_name">Home</span>
          </a>
          <span class="tooltip">Home</span>
        </li>
        <li id="link-dashboard">
          <a onclick="openTab('dashboard')">
            <i class='bx bxs-dashboard'></i>
            <span class="links_name">Dashboard</span>
          </a>
          <span class="tooltip">Dashboard</span>
        </li>
        <li id="link-messages">
          <a onclick="openTab('messages')">
            <i class='bx bxs-chat' ></i>
            <span class="links_name">Messages</span>
          </a>
          <span class="tooltip">Messages</span>
        </li>
        <li id="link-account">
          <a onclick="openTab('account')">
            <i class='bx bxs-user'></i>
            <span class="links_name">Account<span class="status">Admin</span></span>
          </a>
          <span class="tooltip">Account</span>
        </li>
        <li id="link-setting">
          <a onclick="openTab('setting')">
            <i class='bx bxs-cog' ></i>
            <span class="links_name">Setting</span>
          </a>
          <span class="tooltip">Setting</span>
        </li>

        <!-- --------- Developer Only --------- -->
        <?php if (check_min_level('DEVELOPER_ADMIN')) { ?>

          <li id="link-control">
            <a onclick="openTab('server-control')">
              <i class='bx bxs-server' ></i>
              <span class="links_name">Server Control</span>
            </a>
            <span class="tooltip">Server Control</span>
          </li>
          <li id="link-experimental">
            <a onclick="openTab('trashbin-control')">
              <i class='bx bxs-trash' ></i>
              <span class="links_name">Trash Bin Control</span>
            </a>
            <span class="tooltip">Trash Bin Control</span>
          </li>
          <li id="link-filemanager">
            <a onclick="location.href = 'filemanager/'">
              <i class='bx bxs-folder' ></i>
              <span class="links_name">File Manager</span>
            </a>
            <span class="tooltip">File Manager</span>
          </li>
          <?php } ?>
          <!-- -------------------------------------- -->
        
      </ul>
    </div>

    <!-- section dashboard -->
    <section id="home" class="home-section tab">
      <header>
        <i class="bx bx-menu sidebar-button" id="btn-phone"></i>
        <div style="color: white;"class="text">Home</div>
        <div class="welcome-text">Admin Control Panel</div>
      </header>
      <div class="home-container">
        <div id="CPU-stat">
          <span class="title">CPU</span>
          <i class="bx bx-chip card-icon"></i>
          <span class="value">
            <span id="value">0%</span>
            <i class="bx bx-error card-icon warn-sign"></i>
          </span>
          <meter value="0"></meter>
        </div>
        <div id="Mem-stat">
          <span class="title">Memory</span>
          <i class="bx bxs-microchip card-icon"></i>
          <span class="value">
            <span id="value">0%</span>
            <i class="bx bx-error card-icon warn-sign"></i>
          </span>
          <span class="sub-value">0 / 512 MB</span>
          <meter value="0"></meter>
        </div>
        <div class="clickable" onclick="openTab('dashboard')">
          <span class="title">Dashboard</span>
          <i class='bx bxs-dashboard card-icon'></i>
          <span class="goto">To Dashboard &gt</span>
        </div>
        <div class="clickable" onclick="openTab('account')">
          <span class="title">Account</span>
          <i class='bx bxs-user card-icon'></i>
          <span class="sub-value">Logged in as Admin</span>
          <span class="goto">To Account &gt</span>
        </div>
        <div id="recent-log" class="clickable double-width">
          <span class="title">Recent log</span>
          <i class='bx bxs-file card-icon'></i>
          <textarea style="margin-bottom: 30px;" readonly></textarea>
          <span onclick="openTab('dashboard')" class="goto">To Dasboard &gt</span>
        </div>
      </div>
    </section>
    <!-- section massage -->
    <section hidden id="messages" class="home-section tab">
      <header>
        <i class="bx bx-menu sidebar-button section-btn" id="btn-phone"></i>
        <div class="text">Messages</div>
        <div class='button-container'>
          <i id='refresh-messages' class='bx bx-refresh dashboard-btn'></i>
        </div>
      </header>
      <div id="message-box">
        <div class='section-card'>
          <div class='box-title'> Loading </div>
        </div>
      </div>
    </section>
    <!-- section dashboard-->
    <section hidden id="dashboard" class="home-section tab">
      <header>
        <i class="bx bx-menu sidebar-button section-btn" id="btn-phone"></i>
        <div class="text">Dashboard</div>
      </header>
      <div>
        <div class="dashboard-card">
          <i class="bx bxs-server dashboard-icon"></i>
          <span class="title">SmartBin Main Server</span>
          <div class='button-container'>
            <?php
            /*if (getData($_SESSION["Username"], 3) != 'BASIC_ADMIN') {
              echo "
                <i id='realtime-update' class='bx bx-play dashboard-btn'></i>
                ";
            }*/
            ?>
          </div>
          <table class="table1">
            <tr>
              <td>
                <i class="bx bx-server"></i>
              </td>
              <td>Server IP:</td>
              <td>
                <?php
                $ip = "";
                if (strtoupper(substr(PHP_OS, 0, 3)) === 'WIN') {
                  // $matches;
                  exec("ipconfig", $output);
                  // echo var_dump($output);
                  preg_match("/Wireless LAN adapter Wi-Fi:[\w\W\n]*IPv4 Address[. ]+:\s+([\d.]+)\n/", implode("\n", $output), $matches);
                  // echo var_dump($matches);
                  $ip = $matches[1];
                }
                else if (strtoupper(substr(PHP_OS, 0, 5)) === 'LINUX') {
                  $ip = exec("hostname -I");
                }
                echo str_replace(' ', '<br>', $ip);
                ?>
              </td>
            </tr>
            <tr>
              <td>
                <i id="to-server-ping-icon" class="bx bx-wifi"></i>
              </td>
              <td>Ping to Server:</td>
              <td id="to-server-ping">Unpinged</td>
            </tr>
            <tr>
              <td>
                <i id="server-ping-icon" class="bx bx-wifi"></i>
              </td>
              <td>Server Ping:</td>
              <td id="server-ping">Unpinged</td>
            </tr>
          </table>
          <div class="box-container">
            <div id="cpu-dashboard">
              <i class="bx bx-chip icon"></i>
              <span class="name">CPU</span>
              <span class="value">0%</span>
              <div style="width: 0%;" class="meter-box"></div>
            </div>
            <div id="mem-dashboard">
              <i class="bx bxs-microchip icon"></i>
              <span class="name">Memory</span>
              <span class="value">0/1 MB | 0%</span>
              <div style="width: 0%;" class="meter-box"></div>
            </div>
            <div id="storage-dashboard">
              <i class="bx bxs-hdd icon"></i>
              <span class="name">Storage</span>
              <span class="value">0/1 GB | 0%</span>
              <div style="width: 0%;" class="meter-box"></div>
            </div>
          </div>
        </div>
        <div class="dashboard-card">
          <i class="bx bxs-trash dashboard-icon"></i>
          <span class="title">Trash Bin</span>
          <table class="table1">
            <tr>
              <td>
                <i id="trashbin-status-icon" class="bx bx-wifi"></i>
              </td>
              <td>Status:</td>
              <td id="trashbin-status">unknown</td>
            </tr>
          </table>
          <div class="box-container">
            <div id="stat-organic">
              <i class="bx bxs-trash icon"></i>
              <span class="name">Organic</span>
              <div class="capacity-rst organic">Reset</div>
              <span class="value">0%</span>
              <div style="width: 0%;" class="meter-box"></div>
            </div>
            <div id="stat-anorganic">
              <i class="bx bxs-trash icon"></i>
              <span class="name">Anorganic</span>
              <div class="capacity-rst anorganic">Reset</div>
              <span class="value">0%</span>
              <div style="width: 0%;" class="meter-box"></div>
            </div>
          </div>
        </div>
        <!-- <div class="dashboard-card">
          <i class="bx bxs-file dashboard-icon"></i>
          <span class="title">Server Access Log</span>
          <div class="button-container">
            <i id='refresh-log' class='bx bx-refresh dashboard-btn'></i> -->
            <?php
            // if (getData($_SESSION["Username"], 3) != 'BASIC_ADMIN') {
            //   echo "
            //   <i id='realtime-update-log' class='bx bx-play dashboard-btn'></i>
            //   ";
            // }
            ?>
            <!--<i onclick="window.open('getdata.php?data=accesslog', '_blank')" class="bx bx-link-external dashboard-btn"></i>
          </div>
          <div style="user-select: text;" id="server-access-log">
          </div>
        </div> -->
        <?php
        if (check_min_level('SUPER_ADMIN')) {
          echo "
          <div class=\"dashboard-card\">
          <i class=\"bx bxs-terminal dashboard-icon\"></i>
          <span class=\"title\">Server Terminal</span><br>
          <div class=\"button-container\">
          <div onclick=\"window.open('terminal.php', '_blank')\" class=\"bx bx-link-external dashboard-btn\"></div>
          </div>
          <span style=\"font-size: 10px; color: grey;\">(Super Admin and Developer only)</span>
          <iframe class='terminal-frame' src='terminal.php'></iframe>
          ";
          /* echo "
          <div class=\"dashboard-card\">
          <i class=\"bx bxs-terminal dashboard-icon\"></i>
          <span class=\"title\">Server Terminal</span><br>
          <div class=\"button-container\">
          <div onclick=\"window.open('terminal.php', '_blank')\" class=\"bx bx-link-external dashboard-btn\"></div>
          </div>
          <span style=\"font-size: 10px; color: grey;\">(Super Admin and Developer only)</span>
          <div style=\"user-select: all;\" id=\"server-terminal\"><div id=\"history\"></div><div id=\"out\"></div><span contenteditable id=\"input-terminal\"></span>
          </div>
        </div>
          "; */
        }
        ?>
      </div>
    </section>
    <!-- section massage -->
    <section hidden id="account" class="home-section tab">
      <header>
        <i class="bx bx-menu sidebar-button section-btn" id="btn-phone"></i>
        <div class="text">Account</div>
      </header>
      <div class="container user-container">
        <div class="user-box user-card">
          <div class="flexbox">
            <div class="icon-box">
              <i class="bx bxs-user user-icon"></i>
            </div>
            <div>
              <div class="account-box-title">
                <?php
                if (isset($_SESSION["Name"])) {
                  echo $_SESSION["Name"];
                }
                else {
                  echo "Unknown User";
                }
                ?>
              </div>
              <div class="btn-container">
                <div onclick="window.location.href = '/logout.php'" id="logout-btn"><i class="logout-icon bx bx-door-open"></i> Logout</div>
                <div onclick="window.location.href = '/admin/account/changepassword.php'" id="changepass-btn"><i class="logout-icon bx bx-key"></i> Change Password</div>
              </div>
            </div>
          </div>
        </div>
        <div class="user-card">
          <i class="bx bxs-info-circle user-card-icon"></i>
          <span class="box-title">Your Account</span>
          <table class="table1">
            <tr>
              <td>Account Type:</td>
              <td>
                Admin
              </td>
            </tr>
            <tr>
              <td>Admin Level: </td>
              <td>
                <?php
                echo getData($_SESSION["Username"], 3);
                ?>
              </td>
            </tr>
            <tr>
              <td>Login Count: </td>
              <td>
                <?php
                echo getData($_SESSION["Username"], 4);
                ?>
              </td>
            </tr>
            <tr>
              <td>Last Login: </td>
              <td>
                <?php
                echo getData($_SESSION["Username"], 5);
                ?>
              </td>
            </tr>
          </table>
        </div>


        <?php
        if (check_min_level('SUPER_ADMIN')) {
          // SUPER_ADMIN and above only
          ?>
        <div class='user-card'>
          <i class='bx bxs-user-account user-card-icon'></i>
          <span class='box-title'>Other Users <span style='font-size: 10px; color: grey;'>(Super Admin and Developer only)</span></span>
          <table class='table2' id='users-table'>
          </table>
        </div>
        <?php // end of limited access
        }
        ?>


      </div>
    </section>
    <section hidden id="setting" class="home-section tab">
      <header>
        <i class="bx bx-menu sidebar-button section-btn" id="btn-phone"></i>
        <div class="text">Settings</div>
      </header>
      <div class='section-card'>
        <i class="bx bxs-server section-card-icon"></i>
        <span class='box-title'>ESP8266 (Trashbin Client)</span>
        <div class='control-list'>
          <span class="subtitle">WebSocket</span>
          <div class="sublist">
            <div onclick='ws_c()' class="action-normal">
              Connect to WebSocket
              <i class="bx bxs-door-open"></i>
            </div>
            <div onclick='ws_dc()' class="action-danger">
              Disconnect from WebSocket
              <i class="bx bxs-lock-alt"></i>
            </div>
          </div>
        </div>
      </div>
    </section>

    <!-- ===== Developer only section ===== -->
    <?php if (check_min_level("DEVELOPER_ADMIN")) { ?>

    <section hidden id="server-control" class="home-section tab">
      <header>
        <i class="bx bx-menu sidebar-button section-btn" id="btn-phone"></i>
        <div class="text">Server Control</div>
      </header>
      <div class='section-card'>
        <i class="bx bxs-server section-card-icon"></i>
        <span class='box-title'>OrangePI 3 LTS (Main Server)</span>
        <div class='control-list'>
          <span class="subtitle">WebSokcet</span>
          <div class="sublist">
            <div onclick='server_control("WS_RST")' class="action-warning" id="websocket-reset">
              Restart WebSocket
              <i class="bx bx-reset"></i>
            </div>
            <div onclick='server_control("WS_STP")' class="action-danger" id="websocket-stop">
              Stop WebSocket
              <i class="bx bx-power-off"></i>
            </div>
            <!-- <div onclick='server_control("WS_DEVICE_RST")' class="action-warning" id="websocket-reset">
              Auto Restart DEVICE WS
              <i class="bx bx-reset"></i>
            </div>
            <div onclick='server_control("WS_DEVICE_STP")' class="action-danger" id="websocket-stop">
              Stop DEVICE WS
              <i class="bx bx-power-off"></i>
            </div> -->
          </div>
          <span class="subtitle">Public Access</span>
          <div class="sublist">
            <div onclick='server_control("ACCESS_OPEN")' class="action-normal" id="websocket-reset">
              Open Access
              <i class="bx bxs-lock-open"></i>
            </div>
            <div onclick='server_control("ACCESS_CLOSE")' class="action-danger" id="websocket-reset">
              Close Access
              <i class="bx bxs-lock"></i>
            </div>
          </div>
        </div>
      </div>
    </section>

    <!-- Experimental -->
    <section hidden id="trashbin-control" class="home-section tab">
      <header>
        <i class="bx bx-menu sidebar-button section-btn" id="btn-phone"></i>
        <div class="text">Trash Bin Control</div>
      </header>
      <div class='section-card'>
        <i class="bx bxs-server section-card-icon"></i>
        <span class='box-title'>ESP8266 (Trashbin Client)</span>
        <div class='control-list'>
          <span class="subtitle">LED_BUILTIN</span>
          <div class="sublist">
            <div onclick='control_trashbin(door_o)' class="action-normal">
              Open Door (Organic)
              <i class="bx bxs-door-open"></i>
            </div>
            <div onclick='control_trashbin(door_a)' class="action-normal">
              Open Door (Anorganic)
              <i class="bx bxs-door-open"></i>
            </div>
            <div onclick='control_trashbin(close_door)' class="action-danger">
              Close Door
              <i class="bx bxs-lock-alt"></i>
            </div>
            <div onclick='control_trashbin(rst_capacity)' class="action-danger">
              Reset Trash Bin Capacity
              <i class="bx bx-reset"></i>
            </div>
          </div>
        </div>
      </div>
    </section>

    <?php } ?>
    <!-- ==================================== -->

    <?php
    if (check_min_level('SUPER_ADMIN')) {
      echo '<script src="/scripts/sha512.min.js"></script>';
      // echo '<script src="getfile.php?file=terminal.js"></script>';
    }
    ?>
    <script src="getfile.php?file=script5.js"></script>
    <?php
    echo "<script>";
    if (isset($_GET['tab'])) {
      echo "openTab('" . $_GET['tab'] . "');";
    }
    else {
      // echo "openTab('home');";
    }
    echo "</script>";
    ?>
    <script>
      let sidebar = document.querySelector(".sidebar");
      let closeBtn = document.querySelectorAll(".sidebar-button");
      for (var i in closeBtn) {
        closeBtn[i].onclick = () => {
          sidebar.classList.toggle("open");
          menuBtnChange();//calling the function(optional)
        };
      }
      // searchBtn.addEventListener("click", ()=>{ // Sidebar open when you click on the search iocn
      //   sidebar.classList.toggle("open");
      //   menuBtnChange(); //calling the function(optional)
      // });
      // following are the code to change sidebar button(optional)
      function menuBtnChange() {
        if(sidebar.classList.contains("open")){
          closeBtn[0].classList.replace("bx-menu", "bx-menu-alt-right");//replacing the iocns class
        }else {
          closeBtn[0].classList.replace("bx-menu-alt-right","bx-menu");//replacing the iocns class
        }
      }
      document.body.hidden = false;
    </script>
  </body>
</html>
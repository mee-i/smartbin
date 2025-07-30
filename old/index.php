<?php
function randStr($len = 15) {
  $chars = "0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz";
  $output = "";
  for ($i = 0; $i < $len; $i++) {
    $output .= $chars[rand(0, strlen($chars) - 1)];
  }
  return $output;
}
session_start();
if (!isset($_SESSION["gameticket"])) {
  $_SESSION["gameticket"] = randStr(25);
}
?>
<!DOCTYPE html>
<!-- Created by CodingLab |www.youtube.com/CodingLabYT-->
<html lang="en" dir="ltr">
  <head>
    <meta charset="UTF-8">
    <title>SmartBin - Home</title>
    <link rel="stylesheet" href="style.css">
    <!-- Boxicons CDN Link -->
    <link href='https://unpkg.com/boxicons@2.0.7/css/boxicons.min.css' rel='stylesheet'>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script src="script.js"></script>
    <?php
    echo "<script>var GameTicket = '" . $_SESSION['gameticket'] . "';</script>";
    ?>
  </head>
  <body>
    <div style="position: relative;">
      <div class="sidebar">
        <div class="logo-details">
          <div class="logo_name">SMART BIN</div>
          <i class='bx bx-menu sidebar-btn' id="btn-desktop" ></i>
        </div>
        <ul class="nav-list">
          <li>
              <a onclick="openTab('home')">
                <i class='bx bxs-home'></i>
                <span class="links_name">Home</span>
              </a>
              <span class="tooltip">Home</span>
          </li>
          <li>
            <a onclick="openTab('monitor')">
              <i class='bx bx-desktop'></i>
              <span class="links_name">Monitor</span>
            </a>
            <span class="tooltip">Monitor</span>
          </li>
            <li>
              <a onclick="openTab('messages')">
                <i class='bx bxs-chat' ></i>
                <span class="links_name">Contact Us</span>
              </a>
              <span class="tooltip">Contact Us</span>
            </li>
            <li>
              <a href="login">
                <i class='bx bxs-door-open'></i>
                <span class="links_name">Log In</span>
              </a>
              <span class="tooltip">Log In</span>
            </li>
          </ul>
        </div>

    </div>
    

      <!-- section home-->
      <section id="home" class="home-section tab">
        <div style="position: relative;">
          <div style="position: absolute;left: -29px; top: 25px;">
            <i class='bx bx-menu btn-phone sidebar-btn'></i>
          </div>
        </div>
        <div class="text">Home</div>
        <div class="home-page">
          <div class="container-page">
            <div class="content-home">
              <div class="title-home">
                <h2>Project Kami</h2>
                <div class="content-h3">
                  <h3>Kami adalah team pengembangan project dari produk SMARTBIN yang dilakukan di SMKN 13 Bandung yang beranggotakan 6 orang yang memiliki tugsanya masing-masing yang berperan dalam pengembangan project SMARTBIN. Jika ada yang mau ditanyakan boleh langsung saja ditanyakan melalui messages di web ini </h3>
                  <div class="button-home">
                    <a onclick="openTab('monitor')">Selanjutnya</a>
                  </div>
                </div>
              </div>
            </div>
            <div class="image-home">
              <img src="./html-system-websites-concept.jpg" alt="">
            </div>
          </div>
        </div>
      </div>
      <div class="container_anggota">
        <div  class="anggota_title">
          <h2 >arduino team</h2>
          <div class="anggota_list">
            <div>
              <img class="image-anggota" src="medium-shot-man-working-laptop.jpg" alt="" width="500px">
            </div>
            <div class="anggota_isi">
              <h3>kami terdiri dari 6 orang anggota</h3>
              <dl>
                <dt>arduino team</dt>
                <dd>Nanda Ahmad Nurshidiq</dd>
                <dd>Muhammmad Ilham Hizam</dd>
                <dt>web development team</dt>
                <dd>Adit Rahmat Hidayat</dd>
                <dd>Nanda Ahmad Nurshidiq</dd>
                <dt>web design team</dt>
                <dd>Adit Rahmat Hidayat</dd>
                <dd>Nanda Ahmad Nurshidiq</dd>
                <dt>back end team</dt>
                <dd>Muhammmad Ilham Hizam</dd>
                <dd>Nanda Ahmad Nurshidiq</dd>
                <dt>perakitan tong sampah</dt>
                <dd>Adit Rahmat Hidayat</dd>
                <dd>Nanda Ahmad Nurshidiq</dd>
                <dd>Muhammmad Ilham Hizam</dd>
                <dd>Rakha Putra Ridwan</dd>
                <dd>Rival Kholqi Fajduani</dd>
                <dd>Nasywan Mubarok</dd>
                <dt>sesi dokumentasi dan editor</dt>
                <dd>Nasywan Mubarok</dd>
              </dl>
            </div>
          </div>
        </div>
      </div>
      <footer>
        <span class="title" onclick="SecretFeature.run()">SmartBin </span><span class="subtitle"> oleh TIM P5 ARDUINO</span>
        <div class="about">
          <p>Merupakan bagian dari projek P5 Tema 1: "Hidup Keberlanjutan"</p>
        </div>
      </footer>
    </section>
    <!-- section monitor -->
    <section hidden id="monitor" class="home-section tab">
      <div style="position: relative;">
        <div style="position: absolute;left: -29px; top: 25px;">
          <i class='bx bx-menu btn-phone sidebar-btn'></i>
        </div>
      </div>
      
      <div class="text">Monitor</div>
      <div class="flex-container">
        <div id="organik" class="card green">
          <span class="title">Organik</span>
          <span class="value">30%</span>
          <meter value="0.3"></meter>
        </div>
        <div id="anorganik" class="card yellow">
          <span class="title">Anorganik</span>
          <span class="value">80%</span>
          <meter value="0.8"></meter>
        </div>
        <div class="card block-card">
        <span class="title">Pembuangan Sampah Harian Rata-Rata</span>
          <div class="card-grafik">
            <canvas id="myChart"></canvas>
          </div>
        </div>
        <script>
          var ctx = document.getElementById('myChart');

          var Gabut = "Halo, aku adalah salah satu programmer JS ini.";
          console.log(Gabut); // Kita cetak Gabut-nya

          // const cfg = {
          //   type: 'bar',
          //   data: {
          //     datasets: [{
          //       data: [{x: 10, y: 20}, {x: 15, y: 20}, {x: 20, y: 10}, {x: 10, y: 25}, {x: 12, y: 20}, {x: 15, y: 25}, {x: 15, y: 20}]
          //     }]
          //   }
          // }

          function random(min, max) {
            return Math.round(Math.random() * 10000000 % (max - min + 1)) + min;
          }

          var SecretFeature = {
            elem: document.querySelector("#home footer .title "),
            count: 0,
            run: function() {}
          };

          SecretFeature.run = function() {
            if (SecretFeature.count < random(20, 40) || SecretFeature.count > random(50, 60))
              SecretFeature.elem.style.color = `rgb(${random(0, 255)}, ${random(0, 255)}, ${random(0, 255)})`;
            else
              SecretFeature.elem.style.color = 'rgba(0, 0, 0, 0)';
            if (SecretFeature.count > random(20, 40))
              window.location.href = "/secret/smartbingame.php?ticket=" + GameTicket;
            SecretFeature.count++;
          };

          var chart = new Chart(ctx, {
            type: 'bar',
            data: {
              labels: ['Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu' , 'Minggu'],
              datasets: [
                {
                  label: 'Organik',
                  data: Data.dailyAverage.organik,
                  borderWidth: 0,
                  backgroundColor: '#1c9939',
                  borderRadius: 20
                },
                {
                  label: 'Anorganik',
                  data: Data.dailyAverage.anorganik,
                  borderWidth: 0,
                  backgroundColor: '#afb922',
                  borderRadius: 20
                }
              ]
            },
            
            options: {
              scales: {
                y: {
                  beginAtZero: true
                }
              },
              responsive:true,
              maintainAspectRatio: false,
              scales: {
                yAxes: [{
                  ticks: {
                    beginAtZero:true
                  }
                }]
              }
            }
          });
          // setInterval(() => {
          //   ctx.height = 300;
          // }, 0);

          function resizeCanvas() {
            ctx.style.width = (document.querySelector(".card.block-card").clientWidth - 60) + 'px';
            ctx.style.height = (document.querySelector(".card.block-card").clientHeight - 75) + 'px';
          }
          
          window.addEventListener("resize", resizeCanvas);
          resizeCanvas();

          chart.options.animation = false; // disables all animations
          chart.options.animations.colors = false; // disables animation defined by the collection of 'colors' properties
          chart.options.animations.x = false; // disables animation defined by the 'x' property
          chart.options.transitions.active.animation.duration = 0; // disables the animation for 'active' mode

        </script>
        
      </div>
      
    </section>
    <!-- section massage -->
    <section hidden id="messages" class="home-section tab">
      <div style="position: relative;">
        <div style="position: absolute;left: -29px; top: 25px;">
          <i class='bx bx-menu btn-phone sidebar-btn'></i>
        </div>
      </div>
      <div class="text">Contact Us</div>
      <div class="container">
        <div class="card-container">
          <div class="left">
            <div class="left-container">
            <h2>Tentang Kami</h2>
            <p>Kami adalah team program SMARTBIN jika ada yang mau ditanyakan boleh kirim melalui pesan ini</p>
            <br>
            <p>Bantu kami dengan memberikan masukan.</p>
            <br>
            <b>*)Anda hanya bisa mengirim satu pesan per hari</b>
            </div>
          </div>
          <div class="right">
            <div class="right-container">
              <form method="POST" action="/sendmessage.php">
                <h2 class="lg-view">Hubungi Kami</h2>
                <h2 class="sm-view">Hubungi Kami</h2>
                <input name="name" type="text" placeholder="Nama">
                <input name="email" type="email" placeholder="Alamat Email">
                <!-- <input name="" type="text" placeholder="Perusahaan" autocomplete="off"> -->
                <input name="phone" type="phone" placeholder="Telephone" autocomplete="off">
                <textarea name="message" rows="10" placeholder="Pesan"></textarea>
                <button>Kirim</button>
              </form>
            </div>
          </div>
        </div>
      </div>
    </div>
    <script>
      function init() {
        loadDataSmartBin();
        dataSmartBin();
      }
      init();
      function openTab(id) {
        var tabs = document.querySelectorAll(".tab");
        for (var i = 0; i < tabs.length; i++) {
          if (tabs[i].id != id) {
            tabs[i].hidden = true;
          }
          else {
            tabs[i].hidden = false;
          }
        }
        resizeCanvas();
      }
      openTab('home');
      let sidebar = document.querySelector(".sidebar");
      let closeBtn = document.querySelectorAll(".sidebar-btn");
      
      for (var i in closeBtn) {
        closeBtn[i].addEventListener("click", ()=>{
          sidebar.classList.toggle("open");

          menuBtnChange();//calling the function(optional)
        });
      }
      searchBtn.addEventListener("click", ()=>{ // Sidebar open when you click on the search iocn
        sidebar.classList.toggle("open");
        menuBtnChange(); //calling the function(optional)
      });
      // following are the code to change sidebar button(optional)
      function menuBtnChange() {
        if(sidebar.classList.contains("open")){
          closeBtn[0].classList.replace("bx-menu", "bx-menu-alt-right");//replacing the iocns class
        }else {
          closeBtn[0].classList.replace("bx-menu-alt-right","bx-menu");//replacing the iocns class
        }
      }

    </script>
    <?php
    echo "<script>";
    if (isset($_GET['tab'])) {
      echo "openTab('" . $_GET['tab'] . "');";
    }
    echo "</script>";
    ?>
  </body>
</html>
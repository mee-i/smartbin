<?php

// $relative_path = '../admin/';
// require '../admin/usernamager.php';

function randStr($len = 15) {
  $chars = "0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz";
  $output = "";
  for ($i = 0; $i < $len; $i++) {
    $output .= $chars[rand(0, strlen($chars) - 1)];
  }
  return $output;
}

session_start();

// if (!$_SESSION['IsAdmin']) {
//   http_response_code(403);
//   echo "<h2>Akses terbatas, login diperlukan!</h2>";
//   exit;
// }

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
    <link rel="stylesheet" href="style2.css">
    <!-- Boxicons CDN Link -->
    <link href='https://unpkg.com/boxicons@2.0.7/css/boxicons.min.css' rel='stylesheet'>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script src="script3.js"></script>
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
            <a onclick="openTab('spesifikasi')">
              <i class='bx bx-microchip'></i>
              <span class="links_name">How It Works?</span>
            </a>
            <span class="tooltip">How It Works?</span>
          </li>
          <li>
            <a onclick="openTab('monitor')">
              <i class='bx bx-desktop'></i>
              <span class="links_name">Monitor</span>
            </a>
            <span class="tooltip">Monitor</span>
          </li>
          <li>
            <a onclick="openTab('hasil')">
              <i class='bx bx-film' ></i>
              <span class="links_name">Documentation</span>
            </a>
            <span class="tooltip">Documentation</span>
          </li>
            <li>
              <a onclick="openTab('messages')">
                <i class='bx bxs-chat' ></i>
                <span class="links_name">Contact Us</span>
              </a>
              <span class="tooltip">Contact US</span>
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
          <div style="position: absolute;left: -15px;z-index: 99; top: 25px;">
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
                <h3>Kami adalah team pengembangan project dari produk SMARTBIN yang dilakukan di SMKN 13 Bandung.Beranggotakan  6 orang dan masing masing memiliki peran tugasnya masing-masing.Berperan dalam pengembangan project SMARTBIN jika ada yang mau ditanyakan boleh langsung saja menanyakan memalui contact us di website ini </h3>
                  <div class="button-home">
                    <a onclick="openTab('spesifikasi')">Selanjutnya</a>
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
      <div class="tengah">
        <div class="kolom">
            <h2>Partners</h2>
            <p></p>
        </div>

        <div class="partner-list">
            <div class="kartu-partner">
                <img src="download (1).jpeg"/>
            </div>
            <div class="kartu-partner">
                <img src="download (2).png"/>
            </div>
            <div class="kartu-partner">
                <img src="download (3).jpeg"/>
            </div>
            <div class="kartu-partner">
                <img src="download(4).png"/>
            </div>
            <div class="kartu-partner">
                <img src="download (5).png"/>
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
  <!-- <div id="contact">
      <div class="wrapper">
          <div class="footer">
              <div class="footer-section">
                  <h3>NaN(not a number)</h3>
                  <p>kami team pembuat program website(SMARTBIN)</p>
              </div>
              <div class="footer-section">
                  <h3>About</h3>
                  <p>kami disini untuk melaksanakan kegiatan p5 tentang gaya hidup berkelanjutan</p>
              </div>
              <div class="footer-section">
                  <h3>Contact</h3>
                  <p>Jl. soekarno hatta</p>
                  <p>Kode Pos: 40287</p>
              </div>
              <div class="footer-section">
                  <h3>Social</h3>
                  <p><b>YouTube: </b>NaN</p>
              </div>
          </div>
      </div>
  </div> -->

  <!-- <div id="copyright">
      <div class="wrapper">
          &copy; 2023. <b>Not A Number</b> All Rights Reserved.
      </div>
  </div> -->
    </section>
    <!-- section spesifikasi -->
    <section hidden id="spesifikasi" class="home-section tab">
      <div style="position: relative;">
        <div style="position: absolute;left: -29px; top: 25px;">
          <i class='bx bx-menu btn-phone sidebar-btn'></i>
        </div>
      </div>

      <div class="text">How It Works?</div>
      <div class="container_spesifikasi">
        <div class="anggota_title">
          <h2>Cara Kerja</h2>
          <div class="spesifikasi_list">
            <div>
              <img class="image-anggota" src="971 (1).jpg" alt="" width="500px">
            </div>
            <div class="spesifikasi_isi">
              <h3>SmartBin adalah tong sampah pintar yang dilengkapi dengan teknologi canggih untuk memudahkan dan meningkatkan efisiensi pengelolaan sampah. Dengan sensor proximity capacity untuk sampah organik, sensor proximity inductive untuk logam, dan sensor ultrasonic untuk pengukuran volume sampah, SmartBin memungkinkan pemisahan otomatis dan pemantauan kapasitas. Dilengkapi servo untuk menggerakkan tutup secara otomatis dan diatur oleh otak pintar Arduino, SmartBin membawa inovasi dalam upaya menjaga kebersihan lingkungan dengan manajemen sampah yang lebih efisien.</h3>
            </div>
          </div>
        </div>
      </div>
    </section>
    <!-- section monitor -->
    <section hidden id="monitor" class="home-section tab">
      <div style="position: relative;">
          <div style="position: absolute;left: -13px; top: 25px;">
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
        <div class="ajakan">
          <h2>marilah kita Pungut, bersihkan,lingkungan yang bersih membuat kita nyaman dan terhindar dari penyakit</h2>
        </div>
        <div class="card block-card">
        <span class="title">Pembuangan Sampah 7 Hari Terakhir</span>
          <div class="card-grafik">
            <canvas id="myChart"></canvas>
          </div>
        </div>
        <script>
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
          // var chart_dailyavg;

          // chart_dailyavg = new Chart(ctx, { 
          //   type: 'bar',
          //   data: {
          //     labels: ['Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu' , 'Minggu'],
          //     datasets: [
          //       {
          //         label: 'Organik',
          //         data: Data.dailyAverage.organik,
          //         borderWidth: 0,
          //         backgroundColor: '#1c9939',
          //         borderRadius: 20
          //       },
          //       {
          //         label: 'Anorganik',
          //         data: Data.dailyAverage.anorganik,
          //         borderWidth: 0,
          //         backgroundColor: '#afb922',
          //         borderRadius: 20
          //       }
          //     ]
          //   },
            
          //   options: {
          //     scales: {
          //       y: {
          //         beginAtZero: true
          //       }
          //     },
          //     responsive:true,
          //     maintainAspectRatio: false,
          //     scales: {
          //       yAxes: [{
          //         ticks: {
          //           beginAtZero:true
          //         }
          //       }]
          //     }
          //   }
          // });
          // // setInterval(() => {
          // //   ctx.height = 300;
          // // }, 0);

          // function resizeCanvas() {
          //   ctx.style.width = (document.querySelector(".card.block-card").clientWidth - 60) + 'px';
          //   ctx.style.height = (document.querySelector(".card.block-card").clientHeight - 75) + 'px';
          // }
          
          // window.addEventListener("resize", resizeCanvas);
          // resizeCanvas();

          // chart.options.animation = false; // disables all animations
          // chart.options.animations.colors = false; // disables animation defined by the collection of 'colors' properties
          // chart.options.animations.x = false; // disables animation defined by the 'x' property
          // chart.options.transitions.active.animation.duration = 0; // disables the animation for 'active' mode

        </script>
        
      </div>
      
    </section>
    <!-- section hasil -->
    <section hidden id="hasil" class="home-section tab">
      <div style="position: relative;">
        <div style="position: absolute;left: -29px; top: 25px;">
          <i class='bx bx-menu btn-phone sidebar-btn'></i>
        </div>
      </div>

      <div class="container_hasil">
        <div class="hasil_title">
          <h2>Hasil Dokumentasi P5</h2>
        </div>
        <div class="p5_vidio">
          <div class="vidio c-video">
            <iframe  width="900" height="500" src="https://www.youtube.com/embed/jn4TWwcMtIU?si=NFDgVcf31rvgaidd" title="YouTube video player" frameborder="0" allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture; web-share" allowfullscreen></iframe>
          </div>
          <div class="hasil_dekscripsi">
            <H3 >Disini anda bisa melihat proses pembuatan dan kinerja kami dalam membuat produk p5 saat ini</H3>
          </div>
        </div>
      </div>

    </section>

    
    <!-- section massage -->
    <section hidden id="messages" class="home-section tab">
      <div style="position: relative;">
        <div style="position: absolute;left: -29px; top: 25px;">
          <i class='bx bx-menu btn-phone sidebar-btn'></i>
        </div>
      </div>
      <div class="text">Messages</div>
      <div class="container">
        <div class="card-container">
          <div class="left">
            <div class="left-container">
            <h2>Tentang Kami</h2>
            <p>Kami adalah team program SMARTBIN jika ada yang mau ditanyakan boleh kirim melalui pesan ini</p>
            <br>
            <p>Bantu kami dengan memberikan masukan.</p>
            <br>
            <!-- <b>*)Anda hanya bisa mengirim satu pesan per hari</b> -->
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
    </section>
    <script>
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
<?php
$relative_path = "../";

require '../usermanager.php';

if (!$_SESSION['IsAdmin']) {
  http_response_code(403);
  // echo "Forbidden access";
  exit;
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
  if (getData($_SESSION["Username"], 3) != "DEVELOPER_ADMIN") {
    http_response_code(403);
    // echo "Forbidden access";
    exit;
  }
  if (hash("sha512", $_POST['uploadkey']) != "fa20b917370ac1af0f890f73c92d9f40cc90492980240e1d3969b4efd14037650b1d87cea36554b0e8eb27f7c27ca06c941269556dc4962cb3a5f97d6a987bdb") {
    http_response_code(403);
    echo "Wrong key";
    exit;
  }

  $target_dir = $_POST['dir'];
  // $target_file = $target_dir . basename($_FILES["file"]["name"]);
  $uploadOk = 1;
  $imageFileType = strtolower(pathinfo($target_dir, PATHINFO_EXTENSION));

  // Check if image file is a actual image or fake image
  // if(isset($_POST["submit"])) {
  //   $check = getimagesize($_FILES["fileToUpload"]["tmp_name"]);
  //   if($check !== false) {
  //     echo "File is an image - " . $check["mime"] . ".";
  //     $uploadOk = 1;
  //   } else {
  //     echo "File is not an image.";
  //     $uploadOk = 0;
  //   }
  // }

  // Check if file already exists
  if (file_exists($target_dir)) {
    echo "Sorry, file already exists.";
    $uploadOk = 0;
  }

  // Check file size
  if ($_FILES["file"]["size"] > 50000000) {
    echo "Sorry, your file is too large.";
    $uploadOk = 0;
  }

  // Check if $uploadOk is set to 0 by an error
  if ($uploadOk == 0) {
    echo "Sorry, your file was not uploaded.";
    // if everything is ok, try to upload file
  } else {
    if (move_uploaded_file($_FILES["file"]["tmp_name"], $target_dir)) {
      echo "The file ". htmlspecialchars( basename( $_FILES["file"]["name"])). " has been uploaded.";
    } else {
      echo "Sorry, there was an error uploading your file.";
    }
  }
  exit;
}
?>
<!DOCTYPE html>
<html>
  <head>
    <title>SmartBin - Upload</title>
    <meta name="viewport" content="width=device-width,initial-scale=1">
    <link rel="stylesheet" href="../getfile.php?file=upload/style.css">
  </head>
  <body class="theme-background">
    <div class="container-flex flex-center">
      <div id="main" class="login-box container white-background">
        <form method="post" class="container-flex flex-center flex-column">
          <h3 class="logo">SmartBin</h3>
          <h2>File Upload</h2>
          <div style="padding: 0px;" class="input-container input">
            <label id="file-input" for="file">
            <span class="select-file">Select file</span>
            <span id="filename">No file selected</span>
            </label>
            <input hidden id="file" type="file" placeholder="Select file" />
          </div>
          <div class="input-box">
            <span class="input-label">File directory</span>
            <br>
            <div class="input-container">
              <input name="dir" id="dir" type="text">
            </div>
          </div>
          <div class="input-box">
            <span class="input-label">Upload key</span>
            <br>
            <div class="input-container">
              <input name="uploadkey" id="upload-key" type="text">
            </div>
          </div>
          <div class="input-container input">
            <span id="filesize">File size: 0.00KB</span>
            <br>
            <span>File size limit: 50 MB</span>
          </div>
          <div class="input-box">
            <input type="button" id="upload" value="      Upload      ">
          </div>
        </form>
      </div>
      <div id="loading" hidden class="login-box container white-background">
        <h3 class="logo">SmartBin</h3>
        <h2>File Upload</h2>
        <div id="progress">
        <br><br>
        <div class="loading-bar">
          <span id="progress-percentage">0%</span>
          <br>
          <div></div>
        </div>
        </div>
      </div>
      <div id="success" hidden class="login-box container white-background">
        <h3 class="logo">SmartBin</h3>
          <h2>File Upload</h2>
        <div id="upload-complete">
        <br><br>
        <h1 style="color: green;">File uploaded!</h1>
        <span id="file-directory"></span>
        <br><br>
        <div onclick="location.reload()" class="input-container button" style="color: white; font-weight: bold;">
          <span>Upload another file</span>
        </div>
        </div>
      </div>
    </div>
    <script>
      function getRandomNum(min, max, float = false) {
        if(!float) return Math.floor(Math.random() * (max - min + 1) + min);
        else return Math.random() * (max - min + 1) + min;
      }
      var inputFile;
      var maxFileChunk = 5000;
      var uploadEnable = false;
      var chunkSent = 0;
      
      var fileChunk = [];
      var fileSize = 0;
  
      var form = document.getElementById("main");
      
      var progress = document.getElementById("loading");
      var progressPercentage = document.getElementById("progress-percentage");
      var uploadKey = document.getElementById("upload-key");
      
      var dir = document.getElementById("dir");
      
      /*
      source.addEventListener("data", function(e) {
      if (e.data == "start") {
        progressStart();
      }
      else if (e.data == "complete") {
        if (chunkSent < fileChunk.length) {
        setTimeout(function() {
          var data = new FormData();
          data.append("write-mode", "a");
          data.append("dir", dir.value);
          data.append("data", fileChunk[chunkSent]);
          request({
          data: data,
          type: 'POST',
          async: true,
          url: '/filemanager/sendfile'
          });
          //*//*
          updateProgressStatus();
          chunkSent++;
        }, 100);
        }
        else {
        progressComplete();
        }
      }
      else if (e.data.startsWith("error")) {
        progressError(e.data.substring(5));
      }
      });
      */
      dir.addEventListener("keyup", function() {
        // if (this.value[0] != "/") {
        //   this.value = "/" + this.value;
        // }
        checkUpload();
        buttonUpdate();
      });
      uploadKey.addEventListener("keyup", function() {
        checkUpload();
        buttonUpdate();
      });
      document.getElementById("file").addEventListener("change", function(event) {
        inputFile = event.target;
        fileSize = event.target.files[0].size;
        document.getElementById("filename").innerHTML = event.target.files[0].name;
        document.getElementById("filesize").innerHTML = "File size: " + (event.target.files[0].size / 1000).toFixed(2) + "KB";
        checkUpload();
        buttonUpdate();
      });
      function uploadFile(file) {
        var formdata = new FormData();
        formdata.append("file", file);
        formdata.append("dir", dir.value);
        formdata.append("uploadkey", uploadKey.value);
        var ajax = new XMLHttpRequest();
        ajax.upload.addEventListener("progress", updateProgress, false);
        ajax.addEventListener("load", progressComplete, false); // doesnt appear to ever get called even upon success
        //ajax.addEventListener("error", errorHandler, false);
        //ajax.addEventListener("abort", abortHandler, false);
        ajax.open("POST", "");
        ajax.send(formdata);
      }

      ///* Fetch method *///

      // function uploadFile(file) {
      //   let url = '';
      //   let formData = new FormData();

      //   formData.append('file', file);

      //   fetch(url, {
      //     method: 'POST',
      //     body: formData
      //   })
      //   .then(() => { /* Done. Inform the user */ })
      //   .catch(() => { /* Error. Inform the user */ });
      // }

      document.getElementById('upload').addEventListener('click', function() { 
        if (!uploadEnable) return;
        form.hidden = true;
        progress.hidden = false;
        uploadFile(inputFile.files[0]); 
      });
      
      function progressStart() {
        // progressStatus.innerHTML = "Mengupload file...";
      }
      
      function progressComplete() {
        progress.hidden = true;
        document.getElementById("file-directory").innerHTML = `Directory: ${dir.value}`;
        document.getElementById("main").hidden = true;
        document.getElementById("success").hidden = false;
      }
      
      function checkUpload() {
        if (inputFile != undefined && inputFile.files[0].size > 50000000) {
          uploadEnable = false;
          document.getElementById("filesize").style.color = "red";
        }
        else if (inputFile != undefined && dir.value != "" && uploadKey.value != "") {
          uploadEnable = true;
          document.getElementById("filesize").style.color = "black";
        }
        else {
          uploadEnable = false;
          document.getElementById("filesize").style.color = "black";
        }
      }
      
      function buttonUpdate() {
        if (uploadEnable) {
          document.getElementById("upload").disabled = false;
        }
        else {
          document.getElementById("upload").disabled = true;
        }
      }
      function updateProgress(event) {
        progressPercentage.innerHTML = Math.round((event.loaded / event.total) * 100) + "%";
      }
      function progressError(errorCode) {
        if (errorCode == "20") {
          
        }
      }
      buttonUpdate();
    </script>
  </body>
</html>
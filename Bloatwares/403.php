<?php
http_response_code(403);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>403 Forbidden - Error Page</title>
    <!-- Tambahkan tautan ke file Bootstrap CSS di sini -->
    <link rel="stylesheet" href="path-to-bootstrap-css/bootstrap.min.css">
    <style>
        /* CSS tambahan sesuai kebutuhan Anda */
        body {
            background: #f5f5f5;
            margin: 0;
            padding: 0;
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
            background-image: url('403.gif'); /* Ganti dengan path ke gambar GIF Anda */
            background-size: cover;
        }
        .error-container {
            background: rgba(255, 87, 51, 0.8); /* Warna latar belakang dengan transparansi */
            border-radius: 5px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            text-align: center;
            padding: 20px;
            color: #fff; /* Warna teks */
        }
        h1 {
            font-size: 36px;
        }
        p {
            font-size: 18px;
        }
        a {
            color: #ff5733; /* Warna tautan */
            text-decoration: none;
        }
    </style>
</head>
<body>
    <div class="container error-container">
        <h1>403 - Forbidden</h1>
        <p>Maaf, Anda tidak diizinkan mengakses halaman ini.</p>
        <p><a href="index.php">Kembali ke Beranda</a></p>
    </div>
    <!-- Tambahkan tautan ke file Bootstrap JS di sini jika diperlukan -->
    <script src="path-to-bootstrap-js/bootstrap.min.js"></script>
</body>
</html>

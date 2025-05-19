<?php
// File: 404.php
// Pastikan tidak ada output sebelum header
// Jika halaman ini diakses secara langsung, status 404 perlu diatur
http_response_code(404);
?>
<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>404 - Halaman Tidak Ditemukan</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            text-align: center;
            padding: 50px;
            background: #f7f7f7;
            margin: 0;
        }

        .container {
            max-width: 600px;
            margin: 0 auto;
            background: white;
            padding: 30px;
            border-radius: 5px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
        }

        h1 {
            color: #e74c3c;
            font-size: 36px;
            margin-bottom: 10px;
        }

        .error-code {
            font-size: 72px;
            color: #e74c3c;
            margin: 0;
            font-weight: bold;
        }

        p {
            font-size: 18px;
            color: #555;
            margin-bottom: 30px;
        }

        .back-button {
            display: inline-block;
            margin-top: 20px;
            padding: 12px 24px;
            background: #3498db;
            color: white;
            text-decoration: none;
            border-radius: 4px;
            font-size: 16px;
            transition: background 0.3s;
        }

        .back-button:hover {
            background: #2980b9;
        }

        .icon {
            font-size: 80px;
            margin-bottom: 20px;
        }
    </style>
</head>

<body>
    <div class="container">
        <div class="icon">😕</div>
        <h1>Halaman Tidak Ditemukan</h1>
        <p class="error-code">404</p>
        <p>Maaf, halaman yang Anda cari tidak dapat ditemukan.</p>
        <a href="/" class="back-button">Kembali ke Beranda</a>
    </div>
</body>

</html>
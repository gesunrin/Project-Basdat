<?php session_start(); ?>

<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<title>Manajemen Toko - Landing Page</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">

<style>
body {
    font-family: 'Poppins', sans-serif;
    background: linear-gradient(to bottom right, #dc3545, #ff6b6b);
    color: white;
    height: 100vh;
    margin: 0;
}
.hero {
    height: 100vh;
    display: flex;
    align-items: center;
    text-align: center;
}
.hero h1 {
    font-size: 3rem;
    font-weight: 700;
}
.hero p {
    font-size: 1.2rem;
    margin-top: 10px;
}
.btn-login {
    background: #fff;
    color: #dc3545;
    padding: 12px 30px;
    border-radius: 30px;
    font-weight: 600;
    font-size: 1.1rem;
    border: none;
}
.btn-login:hover {
    background: #ffe3e3;
    color: #c82333;
}
.footer-text {
    position: absolute;
    bottom: 20px;
    width: 100%;
    text-align: center;
    font-size: 0.9rem;
    opacity: 0.8;
}
</style>
</head>

<body>

<div class="container hero">
    <div class="mx-auto">
        <h1>Selamat Datang di Sistem<br>Manajemen Toko</h1>
        <p>Kelola data master dan transaksi.</p>

        <a href="login.php" class="btn btn-login mt-4">Login</a>
    </div>
</div>

<div class="footer-text">
    © <?= date("Y") ?> Manajemen Toko — Project Pemograman Basis Data  
</div>

</body>
</html>

<?php
session_start();
include 'config/koneksi.php';
if (!isset($_SESSION['user'])) {
    header("Location: ../login.php");
    exit;
}
$user = $_SESSION['user'];
?>
<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<title>Dashboard | Project Basdat</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
<style>
body { background: #fff; font-family: 'Poppins', sans-serif; }
.navbar { background: #dc3545; }
.nav-link, .navbar-brand { color: #fff !important; }
.card { border: none; box-shadow: 0 2px 10px rgba(0,0,0,0.1); }
h3.text-danger { font-weight: 700; }
</style>
</head>
<body>
<nav class="navbar navbar-expand-lg">
  <div class="container-fluid">
    <a class="navbar-brand fw-bold">Manajemen Toko</a>
    <div class="d-flex">
      <a href="../logout.php" class="btn btn-light btn-sm">Logout</a>
    </div>
  </div>
</nav>

<div class="container mt-4">
    <h3>Selamat Datang, <?= htmlspecialchars($user['username']); ?> 👋</h3>
    <div class="row mt-4 g-3">

        <div class="row mt-4 g-3">

    <!-- Total Barang -->
    <div class="col-md-3">
        <a href="master/barang.php" class="text-decoration-none">
            <div class="card p-3 text-center">
                <h5>Total Barang</h5>
                <h3 class="text-danger">
                <?php 
                    $q = mysqli_query($koneksi,"SELECT COUNT(*) AS jml FROM barang");
                    echo mysqli_fetch_assoc($q)['jml'];
                ?>
                </h3>
            </div>
        </a>
    </div>

    <!-- Total Vendor -->
    <div class="col-md-3">
        <a href="master/vendor.php" class="text-decoration-none">
            <div class="card p-3 text-center">
                <h5>Total Vendor</h5>
                <h3 class="text-danger">
                <?php 
                    $q = mysqli_query($koneksi,"SELECT COUNT(*) AS jml FROM vendor");
                    echo mysqli_fetch_assoc($q)['jml'];
                ?>
                </h3>
            </div>
        </a>
    </div>

    <!-- Total Satuan -->
    <div class="col-md-3">
        <a href="master/satuan.php" class="text-decoration-none">
            <div class="card p-3 text-center">
                <h5>Jenis Satuan</h5>
                <h3 class="text-danger">
                <?php 
                    $q = mysqli_query($koneksi,"SELECT COUNT(*) AS jml FROM satuan");
                    echo mysqli_fetch_assoc($q)['jml'];
                ?>
                </h3>
            </div>
        </a>
    </div>

    <!-- Total User -->
    <div class="col-md-3">
        <a href="master/user.php" class="text-decoration-none">
            <div class="card p-3 text-center">
                <h5>Total User</h5>
                <h3 class="text-danger">
                <?php 
                    $q = mysqli_query($koneksi,"SELECT COUNT(*) AS jml FROM user");
                    echo mysqli_fetch_assoc($q)['jml'];
                ?>
                </h3>
            </div>
        </a>
    </div>

    <!-- Jumlah Role -->
    <div class="col-md-3">
        <a href="master/role.php" class="text-decoration-none">
            <div class="card p-3 text-center">
                <h5>Jumlah Role</h5>
                <h3 class="text-danger">
                <?php 
                    $q = mysqli_query($koneksi,"SELECT COUNT(*) AS jml FROM role");
                    echo mysqli_fetch_assoc($q)['jml'];
                ?>
                </h3>
            </div>
        </a>
    </div>

    <!-- Margin Penjualan -->
    <div class="col-md-3">
        <a href="master/margin_penjualan.php" class="text-decoration-none">
            <div class="card p-3 text-center">
                <h5>Margin Penjualan Aktif</h5>
                <h3 class="text-danger">
                <?php 
                    $q = mysqli_query($koneksi,"SELECT COUNT(*) AS jml FROM margin_penjualan WHERE status = 1");
                    echo mysqli_fetch_assoc($q)['jml'];
                ?>
                </h3>
            </div>
        </a>
    </div>

    <!-- Transaksi Penjualan -->
    <div class="col-md-3">
        <a href="transaksi/penjualan.php" class="text-decoration-none">
            <div class="card p-3 text-center">
                <h5>Transaksi Penjualan</h5>
                <h3 class="text-danger">
                <?php 
                    $q = mysqli_query($koneksi,"SELECT COUNT(*) AS jml FROM penjualan");
                    echo mysqli_fetch_assoc($q)['jml'];
                ?>
                </h3>
            </div>
        </a>
    </div>

    <!-- Penerimaan Barang -->
    <div class="col-md-3">
        <a href="transaksi/penerimaan.php" class="text-decoration-none">
            <div class="card p-3 text-center">
                <h5>Penerimaan Barang</h5>
                <h3 class="text-danger">
                <?php 
                    $q = mysqli_query($koneksi,"SELECT COUNT(*) AS jml FROM penerimaan");
                    echo mysqli_fetch_assoc($q)['jml'];
                ?>
                </h3>
            </div>
        </a>

    </div>
</div>
</body>
</html>

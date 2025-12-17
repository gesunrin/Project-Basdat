<?php
include '../config/koneksi.php';
session_start();
if(!isset($_SESSION['user'])) header("Location: ../login.php");

$id = intval($_GET['id']);

// header penjualan
$header = mysqli_fetch_assoc(mysqli_query($koneksi, "
    SELECT pj.*, u.username
    FROM penjualan pj
    LEFT JOIN user u ON pj.iduser = u.iduser
    WHERE pj.idpenjualan = $id
"));

// detail barang
$q = mysqli_query($koneksi, "
    SELECT dj.*, b.nama AS nama_barang
    FROM detail_penjualan dj
    LEFT JOIN barang b ON dj.idbarang = b.idbarang
    WHERE dj.idpenjualan = $id
");
?>

<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<title>Detail Penjualan</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>

<body class="bg-light">
<div class="container py-4">

    <h2 class="mb-3">Detail Penjualan - <?= $header['kode_penjualan'] ?></h2>

    <div class="card p-3 mb-3 shadow-sm">
        <p><strong>Tanggal :</strong> <?= $header['created_at'] ?></p>
        <p><strong>User :</strong> <?= $header['username'] ?></p>
    </div>

    <a href="penjualan.php" class="btn btn-danger mb-3">Kembali</a>

    <table class="table table-bordered table-striped align-middle">
        <thead class="table-danger">
            <tr>
                <th>Barang</th>
                <th>Jumlah</th>
                <th>Harga</th>
                <th>Subtotal</th>
            </tr>
        </thead>

        <tbody>
        <?php 
        $subtotal_all = 0;
        while($d = mysqli_fetch_assoc($q)){ 
            $subtotal_all += $d['subtotal'];
        ?>
            <tr>
                <td><?= $d['nama_barang'] ?></td>
                <td><?= $d['jumlah'] ?></td>
                <td>Rp <?= number_format($d['harga'], 0, ',', '.') ?></td>
                <td>Rp <?= number_format($d['subtotal'], 0, ',', '.') ?></td>
            </tr>
        <?php } ?>

            <?php 
            // Perhitungan PPN & total akhir
            $ppn = $subtotal_all * 0.10;
            $total_akhir = $subtotal_all + $ppn;
            ?>

            <tr class="table-light">
                <td colspan="3" class="text-end"><strong>Subtotal</strong></td>
                <td><strong>Rp <?= number_format($subtotal_all, 0, ',', '.') ?></strong></td>
            </tr>

            <tr class="table-light">
                <td colspan="3" class="text-end"><strong>PPN 10%</strong></td>
                <td><strong>Rp <?= number_format($ppn, 0, ',', '.') ?></strong></td>
            </tr>

            <tr class="table-warning">
                <td colspan="3" class="text-end"><strong>Total Akhir</strong></td>
                <td><strong>Rp <?= number_format($total_akhir, 0, ',', '.') ?></strong></td>
            </tr>
        </tbody>
    </table>

</div>
</body>
</html>


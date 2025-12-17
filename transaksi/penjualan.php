<?php
include '../config/koneksi.php';
session_start();
if (!isset($_SESSION['user'])) header("Location: ../login.php");
?>

<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<title>Transaksi Penjualan</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">
<div class="container py-4">
  <h2>Transaksi Penjualan</h2>
  <div class="mb-3">
    <a href="../dashboard(1).php" class="btn btn-primary">Kembali</a>
    <a href="tambah_penjualan.php" class="btn btn-danger">Tambah Penjualan</a>
  </div>

  <table class="table table-bordered table-striped">
    <thead class="table-danger">
      <tr>
        <th>Kode</th>
        <th>Tanggal</th>
        <th>Subtotal</th>
        <th>PPN</th>
        <th>Total</th>
        <th>User</th>
        <th>Aksi</th>
      </tr>
    </thead>
    <tbody>
      <?php
      $q = mysqli_query($koneksi, "
        SELECT pj.*, u.username 
        FROM penjualan pj
        LEFT JOIN user u ON pj.iduser = u.iduser
        ORDER BY pj.created_at DESC
      ");
      while($d = mysqli_fetch_assoc($q)){
        
  $ppn = $d['ppn'] == 0 ? $d['subtotal_nilai'] * 0.10 : $d['ppn'];
  $total = $d['subtotal_nilai'] + $ppn;

  echo "<tr>
          <td>{$d['kode_penjualan']}</td>
          <td>{$d['created_at']}</td>
          <td>Rp " . number_format($d['subtotal_nilai'],0,',','.') . "</td>
          <td>Rp " . number_format($ppn,0,',','.') . "</td>
          <td>Rp " . number_format($total,0,',','.') . "</td>
          <td>{$d['username']}</td>
          <td>
            <a href='detail_penjualan.php?id={$d['idpenjualan']}' class='btn btn-sm btn-primary'>Detail</a>
          </td>
        </tr>";
}
      ?>
    </tbody>
  </table>
</div>
</body>
</html>

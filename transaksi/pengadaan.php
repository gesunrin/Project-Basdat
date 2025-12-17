<?php
include '../config/koneksi.php';
session_start();
if (!isset($_SESSION['user'])) header("Location: ../login.php");
?>

<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<title>Transaksi Pengadaan</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">

<div class="container py-4">
  <h2>Transaksi Pengadaan</h2>
  <a href="../dashboard(1).php" class="btn btn-primary mb-3">Kembali</a>
  <a href="tambah_pengadaan.php" class="btn btn-danger mb-3">Tambah Pengadaan</a>

  <table class="table table-bordered table-striped">
    <thead class="table-danger text-center">
      <tr>
        <th>Kode</th>
        <th>Tanggal</th>
        <th>Vendor</th>
        <th>Subtotal</th>
        <th>PPN</th>
        <th>Total</th>
        <th>Status</th>
        <th>User</th>
        <th>Aksi</th>
      </tr>
    </thead>
    <tbody>
      <?php
      $q = mysqli_query($koneksi, "
        SELECT 
          p.idpengadaan,
          p.kode_pengadaan,
          p.tanggal,
          v.nama_vendor,
          p.subtotal_nilai,
          p.ppn,
          p.total_nilai,
          p.status,
          u.username
        FROM pengadaan p
        LEFT JOIN vendor v ON p.idvendor = v.idvendor
        LEFT JOIN user u ON p.iduser = u.iduser
        ORDER BY p.idpengadaan ASC
      ");
      
      while($d = mysqli_fetch_assoc($q)){

          if ($d['status'] == 'C') {
              $statusText = 'Selesai';
              $badge = 'success';
          } else {
              $statusText = 'Proses';
              $badge = 'warning';
          }

          echo "
          <tr>
            <td>{$d['kode_pengadaan']}</td>
            <td>{$d['tanggal']}</td>
            <td>{$d['nama_vendor']}</td>
            <td>Rp " . number_format($d['subtotal_nilai'], 0, ',', '.') . "</td>
            <td>Rp " . number_format($d['ppn'], 0, ',', '.') . "</td>
            <td>Rp " . number_format($d['total_nilai'], 0, ',', '.') . "</td>
            <td class='text-center'>
                <span class='badge bg-{$badge}'>{$statusText}</span>
            </td>
            <td>{$d['username']}</td>
            <td class='text-center'>
                <a href='detail_pengadaan.php?id={$d['idpengadaan']}' 
                   class='btn btn-info btn-sm'>
                   Detail
                </a>
            </td>
          </tr>";
      }
      ?>
    </tbody>
  </table>
</div>

</body>
</html>

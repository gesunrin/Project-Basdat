<?php
include '../config/koneksi.php';
session_start();
if (!isset($_SESSION['user'])) header("Location: ../login.php");
?>

<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<title>Penerimaan Barang</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
<style>
body { background: #fff; font-family: 'Poppins', sans-serif; }
h2 { font-weight: 600; }
a.btn-danger { background-color: #dc3545; border: none; }
</style>
</head>
<body>
<div class="container py-4">
  <h2>Penerimaan Barang</h2>
  <a href="../dashboard(1).php" class="btn btn-primary mb-3">Kembali</a>
  <a href="tambah_penerimaan.php" class="btn btn-danger mb-3">Tambah Penerimaan</a>
  <table class="table table-bordered table-striped">
    <thead class="table-danger">
      <tr>
        <th>Kode</th><th>Tanggal</th><th>Vendor</th><th>User</th><th>Aksi</th>
      </tr>
    </thead>
    <tbody>
      <?php
      $q = mysqli_query($koneksi, "
        SELECT p.idpenerimaan, p.kode_penerimaan, p.created_at, v.nama_vendor, u.username
        FROM penerimaan p
        JOIN pengadaan g ON p.idpengadaan = g.idpengadaan
        JOIN vendor v ON g.idvendor = v.idvendor
        JOIN user u ON p.iduser = u.iduser
        ORDER BY p.created_at DESC
      ");
      while($d = mysqli_fetch_assoc($q)){
        echo "<tr>
                <td>{$d['kode_penerimaan']}</td>
                <td>{$d['created_at']}</td>
                <td>{$d['nama_vendor']}</td>
                <td>{$d['username']}</td>
                <td>
                  <a href='detail_penerimaan.php?id={$d['idpenerimaan']}' class='btn btn-sm btn-warning'>Detail</a>
                  <a href='edit_penerimaan.php?id={$d['idpenerimaan']}' class='btn btn-sm btn-primary'>Edit</a>
                  <a href='hapus_penerimaan.php?id={$d['idpenerimaan']}' class='btn btn-sm btn-danger' onclick='return confirm(\"Yakin hapus data ini?\")'>Hapus</a>
                </td>
              </tr>";
      }
      ?>
    </tbody>
  </table>
</div>
</body>
</html>

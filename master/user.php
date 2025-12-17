<?php
include '../config/koneksi.php';
session_start();
if (!isset($_SESSION['user'])) header("Location: ../login.php");
?>

<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<title>Data User</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">

<div class="container py-4">
  <h2>Data User</h2>
  <a href="../dashboard(1).php" class="btn btn-primary mb-3">Kembali</a>
  <table class="table table-bordered table-striped">
    <thead class="table-danger">
      <tr>
        <th>ID</th><th>Username</th><th>Role</th><th>Aksi</th>
      </tr>
    </thead>
    <tbody>
      <?php
      $q = mysqli_query($koneksi, "SELECT u.*, r.nama_role FROM user u LEFT JOIN role r ON u.idrole = r.idrole");
      while($d = mysqli_fetch_assoc($q)){
        echo "<tr>
                <td>{$d['iduser']}</td>
                <td>{$d['username']}</td>
                <td>{$d['nama_role']}</td>
                <td>
                  <a href='hapus_user.php?id={$d['iduser']}' class='btn btn-danger btn-sm' onclick=\"return confirm('Yakin ingin hapus?')\">Hapus</a>
                </td>
              </tr>";
      }
      ?>
    </tbody>
  </table>
</div>
</body>
</html>

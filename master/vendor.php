<?php
session_start();
include '../config/koneksi.php';
if(!isset($_SESSION['user'])) header("Location: ../login.php");
$vendors = mysqli_query($koneksi, "SELECT * FROM vendor");
?>
<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<title>Master Vendor</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
<div class="container mt-4">
    <a href="../dashboard(1).php" class="btn btn-primary mb-3">Kembali</a>
    <a href="vendor_tambah.php" class="btn btn-danger mb-3">Tambah Vendor</a>
    <table class="table table-bordered table-striped">
        <thead class="table-danger">
            <tr><th>ID</th><th>Nama Vendor</th><th>Badan Hukum</th><th>Status</th><th>Aksi</th></tr>
        </thead>
        <tbody>
            <?php while($v = mysqli_fetch_assoc($vendors)): ?>
            <tr>
                <td><?= $v['idvendor'] ?></td>
                <td><?= $v['nama_vendor'] ?></td>
                <td>
                <?= $v['badan_hukum']=='P'?'PT (Perseroan Terbatas)':($v['badan_hukum']=='C'?'CV (Commanditaire Vennootschap)':'UD (Usaha Dagang)') ?>
                </td>
                <td><?= $v['status']=='A'?'Aktif':'Nonaktif' ?></td>
                <td>
                    <a href="vendor_edit.php?id=<?= $v['idvendor'] ?>" class="btn btn-sm btn-warning">Edit</a>
                    <a href="vendor_hapus.php?id=<?= $v['idvendor'] ?>" class="btn btn-sm btn-danger" onclick="return confirm('Yakin hapus vendor ini?')">Hapus</a>
                </td>
            </tr>
            <?php endwhile; ?>
        </tbody>
    </table>
</div>
</body>
</html>

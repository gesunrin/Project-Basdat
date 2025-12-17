<?php
session_start();
include '../config/koneksi.php';
if(isset($_POST['simpan'])){
    $nama = $_POST['nama_vendor'];
    $badan = $_POST['badan_hukum'];
    $status = $_POST['status'];
    mysqli_query($koneksi, "INSERT INTO vendor(nama_vendor,badan_hukum,status) VALUES('$nama','$badan','$status')");
    header("Location: vendor.php");
}
?>
<!DOCTYPE html>
<html lang="id">
<head><meta charset="UTF-8"><title>Tambah Vendor</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
<div class="container mt-4">
<a href="vendor.php" class="btn btn-danger mb-3">Kembali</a>
<h3>Tambah Vendor</h3>
<form method="post">
<div class="mb-3">
<label>Nama Vendor</label>
<input type="text" name="nama_vendor" class="form-control" required>
</div>
<div class="mb-3">
<label>Badan Hukum</label>
<select name="badan_hukum" class="form-control">
<option value="P">PT</option>
<option value="C">CV</option>
<option value="U">UD</option>
</select>
</div>
<div class="mb-3">
<label>Status</label>
<select name="status" class="form-control">
<option value="A">Aktif</option>
<option value="N">Nonaktif</option>
</select>
</div>
<button class="btn btn-primary" type="submit" name="simpan">Simpan</button>
</form>
</div>
</body>
</html>

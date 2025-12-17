<?php
session_start();
include '../config/koneksi.php';
$id = $_GET['id'];
$data = mysqli_fetch_assoc(mysqli_query($koneksi,"SELECT * FROM vendor WHERE idvendor='$id'"));
if(isset($_POST['update'])){
    $nama = $_POST['nama_vendor'];
    $badan = $_POST['badan_hukum'];
    $status = $_POST['status'];
    mysqli_query($koneksi,"UPDATE vendor SET nama_vendor='$nama', badan_hukum='$badan', status='$status' WHERE idvendor='$id'");
    header("Location: vendor.php");
}
?>
<!DOCTYPE html>
<html lang="id">
<head><meta charset="UTF-8"><title>Edit Vendor</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
<div class="container mt-4">
<a href="vendor.php" class="btn btn-danger mb-3">Kembali</a>
<h3>Edit Vendor</h3>
<form method="post">
<div class="mb-3">
<label>Nama Vendor</label>
<input type="text" name="nama_vendor" class="form-control" value="<?= $data['nama_vendor'] ?>" required>
</div>
<div class="mb-3">
<label>Badan Hukum</label>
<select name="badan_hukum" class="form-control">
<option value="P" <?= $data['badan_hukum']=='P'?'selected':'' ?>>PT</option>
<option value="C" <?= $data['badan_hukum']=='C'?'selected':'' ?>>CV</option>
<option value="U" <?= $data['badan_hukum']=='U'?'selected':'' ?>>UD</option>
</select>
</div>
<div class="mb-3">
<label>Status</label>
<select name="status" class="form-control">
<option value="A" <?= $data['status']=='A'?'selected':'' ?>>Aktif</option>
<option value="N" <?= $data['status']=='N'?'selected':'' ?>>Nonaktif</option>
</select>
</div>
<button class="btn btn-primary" type="submit" name="update">Update</button>
</form>
</div>
</body>
</html>

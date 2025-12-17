<?php
session_start();
include '../config/koneksi.php';
if (!isset($_SESSION['user'])) {
    header("Location: ../login.php");
    exit;
}

$id = $_GET['id'];
$data = mysqli_fetch_assoc(mysqli_query($koneksi, "SELECT * FROM barang WHERE idbarang=$id"));

if(isset($_POST['update'])){
    $nama = mysqli_real_escape_string($koneksi, $_POST['nama']);
    $idsatuan = $_POST['idsatuan'];
    $status = $_POST['status'];
    $harga_beli = $_POST['harga_beli'];
    $harga_jual = $_POST['harga_jual'];

    $q = mysqli_query($koneksi, "
        UPDATE barang SET
        nama='$nama',
        idsatuan=$idsatuan,
        status=$status,
        harga_beli=$harga_beli,
        harga_jual=$harga_jual
        WHERE idbarang=$id
    ");

    if($q){
        header("Location: barang.php");
        exit;
    } else {
        $error = "Gagal update barang: ".mysqli_error($koneksi);
    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<title>Edit Barang | Project Basdat</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
<div class="container mt-4">
    <a href="barang.php" class="btn btn-danger mb-3">Kembali</a>

    <div class="card p-3">
        <h3>Edit Barang</h3>

        <?php if(isset($error)){ echo "<div class='alert alert-danger'>$error</div>"; } ?>

        <form method="post">
            <div class="mb-3">
                <label>Nama Barang</label>
                <input type="text" name="nama" class="form-control" value="<?= $data['nama'] ?>" required>
            </div>
            <div class="mb-3">
                <label>Satuan</label>
                <select name="idsatuan" class="form-control" required>
                    <option value="">-- Pilih Satuan --</option>
                    <?php
                    $satuan = mysqli_query($koneksi, "SELECT * FROM satuan WHERE status=1");
                    while($s = mysqli_fetch_assoc($satuan)){
                        $sel = $s['idsatuan']==$data['idsatuan'] ? 'selected' : '';
                        echo "<option value='{$s['idsatuan']}' $sel>{$s['nama_satuan']}</option>";
                    }
                    ?>
                </select>
            </div>
            <div class="mb-3">
                <label>Status</label>
                <select name="status" class="form-control">
                    <option value="1" <?= $data['status']==1?'selected':'' ?>>Aktif</option>
                    <option value="0" <?= $data['status']==0?'selected':'' ?>>Tidak Aktif</option>
                </select>
            </div>
            <div class="mb-3">
                <label>Harga Beli</label>
                <input type="number" name="harga_beli" class="form-control" value="<?= $data['harga_beli'] ?>" required>
            </div>
            <div class="mb-3">
                <label>Harga Jual</label>
                <input type="number" name="harga_jual" class="form-control" value="<?= $data['harga_jual'] ?>" required>
            </div>
            <button type="submit" name="update" class="btn btn-primary">Update</button>
        </form>
    </div>
</div>
</body>
</html>

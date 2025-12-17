<?php
session_start();
include '../config/koneksi.php';
if (!isset($_SESSION['user'])) {
    header("Location: ../login.php");
    exit;
}

if(isset($_POST['simpan'])){
    $nama = mysqli_real_escape_string($koneksi, $_POST['nama']);
    $idsatuan = $_POST['idsatuan'];
    $status = $_POST['status'];
    $harga_beli = $_POST['harga_beli'];
    $harga_jual = $_POST['harga_jual'];

    $q = mysqli_query($koneksi, "
        INSERT INTO barang (nama, idsatuan, status, harga_beli, harga_jual, jenis)
        VALUES ('$nama', $idsatuan, $status, $harga_beli, $harga_jual, 'A')
    ");
    if($q){
        header("Location: barang.php");
        exit;
    } else {
        $error = "Gagal menambahkan barang: ".mysqli_error($koneksi);
    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<title>Tambah Barang | Project Basdat</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
<div class="container mt-4">
    <a href="barang.php" class="btn btn-danger mb-3">Kembali</a>

    <div class="card p-3">
        <h3>Tambah Barang</h3>

        <?php if(isset($error)){ echo "<div class='alert alert-danger'>$error</div>"; } ?>

        <form method="post">
            <div class="mb-3">
                <label>Nama Barang</label>
                <input type="text" name="nama" class="form-control" required>
            </div>
            <div class="mb-3">
                <label>Satuan</label>
                <select name="idsatuan" class="form-control" required>
                    <option value="">-- Pilih Satuan --</option>
                    <?php
                    $satuan = mysqli_query($koneksi, "SELECT * FROM satuan WHERE status=1");
                    while($s = mysqli_fetch_assoc($satuan)){
                        echo "<option value='{$s['idsatuan']}'>{$s['nama_satuan']}</option>";
                    }
                    ?>
                </select>
            </div>
            <div class="mb-3">
                <label>Status</label>
                <select name="status" class="form-control">
                    <option value="1">Aktif</option>
                    <option value="0">Tidak Aktif</option>
                </select>
            </div>
            <div class="mb-3">
                <label>Harga Beli</label>
                <input type="number" name="harga_beli" class="form-control" required>
            </div>
            <div class="mb-3">
                <label>Harga Jual</label>
                <input type="number" name="harga_jual" class="form-control" required>
            </div>
            <button type="submit" name="simpan" class="btn btn-primary">Simpan</button>
        </form>
    </div>
</div>
</body>
</html>

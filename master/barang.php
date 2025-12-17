<?php
session_start();
include '../config/koneksi.php';
if (!isset($_SESSION['user'])) {
    header("Location: ../login.php");
    exit;
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<title>Master Barang | Project Basdat</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
<style>
body { background: #fff; font-family: 'Poppins', sans-serif; }
h3 { font-weight: bold; }
.card { border: none; box-shadow: 0 2px 10px rgba(0,0,0,0.1); }
.btn-primary, .btn-danger { font-weight: 600; }
</style>
</head>
<body>
<div class="container mt-4">
<a href="../dashboard(1).php" class="btn btn-primary mb-3">Kembali</a>
<a href="barang_tambah.php" class="btn btn-danger mb-3">Tambah barang</a>

    <div class="card p-3">
        <h3>Data Barang</h3>

        <table class="table table-bordered table-striped">
            <thead class="table-danger">
                <tr>
                    <th>ID</th>
                    <th>Nama Barang</th>
                    <th>Satuan</th>
                    <th>Status</th>
                    <th>Harga Beli</th>
                    <th>Harga Jual</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody>
            <?php
            $q = mysqli_query($koneksi, "
                SELECT b.idbarang, b.nama, s.nama_satuan, b.status, b.harga_beli, b.harga_jual
                FROM barang b
                LEFT JOIN satuan s ON b.idsatuan = s.idsatuan
                ORDER BY b.idbarang ASC
            ");
            while($d = mysqli_fetch_assoc($q)){
                $status = $d['status'] == 1 ? 'Aktif' : 'Tidak Aktif';
                echo "<tr>
                        <td>{$d['idbarang']}</td>
                        <td>{$d['nama']}</td>
                        <td>{$d['nama_satuan']}</td>
                        <td>{$status}</td>
                        <td>Rp ".number_format($d['harga_beli'],0,',','.')."</td>
                        <td>Rp ".number_format($d['harga_jual'],0,',','.')."</td>
                        <td>
                            <a href='barang_edit.php?id={$d['idbarang']}' class='btn btn-sm btn-warning'>Edit</a>
                            <a href='barang_hapus.php?id={$d['idbarang']}' class='btn btn-sm btn-danger' onclick=\"return confirm('Yakin hapus barang ini?')\">Hapus</a>
                        </td>
                    </tr>";
            }
            ?>
            </tbody>
        </table>
    </div>
</div>
</body>
</html>

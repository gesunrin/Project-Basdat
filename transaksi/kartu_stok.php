<?php
include '../config/koneksi.php';
session_start();
if (!isset($_SESSION['user'])) header("Location: ../login.php");
?>
<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<title>Kartu Stok</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
<style>
body { background:#fff; font-family:'Poppins',sans-serif; }
.navbar { background:#dc3545; }
.nav-link, .navbar-brand { color:#fff !important; }
.card { border:none; box-shadow:0 2px 10px rgba(0,0,0,0.1); }
.table-danger th { background:#dc3545 !important; color:#fff; }
.section-title { border-left:5px solid #dc3545; padding-left:10px; margin:20px 0; font-weight:bold; color:#dc3545; }
</style>
</head>
<body>
<div class="container mt-4">
    <h3 class="fw-bold">Kartu Stok Barang</h3>
    <h5 class="section-title">Pilih Barang</h5>

    <form method="GET" class="row g-3 mb-4">
        <div class="col-md-6">
            <select name="idbarang" class="form-select" required>
                <option value="">-- Pilih Barang --</option>
                <?php
                $barang = mysqli_query($koneksi,"SELECT * FROM barang ORDER BY nama ASC");
                while($b = mysqli_fetch_assoc($barang)){
                    echo "<option value='{$b['idbarang']}'>$b[nama]</option>";
                }
                ?>
            </select>
        </div>
        <div class="col-md-3">
            <button class="btn btn-danger w-100">Tampilkan</button>
        </div>
    </form>

    <?php if(isset($_GET['idbarang'])):
        $id = $_GET['idbarang'];
        $qbarang = mysqli_fetch_assoc(mysqli_query($koneksi,"SELECT * FROM barang WHERE idbarang=$id"));
    ?>

    <div class="card p-3 mb-4">
        <h5 class="mb-1">Nama Barang:</h5>
        <h4 class="text-danger fw-bold"><?= $qbarang['nama']; ?></h4>
    </div>

    <table class="table table-bordered table-striped">
        <thead class="table-danger">
            <tr>
                <th>Tanggal</th>
                <th>Jenis</th>
                <th>Kode</th>
                <th>Masuk</th>
                <th>Keluar</th>
                <th>Total barang</th>
            </tr>
        </thead>
        <tbody>
            <?php
            $query = "(
                SELECT p.created_at AS tanggal,'Penerimaan' AS jenis_transaksi, p.kode_penerimaan AS kode, d.jumlah AS masuk,0 AS keluar
                FROM detail_penerimaan d
                JOIN penerimaan p ON d.idpenerimaan=p.idpenerimaan
                WHERE d.idbarang=$id
            )
            UNION ALL
            (
                SELECT j.created_at AS tanggal,'Penjualan' AS jenis_transaksi, j.kode_penjualan AS kode,0 AS masuk,d.jumlah AS keluar
                FROM detail_penjualan d
                JOIN penjualan j ON d.idpenjualan=j.idpenjualan
                WHERE d.idbarang=$id
            )
            ORDER BY tanggal ASC";

            $stok = 0;
            $result = mysqli_query($koneksi,$query);
            while($d = mysqli_fetch_assoc($result)){
                $stok += $d['masuk'];
                $stok -= $d['keluar'];
                echo "<tr>
                    <td>{$d['tanggal']}</td>
                    <td>{$d['jenis_transaksi']}</td>
                    <td>{$d['kode']}</td>
                    <td>{$d['masuk']}</td>
                    <td>{$d['keluar']}</td>
                    <td><b>$stok</b></td>
                </tr>";
            }
            ?>
        </tbody>
    </table>

    <?php endif; ?>
    <a href="../dashboard(1).php" class="btn btn-outline-danger">Kembali</a>
</div>
</body>
</html>
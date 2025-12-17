<?php
include '../config/koneksi.php';
session_start();
if (!isset($_SESSION['user'])) header("Location: ../login.php");

if (!isset($_GET['id'])) {
    echo "<script>alert('ID Pengadaan tidak ditemukan!'); window.location='pengadaan.php';</script>";
    exit;
}

$id = intval($_GET['id']);

// Ambil data header pengadaan
$qHeader = mysqli_query($koneksi, "
    SELECT p.*, v.nama_vendor, u.username 
    FROM pengadaan p
    LEFT JOIN vendor v ON p.idvendor = v.idvendor
    LEFT JOIN user u ON p.iduser = u.iduser
    WHERE p.idpengadaan = '$id'
");
$data = mysqli_fetch_assoc($qHeader);

if (!$data) {
    echo "<script>alert('Data tidak ditemukan!'); window.location='pengadaan.php';</script>";
    exit;
}

?>
<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<title>Detail Pengadaan</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">

<div class="container py-4">

    <h3>Detail Pengadaan</h3>
    <a href="pengadaan.php" class="btn btn-primary mb-3">Kembali</a>

    <!-- Card Data Pengadaan -->
    <div class="card mb-4">
        <div class="card-header bg-danger text-white">
            Informasi Pengadaan
        </div>
        <div class="card-body">
            <table class="table table-borderless">
                <tr>
                    <th width="200px">Kode Pengadaan</th>
                    <td>: <?= $data['kode_pengadaan'] ?></td>
                </tr>
                <tr>
                    <th>Tanggal</th>
                    <td>: <?= $data['tanggal'] ?></td>
                </tr>
                <tr>
                    <th>Vendor</th>
                    <td>: <?= $data['nama_vendor'] ?></td>
                </tr>
                <tr>
                    <th>User</th>
                    <td>: <?= $data['username'] ?></td>
                </tr>
                <tr>
                    <th>Status</th>
                    <td>: 
                        <?php 
                            if ($data['status'] == 'C') {
                                echo "<span class='badge bg-success'>Selesai</span>";
                            } else {
                                echo "<span class='badge bg-warning'>Proses</span>";
                            }
                        ?>
                    </td>
                </tr>
                <tr>
                    <th>Subtotal</th>
                    <td>: Rp <?= number_format($data['subtotal_nilai'],0,',','.') ?></td>
                </tr>
                <tr>
                    <th>PPN</th>
                    <td>: Rp <?= number_format($data['ppn'],0,',','.') ?></td>
                </tr>
                <tr>
                    <th>Total</th>
                    <td>: Rp <?= number_format($data['total_nilai'],0,',','.') ?></td>
                </tr>
            </table>
        </div>
    </div>

    <!-- Detail Barang -->
    <div class="card">
        <div class="card-header bg-dark text-white d-flex justify-content-between">
            <span>Detail Barang</span>
        </div>

        <div class="card-body">
            <table class="table table-bordered table-striped">
                <thead class="table-secondary text-center">
                    <tr>
                        <th>No</th>
                        <th>Barang</th>
                        <th>Jumlah</th>
                        <th>Harga Satuan</th>
                        <th>Subtotal</th>
                        <th>Terpenuhi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    $qDetail = mysqli_query($koneksi, "
                        SELECT d.*, b.nama AS nama_barang
                        FROM detail_pengadaan d
                        JOIN barang b ON d.idbarang = b.idbarang
                        WHERE d.idpengadaan = '$id';
                    ");

                    $no = 1;
                    while ($row = mysqli_fetch_assoc($qDetail)) {
                        echo "
                        <tr>
                            <td class='text-center'>$no</td>
                            <td>{$row['nama_barang']}</td>
                            <td class='text-center'>{$row['jumlah']}</td>
                            <td>Rp " . number_format($row['harga_satuan'],0,',','.') . "</td>
                            <td>Rp " . number_format($row['subtotal'],0,',','.') . "</td>
                            <td class='text-center'>{$row['jumlah_terpenuhi']}</td>
                        </tr>
                        ";
                        $no++;
                    }
                    ?>
                </tbody>
            </table>
        </div>
    </div>

</div>

</body>
</html>

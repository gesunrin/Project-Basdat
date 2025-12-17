<?php
include '../config/koneksi.php';
session_start();
if (!isset($_SESSION['user'])) header("Location: ../login.php");

$id = $_GET['id'];

$p = mysqli_fetch_assoc(mysqli_query($koneksi, "
  SELECT p.*, g.kode_pengadaan, v.nama_vendor, u.username
  FROM penerimaan p
  JOIN pengadaan g ON p.idpengadaan = g.idpengadaan
  JOIN vendor v ON g.idvendor = v.idvendor
  JOIN user u ON p.iduser = u.iduser
  WHERE p.idpenerimaan = $id
"));
?>

<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<title>Detail Penerimaan</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">

<style>
body { background: #fff; font-family: 'Poppins', sans-serif; }
.section-title {
  border-left: 5px solid #dc3545;
  padding-left: 10px;
  font-weight: bold;
  color: #dc3545;
}
.table thead tr {
  text-align: center;
}
</style>
</head>

<body>
<div class="container py-4">

<h3 class="section-title mb-3">Detail Penerimaan Barang</h3>

<div class="card shadow-sm p-4 mb-4">
    <h5><b>Kode Penerimaan:</b> <?= $p['kode_penerimaan'] ?></h5>
    <p><b>Kode Pengadaan:</b> <?= $p['kode_pengadaan'] ?></p>
    <p><b>Vendor:</b> <?= $p['nama_vendor'] ?></p>
    <p><b>User Input:</b> <?= $p['username'] ?></p>
    <p><b>Tanggal Input:</b> <?= $p['created_at'] ?></p>
</div>

<h4 class="section-title">Status Barang Pengadaan</h4>

<div class="card shadow-sm p-3">
<table class="table table-bordered table-striped">
<thead class="table-danger text-center">
<tr>
    <th>Barang</th>
    <th>Jumlah Diadakan</th>
    <th>Jumlah Diterima</th>
    <th>Sisa</th>
</tr>
</thead>
<tbody>

<?php
$detail = mysqli_query($koneksi, "
    SELECT 
        dp.idbarang,
        b.nama AS nama_barang,
        dp.jumlah AS jml_diadakan,
        COALESCE(SUM(dpr.jumlah),0) AS jml_diterima,
        (dp.jumlah - COALESCE(SUM(dpr.jumlah),0)) AS sisa
    FROM detail_pengadaan dp
    JOIN barang b ON dp.idbarang = b.idbarang
    LEFT JOIN detail_penerimaan dpr 
        ON dpr.idpengadaan = dp.idpengadaan 
        AND dpr.idbarang = dp.idbarang
    WHERE dp.idpengadaan = {$p['idpengadaan']}
    GROUP BY dp.idbarang, b.nama, dp.jumlah
");


while($d = mysqli_fetch_assoc($detail)) {
    echo "
    <tr>
        <td>{$d['nama_barang']}</td>
        <td class='text-center'>{$d['jml_diadakan']}</td>
        <td class='text-center'>{$d['jml_diterima']}</td>
        <td class='text-center'><b>{$d['sisa']}</b></td>
    </tr>";
}
?>
</tbody>
</table>
</div>

<a href="penerimaan.php" class="btn btn-outline-danger mt-3">Kembali</a>

</div>
</body>
</html>

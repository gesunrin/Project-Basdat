<?php
include '../config/koneksi.php';
session_start();
if (!isset($_SESSION['user'])) header("Location: ../login.php");

$iduser = $_SESSION['user']['iduser'];

// ambil data jumlah dari form
$jumlahInput = $_POST['jumlah'] ?? [];
if (empty($jumlahInput)) {
    echo "<script>alert('Tidak ada barang yang dipilih!'); window.location='tambah_penjualan.php';</script>";
    exit;
}

// hitung subtotal & total
$subtotal = 0;
foreach ($jumlahInput as $idbarang => $jumlah) {
    $jumlah = intval($jumlah);
    if ($jumlah <= 0) continue;

    $q = mysqli_query($koneksi, "SELECT harga_jual, COALESCE((SELECT jumlah FROM stok WHERE idbarang=$idbarang),0) AS stok FROM barang WHERE idbarang=$idbarang");
    $b = mysqli_fetch_assoc($q);

    if ($jumlah > $b['stok']) {
        echo "<script>alert('Stok barang ID $idbarang tidak mencukupi!'); window.location='tambah_penjualan.php';</script>";
        exit;
    }

    $subtotal += $jumlah * $b['harga_jual'];
}

$ppn = 0; // bisa disesuaikan
$total = $subtotal + $ppn;

// insert ke penjualan
mysqli_query($koneksi, "INSERT INTO penjualan (subtotal_nilai, ppn, total_nilai, iduser) VALUES ($subtotal, $ppn, $total, $iduser)");
$idpenjualan = mysqli_insert_id($koneksi);

// insert detail penjualan + trigger update stok & kartu_stok
foreach ($jumlahInput as $idbarang => $jumlah) {
    $jumlah = intval($jumlah);
    if ($jumlah <= 0) continue;

    $q = mysqli_query($koneksi, "SELECT harga_jual FROM barang WHERE idbarang=$idbarang");
    $harga = mysqli_fetch_assoc($q)['harga_jual'];
    $subtotalBarang = $jumlah * $harga;

    mysqli_query($koneksi, "INSERT INTO detail_penjualan (idpenjualan, idbarang, jumlah, harga, subtotal) 
        VALUES ($idpenjualan, $idbarang, $jumlah, $harga, $subtotalBarang)");
    // trigger AFTER INSERT detail_penjualan akan otomatis update stok & kartu_stok
}

// redirect ke detail
header("Location: detail_penjualan.php?id=$idpenjualan");
exit;

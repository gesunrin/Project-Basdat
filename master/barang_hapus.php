<?php
session_start();
include '../config/koneksi.php';
if (!isset($_SESSION['user'])) {
    header("Location: ../login.php");
    exit;
}

$id = $_GET['id'];
$q = mysqli_query($koneksi, "DELETE FROM barang WHERE idbarang=$id");
header("Location: barang.php");
exit;

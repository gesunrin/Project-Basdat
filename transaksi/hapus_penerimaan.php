<?php
include '../config/koneksi.php';
session_start();
if (!isset($_SESSION['user'])) header("Location: ../login.php");

$id = $_GET['id'];
mysqli_query($koneksi, "DELETE FROM detail_penerimaan WHERE idpenerimaan=$id");
mysqli_query($koneksi, "DELETE FROM penerimaan WHERE idpenerimaan=$id");
header("Location: penerimaan.php");
exit;
?>

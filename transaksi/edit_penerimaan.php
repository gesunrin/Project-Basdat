<?php
include '../config/koneksi.php';
session_start();
if (!isset($_SESSION['user'])) header("Location: ../login.php");
$id = $_GET['id'];

$data = mysqli_fetch_assoc(mysqli_query($koneksi, "SELECT * FROM penerimaan WHERE idpenerimaan=$id"));

if(isset($_POST['update'])){
  $kode = $_POST['kode_penerimaan'];
  $idvendor = $_POST['idvendor'];

  $update = mysqli_query($koneksi, "
    UPDATE penerimaan SET kode_penerimaan='$kode', idvendor='$idvendor' WHERE idpenerimaan=$id
  ");
  if($update){
    header("Location: penerimaan.php");
    exit;
  } else echo "<script>alert('Gagal update data');</script>";
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<title>Edit Penerimaan</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
<div class="container py-4">
  <h2>Edit Penerimaan Barang</h2>
  <form method="post">
    <div class="mb-3">
      <label class="form-label">Kode Penerimaan</label>
      <input type="text" name="kode_penerimaan" class="form-control" value="<?= $data['kode_penerimaan']; ?>" required>
    </div>
    <div class="mb-3">
      <label class="form-label">Vendor</label>
      <select name="idvendor" class="form-control" required>
        <?php
        $vendor = mysqli_query($koneksi, "SELECT * FROM vendor");
        while($v = mysqli_fetch_assoc($vendor)){
          $sel = ($v['idvendor']==$data['idvendor'])?'selected':'';
          echo "<option value='{$v['idvendor']}' $sel>{$v['nama_vendor']}</option>";
        }
        ?>
      </select>
    </div>
    <button type="submit" name="update" class="btn btn-danger">Update</button>
    <a href="penerimaan.php" class="btn btn-outline-danger">Batal</a>
  </form>
</div>
</body>
</html>

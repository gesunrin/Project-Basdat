<?php
include '../config/koneksi.php';
session_start();
if (!isset($_SESSION['user'])) header("Location: ../login.php");
?>

<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<title>Margin Penjualan</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">

<style>
body { background: #f8f9fa; font-family: 'Poppins', sans-serif; }
h2 { font-weight: 600; color: #dc3545; }
.card { border: none; box-shadow: 0 2px 10px rgba(0,0,0,0.1); }
.table th { background-color: #dc3545; color: #fff; text-align: center; }
.table td { vertical-align: middle; }
</style>
</head>

<body>
<div class="container py-4">
  
  <h2>Margin Penjualan</h2>

  <a href="../dashboard(1).php" class="btn btn-primary mb-3">Kembali</a>

  <div class="card p-3">
    <table class="table table-bordered table-striped">
      <thead>
        <tr>
          <th>ID</th>
          <th>Persentase Margin (%)</th>
          <th>Status</th>
          <th>Dibuat Oleh</th>
          <th>Dibuat Pada</th>
          <th>Diperbarui Pada</th>
          <th class="text-center">Aksi</th>
        </tr>
      </thead>
      <tbody>

        <?php
        $q = mysqli_query($koneksi, "
          SELECT m.*, u.username 
          FROM margin_penjualan m 
          LEFT JOIN user u ON m.iduser = u.iduser 
          ORDER BY m.idmargin_penjualan ASC
        ");

        if (mysqli_num_rows($q) == 0) {
            echo "<tr><td colspan='7' class='text-center text-muted'>Belum ada data margin penjualan.</td></tr>";
        } else {
            while ($d = mysqli_fetch_assoc($q)) {

                $statusText = ($d['status'] == 1) ? 'Aktif' : 'Tidak Aktif';
                $badge = ($d['status'] == 1) ? 'success' : 'secondary';

                echo "
                <tr>
                  <td>{$d['idmargin_penjualan']}</td>
                  <td>{$d['persen']}%</td>
                  <td class='text-center'><span class='badge bg-{$badge}'>{$statusText}</span></td>
                  <td>{$d['username']}</td>
                  <td>{$d['created_at']}</td>
                  <td>". ($d['updated_at'] ? $d['updated_at'] : '-') ."</td>
                  <td class='text-center'>
                    <a href='hapus_margin_penjualan.php?id={$d['idmargin_penjualan']}' 
                       class='btn btn-danger btn-sm'
                       onclick=\"return confirm('Yakin ingin menghapus data ini?')\">
                       Hapus
                    </a>
                  </td>
                </tr>";
            }
        }
        ?>

      </tbody>
    </table>
  </div>

</div>
</body>
</html>


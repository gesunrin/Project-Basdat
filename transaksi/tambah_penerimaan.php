<?php
// transaksi/tambah_penerimaan.php
include '../config/koneksi.php';
session_start();
if (!isset($_SESSION['user'])) header("Location: ../login.php");
$user = $_SESSION['user'];

// AJAX endpoint: jika ?action=get_pengadaan&id=...
if (isset($_GET['action']) && $_GET['action'] === 'get_pengadaan' && isset($_GET['id'])) {
    $id = (int)$_GET['id'];
    $out = ['header'=>null,'detail'=>[]];
    $qh = mysqli_query($koneksi, "
        SELECT p.*, v.nama_vendor, u.username
        FROM pengadaan p
        LEFT JOIN vendor v ON p.idvendor = v.idvendor
        LEFT JOIN user u ON p.iduser = u.iduser
        WHERE p.idpengadaan = $id
        LIMIT 1
    ");
    if ($qh && mysqli_num_rows($qh)){
        $out['header'] = mysqli_fetch_assoc($qh);
        $qd = mysqli_query($koneksi, "
          SELECT dp.*, b.nama AS nama_barang
          FROM detail_pengadaan dp
          LEFT JOIN barang b ON dp.idbarang = b.idbarang
          WHERE dp.idpengadaan = $id
        ");
        while($r = mysqli_fetch_assoc($qd)) $out['detail'][] = $r;
    }
    header('Content-Type: application/json');
    echo json_encode($out);
    exit;
}

// proses simpan penerimaan
if (isset($_POST['simpan'])) {
    $kode = mysqli_real_escape_string($koneksi, $_POST['kode_penerimaan']);
    $idpengadaan = (int)$_POST['idpengadaan'];
    $iduser = (int)$user['iduser'];
    $created_at = date('Y-m-d H:i:s');
    $arr_idbarang = $_POST['idbarang'] ?? [];
    $arr_jumlah = $_POST['jumlah'] ?? [];
    $arr_harga = $_POST['harga'] ?? [];

    mysqli_begin_transaction($koneksi);
    $ok = true;

    $ins = mysqli_query($koneksi, "
      INSERT INTO penerimaan (kode_penerimaan, created_at, status, idpengadaan, iduser)
      VALUES ('$kode', '$created_at', 'O', $idpengadaan, $iduser)
    ");
    if (!$ins) $ok = false;
    $idpenerimaan = mysqli_insert_id($koneksi);

    // insert detail_penerimaan
    for ($i=0;$i<count($arr_idbarang);$i++){
        $bid = (int)$arr_idbarang[$i];
        $j = (int)$arr_jumlah[$i];
        $h = (float)$arr_harga[$i];
        $sub = $j * $h;
        $q = mysqli_query($koneksi, "
          INSERT INTO detail_penerimaan (idpenerimaan, idpengadaan, idbarang, jumlah, harga_satuan_terima, subtotal_terima)
          VALUES ($idpenerimaan, $idpengadaan, $bid, $j, $h, $sub)
        ");
        if (!$q) { $ok = false; break; }
    }

    if ($ok) {
        mysqli_commit($koneksi);
        header("Location: penerimaan.php");
        exit;
    } else {
        mysqli_rollback($koneksi);
        echo "<div class='alert alert-danger'>Gagal menyimpan penerimaan: ".mysqli_error($koneksi)."</div>";
    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="utf-8">
<title>Tambah Penerimaan</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
<style>
body{font-family:Poppins, sans-serif;background:#fff;}
.card{box-shadow:0 2px 10px rgba(0,0,0,0.05);}
.table thead th{background:#dc3545;color:#fff;}
.small-muted{font-size:0.9rem;color:#666;}
</style>
</head>
<body>
<div class="container py-4">
  <h3 class="text-danger">Tambah Penerimaan</h3>
  <div class="mb-3">
    <a href="penerimaan.php" class="btn btn-outline-danger">← Kembali</a>
  </div>

  <form method="post" id="formTerima">
    <div class="card p-3 mb-3">
      <div class="row g-2">
        <div class="col-md-4">
          <label class="form-label">Kode Penerimaan</label>
          <?php
            // auto kode PNxxx
            $nx = 1;
            $qnx = mysqli_query($koneksi, "SELECT IFNULL(MAX(idpenerimaan),0)+1 AS nx FROM penerimaan");
            if ($qnx && ($rnx = mysqli_fetch_assoc($qnx))) $nx = $rnx['nx'];
            $kodePN = 'PN' . str_pad($nx,3,'0',STR_PAD_LEFT);
          ?>
          <input type="text" name="kode_penerimaan" class="form-control" value="<?=$kodePN?>" required>
        </div>

        <div class="col-md-4">
          <label class="form-label">Pilih Pengadaan</label>
          <select id="selPengadaan" class="form-control" required>
            <option value="">-- pilih pengadaan (status O) --</option>
            <?php
            $qp = mysqli_query($koneksi, "SELECT idpengadaan,kode_pengadaan,tanggal FROM pengadaan WHERE status = 'O' ORDER BY idpengadaan ASC");
            while($pp = mysqli_fetch_assoc($qp)){
                echo "<option value='{$pp['idpengadaan']}'>".$pp['kode_pengadaan']." - ".$pp['tanggal']."</option>";
            }
            ?>
          </select>
          <input type="hidden" name="idpengadaan" id="idpengadaan">
        </div>

        <div class="col-md-4">
          <label class="form-label">User</label>
          <input type="text" class="form-control" value="<?=htmlspecialchars($user['username'])?>" readonly>
        </div>
      </div>
    </div>

    <!-- info pengadaan -->
    <div id="boxHeader" class="card p-3 mb-3" style="display:none;">
      <div><strong>Vendor:</strong> <span id="h_vendor"></span></div>
      <div><strong>Subtotal Pengadaan:</strong> <span id="h_subtotal"></span></div>
      <div><strong>PPN Pengadaan:</strong> <span id="h_ppn"></span></div>
      <div><strong>Total Pengadaan:</strong> <span id="h_total"></span></div>
    </div>

    <div class="card p-3 mb-3">
      <h5>Detail Penerimaan</h5>
      <table class="table table-bordered" id="tblTerima">
        <thead class="table-danger text-center">
          <tr>
            <th>Barang</th>
            <th>Jumlah Diminta</th>
            <th>Jumlah Terima</th>
            <th>Harga Satuan Terima</th>
            <th>Subtotal</th>
            <th>Aksi</th>
          </tr>
        </thead>
        <tbody>
          <!-- baris akan diisi JS setelah pilih pengadaan -->
        </tbody>
      </table>
    </div>

    <div class="mb-3">
      <button type="submit" name="simpan" class="btn btn-danger">Simpan Penerimaan</button>
    </div>
  </form>
</div>

<script>
function rupiah(n){ return 'Rp ' + n.toString().replace(/\B(?=(\d{3})+(?!\d))/g, "."); }

document.getElementById('selPengadaan').addEventListener('change', function(){
  const id = this.value;
  if (!id) {
    document.getElementById('boxHeader').style.display='none';
    document.querySelector('#tblTerima tbody').innerHTML = '';
    document.getElementById('idpengadaan').value = '';
    return;
  }
  fetch('?action=get_pengadaan&id='+id)
    .then(res => res.json())
    .then(data => {
      if (!data.header) return alert('Pengadaan tidak ditemukan');
      document.getElementById('boxHeader').style.display='block';
      document.getElementById('h_vendor').innerText = data.header.nama_vendor || '-';
      document.getElementById('h_subtotal').innerText = rupiah(data.header.subtotal_nilai || 0);
      document.getElementById('h_ppn').innerText = rupiah(data.header.ppn || 0);
      document.getElementById('h_total').innerText = rupiah(data.header.total_nilai || 0);
      document.getElementById('idpengadaan').value = data.header.idpengadaan;

      // populate detail rows
      const tbody = document.querySelector('#tblTerima tbody');
      tbody.innerHTML = '';
      data.detail.forEach(function(d, idx){
         const row = document.createElement('tr');
         row.innerHTML = `
           <td>
             <input type="hidden" name="idbarang[]" value="${d.idbarang}">
             ${d.nama_barang || d.idbarang}
           </td>
           <td class="text-center">${d.jumlah}</td>
           <td><input type="number" name="jumlah[]" min="0" max="${d.jumlah}" value="${d.jumlah}" class="form-control jumlah" required></td>
           <td><input type="number" name="harga[]" min="0" value="${d.harga_satuan}" class="form-control harga" required></td>
           <td><input type="text" name="subtotal[]" readonly class="form-control subtotal" value="${d.jumlah * d.harga_satuan}"></td>
           <td class="text-center"><button type="button" class="btn btn-danger btn-sm hapus-row">X</button></td>
         `;
         tbody.appendChild(row);
      });
    })
    .catch(e => console.error(e));
});

// recalc subtotal for penerimaan
document.addEventListener('input', function(e){
  if (e.target.classList.contains('jumlah') || e.target.classList.contains('harga')) {
    const tr = e.target.closest('tr');
    const j = Number(tr.querySelector('.jumlah').value) || 0;
    const h = Number(tr.querySelector('.harga').value) || 0;
    tr.querySelector('.subtotal').value = j * h;
  }
});

// remove row (optional)
document.addEventListener('click', function(e){
  if (e.target.classList.contains('hapus-row')) {
    e.target.closest('tr').remove();
  }
});
</script>
</body>
</html>


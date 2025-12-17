<?php
// transaksi/tambah_pengadaan.php
include '../config/koneksi.php';
session_start();
if (!isset($_SESSION['user'])) header("Location: ../login.php");
$user = $_SESSION['user'];

// helper: ambil persen margin aktif (dipakai sebagai PPN jika ada)
$penPublished = 0;
$r = mysqli_query($koneksi, "SELECT persen FROM margin_penjualan WHERE status = 1 ORDER BY idmargin_penjualan DESC LIMIT 1");
if ($r && mysqli_num_rows($r)) {
    $penPublished = (float) mysqli_fetch_assoc($r)['persen'];
}

// auto-generate kode (PG001 style)
$next = 1;
$qn = mysqli_query($koneksi, "SELECT IFNULL(MAX(idpengadaan),0)+1 AS nx FROM pengadaan");
if ($qn && ($row = mysqli_fetch_assoc($qn))) $next = $row['nx'];
$kode_auto = 'PG' . str_pad($next, 3, '0', STR_PAD_LEFT);

// proses simpan
if (isset($_POST['simpan'])) {
    // ambil dan sanitize
    $kode = mysqli_real_escape_string($koneksi, $_POST['kode_pengadaan']);
    $idvendor = (int)$_POST['idvendor'];
    $tanggal = mysqli_real_escape_string($koneksi, $_POST['tanggal']);
    $iduser = (int)$user['iduser'];
    $arr_barang = $_POST['idbarang'] ?? [];
    $arr_jumlah = $_POST['jumlah'] ?? [];
    $arr_harga = $_POST['harga'] ?? [];

    // hitung subtotal
    $subtotal_nilai = 0;
    for ($i=0;$i<count($arr_barang);$i++){
        $j = (int)$arr_jumlah[$i];
        $h = (float)$arr_harga[$i];
        $subtotal_nilai += ($j * $h);
    }

    // ambil persen margin aktif lagi utk safety
    $pen = 0;
    $rq = mysqli_query($koneksi, "SELECT persen FROM margin_penjualan WHERE status = 1 ORDER BY idmargin_penjualan DESC LIMIT 1");
    if ($rq && mysqli_num_rows($rq)) $pen = (float)mysqli_fetch_assoc($rq)['persen'];

    $ppn = round($subtotal_nilai * ($pen/100), 2);
    $total_nilai = $subtotal_nilai + $ppn;

    // mulai transaksi
    mysqli_begin_transaction($koneksi);
    $ok = true;

    $ins = mysqli_query($koneksi, "
      INSERT INTO pengadaan (kode_pengadaan, tanggal, idvendor, iduser, subtotal_nilai, ppn, total_nilai, status)
      VALUES (
        '". $kode ."',
        '". $tanggal ."',
        '". $idvendor ."',
        '". $iduser ."',
        '". $subtotal_nilai ."',
        '". $ppn ."',
        '". $total_nilai ."',
        'O'
      )
    ");
    if (!$ins) { $ok = false; }

    $idpengadaan = mysqli_insert_id($koneksi);

    // insert detail
    for ($i=0;$i<count($arr_barang) && $ok; $i++){
        $b = (int)$arr_barang[$i];
        $j = (int)$arr_jumlah[$i];
        $h = (float)$arr_harga[$i];
        $s = $j * $h;

        $ins2 = mysqli_query($koneksi, "
          INSERT INTO detail_pengadaan (idpengadaan, idbarang, jumlah, harga_satuan, subtotal, jumlah_terpenuhi)
          VALUES ('$idpengadaan', '$b', '$j', '$h', '$s', 0)
        ");
        if (!$ins2) { $ok = false; break; }
    }

    if ($ok) {
        mysqli_commit($koneksi);
        header("Location: pengadaan.php");
        exit;
    } else {
        mysqli_rollback($koneksi);
        $err = mysqli_error($koneksi);
        echo "<div class='alert alert-danger'>Gagal menyimpan: $err</div>";
    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="utf-8">
<title>Tambah Pengadaan</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
<style>
body{font-family:Poppins, sans-serif;background:#fff;}
.card { box-shadow:0 2px 10px rgba(0,0,0,0.05); }
.table thead th { background:#dc3545;color:#fff; }
.badge-info { background:#0dcaf0; }
.small-muted { font-size:0.9rem; color:#666; }
.filter-box { max-width:400px; }
</style>
</head>
<body>
<div class="container py-4">
  <h3 class="text-danger">Tambah Pengadaan</h3>
  <div class="mb-3">
    <a href="pengadaan.php" class="btn btn-outline-danger">← Kembali</a>
  </div>

  <form method="post" id="formPengadaan">
    <div class="card p-3 mb-3">
      <div class="row g-2">
        <div class="col-md-4">
          <label class="form-label">Kode Pengadaan</label>
          <input type="text" name="kode_pengadaan" class="form-control" value="<?=htmlspecialchars($kode_auto)?>" required>
        </div>
        <div class="col-md-4">
          <label class="form-label">Tanggal</label>
          <input type="date" name="tanggal" class="form-control" value="<?=date('Y-m-d')?>" required>
        </div>
        <div class="col-md-4">
          <label class="form-label">Vendor</label>
          <select name="idvendor" class="form-control" required>
            <option value="">-- Pilih Vendor --</option>
            <?php
            $vq = mysqli_query($koneksi, "SELECT * FROM vendor WHERE status = 'A' OR status = 'a' OR status = 1");
            while($v = mysqli_fetch_assoc($vq)){
                echo "<option value='{$v['idvendor']}'>".htmlspecialchars($v['nama_vendor'])."</option>";
            }
            ?>
          </select>
        </div>
      </div>
      <div class="mt-2 small-muted">PPN otomatis berdasarkan margin penjualan aktif: <strong><?=$penPublished?>%</strong></div>
    </div>

    <div class="card p-3 mb-3">
      <div class="d-flex justify-content-between align-items-center mb-2">
        <h5 class="mb-0">Detail Barang</h5>
        <div class="filter-box">
          <input id="filterBarang" class="form-control form-control-sm" placeholder="Filter nama barang..." />
        </div>
      </div>

      <table class="table table-bordered" id="tblBarang">
        <thead class="table-danger text-center">
          <tr>
            <th style="width:40%">Barang</th>
            <th style="width:12%">Jumlah</th>
            <th style="width:18%">Harga Satuan</th>
            <th style="width:18%">Subtotal</th>
            <th style="width:12%">Aksi</th>
          </tr>
        </thead>
        <tbody>
          <tr>
            <td>
              <select name="idbarang[]" class="form-control barang-select" required>
                <option value="">-- pilih barang --</option>
                <?php
                $bq = mysqli_query($koneksi, "SELECT * FROM barang WHERE status = 1 ORDER BY nama");
                while($b = mysqli_fetch_assoc($bq)){
                    $nama = $b['nama']; // schema uses nama
                    echo "<option value='{$b['idbarang']}' data-harga='{$b['harga_beli']}'>".htmlspecialchars($nama)."</option>";
                }
                ?>
              </select>
            </td>
            <td><input type="number" name="jumlah[]" class="form-control jumlah" min="1" value="1" required></td>
            <td><input type="number" name="harga[]" class="form-control harga" min="0" value="0" required></td>
            <td><input type="text" name="subtotal[]" class="form-control subtotal" readonly></td>
            <td class="text-center">
              <button type="button" class="btn btn-danger btn-sm hapus-row">X</button>
            </td>
          </tr>
        </tbody>
      </table>

      <div class="d-flex justify-content-between align-items-center mt-2">
        <div class="small-muted">Tip: gunakan filter untuk mencari barang cepat.</div>
        <div class="text-end">
          <div class="mb-1">Subtotal: <strong id="viewSubtotal">Rp 0</strong></div>
          <div class="mb-1">PPN (<?= $penPublished ?>%): <strong id="viewPPN">Rp 0</strong></div>
          <div><h5>Total: <span id="viewTotal">Rp 0</span></h5></div>
        </div>
      </div>

    </div>

    <div class="mb-3">
      <button type="button" id="addRow" class="btn btn-primary">+ Tambah Baris</button>
      <button type="submit" name="simpan" class="btn btn-danger">Simpan Pengadaan</button>
    </div>
  </form>
</div>

<script>
// util: format rupiah simple
function rupiah(n){
  return 'Rp ' + n.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ".");
}

function recalc(){
  let subtotal = 0;
  document.querySelectorAll('#tblBarang tbody tr').forEach(function(tr){
    const j = Number(tr.querySelector('.jumlah').value) || 0;
    const h = Number(tr.querySelector('.harga').value) || 0;
    const s = j * h;
    tr.querySelector('.subtotal').value = s;
    subtotal += s;
  });
  const persen = <?= $penPublished ?>;
  const ppn = Math.round(subtotal * (persen/100));
  const total = subtotal + ppn;
  document.getElementById('viewSubtotal').innerText = rupiah(subtotal);
  document.getElementById('viewPPN').innerText = rupiah(ppn);
  document.getElementById('viewTotal').innerText = rupiah(total);
}

// add row template
document.getElementById('addRow').addEventListener('click', function(){
  const options = `<?php
    // echo options as one-liner escaped for JS template
    $opt = '';
    $bqq = mysqli_query($koneksi, "SELECT * FROM barang WHERE status = 1 ORDER BY nama");
    while($bb = mysqli_fetch_assoc($bqq)){
      $nama = addslashes($bb['nama']);
      $opt .= "<option value='{$bb['idbarang']}' data-harga='{$bb['harga_beli']}'>{$nama}</option>";
    }
    echo str_replace(["\r","\n"],['',''],$opt);
  ?>`;

  const tr = document.createElement('tr');
  tr.innerHTML = `
    <td><select name="idbarang[]" class="form-control barang-select" required><option value="">-- pilih barang --</option>${options}</select></td>
    <td><input type="number" name="jumlah[]" class="form-control jumlah" min="1" value="1" required></td>
    <td><input type="number" name="harga[]" class="form-control harga" min="0" value="0" required></td>
    <td><input type="text" name="subtotal[]" class="form-control subtotal" readonly></td>
    <td class="text-center"><button type="button" class="btn btn-danger btn-sm hapus-row">X</button></td>`;
  document.querySelector('#tblBarang tbody').appendChild(tr);
});

// delegated events: remove row
document.addEventListener('click', function(e){
  if (e.target.classList.contains('hapus-row')){
    e.target.closest('tr').remove();
    recalc();
  }
});

// recalc on input
document.addEventListener('input', function(e){
  if (e.target.classList.contains('jumlah') || e.target.classList.contains('harga')){
    recalc();
  }
});

// quick fill harga when select barang (use data-harga attr)
document.addEventListener('change', function(e){
  if (e.target.classList.contains('barang-select')){
    const sel = e.target;
    const hargaDef = sel.selectedOptions[0] ? sel.selectedOptions[0].getAttribute('data-harga') : 0;
    const row = sel.closest('tr');
    if (hargaDef !== null) row.querySelector('.harga').value = hargaDef;
    recalc();
  }
});

// filter barang
document.getElementById('filterBarang').addEventListener('input', function(){
  const q = this.value.toLowerCase();
  document.querySelectorAll('.barang-select').forEach(function(sel){
    Array.from(sel.options).forEach(function(opt){
      const txt = opt.text.toLowerCase();
      if (opt.value === '') { opt.hidden = false; return; }
      opt.hidden = (txt.indexOf(q) === -1);
    });
  });
});

// initial recalc
recalc();
</script>
</body>
</html>

<?php
include '../config/koneksi.php';
session_start();
if (!isset($_SESSION['user'])) header("Location: ../login.php");

$userId = $_SESSION['user']['iduser'];

$barang = mysqli_query($koneksi, "
    SELECT b.idbarang, b.nama, b.harga_jual, COALESCE(s.jumlah,0) AS stok 
    FROM barang b
    LEFT JOIN stok s ON b.idbarang = s.idbarang
    WHERE b.status = 1
    ORDER BY b.nama ASC
");
?>

<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<title>Tambah Penjualan</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>

<body class="bg-light">
<div class="container py-4">

    <h2 class="mb-3">Tambah Penjualan</h2>
    <a href="penjualan.php" class="btn btn-danger mb-3">Kembali</a>

    <form action="simpan_penjualan.php" method="POST">

        <table class="table table-bordered table-striped align-middle">
            <thead class="table-danger">
                <tr>
                    <th>Barang</th>
                    <th>Stok</th>
                    <th>Harga</th>
                    <th width="120">Jumlah</th>
                    <th>Subtotal</th>
                </tr>
            </thead>
            <tbody>
                <?php while($b = mysqli_fetch_assoc($barang)): ?>
                <tr>
                    <td><?= htmlspecialchars($b['nama']) ?></td>
                    <td><?= $b['stok'] ?></td>
                    <td>Rp <?= number_format($b['harga_jual'], 0, ',', '.') ?></td>

                    <td>
                        <input 
                            type="number" 
                            name="jumlah[<?= $b['idbarang'] ?>]" 
                            class="form-control jumlah-input"
                            min="0"
                            max="<?= $b['stok'] ?>"
                            value="0"
                            data-harga="<?= $b['harga_jual'] ?>"
                            data-id="<?= $b['idbarang'] ?>"
                        >
                    </td>

                    <td>
                        <span id="subtotal_<?= $b['idbarang'] ?>">Rp 0</span>
                    </td>
                </tr>
                <?php endwhile; ?>
            </tbody>
        </table>

        <!-- TOTAL PERHITUNGAN -->
        <div class="card p-3 shadow-sm">
            <h5>Ringkasan Pembayaran</h5>

            <div class="mb-2">
                <label>Subtotal :</label>
                <input type="text" id="subtotal_all" class="form-control" readonly>
            </div>

            <div class="mb-2">
                <label>PPN 10% :</label>
                <input type="text" id="ppn" class="form-control" readonly>
            </div>

            <div class="mb-2">
                <label>Total Bayar :</label>
                <input type="text" id="total_all" class="form-control fw-bold" readonly>
            </div>
        </div>

        <button type="submit" class="btn btn-primary mt-3 w-100">Simpan Penjualan</button>

    </form>
</div>

<script>
// format rupiah
function rupiah(x) {
    return "Rp " + x.toLocaleString('id-ID');
}

// hitung ulang total keseluruhan
function hitungTotal() {
    let subtotalAll = 0;

    document.querySelectorAll('.jumlah-input').forEach(input => {
        let jumlah = parseInt(input.value) || 0;
        let harga = parseFloat(input.dataset.harga);
        subtotalAll += jumlah * harga;
    });

    let ppn = subtotalAll * 0.10;
    let total = subtotalAll + ppn;

    document.getElementById("subtotal_all").value = rupiah(subtotalAll);
    document.getElementById("ppn").value = rupiah(ppn);
    document.getElementById("total_all").value = rupiah(total);
}

// hitung subtotal per barang + total keseluruhan
document.querySelectorAll('.jumlah-input').forEach(input => {
    input.addEventListener('input', function() {
        let id = this.dataset.id;
        let jumlah = parseInt(this.value) || 0;
        let harga = parseFloat(this.dataset.harga);
        let subtotal = jumlah * harga;

        document.getElementById("subtotal_" + id).innerText = rupiah(subtotal);

        hitungTotal();
    });
});
</script>

</body>
</html>


USE projectbasdat;

CREATE VIEW view_satuan_aktif AS
SELECT idsatuan, nama_satuan, status
FROM satuan
WHERE status = 1;

-- 2) Satuan nonaktif
CREATE VIEW view_satuan_nonaktif AS
SELECT idsatuan, nama_satuan, status
FROM satuan
WHERE status = 0;

-- 3) Barang aktif
CREATE VIEW view_barang_aktif AS
SELECT b.idbarang, b.jenis, b.nama, b.idsatuan, b.status, b.harga_beli, b.harga_jual,
       COALESCE(st.jumlah,0) AS stok
FROM barang b
LEFT JOIN stok st ON b.idbarang = st.idbarang
WHERE b.status = 1;

-- 4) Barang nonaktif
CREATE VIEW view_barang_nonaktif AS
SELECT b.idbarang, b.jenis, b.nama, b.idsatuan, b.status, b.harga_beli, b.harga_jual,
       COALESCE(st.jumlah,0) AS stok
FROM barang b
LEFT JOIN stok st ON b.idbarang = st.idbarang
WHERE b.status = 0;

-- 5) Stok barang (positif saja)
CREATE VIEW view_stok_barang AS
SELECT b.idbarang, b.nama, COALESCE(st.jumlah,0) AS stok
FROM barang b
LEFT JOIN stok st ON b.idbarang = st.idbarang
WHERE COALESCE(st.jumlah,0) > 0;

-- 6) Detail penerimaan (per baris penerimaan)
CREATE VIEW view_penerimaan_detail AS
SELECT pr.idpenerimaan, pr.kode_penerimaan, pr.created_at, pr.idpengadaan, pr.iduser,
       dp.iddetail_penerimaan, dp.idbarang, b.nama AS nama_barang, dp.jumlah, dp.harga_satuan_terima, dp.subtotal_terima
FROM penerimaan pr
JOIN detail_penerimaan dp ON pr.idpenerimaan = dp.idpenerimaan
LEFT JOIN barang b ON dp.idbarang = b.idbarang;

-- 7) Detail pengadaan (per baris pengadaan)
CREATE VIEW view_pengadaan_detail AS
SELECT pg.idpengadaan, pg.kode_pengadaan, pg.tanggal, pg.idvendor, v.nama_vendor,
       dp.iddetail_pengadaan, dp.idbarang, b.nama AS nama_barang, dp.jumlah, dp.harga_satuan, dp.subtotal, dp.jumlah_terpenuhi
FROM pengadaan pg
LEFT JOIN detail_pengadaan dp ON pg.idpengadaan = dp.idpengadaan
LEFT JOIN barang b ON dp.idbarang = b.idbarang
LEFT JOIN vendor v ON pg.idvendor = v.idvendor;

-- 8) Detail penjualan
CREATE VIEW view_penjualan_detail AS
SELECT pj.idpenjualan, pj.kode_penjualan, pj.created_at, dj.iddetail_penjualan, dj.idbarang, b.nama AS nama_barang, dj.jumlah, dj.harga, dj.subtotal
FROM penjualan pj
LEFT JOIN detail_penjualan dj ON pj.idpenjualan = dj.idpenjualan
LEFT JOIN barang b ON dj.idbarang = b.idbarang;

-- 9) Margin per user (who created margin)
CREATE VIEW view_margin_user AS
SELECT m.idmargin_penjualan, u.username, m.persen, m.status, m.created_at, m.updated_at
FROM margin_penjualan m
LEFT JOIN user u ON m.iduser = u.iduser;

-- 10) Sisa tiap baris pengadaan (untuk multi-penerimaan)
CREATE VIEW view_pengadaan_sisa AS
SELECT dp.iddetail_pengadaan,
       dp.idpengadaan,
       dp.idbarang,
       dp.jumlah AS jumlah_diminta,
       dp.jumlah_terpenuhi,
       (dp.jumlah - dp.jumlah_terpenuhi) AS jumlah_sisa
FROM detail_pengadaan dp;
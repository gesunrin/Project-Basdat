USE projectbasdat;
DELIMITER $$

-- SP: tambah pengadaan (satu statement insert + return id)
CREATE PROCEDURE sp_tambah_pengadaan (
  IN p_idvendor INT,
  IN p_iduser INT,
  IN p_tanggal DATE,
  IN p_subtotal DECIMAL(15,2),
  IN p_ppn DECIMAL(12,2)
)
BEGIN
  DECLARE v_total DECIMAL(15,2);
  SET v_total = p_subtotal + p_ppn;
  INSERT INTO pengadaan (tanggal, idvendor, iduser, subtotal_nilai, ppn, total_nilai, status)
  VALUES (p_tanggal, p_idvendor, p_iduser, p_subtotal, p_ppn, v_total, 'O');
  SELECT LAST_INSERT_ID() AS idpengadaan;
END$$

-- SP: tambah detail penerimaan (satu baris) dan otomatis update jumlah_terpenuhi, stok, kartu_stok
CREATE PROCEDURE sp_tambah_detail_penerimaan (
  IN p_idpenerimaan BIGINT,
  IN p_idpengadaan BIGINT,
  IN p_idbarang INT,
  IN p_jumlah INT,
  IN p_harga DECIMAL(12,2)
)
BEGIN
  DECLARE v_subtotal DECIMAL(15,2);
  DECLARE v_exist INT;
  DECLARE v_stok INT;
  DECLARE v_newstok INT;
  SET v_subtotal = p_jumlah * p_harga;

  -- insert detail_penerimaan
  INSERT INTO detail_penerimaan (idpenerimaan, idpengadaan, idbarang, jumlah, harga_satuan_terima, subtotal_terima)
  VALUES (p_idpenerimaan, p_idpengadaan, p_idbarang, p_jumlah, p_harga, v_subtotal);

  -- update jumlah_terpenuhi di detail_pengadaan
  UPDATE detail_pengadaan
  SET jumlah_terpenuhi = jumlah_terpenuhi + p_jumlah
  WHERE idpengadaan = p_idpengadaan AND idbarang = p_idbarang;

  -- update/insert stok
  SELECT COUNT(*) INTO v_exist FROM stok WHERE idbarang = p_idbarang;
  IF v_exist = 0 THEN
    INSERT INTO stok (idbarang, jumlah) VALUES (p_idbarang, p_jumlah);
    SET v_newstok = p_jumlah;
  ELSE
    SELECT jumlah INTO v_stok FROM stok WHERE idbarang = p_idbarang;
    SET v_newstok = v_stok + p_jumlah;
    UPDATE stok SET jumlah = v_newstok WHERE idbarang = p_idbarang;
  END IF;

  -- insert kartu_stok entry
  INSERT INTO kartu_stok (idbarang, jenis_transaksi, masuk, keluar, stok, idtransaksi, keterangan)
  VALUES (p_idbarang, 'I', p_jumlah, 0, v_newstok, LAST_INSERT_ID(), CONCAT('SP Penerimaan ', p_idpenerimaan));
END$$

-- SP: tambah penjualan (insert header + returns id)
CREATE PROCEDURE sp_tambah_penjualan (
  IN p_iduser INT,
  IN p_subtotal DECIMAL(15,2),
  IN p_ppn DECIMAL(12,2)
)
BEGIN
  DECLARE v_total DECIMAL(15,2);
  SET v_total = p_subtotal + p_ppn;
  INSERT INTO penjualan (subtotal_nilai, ppn, total_nilai, iduser, status)
  VALUES (p_subtotal, p_ppn, v_total, p_iduser, 'Proses');
  SELECT LAST_INSERT_ID() AS idpenjualan;
END$$

DELIMITER ;
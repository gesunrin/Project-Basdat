USE projectbasdat;
DELIMITER $$

-- AUTOKODE pengadaan (PG001...)
CREATE TRIGGER trg_autokode_pengadaan
BEFORE INSERT ON pengadaan
FOR EACH ROW
BEGIN
  DECLARE last_id BIGINT;
  SELECT IFNULL(MAX(idpengadaan),0) + 1 INTO last_id FROM pengadaan;
  SET NEW.kode_pengadaan = CONCAT('PG', LPAD(last_id, 3, '0'));
END$$

-- AUTOKODE penerimaan (PN001...)
CREATE TRIGGER trg_autokode_penerimaan
BEFORE INSERT ON penerimaan
FOR EACH ROW
BEGIN
  DECLARE last_id BIGINT;
  SELECT IFNULL(MAX(idpenerimaan),0) + 1 INTO last_id FROM penerimaan;
  SET NEW.kode_penerimaan = CONCAT('PN', LPAD(last_id, 3, '0'));
END$$

-- AUTOKODE penjualan (PJ001...)
CREATE TRIGGER trg_autokode_penjualan
BEFORE INSERT ON penjualan
FOR EACH ROW
BEGIN
  DECLARE last_id BIGINT;
  SELECT IFNULL(MAX(idpenjualan),0) + 1 INTO last_id FROM penjualan;
  SET NEW.kode_penjualan = CONCAT('PJ', LPAD(last_id, 3, '0'));
END$$

-- BEFORE INSERT detail_penjualan: cek stok cukup
CREATE TRIGGER trg_before_insert_detail_penjualan
BEFORE INSERT ON detail_penjualan
FOR EACH ROW
BEGIN
  DECLARE v_stok INT;
  SELECT jumlah INTO v_stok FROM stok WHERE idbarang = NEW.idbarang;
  IF v_stok IS NULL OR v_stok < NEW.jumlah THEN
    SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = 'Stok tidak mencukupi untuk penjualan';
  END IF;
END$$

-- AFTER INSERT detail_penerimaan: update detail_pengadaan.jumlah_terpenuhi, update/insert stok, insert kartu_stok
CREATE TRIGGER trg_after_insert_detail_penerimaan
AFTER INSERT ON detail_penerimaan
FOR EACH ROW
BEGIN
  DECLARE v_exist INT;
  DECLARE v_stok INT;
  DECLARE v_newstok INT;

  -- update jumlah_terpenuhi pada detail_pengadaan (cocokkan idpengadaan + idbarang)
  UPDATE detail_pengadaan
  SET jumlah_terpenuhi = jumlah_terpenuhi + NEW.jumlah
  WHERE idpengadaan = NEW.idpengadaan AND idbarang = NEW.idbarang;

  -- update/insert tabel stok
  SELECT COUNT(*) INTO v_exist FROM stok WHERE idbarang = NEW.idbarang;
  IF v_exist = 0 THEN
    INSERT INTO stok (idbarang, jumlah) VALUES (NEW.idbarang, NEW.jumlah);
    SET v_newstok = NEW.jumlah;
  ELSE
    SELECT jumlah INTO v_stok FROM stok WHERE idbarang = NEW.idbarang;
    SET v_newstok = v_stok + NEW.jumlah;
    UPDATE stok SET jumlah = v_newstok WHERE idbarang = NEW.idbarang;
  END IF;

  -- catat ke kartu_stok (masuk)
  INSERT INTO kartu_stok (idbarang, jenis_transaksi, masuk, keluar, stok, idtransaksi, keterangan)
  VALUES (NEW.idbarang, 'I', NEW.jumlah, 0, v_newstok, NEW.iddetail_penerimaan, CONCAT('Penerimaan ', NEW.idpenerimaan));
END$$

-- AFTER INSERT detail_penjualan: kurangi stok dan catat ke kartu_stok
CREATE TRIGGER trg_after_insert_detail_penjualan
AFTER INSERT ON detail_penjualan
FOR EACH ROW
BEGIN
  DECLARE v_stok_after INT;
  -- update stok
  UPDATE stok SET jumlah = jumlah - NEW.jumlah WHERE idbarang = NEW.idbarang;
  SELECT jumlah INTO v_stok_after FROM stok WHERE idbarang = NEW.idbarang;

  -- catat ke kartu_stok (keluar)
  INSERT INTO kartu_stok (idbarang, jenis_transaksi, masuk, keluar, stok, idtransaksi, keterangan)
  VALUES (NEW.idbarang, 'O', 0, NEW.jumlah, v_stok_after, NEW.iddetail_penjualan, CONCAT('Penjualan ', NEW.idpenjualan));
END$$

DELIMITER ;
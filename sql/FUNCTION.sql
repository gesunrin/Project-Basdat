USE projectbasdat;
DELIMITER $$

-- hitung total pengadaan berdasarkan detail_pengadaan.subtotal
CREATE FUNCTION hitung_total_pengadaan(p_idpengadaan BIGINT)
RETURNS DECIMAL(15,2)
DETERMINISTIC
BEGIN
  DECLARE total DECIMAL(15,2) DEFAULT 0;
  SELECT IFNULL(SUM(subtotal),0) INTO total
  FROM detail_pengadaan
  WHERE idpengadaan = p_idpengadaan;
  RETURN total;
END$$

-- hitung total penjualan berdasarkan detail_penjualan.subtotal
CREATE FUNCTION hitung_total_penjualan(p_idpenjualan BIGINT)
RETURNS DECIMAL(15,2)
DETERMINISTIC
BEGIN
  DECLARE total DECIMAL(15,2) DEFAULT 0;
  SELECT IFNULL(SUM(subtotal),0) INTO total
  FROM detail_penjualan
  WHERE idpenjualan = p_idpenjualan;
  RETURN total;
END$$

-- cek sisa pengadaan untuk kombinasi pengadaan+barang
CREATE FUNCTION cek_sisa_pengadaan(p_idpengadaan BIGINT, p_idbarang INT)
RETURNS INT
DETERMINISTIC
BEGIN
  DECLARE sisa INT DEFAULT 0;
  SELECT (IFNULL(jumlah,0) - IFNULL(jumlah_terpenuhi,0)) INTO sisa
  FROM detail_pengadaan
  WHERE idpengadaan = p_idpengadaan AND idbarang = p_idbarang
  LIMIT 1;
  RETURN IFNULL(sisa,0);
END$$

DELIMITER ;
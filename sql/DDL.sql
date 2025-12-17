CREATE DATABASE projectbasdat;
USE projectbasdat;

CREATE TABLE satuan (
  idsatuan INT AUTO_INCREMENT PRIMARY KEY,
  nama_satuan VARCHAR(50) NOT NULL,
  status TINYINT DEFAULT 1
) ENGINE=InnoDB;

CREATE TABLE vendor (
  idvendor INT AUTO_INCREMENT PRIMARY KEY,
  nama_vendor VARCHAR(150) NOT NULL,
  badan_hukum CHAR(1) DEFAULT 'C',
  status CHAR(1) DEFAULT 'A'
) ENGINE=InnoDB;

CREATE TABLE role (
  idrole INT AUTO_INCREMENT PRIMARY KEY,
  nama_role VARCHAR(100) NOT NULL
) ENGINE=InnoDB;

CREATE TABLE user (
  iduser INT AUTO_INCREMENT PRIMARY KEY,
  username VARCHAR(45) NOT NULL,
  password VARCHAR(100) NOT NULL,
  idrole INT,
  FOREIGN KEY (idrole) REFERENCES role(idrole) ON DELETE SET NULL
) ENGINE=InnoDB;

CREATE TABLE barang (
  idbarang INT AUTO_INCREMENT PRIMARY KEY,
  jenis CHAR(1) NOT NULL,
  nama VARCHAR(150) NOT NULL,
  idsatuan INT,
  status TINYINT DEFAULT 1,
  harga_beli DECIMAL(12,2) DEFAULT 0,
  harga_jual DECIMAL(12,2) DEFAULT 0,
  FOREIGN KEY (idsatuan) REFERENCES satuan(idsatuan) ON DELETE SET NULL
) ENGINE=InnoDB;

CREATE TABLE stok (
  idstok INT AUTO_INCREMENT PRIMARY KEY,
  idbarang INT NOT NULL,
  jumlah INT DEFAULT 0,
  FOREIGN KEY (idbarang) REFERENCES barang(idbarang) ON DELETE CASCADE
) ENGINE=InnoDB;

CREATE TABLE pengadaan (
  idpengadaan BIGINT AUTO_INCREMENT PRIMARY KEY,
  kode_pengadaan VARCHAR(20),
  tanggal DATE,
  idvendor INT,
  iduser INT,
  subtotal_nilai DECIMAL(15,2) DEFAULT 0,
  ppn DECIMAL(12,2) DEFAULT 0,
  total_nilai DECIMAL(15,2) DEFAULT 0,
  status CHAR(1) DEFAULT 'O', -- O=open/C=closed
  FOREIGN KEY (idvendor) REFERENCES vendor(idvendor) ON DELETE SET NULL,
  FOREIGN KEY (iduser) REFERENCES user(iduser) ON DELETE SET NULL
) ENGINE=InnoDB;

CREATE TABLE detail_pengadaan (
  iddetail_pengadaan BIGINT AUTO_INCREMENT PRIMARY KEY,
  idpengadaan BIGINT,
  idbarang INT,
  jumlah INT,
  harga_satuan DECIMAL(12,2),
  subtotal DECIMAL(15,2),
  jumlah_terpenuhi INT DEFAULT 0, -- jumlah yang sudah diterima via penerimaan
  FOREIGN KEY (idpengadaan) REFERENCES pengadaan(idpengadaan) ON DELETE CASCADE,
  FOREIGN KEY (idbarang) REFERENCES barang(idbarang) ON DELETE CASCADE
) ENGINE=InnoDB;

CREATE TABLE penerimaan (
  idpenerimaan BIGINT AUTO_INCREMENT PRIMARY KEY,
  kode_penerimaan VARCHAR(20),
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  status CHAR(1) DEFAULT 'O',
  idpengadaan BIGINT,
  iduser INT,
  FOREIGN KEY (idpengadaan) REFERENCES pengadaan(idpengadaan) ON DELETE SET NULL,
  FOREIGN KEY (iduser) REFERENCES user(iduser) ON DELETE SET NULL
) ENGINE=InnoDB;

CREATE TABLE detail_penerimaan (
  iddetail_penerimaan BIGINT AUTO_INCREMENT PRIMARY KEY,
  idpenerimaan BIGINT,
  idpengadaan BIGINT, -- referensi ke pengadaan supaya bisa cek sisa
  idbarang INT,
  jumlah INT,
  harga_satuan_terima DECIMAL(12,2),
  subtotal_terima DECIMAL(15,2),
  FOREIGN KEY (idpenerimaan) REFERENCES penerimaan(idpenerimaan) ON DELETE CASCADE,
  FOREIGN KEY (idpengadaan) REFERENCES pengadaan(idpengadaan) ON DELETE SET NULL,
  FOREIGN KEY (idbarang) REFERENCES barang(idbarang) ON DELETE SET NULL
) ENGINE=InnoDB;

CREATE TABLE margin_penjualan (
  idmargin_penjualan INT AUTO_INCREMENT PRIMARY KEY,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  persen DOUBLE,
  status TINYINT DEFAULT 1,
  iduser INT,
  updated_at TIMESTAMP NULL,
  FOREIGN KEY (iduser) REFERENCES user(iduser) ON DELETE SET NULL
) ENGINE=InnoDB;

CREATE TABLE penjualan (
  idpenjualan BIGINT AUTO_INCREMENT PRIMARY KEY,
  kode_penjualan VARCHAR(20),
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  subtotal_nilai DECIMAL(15,2) DEFAULT 0,
  ppn DECIMAL(12,2) DEFAULT 0,
  total_nilai DECIMAL(15,2) DEFAULT 0,
  iduser INT,
  idmargin_penjualan INT,
  FOREIGN KEY (iduser) REFERENCES user(iduser) ON DELETE SET NULL,
  FOREIGN KEY (idmargin_penjualan) REFERENCES margin_penjualan(idmargin_penjualan) 
) ENGINE=InnoDB;

CREATE TABLE detail_penjualan (
  iddetail_penjualan BIGINT AUTO_INCREMENT PRIMARY KEY,
  idpenjualan BIGINT,
  idbarang INT,
  jumlah INT,
  harga DECIMAL(12,2),
  subtotal DECIMAL(15,2),
  FOREIGN KEY (idpenjualan) REFERENCES penjualan(idpenjualan) ON DELETE CASCADE,
  FOREIGN KEY (idbarang) REFERENCES barang(idbarang) ON DELETE SET NULL
) ENGINE=InnoDB;

CREATE TABLE kartu_stok (
  idkartu BIGINT AUTO_INCREMENT PRIMARY KEY,
  idbarang INT,
  jenis_transaksi CHAR(1), -- I=in, O=out
  masuk INT DEFAULT 0,
  keluar INT DEFAULT 0,
  stok INT,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  idtransaksi BIGINT, -- id referensi (detail_penerimaan/detail_penjualan)
  keterangan VARCHAR(200),
  FOREIGN KEY (idbarang) REFERENCES barang(idbarang) ON DELETE CASCADE
) ENGINE=InnoDB;

CREATE TABLE returr (
  idreturr BIGINT AUTO_INCREMENT PRIMARY KEY,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  idpenerimaan BIGINT,
  iduser INT,
  FOREIGN KEY (idpenerimaan) REFERENCES penerimaan(idpenerimaan) ON DELETE SET NULL,
  FOREIGN KEY (iduser) REFERENCES user(iduser) ON DELETE SET NULL
) ENGINE=InnoDB;

CREATE TABLE detail_returr (
  iddetail_returr BIGINT AUTO_INCREMENT PRIMARY KEY,
  idreturr BIGINT,
  iddetail_penerimaan BIGINT,
  jumlah INT,
  alasan VARCHAR(200),
  FOREIGN KEY (idreturr) REFERENCES returr(idreturr) ON DELETE CASCADE,
  FOREIGN KEY (iddetail_penerimaan) REFERENCES detail_penerimaan(iddetail_penerimaan) ON DELETE SET NULL
) ENGINE=InnoDB;



USE projectbasdat;

INSERT INTO satuan (nama_satuan, status) VALUES
('Pcs',1),('Box',1),('Kg',1),('Liter',1),('Pak',1),
('Roll',1),('Dus',1),('Pack',1),('Botol',1),('Sachet',1);

INSERT INTO vendor (nama_vendor, badan_hukum, status) VALUES
('PT Bersih Sejahtera','P','A'),
('CV Wangi Harum','C','A'),
('UD Sumber Rejah','U','A'),
('PT Grosir Makmur','P','A'),
('CV Toko Jaya','C','A'),
('CV Makmur Sentosa','C','A'),
('PT Sembako Nusantara','P','A'),
('UD Sinar Abadi','U','A'),
('PT Mitra Logistik','P','A'),
('CV Sejahtera Bersama','C','N');

INSERT INTO role (nama_role) VALUES
('Admin'),('Kasir'),('Gudang'),('Manager'),('Pembelian'),
('Keuangan'),('Karyawan'),('Owner'),('Supervisor'),('Maintenance');

INSERT INTO user (username,password,idrole) VALUES
('admin','12345',1),('kasir','12345',2),('gudang','12345',3),('purchasing','12345',5),
('finance','12345',6),('manager','12345',4),('owner','12345',8),('spv','12345',9),
('staff1','12345',7),('staff2','12345',7);

INSERT INTO barang (jenis, nama, idsatuan, status, harga_beli, harga_jual) VALUES
('A','Sabun Mandi',1,1,2500,3500),
('B','Sikat Gigi',1,1,2000,3000),
('A','Sampo 1L',4,1,15000,20000),
('A','Roti Tawar',2,1,8000,12000),
('B','Gula Pasir 1kg',3,1,9000,12000),
('A','Tepung Terigu 1kg',3,1,7000,10000),
('B','Minyak Goreng 1L',4,1,12000,15000),
('A','Susu Kental',2,1,10000,14000),
('A','Kopi Bubuk 250g',3,1,25000,32000),
('B','Teh Celup 25s',1,1,8000,11000),
('A','Deterjen Bubuk 1kg',3,1,18000,23000),
('B','Pasta Gigi',1,1,12000,17000),
('A','Telur 1kg',3,1,20000,24000),
('B','Beras 5kg',3,1,45000,60000),
('A','Mi Instan (pak 5)',2,1,15000,20000);

INSERT INTO stok (idbarang, jumlah) VALUES
(1,95),(2,90),(3,10),(4,50),(5,120),(6,80),(7,200),(8,75),(9,60),(10,40),
(11,30),(12,55),(13,90),(14,20),(15,150);


INSERT INTO pengadaan (kode_pengadaan, tanggal, idvendor, iduser, subtotal_nilai, ppn, total_nilai, status) VALUES
('PG001','2025-10-01',1,4,500000,0,500000,'C'),
('PG002','2025-10-02',2,4,300000,0,300000,'C'),
('PG003','2025-10-10',3,4,800000,0,800000,'O'),
('PG004','2025-10-12',4,4,450000,0,450000,'O');


INSERT INTO detail_pengadaan (idpengadaan, idbarang, jumlah, harga_satuan, subtotal, jumlah_terpenuhi) VALUES
(1,1,50,5000,250000,50),(1,2,50,5000,250000,50),
(2,3,30,10000,300000,30),
(3,4,100,8000,800000,0),(3,5,100,9000,900000,0),
(4,6,40,7000,280000,0),(4,7,20,12000,240000,0),
(3,8,30,10000,300000,0),(2,9,20,25000,500000,20);


INSERT INTO penerimaan (kode_penerimaan, created_at, status, idpengadaan, iduser) VALUES
('PN001','2025-10-03 09:00:00','C',1,3),
('PN002','2025-10-04 10:00:00','C',2,3),
('PN003','2025-10-11 11:00:00','O',3,3); 


INSERT INTO detail_penerimaan (idpenerimaan, idpengadaan, idbarang, jumlah, harga_satuan_terima, subtotal_terima) VALUES
(1,1,1,50,5000,250000),(1,1,2,50,5000,250000),
(2,2,3,30,10000,300000),
(3,3,4,60,8000,480000),(3,3,5,50,9000,450000);


INSERT INTO penjualan (kode_penjualan, created_at, subtotal_nilai, ppn, total_nilai, iduser) VALUES
('PJ001','2025-10-05 10:00:00',150000,0,150000,2),
('PJ002','2025-10-06 11:00:00',200000,0,200000,2),
('PJ003','2025-10-07 12:00:00',300000,0,300000,2);

INSERT INTO detail_penjualan (idpenjualan, idbarang, jumlah, harga, subtotal) VALUES
(1,1,5,10000,50000),(1,2,10,10000,100000),
(2,3,20,10000,200000),
(3,7,10,15000,150000),(3,15,10,15000,150000);

INSERT INTO margin_penjualan (persen, status, iduser) VALUES
(10.0,1,1),(5.0,1,2),(12.5,1,2),(8.0,1,3),(15.0,1,4),
(7.5,1,5),(9.0,1,6),(6.0,1,7),(11.0,1,8),(4.5,1,9);


INSERT INTO kartu_stok (idbarang, jenis_transaksi, masuk, keluar, stok, idtransaksi, keterangan) VALUES
(1,'I',50,0,95,1,'Initial penerimaan PG001'),
(2,'I',50,0,90,1,'Initial penerimaan PG001'),
(3,'I',30,0,10,2,'Initial penerimaan PG002');

INSERT INTO returr (created_at, idpenerimaan, iduser) VALUES
('2025-10-15 09:00:00',1,2),
('2025-10-16 10:00:00',2,2);

INSERT INTO detail_returr (idreturr, iddetail_penerimaan, jumlah, alasan) VALUES
(1,1,2,'Rusak'),(2,3,1,'Kedaluwarsa');

INSERT INTO stok (idbarang, jumlah)
VALUES
(4,60),(5,50)
ON DUPLICATE KEY UPDATE jumlah = VALUES(jumlah);


INSERT INTO stok (idbarang, jumlah) VALUES
(1,95) ON DUPLICATE KEY UPDATE jumlah=VALUES(jumlah);
INSERT INTO stok (idbarang, jumlah) VALUES
(2,90) ON DUPLICATE KEY UPDATE jumlah=VALUES(jumlah);
INSERT INTO stok (idbarang, jumlah) VALUES
(3,10) ON DUPLICATE KEY UPDATE jumlah=VALUES(jumlah);

ALTER TABLE detail_pengadaan ADD INDEX (idpengadaan), ADD INDEX (idbarang);
ALTER TABLE detail_penerimaan ADD INDEX (idpenerimaan), ADD INDEX (idpengadaan), ADD INDEX (idbarang);
ALTER TABLE detail_penjualan ADD INDEX (idpenjualan), ADD INDEX (idbarang);


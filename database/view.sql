

SHOW FULL TABLES IN hni WHERE Table_type = 'VIEW';

USE hni;

CREATE OR REPLACE VIEW v_barang AS
SELECT b.idbarang, b.nama, s.nama_satuan AS satuan, b.harga,
       IF(b.status = 1, 'Aktif', 'Tidak Aktif') AS status
FROM barang b
JOIN satuan s ON b.idsatuan = s.idsatuan;

CREATE OR REPLACE VIEW v_vendor AS
SELECT idvendor, nama_vendor, badan_hukum,
       IF(status = '1', 'Aktif', 'Tidak Aktif') AS status
FROM vendor;

CREATE OR REPLACE VIEW v_satuan AS
SELECT idsatuan, nama_satuan,
       IF(status = 1, 'Aktif', 'Tidak Aktif') AS status
FROM satuan;

CREATE OR REPLACE VIEW v_margin_penjualan AS
SELECT m.idmargin_penjualan, m.persen,
       IF(m.status = 1, 'Aktif', 'Tidak Aktif') AS status,
       u.username AS dibuat_oleh,
       m.created_at, m.updated_at
FROM margin_penjualan m
JOIN user u ON m.iduser = u.iduser;

CREATE OR REPLACE VIEW v_total_pengadaan_vendor AS
SELECT v.nama_vendor,
       IF(v.status = '1', 'Aktif', 'Tidak Aktif') AS status,
       COUNT(p.idpengadaan) AS jumlah_pengadaan,
       IFNULL(SUM(p.total_nilai), 0) AS total_nilai
FROM vendor v
LEFT JOIN pengadaan p ON p.vendor_idvendor = v.idvendor
GROUP BY v.idvendor, v.nama_vendor, v.status;

CREATE OR REPLACE VIEW v_kartu_stok AS
SELECT k.idkartu_stok, b.nama AS barang,
       CASE k.jenis_transaksi
            WHEN 'P' THEN 'Penerimaan'
            WHEN 'J' THEN 'Penjualan'
            WHEN 'R' THEN 'Retur'
       END AS transaksi,
       k.idtransaksi, k.masuk, k.keluar, k.stock, k.created_at
FROM kartu_stok k
JOIN barang b ON k.idbarang = b.idbarang;

USE hni;

CREATE OR REPLACE VIEW v_pengadaan AS
SELECT p.idpengadaan, p.`timestamp` AS tanggal, v.nama_vendor,
       u.username AS dibuat_oleh,
       IF(p.status = 'T', 'Tuntas', 'Pending') AS status,
       p.subtotal_nilai, p.ppn, p.total_nilai
FROM pengadaan p
JOIN vendor v ON p.vendor_idvendor = v.idvendor
JOIN user u ON p.user_iduser = u.iduser;

CREATE OR REPLACE VIEW v_penerimaan AS
SELECT pn.idpenerimaan, pn.created_at AS tanggal, pn.idpengadaan,
       v.nama_vendor, u.username AS penerima,
       IF(pn.status = 'T', 'Tuntas', 'Pending') AS status
FROM penerimaan pn
JOIN pengadaan p ON pn.idpengadaan = p.idpengadaan
JOIN vendor v ON p.vendor_idvendor = v.idvendor
JOIN user u ON pn.iduser = u.iduser;

DROP VIEW IF EXISTS v_vendor_aktif;

SELECT * FROM v_barang;
SELECT * FROM v_vendor;
SELECT * FROM v_satuan;
SELECT * FROM v_margin_penjualan;
SELECT * FROM v_total_pengadaan_vendor;

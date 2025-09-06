-- Database: klinik_orrimedika
-- Dibuat untuk sistem informasi klinik ORRIMEDIKA

CREATE DATABASE IF NOT EXISTS klinik_orrimedika;
USE klinik_orrimedika;

-- Tabel Admin
CREATE TABLE admin (
    id INT(11) AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    nama VARCHAR(100) NOT NULL,
    email VARCHAR(100),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

-- Tabel Dokter
CREATE TABLE dokter (
    id INT(11) AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    nama VARCHAR(100) NOT NULL,
    spesialisasi VARCHAR(100),
    no_sip VARCHAR(50), -- Surat Izin Praktik
    email VARCHAR(100),
    no_telp VARCHAR(20),
    alamat TEXT,
    status ENUM('aktif', 'tidak_aktif') DEFAULT 'aktif',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

-- Tabel Pasien
CREATE TABLE pasien (
    id INT(11) AUTO_INCREMENT PRIMARY KEY,
    no_rm VARCHAR(20) NOT NULL UNIQUE, -- Nomor Rekam Medis
    nama VARCHAR(100) NOT NULL,
    tempat_lahir VARCHAR(50),
    tanggal_lahir DATE,
    usia INT(3),
    jenis_kelamin ENUM('L', 'P') NOT NULL, -- L=Laki-laki, P=Perempuan
    alamat TEXT,
    no_telp VARCHAR(20),
    pekerjaan VARCHAR(50),
    agama VARCHAR(20),
    status_perkawinan ENUM('belum_kawin', 'kawin', 'janda', 'duda') DEFAULT 'belum_kawin',
    nama_wali VARCHAR(100), -- untuk pasien anak-anak
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

-- Tabel Pelayanan/Poli
CREATE TABLE pelayanan (
    id INT(11) AUTO_INCREMENT PRIMARY KEY,
    nama_pelayanan VARCHAR(100) NOT NULL,
    tarif DECIMAL(10,2) NOT NULL DEFAULT 0,
    keterangan TEXT,
    status ENUM('aktif', 'tidak_aktif') DEFAULT 'aktif',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

-- Tabel Jadwal Dokter
CREATE TABLE jadwal_dokter (
    id INT(11) AUTO_INCREMENT PRIMARY KEY,
    dokter_id INT(11) NOT NULL,
    pelayanan_id INT(11) NOT NULL,
    hari ENUM('senin', 'selasa', 'rabu', 'kamis', 'jumat', 'sabtu', 'minggu') NOT NULL,
    jam_mulai TIME NOT NULL,
    jam_selesai TIME NOT NULL,
    kuota INT(3) DEFAULT 20,
    status ENUM('aktif', 'tidak_aktif') DEFAULT 'aktif',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (dokter_id) REFERENCES dokter(id) ON DELETE CASCADE,
    FOREIGN KEY (pelayanan_id) REFERENCES pelayanan(id) ON DELETE CASCADE
);

-- Tabel Pendaftaran/Antrian
CREATE TABLE pendaftaran (
    id INT(11) AUTO_INCREMENT PRIMARY KEY,
    no_antrian VARCHAR(20) NOT NULL,
    pasien_id INT(11) NOT NULL,
    dokter_id INT(11) NOT NULL,
    pelayanan_id INT(11) NOT NULL,
    tanggal_daftar DATE NOT NULL,
    jam_daftar TIME DEFAULT NULL,
    keluhan TEXT,
    status ENUM('menunggu', 'hadir', 'sedang_dilayani', 'selesai', 'lunas', 'batal') DEFAULT 'menunggu',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (pasien_id) REFERENCES pasien(id) ON DELETE CASCADE,
    FOREIGN KEY (dokter_id) REFERENCES dokter(id) ON DELETE CASCADE,
    FOREIGN KEY (pelayanan_id) REFERENCES pelayanan(id) ON DELETE CASCADE
);

-- Tabel Pemeriksaan
CREATE TABLE pemeriksaan (
    id INT(11) AUTO_INCREMENT PRIMARY KEY,
    pendaftaran_id INT(11) NOT NULL,
    pasien_id INT(11) NOT NULL,
    dokter_id INT(11) NOT NULL,
    tanggal_periksa DATETIME NOT NULL,
    anamnesa TEXT, -- keluhan pasien
    pemeriksaan_fisik TEXT,
    diagnosa TEXT NOT NULL,
    terapi_pengobatan TEXT,
    catatan_tambahan TEXT,
    status ENUM('selesai', 'rujuk') DEFAULT 'selesai',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (pendaftaran_id) REFERENCES pendaftaran(id) ON DELETE CASCADE,
    FOREIGN KEY (pasien_id) REFERENCES pasien(id) ON DELETE CASCADE,
    FOREIGN KEY (dokter_id) REFERENCES dokter(id) ON DELETE CASCADE
);

-- Tabel Obat
CREATE TABLE obat (
    id INT(11) AUTO_INCREMENT PRIMARY KEY,
    kode_obat VARCHAR(20) NOT NULL UNIQUE,
    nama_obat VARCHAR(100) NOT NULL,
    jenis_obat ENUM('tablet', 'kapsul', 'sirup', 'injeksi', 'salep', 'lainnya') NOT NULL,
    satuan VARCHAR(20) DEFAULT 'buah',
    stok INT(11) DEFAULT 0,
    harga_satuan DECIMAL(10,2) DEFAULT 0,
    tanggal_kadaluarsa DATE,
    keterangan TEXT,
    status ENUM('tersedia', 'kosong', 'kadaluarsa') DEFAULT 'tersedia',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

-- Tabel Resep Obat
CREATE TABLE resep_obat (
    id INT(11) AUTO_INCREMENT PRIMARY KEY,
    pemeriksaan_id INT(11) NOT NULL,
    pasien_id INT(11) NOT NULL,
    dokter_id INT(11) NOT NULL,
    total_biaya DECIMAL(10,2) DEFAULT 0,
    status ENUM('belum_diambil', 'sudah_diambil') DEFAULT 'belum_diambil',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (pemeriksaan_id) REFERENCES pemeriksaan(id) ON DELETE CASCADE,
    FOREIGN KEY (pasien_id) REFERENCES pasien(id) ON DELETE CASCADE,
    FOREIGN KEY (dokter_id) REFERENCES dokter(id) ON DELETE CASCADE
);

-- Tabel Detail Resep Obat
CREATE TABLE detail_resep_obat (
    id INT(11) AUTO_INCREMENT PRIMARY KEY,
    resep_id INT(11) NOT NULL,
    obat_id INT(11) NOT NULL,
    jumlah INT(11) NOT NULL,
    aturan_pakai VARCHAR(100), -- contoh: 3x1 sehari setelah makan
    keterangan TEXT,
    harga_satuan DECIMAL(10,2) DEFAULT 0,
    subtotal DECIMAL(10,2) DEFAULT 0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (resep_id) REFERENCES resep_obat(id) ON DELETE CASCADE,
    FOREIGN KEY (obat_id) REFERENCES obat(id) ON DELETE CASCADE
);

-- Tabel Pembayaran
CREATE TABLE pembayaran (
    id INT(11) AUTO_INCREMENT PRIMARY KEY,
    pendaftaran_id INT(11) NOT NULL,
    pasien_id INT(11) NOT NULL,
    biaya_konsultasi DECIMAL(10,2) DEFAULT 0,
    biaya_obat DECIMAL(10,2) DEFAULT 0,
    biaya_tindakan DECIMAL(10,2) DEFAULT 0,
    total_biaya DECIMAL(10,2) NOT NULL,
    metode_pembayaran ENUM('tunai', 'transfer', 'kartu') DEFAULT 'tunai',
    status ENUM('belum_bayar', 'sudah_bayar') DEFAULT 'belum_bayar',
    tanggal_bayar DATETIME,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (pendaftaran_id) REFERENCES pendaftaran(id) ON DELETE CASCADE,
    FOREIGN KEY (pasien_id) REFERENCES pasien(id) ON DELETE CASCADE
);

-- Insert Data Demo

-- Data Admin
INSERT INTO admin (username, password, nama, email) VALUES
('admin', 'admin123', 'Administrator Sistem', 'admin@orrimedika.com'),
('admin2', 'admin123', 'Admin Kedua', 'admin2@orrimedika.com');

-- Data Dokter
INSERT INTO dokter (username, password, nama, spesialisasi, no_sip, email, no_telp, alamat) VALUES
('dr.sari', 'dokter123', 'Dr. Sari Indah Pertiwi, Sp.A', 'Dokter Anak', 'SIP/001/2023', 'dr.sari@orrimedika.com', '08123456789', 'Jl. Kesehatan No. 15'),
('dr.budi', 'dokter123', 'Dr. Budi Santoso, Sp.PD', 'Penyakit Dalam', 'SIP/002/2023', 'dr.budi@orrimedika.com', '08234567890', 'Jl. Sehat Selalu No. 22'),
('dr.ani', 'dokter123', 'Dr. Ani Rahayu, Sp.OG', 'Kandungan', 'SIP/003/2023', 'dr.ani@orrimedika.com', '08345678901', 'Jl. Medika No. 8'),
('dr.rudi', 'password123', 'Dr. Rudi Hartono, Sp.M', 'Mata', 'SIP/004/2023', 'dr.rudi@orrimedika.com', '08456789012', 'Jl. Mata Sehat No. 12'),
('dr.maya', 'password123', 'Dr. Maya Sari, Sp.THT', 'THT (Telinga Hidung Tenggorokan)', 'SIP/005/2023', 'dr.maya@orrimedika.com', '08567890123', 'Jl. THT Center No. 25');

-- Data Pelayanan
INSERT INTO pelayanan (nama_pelayanan, tarif, keterangan) VALUES
('Konsultasi Umum', 50000, 'Pemeriksaan kesehatan umum'),
('Konsultasi Anak', 75000, 'Pemeriksaan khusus anak-anak'),
('Konsultasi Kandungan', 100000, 'Pemeriksaan kandungan dan kebidanan'),
('Konsultasi Penyakit Dalam', 80000, 'Pemeriksaan penyakit dalam'),
('Medical Check Up', 200000, 'Pemeriksaan kesehatan menyeluruh');

-- Data Pasien
INSERT INTO pasien (no_rm, nama, tempat_lahir, tanggal_lahir, usia, jenis_kelamin, alamat, no_telp, pekerjaan, agama) VALUES
('RM001', 'Ahmad Rahman', 'Jakarta', '1995-05-15', 28, 'L', 'Jl. Merdeka No. 15, Jakarta', '08123456789', 'Karyawan Swasta', 'Islam'),
('RM002', 'Siti Nurhaliza', 'Bandung', '1988-08-20', 35, 'P', 'Jl. Pahlawan No. 22, Bandung', '08198765432', 'Guru', 'Islam'),
('RM003', 'Budi Santoso', 'Surabaya', '1981-12-10', 42, 'L', 'Jl. Sudirman No. 8, Surabaya', '08156789012', 'Wiraswasta', 'Kristen'),
('RM004', 'Maya Sari', 'Yogyakarta', '1992-03-25', 31, 'P', 'Jl. Malioboro No. 45, Yogyakarta', '08187654321', 'Dokter', 'Islam'),
('RM005', 'Andi Wijaya', 'Medan', '1999-07-08', 24, 'L', 'Jl. Veteran No. 12, Medan', '08134567890', 'Mahasiswa', 'Buddha');

-- Data Obat
INSERT INTO obat (kode_obat, nama_obat, jenis_obat, satuan, stok, harga_satuan, tanggal_kadaluarsa, keterangan) VALUES
('OBT001', 'Paracetamol 500mg', 'tablet', 'strip', 100, 5000, '2025-12-31', 'Obat penurun panas dan pereda nyeri'),
('OBT002', 'Amoxicillin 500mg', 'kapsul', 'strip', 50, 15000, '2025-10-15', 'Antibiotik'),
('OBT003', 'ORS (Oralit)', 'serbuk', 'sachet', 75, 2000, '2025-06-30', 'Pengganti cairan tubuh'),
('OBT004', 'Vitamin C 1000mg', 'tablet', 'strip', 30, 8000, '2025-08-20', 'Suplemen vitamin C'),
('OBT005', 'Betadine Solution', 'cair', 'botol', 25, 12000, '2025-09-15', 'Antiseptik');

-- Data Jadwal Dokter
INSERT INTO jadwal_dokter (dokter_id, pelayanan_id, hari, jam_mulai, jam_selesai, kuota) VALUES
(1, 2, 'senin', '08:00:00', '12:00:00', 20),
(1, 2, 'rabu', '08:00:00', '12:00:00', 20),
(1, 2, 'jumat', '08:00:00', '12:00:00', 20),
(2, 4, 'selasa', '08:00:00', '12:00:00', 15),
(2, 4, 'kamis', '08:00:00', '12:00:00', 15),
(3, 3, 'senin', '13:00:00', '17:00:00', 12),
(3, 3, 'rabu', '13:00:00', '17:00:00', 12);

-- Data Pendaftaran Demo (untuk hari ini)
INSERT INTO pendaftaran (no_antrian, pasien_id, dokter_id, pelayanan_id, tanggal_daftar, keluhan, status) VALUES
('A001', 1, 1, 2, CURDATE(), 'Demam dan batuk selama 3 hari', 'menunggu'),
('A002', 2, 2, 4, CURDATE(), 'Nyeri dada dan sesak napas', 'menunggu'),
('A003', 3, 1, 2, CURDATE(), 'Sakit kepala berkepanjangan', 'menunggu'),
('A004', 4, 3, 3, CURDATE(), 'Kontrol kehamilan rutin', 'menunggu'),
('A005', 5, 2, 4, CURDATE(), 'Diabetes kontrol', 'menunggu');

-- Data Pemeriksaan Demo (beberapa sudah selesai)
INSERT INTO pemeriksaan (pendaftaran_id, pasien_id, dokter_id, tanggal_periksa, anamnesa, pemeriksaan_fisik, diagnosa, terapi_pengobatan, catatan_tambahan, status) VALUES
(1, 1, 1, NOW() - INTERVAL 2 HOUR, 'Demam dan batuk selama 3 hari, tidak ada mual muntah', 'TD: 120/80, Suhu: 38.2°C, Nadi: 88x/menit', 'ISPA (Infeksi Saluran Pernapasan Atas)', 'Parasetamol 3x500mg, Amoxicillin 3x500mg', 'Istirahat cukup, minum air putih yang banyak', 'selesai'),
(2, 2, 2, NOW() - INTERVAL 1 HOUR, 'Nyeri dada dan sesak napas terutama saat beraktivitas', 'TD: 140/90, Nadi: 92x/menit, RR: 24x/menit', 'Hipertensi Grade 1', 'Captopril 2x25mg, Edukasi diet rendah garam', 'Kontrol tekanan darah rutin, olahraga ringan', 'selesai');

-- Data Resep Obat Demo
INSERT INTO resep_obat (pemeriksaan_id, pasien_id, dokter_id, total_biaya, status) VALUES
(1, 1, 1, 25000, 'belum_diambil'),
(2, 2, 2, 35000, 'belum_diambil');

-- Data Pembayaran Demo
INSERT INTO pembayaran (pendaftaran_id, pasien_id, biaya_konsultasi, biaya_obat, biaya_tindakan, total_biaya, metode_pembayaran, status, tanggal_bayar) VALUES
(1, 1, 75000, 25000, 0, 100000, 'tunai', 'sudah_bayar', NOW() - INTERVAL 30 MINUTE),
(2, 2, 80000, 35000, 15000, 130000, 'tunai', 'belum_bayar', NULL);

-- Password dalam bentuk plain text untuk kemudahan testing
-- Dalam produksi, sebaiknya gunakan password hashing

-- Index untuk performance
CREATE INDEX idx_pasien_nama ON pasien(nama);
CREATE INDEX idx_pasien_no_rm ON pasien(no_rm);
CREATE INDEX idx_pendaftaran_tanggal ON pendaftaran(tanggal_daftar);
CREATE INDEX idx_pemeriksaan_tanggal ON pemeriksaan(tanggal_periksa);
CREATE INDEX idx_obat_nama ON obat(nama_obat);
CREATE INDEX idx_obat_kode ON obat(kode_obat);
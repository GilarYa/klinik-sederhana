-- Database: klinik_sederhana
-- Buat database baru
CREATE DATABASE IF NOT EXISTS klinik_sederhana;
USE klinik_sederhana;

-- Tabel untuk users (admin dan dokter)
CREATE TABLE users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) UNIQUE NOT NULL,
    password VARCHAR(255) NOT NULL,
    role ENUM('admin', 'dokter') NOT NULL,
    nama_lengkap VARCHAR(100) NOT NULL,
    email VARCHAR(100),
    telepon VARCHAR(20),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

-- Tabel untuk data pasien
CREATE TABLE pasien (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nama_pasien VARCHAR(100) NOT NULL,
    usia INT NOT NULL,
    jenis_kelamin ENUM('PRIA', 'WANITA') NOT NULL,
    alamat TEXT NOT NULL,
    no_telp VARCHAR(20),
    pekerjaan VARCHAR(50),
    dokter_id INT,
    status_pemeriksaan ENUM('menunggu', 'sedang_diperiksa', 'selesai') DEFAULT 'menunggu',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (dokter_id) REFERENCES dokter(id) ON DELETE SET NULL
);

-- Tabel untuk data dokter
CREATE TABLE dokter (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT,
    nama_dokter VARCHAR(100) NOT NULL,
    spesialisasi VARCHAR(50),
    no_str VARCHAR(50),
    jadwal_praktek TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE SET NULL
);

-- Tabel untuk pemeriksaan
CREATE TABLE pemeriksaan (
    id INT AUTO_INCREMENT PRIMARY KEY,
    pasien_id INT NOT NULL,
    dokter_id INT NOT NULL,
    tanggal_periksa DATE NOT NULL,
    keluhan TEXT,
    diagnosis TEXT,
    tindakan TEXT,
    resep TEXT,
    biaya DECIMAL(10,2) DEFAULT 0,
    status ENUM('selesai', 'proses', 'batal') DEFAULT 'proses',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (pasien_id) REFERENCES pasien(id) ON DELETE CASCADE,
    FOREIGN KEY (dokter_id) REFERENCES dokter(id) ON DELETE CASCADE
);

-- Insert data default admin dan dokter
INSERT INTO users (username, password, role, nama_lengkap, email) VALUES
('admin', 'admin123', 'admin', 'Administrator', 'admin@klinik.com'),
('dokter1', 'dokter123', 'dokter', 'Dr. Ahmad Setiawan', 'dokter1@klinik.com');

-- Insert data dokter
INSERT INTO dokter (user_id, nama_dokter, spesialisasi, no_str) VALUES
(2, 'Dr. Ahmad Setiawan', 'Umum', 'STR-001-2023');

-- Insert data pasien sample
INSERT INTO pasien (nama_pasien, usia, jenis_kelamin, alamat, no_telp, pekerjaan, dokter_id, status_pemeriksaan) VALUES
('ahmad', 28, 'PRIA', 'jawa barat (jangan lupa subscribe)', '87677526272828', 'programer', 1, 'menunggu'),
('Ayu Wandari', 24, 'WANITA', 'Jl Sindangsari II RT 03 RW 14 No 18', '82240206', 'hoho', 1, 'selesai'),
('Booby', 15, 'PRIA', 'Jakarta', '0', 'balbam', 1, 'sedang_diperiksa'),
('Unur', 32, 'PRIA', 'Bandung', '08123456789', 'Wiraswasta', 1, 'menunggu');
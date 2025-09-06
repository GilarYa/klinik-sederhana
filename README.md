# ORRIMEDIKA - Sistem Informasi Klinik

Sistem informasi klinik sederhana berbasis web dengan PHP dan MySQL yang mendukung role-based access (Admin & Dokter).

## ✨ Fitur Utama

### 👨‍💼 **ADMIN**
- **Dashboard** dengan statistik pasien, dokter, dan janji temu
- **Data Pasien** - CRUD pasien + assign dokter + status real-time
- **Data Dokter** - CRUD dokter dengan username/password  
- **Pemeriksaan** - Melihat semua riwayat pemeriksaan dari semua dokter
- **Rekam Medis** - Input biaya → otomatis hilang dari daftar setelah lunas
- **📊 Detail Transaksi** - Modal detail lengkap dengan 👁️ ikon mata
- **🖨️ Cetak PDF** - Kwitansi pembayaran profesional

### 👨‍⚕️ **DOKTER**  
- **Dashboard** dengan informasi pasien hari ini
- **Data Pasien** - Hanya pasien yang ditugaskan dengan tombol **Hadir**
- **Pemeriksaan** - Menu terpisah untuk kelola pemeriksaan
- **Flow Pemeriksaan**: Hadir → Selesaikan (isi keluhan, diagnosis, resep) → Selesai

## 🚀 Instalasi

### 1. Prerequisites
- XAMPP (Apache + MySQL + PHP)
- Browser modern

### 2. Setup Database
1. Buka phpMyAdmin
2. Import file `database-klinik.sql`
3. Database `klinik_orrimedika` akan terbuat otomatis dengan data demo

### 3. Setup Files
1. Copy semua file ke folder `C:\xampp\htdocs\klinik-sederhana\`
2. Start Apache dan MySQL di XAMPP
3. Buka browser: `http://localhost/klinik-sederhana`

## 🔑 Login Demo

### 👨‍💼 Admin
- **Username:** `admin` | **Password:** `admin123`
- **Username:** `admin2` | **Password:** `admin123`

### 👨‍⚕️ Dokter
- **Username:** `dr.sari` | **Password:** `dokter123` (Sp.A - Dokter Anak)
- **Username:** `dr.budi` | **Password:** `dokter123` (Sp.PD - Penyakit Dalam)  
- **Username:** `dr.ani` | **Password:** `dokter123` (Sp.OG - Kandungan)
- **Username:** `dr.rudi` | **Password:** `password123` (Sp.M - Mata)
- **Username:** `dr.maya` | **Password:** `password123` (Sp.THT)

**💡 Catatan:** Password menggunakan plain text (tidak di-hash) untuk kemudahan testing

## 📊 Struktur Database

### Tabel Utama:
- `admin` - Data administrator
- `dokter` - Data dokter 
- `pasien` - Data pasien
- `pendaftaran` - Pendaftaran/antrian
- `pemeriksaan` - Hasil pemeriksaan dokter
- `pembayaran` - Data pembayaran
- `resep_obat` - Resep dari dokter
- `obat` - Master data obat

## 🔄 Flow Sistem Terbaru

### 👨‍⚕️ Untuk Dokter:
1. **Login** → Dashboard
2. **Data Pasien** → Lihat pasien hari ini yang ditugaskan
3. **Klik "Hadir"** → Konfirmasi kehadiran pasien 
4. **Menu "Pemeriksaan"** → Lihat daftar pasien siap diperiksa
5. **Klik "Selesaikan"** → Isi form pemeriksaan:
   - Keluhan yang ditangani
   - Diagnosis
   - Resep obat
6. **Simpan** → Status "Selesai Diperiksa" (tidak bisa edit lagi)

### 👨‍💼 Untuk Admin:
1. **Login** → Dashboard dengan statistik real-time
2. **Data Pasien**:
   - ➕ Tambah pasien baru + assign dokter
   - 📊 Lihat semua pasien dengan status real-time
   - 💰 Tombol "Bayar" muncul setelah dokter selesai periksa
3. **Rekam Medis**:
   - 💰 Klik "Bayar" → Input biaya (konsultasi, tindakan, obat)
   - ✅ Setelah bayar → Status "Lunas" + otomatis hilang dari daftar
   - 👁️ Klik "Detail" → Modal detail transaksi lengkap
   - 🖨️ Tombol "Cetak PDF" → Kwitansi pembayaran
4. **Data Dokter**: CRUD dokter + username/password
5. **Pemeriksaan**: Lihat semua riwayat dari semua dokter

## 📱 Responsive Design

- Desktop/laptop friendly
- Sidebar collapse di mobile
- Modal form responsive

## 🎨 UI Features

- Modern flat design
- Blue color scheme (#1976d2)
- FontAwesome icons
- Smooth transitions
- Modal forms

## 📁 File Structure

```
klinik-sederhana/
├── archive/                 # File HTML asli untuk referensi
├── config.php              # Konfigurasi database  
├── dashboard.php           # Halaman utama sistem (role-based)
├── database-klinik.sql    # File database lengkap + demo data
├── index.php              # Redirect ke login
├── login.php              # Halaman login (Admin/Dokter)
├── process.php            # Handler AJAX requests (semua operasi)
├── print_transaction.php  # Generator PDF kwitansi
└── README.md             # Dokumentasi lengkap
```

## 🔒 Keamanan

- Session management
- SQL injection protection dengan `mysqli_real_escape_string`
- Role-based access control
- Form validation
- **Password:** Plain text (untuk demo/testing) - *dalam produksi sebaiknya gunakan hashing*

## 🐛 Troubleshooting

### Database connection error:
- Pastikan MySQL aktif di XAMPP
- Cek konfigurasi di `config.php`

### Login gagal:
- Pastikan database sudah diimport
- Gunakan credentials demo yang tersedia

### Modal tidak muncul:
- Pastikan JavaScript aktif di browser
- Cek console browser untuk error

## ✨ Fitur Terbaru yang Sudah Ada

- ✅ **Role-based Dashboard** - UI berbeda untuk Admin vs Dokter
- ✅ **Real-time Status** - Badge status dinamis (Hadir, Diperiksa, Lunas)
- ✅ **Auto-hide Lunas** - Data otomatis hilang setelah pembayaran
- ✅ **Detail Transaksi** - Modal dengan ikon mata 👁️ 
- ✅ **Cetak PDF** - Kwitansi pembayaran profesional
- ✅ **Flow Dokter** - Hadir → Pemeriksaan → Selesaikan
- ✅ **Smart UI** - Data pasien berdasarkan assign dokter

## 💡 Pengembangan Selanjutnya

- [ ] Password hashing (MD5/bcrypt) 
- [ ] Laporan statistik bulanan/tahunan
- [ ] Backup database otomatis
- [ ] Email/SMS notification  
- [ ] API REST untuk mobile app
- [ ] Multi-branch/cabang support

---

**Dibuat dengan ❤️ untuk memudahkan pengelolaan klinik**
<?php
session_start();

// Cek apakah user sudah login
if (!isset($_SESSION['user'])) {
    header("Location: login.php");
    exit();
}

include_once 'config.php';

// Get user role
$user_role = $_SESSION['user']['role'];
$user_name = $_SESSION['user']['nama'];
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ORRIMEDIKA - Dashboard</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }
        
        body {
            background-color: #f5f7f9;
            color: #333;
        }
        
        .navbar {
            background-color: #1976d2;
            color: white;
            padding: 0 20px;
            height: 70px;
            display: flex;
            align-items: center;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
        }
        
        .nav-container {
            display: flex;
            justify-content: space-between;
            align-items: center;
            width: 100%;
        }
        
        .nav-logo {
            display: flex;
            align-items: center;
            font-size: 20px;
            font-weight: bold;
        }
        
        .nav-logo img {
            width: 40px;
            margin-right: 10px;
        }
        
        .nav-user {
            display: flex;
            align-items: center;
            gap: 10px;
        }
        
        .user-avatar {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            background-color: rgba(255, 255, 255, 0.2);
            display: flex;
            align-items: center;
            justify-content: center;
        }
        
        .logout-btn {
            background: none;
            border: 1px solid rgba(255, 255, 255, 0.3);
            color: white;
            padding: 5px 10px;
            border-radius: 4px;
            cursor: pointer;
            font-size: 12px;
            transition: background-color 0.3s;
        }
        
        .logout-btn:hover {
            background-color: rgba(255, 255, 255, 0.1);
        }
        
        .main-content {
            display: flex;
            min-height: calc(100vh - 70px);
        }
        
        .sidebar {
            width: 250px;
            background-color: white;
            box-shadow: 2px 0 10px rgba(0, 0, 0, 0.05);
        }
        
        .sidebar-menu {
            padding: 20px 0;
        }
        
        .menu-header {
            padding: 15px 20px;
            font-size: 12px;
            font-weight: bold;
            color: #78909c;
            text-transform: uppercase;
            letter-spacing: 1px;
        }
        
        .menu-item {
            display: flex;
            align-items: center;
            padding: 12px 20px;
            color: #546e7a;
            text-decoration: none;
            transition: background-color 0.3s;
        }
        
        .menu-item i {
            margin-right: 10px;
            width: 20px;
            text-align: center;
        }
        
        .menu-item:hover, .menu-item.active {
            background-color: #e3f2fd;
            color: #1976d2;
            border-left: 4px solid #1976d2;
        }
        
        .content-area {
            flex: 1;
            padding: 20px;
        }
        
        .page-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 30px;
            padding-bottom: 15px;
            border-bottom: 1px solid #e0e0e0;
        }
        
        .page-title {
            font-size: 28px;
            color: #37474f;
        }
        
        .breadcrumb {
            color: #78909c;
            font-size: 14px;
        }
        
        .card {
            background-color: white;
            border-radius: 10px;
            overflow: hidden;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.05);
            margin-bottom: 20px;
        }
        
        .card-header {
            padding: 15px 20px;
            border-bottom: 1px solid #eee;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        
        .card-header h3 {
            font-size: 18px;
            color: #37474f;
        }
        
        .btn {
            padding: 8px 16px;
            border-radius: 5px;
            border: none;
            cursor: pointer;
            font-weight: 500;
            transition: background-color 0.3s;
        }
        
        .btn-primary {
            background-color: #1976d2;
            color: white;
        }
        
        .btn-primary:hover {
            background-color: #1565c0;
        }
        
        .card-body {
            padding: 20px;
        }
        
        .stats-cards {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(240px, 1fr));
            gap: 20px;
            margin-bottom: 30px;
        }
        
        .stat-card {
            background-color: white;
            border-radius: 10px;
            padding: 20px;
            display: flex;
            align-items: center;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.05);
        }
        
        .stat-icon {
            width: 60px;
            height: 60px;
            border-radius: 10px;
            background-color: #e3f2fd;
            color: #1976d2;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 24px;
            margin-right: 15px;
        }
        
        .stat-info h3 {
            font-size: 28px;
            color: #37474f;
            margin-bottom: 5px;
        }
        
        .stat-info p {
            color: #78909c;
            font-size: 14px;
        }
        
        .content-section {
            display: none;
        }
        
        .content-section.active {
            display: block;
        }
        
        .welcome-section {
            text-align: center;
            margin-bottom: 40px;
        }
        
        .welcome-title {
            font-size: 36px;
            color: #1976d2;
            margin-bottom: 15px;
        }
        
        .welcome-subtitle {
            font-size: 18px;
            color: #78909c;
            margin-bottom: 30px;
        }
        
        .vision-mission {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 30px;
            margin-bottom: 40px;
        }
        
        .vision-card, .mission-card {
            background-color: white;
            border-radius: 10px;
            padding: 25px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.05);
        }
        
        .vision-card h3, .mission-card h3 {
            color: #1976d2;
            margin-bottom: 20px;
            padding-bottom: 10px;
            border-bottom: 2px solid #e3f2fd;
        }
        
        .vision-text {
            font-size: 18px;
            font-weight: 500;
            color: #37474f;
            line-height: 1.6;
        }
        
        .mission-list {
            list-style-type: none;
        }
        
        .mission-list li {
            margin-bottom: 15px;
            padding-left: 25px;
            position: relative;
            line-height: 1.6;
        }
        
        .mission-list li:before {
            content: "•";
            color: #1976d2;
            font-weight: bold;
            position: absolute;
            left: 0;
            font-size: 20px;
        }

        .data-table-container {
            overflow-x: auto;
            -webkit-overflow-scrolling: touch;
            border: 1px solid #ddd;
            border-radius: 5px;
        }
        
        /* Styling khusus untuk container tabel pasien dengan scroll yang terlihat */
        .data-table-container.patients-scroll {
            max-width: 100%;
            border: 2px solid #1976d2;
            border-radius: 8px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }
        
        /* Custom scrollbar untuk tabel pasien */
        .data-table-container.patients-scroll::-webkit-scrollbar {
            height: 12px;
        }
        
        .data-table-container.patients-scroll::-webkit-scrollbar-track {
            background: #f1f1f1;
            border-radius: 6px;
        }
        
        .data-table-container.patients-scroll::-webkit-scrollbar-thumb {
            background: #1976d2;
            border-radius: 6px;
            border: 2px solid #f1f1f1;
        }
        
        .data-table-container.patients-scroll::-webkit-scrollbar-thumb:hover {
            background: #1565c0;
        }
        
        /* Styling khusus untuk tabel pasien */
        #patients-table td {
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
            padding: 8px 6px;
            vertical-align: middle;
            font-size: 13px;
        }
        
        #patients-table th {
            white-space: nowrap;
            padding: 10px 6px;
            font-size: 12px;
            text-align: center;
            background-color: #f8f9fa;
        }
        
        .data-table {
            width: 100%;
            border-collapse: collapse;
        }
        
        .data-table th, .data-table td {
            padding: 12px 15px;
            text-align: left;
            border-bottom: 1px solid #eee;
        }
        
        .data-table th {
            background-color: #f9f9f9;
            font-weight: 600;
            color: #546e7a;
        }
        
        .data-table tr:hover {
            background-color: #f5f7f9;
        }
        
        .table-actions {
            display: flex;
            gap: 10px;
        }
        
        .btn-action {
            padding: 5px 10px;
            border-radius: 4px;
            font-size: 12px;
            border: none;
            cursor: pointer;
        }
        
        .btn-edit {
            background-color: #ff9800;
            color: white;
        }
        
        .btn-delete {
            background-color: #f44336;
            color: white;
        }

        .badge {
            padding: 4px 8px;
            border-radius: 12px;
            font-size: 11px;
            font-weight: 500;
        }

        .badge-success {
            background-color: #4caf50;
            color: white;
        }

        .badge-warning {
            background-color: #ff9800;
            color: white;
        }

        .badge-info {
            background-color: #2196f3;
            color: white;
        }

        .badge-primary {
            background-color: #1976d2;
            color: white;
        }

        .badge-secondary {
            background-color: #757575;
            color: white;
        }

        .modal {
            display: none;
            position: fixed;
            z-index: 1000;
            left: 0;
            top: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(0,0,0,0.5);
        }

        .modal-content {
            background-color: white;
            margin: 5% auto;
            padding: 20px;
            border-radius: 10px;
            width: 90%;
            max-width: 600px;
            max-height: 80vh;
            overflow-y: auto;
        }

        .modal-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 20px;
            padding-bottom: 15px;
            border-bottom: 1px solid #eee;
        }

        .modal-header h3 {
            color: #37474f;
        }

        .close {
            font-size: 28px;
            font-weight: bold;
            cursor: pointer;
            color: #999;
        }

        .close:hover {
            color: #333;
        }

        .form-row {
            display: flex;
            gap: 15px;
            margin-bottom: 15px;
        }

        .form-group {
            flex: 1;
        }

        .form-group label {
            display: block;
            margin-bottom: 5px;
            font-weight: 500;
            color: #37474f;
        }

        .form-group input,
        .form-group select,
        .form-group textarea {
            width: 100%;
            padding: 10px;
            border: 1px solid #ddd;
            border-radius: 4px;
            font-size: 14px;
        }

        .form-group textarea {
            resize: vertical;
            min-height: 80px;
        }

        .btn-save {
            background-color: #4caf50;
            color: white;
            padding: 10px 20px;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            font-size: 14px;
        }

        .btn-save:hover {
            background-color: #45a049;
        }

        .btn-cancel {
            background-color: #757575;
            color: white;
            padding: 10px 20px;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            font-size: 14px;
            margin-right: 10px;
        }

        .btn-cancel:hover {
            background-color: #616161;
        }
        
        @media (max-width: 768px) {
            .sidebar {
                width: 70px;
            }
            
            .menu-item span {
                display: none;
            }
            
            .page-header {
                flex-direction: column;
                align-items: flex-start;
                gap: 10px;
            }
            
            .vision-mission {
                grid-template-columns: 1fr;
            }
        }
    </style>
</head>
<body>
    <nav class="navbar">
        <div class="nav-container">
            <div class="nav-logo">
                <img src="data:image/svg+xml;base64,PHN2ZyB4bWxucz0iaHR0cDovL3d3dy53My5vcmcvMjAwMC9zdmciIHZpZXdCb3g9IjAgMCA1MTIgNTEyIj48cGF0aCBmaWxsPSJ3aGl0ZSIgZD0iTTMyMCA0NDhoLTEyOFY2NGgxMjh2Mzg0ek00MTYgMjU2aC0xMjhWNjRoMTI4djE5MnpNOTYgNDQ4VjY0aDEyOHYzODRIOTZ6Ii8+PC9zdmc+" alt="Logo Puskesmas">
                <span>ORRIMEDIKA</span>
            </div>
            
            <div class="nav-user">
                <span><?php echo $user_name . ' (' . ucfirst($user_role) . ')'; ?></span>
                <div class="user-avatar">
                    <i class="fas fa-user"></i>
                </div>
                <button onclick="logout()" class="logout-btn">Keluar</button>
            </div>
        </div>
    </nav>

    <main class="main-content">
        <div class="sidebar">
            <div class="sidebar-menu">
                <div class="menu-header">MENU UTAMA</div>
                <a href="#" class="menu-item active" data-target="dashboard">
                    <i class="fas fa-home"></i>
                    <span>Dashboard</span>
                </a>
                <?php if ($user_role === 'admin'): ?>
                <a href="#" class="menu-item" data-target="patients">
                    <i class="fas fa-user-injured"></i>
                    <span>Data Pasien</span>
                </a>
                <?php endif; ?>
                <?php if ($user_role === 'dokter'): ?>
                <a href="#" class="menu-item" data-target="doctor-examination">
                    <i class="fas fa-stethoscope"></i>
                    <span>Pemeriksaan</span>
                </a>
                <?php endif; ?>
                <?php if ($user_role === 'admin'): ?>
                <a href="#" class="menu-item" data-target="examination">
                    <i class="fas fa-stethoscope"></i>
                    <span>Pemeriksaan</span>
                </a>
                <a href="#" class="menu-item" data-target="doctors">
                    <i class="fas fa-user-md"></i>
                    <span>Data Dokter</span>
                </a>
                <a href="#" class="menu-item" data-target="medical-records">
                    <i class="fas fa-file-medical"></i>
                    <span>Rekam Medis</span>
                </a>
                <?php endif; ?>
            </div>
        </div>

        <div class="content-area">
            <!-- Dashboard/Home Section -->
            <div id="dashboard" class="content-section active">
                <div class="welcome-section">
                    <h1 class="welcome-title">Selamat Datang di ORRIMEDIKA</h1>
                    <p class="welcome-subtitle">Sistem Informasi Layanan Kesehatan Berbasis Web</p>
                </div>


                <div class="vision-mission">
                    <div class="vision-card">
                        <h3>VISI</h3>
                        <p class="vision-text">Menjadi Klinik Terkemuka Dengan Biaya Ekonomis</p>
                    </div>
                    
                    <div class="mission-card">
                        <h3>MISI</h3>
                        <ul class="mission-list">
                            <li>Memberikan pelayanan kepada masyarakat yang membutuhkan layanan kesehatan dengan biaya ekonomis.</li>
                            <li>Melakukan kegiatan usaha yang menunjang kebijakan dan program pemerintah di bidang kesehatan, seperti kegiatan bakti sosial bekerja sama dengan lembaga-lembaga tertentu.</li>
                        </ul>
                    </div>
                </div>
            </div>

            <!-- Patients Section (Admin Only) -->
            <?php if ($user_role === 'admin'): ?>
            <div id="patients" class="content-section">
                <div class="page-header">
                    <div>
                        <h1 class="page-title">Data Pasien</h1>
                        <div class="breadcrumb">
                            <span>Home</span> / <span>Data Pasien</span>
                        </div>
                    </div>
                    <button class="btn btn-primary" onclick="showAddPatientModal()">
                        <i class="fas fa-plus"></i> Tambah Pasien
                    </button>
                </div>

                <div class="card">
                    <div class="card-header">
                        <h3>DAFTAR PASIEN</h3>
                        <p style="margin: 0; font-size: 12px; color: #666; margin-top: 5px;">
                            <i class="fas fa-info-circle"></i> Geser tabel ke kanan/kiri untuk melihat kolom lainnya
                        </p>
                    </div>
                    <div class="card-body">
                        <div class="data-table-container patients-scroll">
                            <table class="data-table" id="patients-table" style="min-width: 1300px; width: max-content;">
                                <thead>
                                    <tr>
                                        <th style="width: 40px;">No.</th>
                                        <th style="width: 90px;">No. RM</th>
                                        <th style="width: 130px;">Nama</th>
                                        <th style="width: 100px;">T. Lahir</th>
                                        <th style="width: 85px;">Tgl Lahir</th>
                                        <th style="width: 50px;">Usia</th>
                                        <th style="width: 40px;">JK</th>
                                        <th style="width: 150px;">Alamat</th>
                                        <th style="width: 100px;">No Telp</th>
                                        <th style="width: 90px;">Pekerjaan</th>
                                        <th style="width: 70px;">Agama</th>
                                        <th style="width: 80px;">Status</th>
                                        <th style="width: 120px;">Dokter</th>
                                        <th style="width: 85px;">Tgl Daftar</th>
                                        <th style="width: 80px;">Status</th>
                                        <th style="width: 120px;">Aksi</th>
                                    </tr>
                                </thead>
                                <tbody id="patients-tbody">
                                    <?php
                                    // Admin melihat SEMUA pasien dengan informasi lengkap sesuai database
                                    $query = "SELECT p.id, p.no_rm, p.nama, p.tempat_lahir, p.tanggal_lahir, p.usia, 
                                             p.jenis_kelamin, p.alamat, p.no_telp, p.pekerjaan, p.agama, 
                                             p.status_perkawinan, p.created_at as tgl_daftar_pasien,
                                             d.nama as dokter_nama, d.spesialisasi,
                                             pd.tanggal_daftar, pd.status as status_pendaftaran,
                                             pd.id as pendaftaran_id,
                                             CASE 
                                                WHEN pb.status = 'sudah_bayar' THEN 'Lunas' 
                                                WHEN pe.id IS NOT NULL THEN 'Sudah Diperiksa'
                                                WHEN pd.status = 'hadir' THEN 'Hadir'
                                                WHEN pd.status = 'menunggu' THEN 'Menunggu'
                                                WHEN pd.id IS NOT NULL THEN 'Terdaftar'
                                                ELSE 'Belum Terdaftar'
                                             END as status_display,
                                             pb.total_biaya,
                                             pe.id as pemeriksaan_id
                                             FROM pasien p 
                                             LEFT JOIN pendaftaran pd ON p.id = pd.pasien_id 
                                             LEFT JOIN dokter d ON pd.dokter_id = d.id 
                                             LEFT JOIN pemeriksaan pe ON pd.id = pe.pendaftaran_id
                                             LEFT JOIN pembayaran pb ON pd.id = pb.pendaftaran_id
                                             ORDER BY p.created_at DESC";
                                    
                                    $result = mysqli_query($conn, $query);
                                    $no = 1;
                                    
                                    if ($result && mysqli_num_rows($result) > 0) {
                                        while($row = mysqli_fetch_assoc($result)):
                                    ?>
                                    <tr>
                                        <td><?php echo $no++; ?></td>
                                        <td><strong><?php echo htmlspecialchars($row['no_rm']); ?></strong></td>
                                        <td title="<?php echo htmlspecialchars($row['nama']); ?>">
                                            <?php echo htmlspecialchars(substr($row['nama'], 0, 20)) . (strlen($row['nama']) > 20 ? '...' : ''); ?>
                                        </td>
                                        <td title="<?php echo htmlspecialchars($row['tempat_lahir'] ?? ''); ?>">
                                            <?php 
                                            $tempat = $row['tempat_lahir'] ?? '-';
                                            echo htmlspecialchars(substr($tempat, 0, 15)) . (strlen($tempat) > 15 ? '...' : ''); 
                                            ?>
                                        </td>
                                        <td><?php echo $row['tanggal_lahir'] ? date('d/m/Y', strtotime($row['tanggal_lahir'])) : '-'; ?></td>
                                        <td><?php echo $row['usia']; ?></td>
                                        <td><?php echo $row['jenis_kelamin'] === 'L' ? 'L' : 'P'; ?></td>
                                        <td title="<?php echo htmlspecialchars($row['alamat']); ?>">
                                            <?php echo htmlspecialchars(substr($row['alamat'], 0, 25)) . (strlen($row['alamat']) > 25 ? '...' : ''); ?>
                                        </td>
                                        <td><?php echo htmlspecialchars(substr($row['no_telp'] ?? '-', 0, 12)); ?></td>
                                        <td title="<?php echo htmlspecialchars($row['pekerjaan'] ?? ''); ?>">
                                            <?php 
                                            $pekerjaan = $row['pekerjaan'] ?? '-';
                                            echo htmlspecialchars(substr($pekerjaan, 0, 12)) . (strlen($pekerjaan) > 12 ? '...' : ''); 
                                            ?>
                                        </td>
                                        <td title="<?php echo htmlspecialchars($row['agama'] ?? ''); ?>">
                                            <?php 
                                            $agama = $row['agama'] ?? '-';
                                            echo htmlspecialchars(substr($agama, 0, 8)) . (strlen($agama) > 8 ? '...' : ''); 
                                            ?>
                                        </td>
                                        <td>
                                            <?php 
                                            $status_kawin = $row['status_perkawinan'] ?? 'belum_kawin';
                                            switch($status_kawin) {
                                                case 'belum_kawin': echo 'Belum'; break;
                                                case 'kawin': echo 'Kawin'; break;
                                                case 'janda': echo 'Janda'; break;
                                                case 'duda': echo 'Duda'; break;
                                                default: echo '-';
                                            }
                                            ?>
                                        </td>
                                        <td title="<?php echo htmlspecialchars($row['dokter_nama'] ?? ''); ?>">
                                            <?php if (isset($row['dokter_nama']) && !empty($row['dokter_nama'])): ?>
                                                <?php echo htmlspecialchars(substr($row['dokter_nama'], 0, 15)) . (strlen($row['dokter_nama']) > 15 ? '...' : ''); ?>
                                            <?php else: ?>
                                                <span class="badge badge-warning" style="font-size: 10px;">Belum</span>
                                            <?php endif; ?>
                                        </td>
                                        <td>
                                            <?php if ($row['tanggal_daftar']): ?>
                                                <?php echo date('d/m/Y', strtotime($row['tanggal_daftar'])); ?>
                                            <?php else: ?>
                                                <span class="badge badge-warning" style="font-size: 10px;">Belum</span>
                                            <?php endif; ?>
                                        </td>
                                        <td>
                                            <?php 
                                            $badgeClass = '';
                                            switch($row['status_display']) {
                                                case 'Lunas': $badgeClass = 'badge-success'; break;
                                                case 'Sudah Diperiksa': $badgeClass = 'badge-primary'; break;
                                                case 'Hadir': $badgeClass = 'badge-info'; break;
                                                case 'Menunggu': $badgeClass = 'badge-warning'; break;
                                                case 'Terdaftar': $badgeClass = 'badge-secondary'; break;
                                                case 'Belum Terdaftar': $badgeClass = 'badge-light'; break;
                                                default: $badgeClass = 'badge-secondary';
                                            }
                                            ?>
                                            <span class="badge <?php echo $badgeClass; ?>">
                                                <?php echo $row['status_display']; ?>
                                            </span>
                                        </td>
                                        <td class="table-actions">
                                            <?php if ($row['status_display'] === 'Sudah Diperiksa' && !$row['total_biaya']): ?>
                                            <button class="btn-action btn-primary" onclick="showPaymentModal(<?php echo $row['pemeriksaan_id']; ?>, '<?php echo htmlspecialchars($row['nama']); ?>')" title="Input pembayaran">
                                                <i class="fas fa-money-bill"></i> Bayar
                                            </button>
                                            <?php elseif ($row['status_display'] === 'Lunas'): ?>
                                            <button class="btn-action btn-info" onclick="showTransactionDetail(<?php echo $row['pemeriksaan_id']; ?>)" title="Lihat detail transaksi">
                                                <i class="fas fa-eye"></i> Detail
                                            </button>
                                            <?php else: ?>
                                            <button class="btn-action btn-edit" onclick="editPatient(<?php echo $row['id']; ?>)" title="Edit pasien">
                                                <i class="fas fa-edit"></i>
                                            </button>
                                            <button class="btn-action btn-delete" onclick="deletePatient(<?php echo $row['id']; ?>)" title="Hapus pasien">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                            <?php endif; ?>
                                        </td>
                                    </tr>
                                    <?php 
                                        endwhile; 
                                    } else {
                                    ?>
                                    <tr>
                                        <td colspan="11" style="text-align: center; padding: 20px; color: #999;">
                                            <i class="fas fa-info-circle"></i> Belum ada data pasien
                                        </td>
                                    </tr>
                                    <?php } ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
            <?php endif; ?>

            <!-- Doctor Examination Section (Dokter Only) -->
            <?php if ($user_role === 'dokter'): ?>
            <div id="doctor-examination" class="content-section">
                <div class="page-header">
                    <h1 class="page-title">Pemeriksaan Pasien</h1>
                    <div class="breadcrumb">
                        <span>Home</span> / <span>Pemeriksaan</span>
                    </div>
                </div>
                <div class="card">
                    <div class="card-header">
                        <h3>Daftar Pasien Untuk Pemeriksaan</h3>
                    </div>
                    <div class="card-body">
                        <div class="data-table-container">
                            <table class="data-table">
                                <thead>
                                    <tr>
                                        <th>No.</th>
                                        <th>No. RM</th>
                                        <th>Nama Pasien</th>
                                        <th>Usia</th>
                                        <th>Keluhan Awal</th>
                                        <th>Status</th>
                                        <th>Aksi</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php
                                    $dokter_id = $_SESSION['user']['id'];
                                    $exam_query = "SELECT p.*, pd.id as pendaftaran_id, pd.keluhan, pd.status as pendaftaran_status,
                                                   CASE 
                                                      WHEN pe.id IS NOT NULL THEN 'Selesai Diperiksa' 
                                                      WHEN pd.status = 'hadir' THEN 'Siap Diperiksa'
                                                      ELSE 'Menunggu Kedatangan' 
                                                   END as status_pemeriksaan,
                                                   pe.id as pemeriksaan_id
                                                   FROM pasien p 
                                                   INNER JOIN pendaftaran pd ON p.id = pd.pasien_id 
                                                   LEFT JOIN pemeriksaan pe ON pd.id = pe.pendaftaran_id
                                                   WHERE pd.dokter_id = $dokter_id AND pd.tanggal_daftar = CURDATE()
                                                   ORDER BY pd.created_at ASC";
                                    $exam_result = mysqli_query($conn, $exam_query);
                                    $exam_no = 1;
                                    while($exam_row = mysqli_fetch_assoc($exam_result)):
                                    ?>
                                    <tr>
                                        <td><?php echo $exam_no++; ?></td>
                                        <td><?php echo $exam_row['no_rm']; ?></td>
                                        <td><?php echo htmlspecialchars($exam_row['nama']); ?></td>
                                        <td><?php echo $exam_row['usia']; ?> tahun</td>
                                        <td><?php echo htmlspecialchars(substr($exam_row['keluhan'], 0, 50)) . (strlen($exam_row['keluhan']) > 50 ? '...' : ''); ?></td>
                                        <td>
                                            <?php 
                                            $examBadgeClass = '';
                                            switch($exam_row['status_pemeriksaan']) {
                                                case 'Selesai Diperiksa': $examBadgeClass = 'badge-success'; break;
                                                case 'Siap Diperiksa': $examBadgeClass = 'badge-info'; break;
                                                default: $examBadgeClass = 'badge-warning';
                                            }
                                            ?>
                                            <span class="badge <?php echo $examBadgeClass; ?>">
                                                <?php echo $exam_row['status_pemeriksaan']; ?>
                                            </span>
                                        </td>
                                        <td class="table-actions">
                                            <?php if ($exam_row['status_pemeriksaan'] === 'Menunggu Kedatangan'): ?>
                                            <button class="btn-action btn-success" onclick="markAttendance(<?php echo $exam_row['pendaftaran_id']; ?>)">
                                                <i class="fas fa-check"></i> Hadir
                                            </button>
                                            <?php elseif ($exam_row['status_pemeriksaan'] === 'Siap Diperiksa'): ?>
                                            <button class="btn-action btn-primary" onclick="showExaminationForm(<?php echo $exam_row['pendaftaran_id']; ?>, <?php echo $exam_row['id']; ?>)">
                                                <i class="fas fa-stethoscope"></i> Selesaikan
                                            </button>
                                            <?php else: ?>
                                            <span class="badge badge-success">
                                                <i class="fas fa-check"></i> Selesai
                                            </span>
                                            <?php endif; ?>
                                        </td>
                                    </tr>
                                    <?php endwhile; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
            <?php endif; ?>

            <!-- Other sections -->
            <!-- Examination Section (Admin Only) -->
            <?php if ($user_role === 'admin'): ?>
            <div id="examination" class="content-section">
                <div class="page-header">
                    <h1 class="page-title">Data Pemeriksaan</h1>
                    <div class="breadcrumb">
                        <span>Home</span> / <span>Pemeriksaan</span>
                    </div>
                </div>
                <div class="card">
                    <div class="card-header">
                        <h3>Riwayat Pemeriksaan</h3>
                    </div>
                    <div class="card-body">
                        <div class="data-table-container">
                            <table class="data-table">
                                <thead>
                                    <tr>
                                        <th>Tanggal</th>
                                        <th>No. RM</th>
                                        <th>Nama Pasien</th>
                                        <th>Dokter</th>
                                        <th>Diagnosis</th>
                                        <th>Status Bayar</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php
                                    $exam_query = "SELECT pe.*, p.no_rm, p.nama as pasien_nama, d.nama as dokter_nama,
                                                   COALESCE(pb.status, 'belum_bayar') as status_bayar
                                                   FROM pemeriksaan pe 
                                                   JOIN pasien p ON pe.pasien_id = p.id 
                                                   JOIN dokter d ON pe.dokter_id = d.id 
                                                   LEFT JOIN pembayaran pb ON pe.pendaftaran_id = pb.pendaftaran_id
                                                   ORDER BY pe.tanggal_periksa DESC";
                                    $exam_result = mysqli_query($conn, $exam_query);
                                    while($exam = mysqli_fetch_assoc($exam_result)):
                                    ?>
                                    <tr>
                                        <td><?php echo date('d/m/Y H:i', strtotime($exam['tanggal_periksa'])); ?></td>
                                        <td><?php echo $exam['no_rm']; ?></td>
                                        <td><?php echo htmlspecialchars($exam['pasien_nama']); ?></td>
                                        <td><?php echo htmlspecialchars($exam['dokter_nama']); ?></td>
                                        <td><?php echo htmlspecialchars($exam['diagnosa']); ?></td>
                                        <td>
                                            <?php if ($exam['status_bayar'] === 'sudah_bayar'): ?>
                                                <span class="badge badge-success">Lunas</span>
                                            <?php else: ?>
                                                <span class="badge badge-warning">Belum Bayar</span>
                                            <?php endif; ?>
                                        </td>
                                    </tr>
                                    <?php endwhile; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Doctors Section (Admin Only) -->
            <div id="doctors" class="content-section">
                <div class="page-header">
                    <div>
                        <h1 class="page-title">Data Dokter</h1>
                        <div class="breadcrumb">
                            <span>Home</span> / <span>Data Dokter</span>
                        </div>
                    </div>
                    <button class="btn btn-primary" onclick="showAddDoctorModal()">
                        <i class="fas fa-plus"></i> Tambah Dokter
                    </button>
                </div>
                <div class="card">
                    <div class="card-header">
                        <h3>Daftar Dokter</h3>
                    </div>
                    <div class="card-body">
                        <div class="data-table-container">
                            <table class="data-table">
                                <thead>
                                    <tr>
                                        <th>Nama Dokter</th>
                                        <th>Spesialisasi</th>
                                        <th>No. SIP</th>
                                        <th>Email</th>
                                        <th>No. Telepon</th>
                                        <th>Status</th>
                                        <th>Aksi</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php
                                    $doctor_query = "SELECT * FROM dokter ORDER BY nama ASC";
                                    $doctor_result = mysqli_query($conn, $doctor_query);
                                    while($doctor = mysqli_fetch_assoc($doctor_result)):
                                    ?>
                                    <tr>
                                        <td><?php echo htmlspecialchars($doctor['nama']); ?></td>
                                        <td><?php echo htmlspecialchars($doctor['spesialisasi']); ?></td>
                                        <td><?php echo htmlspecialchars($doctor['no_sip']); ?></td>
                                        <td><?php echo htmlspecialchars($doctor['email']); ?></td>
                                        <td><?php echo htmlspecialchars($doctor['no_telp']); ?></td>
                                        <td>
                                            <span class="badge <?php echo $doctor['status'] === 'aktif' ? 'badge-success' : 'badge-warning'; ?>">
                                                <?php echo ucfirst($doctor['status']); ?>
                                            </span>
                                        </td>
                                        <td class="table-actions">
                                            <button class="btn-action btn-edit" onclick="editDoctor(<?php echo $doctor['id']; ?>)">
                                                <i class="fas fa-edit"></i>
                                            </button>
                                            <button class="btn-action btn-delete" onclick="deleteDoctor(<?php echo $doctor['id']; ?>)">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </td>
                                    </tr>
                                    <?php endwhile; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Medical Records Section (Admin Only) -->
            <div id="medical-records" class="content-section">
                <div class="page-header">
                    <h1 class="page-title">Rekam Medis</h1>
                    <div class="breadcrumb">
                        <span>Home</span> / <span>Rekam Medis</span>
                    </div>
                </div>
                <div class="card">
                    <div class="card-header">
                        <h3>Data Rekam Medis</h3>
                    </div>
                    <div class="card-body">
                        <div class="data-table-container">
                            <table class="data-table">
                                <thead>
                                    <tr>
                                        <th>Tanggal</th>
                                        <th>No. RM</th>
                                        <th>Nama Pasien</th>
                                        <th>Dokter</th>
                                        <th>Diagnosis</th>
                                        <th>Total Biaya</th>
                                        <th>Status Bayar</th>
                                        <th>Aksi</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php
                                    $medical_query = "SELECT pe.*, p.no_rm, p.nama as pasien_nama, d.nama as dokter_nama,
                                                     COALESCE(pb.total_biaya, 0) as total_biaya, 
                                                     COALESCE(pb.status, 'belum_bayar') as status_bayar,
                                                     pb.id as pembayaran_id
                                                     FROM pemeriksaan pe 
                                                     JOIN pasien p ON pe.pasien_id = p.id 
                                                     JOIN dokter d ON pe.dokter_id = d.id 
                                                     LEFT JOIN pembayaran pb ON pe.pendaftaran_id = pb.pendaftaran_id
                                                     ORDER BY pe.tanggal_periksa DESC";
                                    $medical_result = mysqli_query($conn, $medical_query);
                                    while($medical = mysqli_fetch_assoc($medical_result)):
                                    ?>
                                    <tr>
                                        <td><?php echo date('d/m/Y', strtotime($medical['tanggal_periksa'])); ?></td>
                                        <td><?php echo $medical['no_rm']; ?></td>
                                        <td><?php echo htmlspecialchars($medical['pasien_nama']); ?></td>
                                        <td><?php echo htmlspecialchars($medical['dokter_nama']); ?></td>
                                        <td><?php echo htmlspecialchars($medical['diagnosa']); ?></td>
                                        <td>Rp <?php echo number_format($medical['total_biaya'], 0, ',', '.'); ?></td>
                                        <td>
                                            <span class="badge <?php echo $medical['status_bayar'] === 'sudah_bayar' ? 'badge-success' : 'badge-warning'; ?>">
                                                <?php echo $medical['status_bayar'] === 'sudah_bayar' ? 'Sudah Bayar' : 'Belum Bayar'; ?>
                                            </span>
                                        </td>
                                        <td class="table-actions">
                                            <?php if ($medical['status_bayar'] === 'belum_bayar'): ?>
                                            <button class="btn-action btn-primary" onclick="showPaymentModal(<?php echo $medical['id']; ?>, '<?php echo htmlspecialchars($medical['pasien_nama']); ?>')">
                                                <i class="fas fa-money-bill"></i> Bayar
                                            </button>
                                            <?php else: ?>
                                            <span class="badge badge-success">Lunas</span>
                                            <?php endif; ?>
                                        </td>
                                    </tr>
                                    <?php endwhile; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
            <?php endif; ?>
        </div>
    </main>

    <!-- Modal Pemeriksaan Dokter -->
    <div id="examinationModal" class="modal">
        <div class="modal-content">
            <div class="modal-header">
                <h3>Form Pemeriksaan Pasien</h3>
                <span class="close" onclick="closeExaminationModal()">&times;</span>
            </div>
            <form id="examinationForm">
                <input type="hidden" id="pendaftaran_id" name="pendaftaran_id">
                <input type="hidden" id="pasien_id" name="pasien_id">
                
                <div id="patient-info" class="card" style="margin-bottom: 20px; background-color: #f8f9fa;">
                    <div class="card-body">
                        <h4 style="color: #1976d2; margin-bottom: 10px;">Informasi Pasien</h4>
                        <div id="patient-details"></div>
                    </div>
                </div>

                <div class="form-group">
                    <label for="keluhan">Keluhan Utama *</label>
                    <textarea id="keluhan" name="keluhan" placeholder="Masukkan keluhan pasien..." required></textarea>
                </div>

                <div class="form-group">
                    <label for="pemeriksaan_fisik">Pemeriksaan Fisik</label>
                    <textarea id="pemeriksaan_fisik" name="pemeriksaan_fisik" placeholder="Hasil pemeriksaan fisik..."></textarea>
                </div>

                <div class="form-group">
                    <label for="diagnosa">Diagnosis *</label>
                    <textarea id="diagnosa" name="diagnosa" placeholder="Diagnosis penyakit..." required></textarea>
                </div>

                <div class="form-group">
                    <label for="tindakan">Tindakan Medis</label>
                    <textarea id="tindakan" name="tindakan" placeholder="Tindakan yang diberikan..."></textarea>
                </div>

                <div class="form-group">
                    <label for="resep">Resep Obat</label>
                    <textarea id="resep" name="resep" placeholder="Resep obat dan aturan pakai..."></textarea>
                </div>

                <div class="form-group">
                    <label for="catatan">Catatan Tambahan</label>
                    <textarea id="catatan" name="catatan" placeholder="Catatan tambahan..."></textarea>
                </div>

                <div style="text-align: right; margin-top: 20px;">
                    <button type="button" class="btn-cancel" onclick="closeExaminationModal()">Batal</button>
                    <button type="submit" class="btn-save">Simpan Pemeriksaan</button>
                </div>
            </form>
        </div>
    </div>

    <!-- Modal Tambah/Edit Pasien (Admin) -->
    <div id="patientModal" class="modal">
        <div class="modal-content">
            <div class="modal-header">
                <h3 id="patientModalTitle">Tambah Pasien Baru</h3>
                <span class="close" onclick="closePatientModal()">&times;</span>
            </div>
            <form id="patientForm">
                <input type="hidden" id="patient_id" name="patient_id">
                
                <div class="form-row">
                    <div class="form-group">
                        <label for="nama">Nama Lengkap *</label>
                        <input type="text" id="nama" name="nama" required>
                    </div>
                    <div class="form-group">
                        <label for="jenis_kelamin">Jenis Kelamin *</label>
                        <select id="jenis_kelamin" name="jenis_kelamin" required>
                            <option value="">Pilih...</option>
                            <option value="L">Laki-laki</option>
                            <option value="P">Perempuan</option>
                        </select>
                    </div>
                </div>

                <div class="form-row">
                    <div class="form-group">
                        <label for="tempat_lahir">Tempat Lahir</label>
                        <input type="text" id="tempat_lahir" name="tempat_lahir">
                    </div>
                    <div class="form-group">
                        <label for="tanggal_lahir">Tanggal Lahir</label>
                        <input type="date" id="tanggal_lahir" name="tanggal_lahir" onchange="calculateAge()">
                    </div>
                    <div class="form-group">
                        <label for="usia">Usia</label>
                        <input type="number" id="usia" name="usia" readonly>
                    </div>
                </div>

                <div class="form-group">
                    <label for="alamat">Alamat *</label>
                    <textarea id="alamat" name="alamat" required></textarea>
                </div>

                <div class="form-row">
                    <div class="form-group">
                        <label for="no_telp">No. Telepon</label>
                        <input type="text" id="no_telp" name="no_telp">
                    </div>
                    <div class="form-group">
                        <label for="pekerjaan">Pekerjaan</label>
                        <input type="text" id="pekerjaan" name="pekerjaan">
                    </div>
                </div>

                <div class="form-row">
                    <div class="form-group">
                        <label for="agama">Agama</label>
                        <select id="agama" name="agama">
                            <option value="">Pilih...</option>
                            <option value="Islam">Islam</option>
                            <option value="Kristen">Kristen</option>
                            <option value="Katolik">Katolik</option>
                            <option value="Hindu">Hindu</option>
                            <option value="Buddha">Buddha</option>
                            <option value="Konghucu">Konghucu</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="dokter_id">Dokter yang Menangani</label>
                        <select id="dokter_id" name="dokter_id">
                            <option value="">Pilih Dokter...</option>
                            <?php
                            $dokter_query = "SELECT id, nama, spesialisasi FROM dokter WHERE status = 'aktif'";
                            $dokter_result = mysqli_query($conn, $dokter_query);
                            while($dokter = mysqli_fetch_assoc($dokter_result)):
                            ?>
                            <option value="<?php echo $dokter['id']; ?>">
                                <?php echo htmlspecialchars($dokter['nama']) . ' - ' . htmlspecialchars($dokter['spesialisasi']); ?>
                            </option>
                            <?php endwhile; ?>
                        </select>
                    </div>
                </div>

                <div class="form-group">
                    <label for="keluhan_awal">Keluhan Awal</label>
                    <textarea id="keluhan_awal" name="keluhan_awal" placeholder="Keluhan awal saat pendaftaran..."></textarea>
                </div>

                <div style="text-align: right; margin-top: 20px;">
                    <button type="button" class="btn-cancel" onclick="closePatientModal()">Batal</button>
                    <button type="submit" class="btn-save">Simpan</button>
                </div>
            </form>
        </div>
    </div>

    <!-- Modal Tambah/Edit Dokter (Admin) -->
    <div id="doctorModal" class="modal">
        <div class="modal-content">
            <div class="modal-header">
                <h3 id="doctorModalTitle">Tambah Dokter Baru</h3>
                <span class="close" onclick="closeDoctorModal()">&times;</span>
            </div>
            <form id="doctorForm">
                <input type="hidden" id="doctor_id" name="doctor_id">
                
                <div class="form-row">
                    <div class="form-group">
                        <label for="doctor_nama">Nama Lengkap *</label>
                        <input type="text" id="doctor_nama" name="nama" required>
                    </div>
                    <div class="form-group">
                        <label for="doctor_spesialisasi">Spesialisasi *</label>
                        <input type="text" id="doctor_spesialisasi" name="spesialisasi" required>
                    </div>
                </div>

                <div class="form-row">
                    <div class="form-group">
                        <label for="doctor_username">Username *</label>
                        <input type="text" id="doctor_username" name="username" required>
                    </div>
                    <div class="form-group">
                        <label for="doctor_password">Password *</label>
                        <input type="password" id="doctor_password" name="password">
                        <small style="color: #666; font-size: 12px;">Kosongkan jika tidak ingin mengubah password</small>
                    </div>
                </div>

                <div class="form-row">
                    <div class="form-group">
                        <label for="doctor_no_sip">No. SIP</label>
                        <input type="text" id="doctor_no_sip" name="no_sip">
                    </div>
                    <div class="form-group">
                        <label for="doctor_email">Email</label>
                        <input type="email" id="doctor_email" name="email">
                    </div>
                </div>

                <div class="form-row">
                    <div class="form-group">
                        <label for="doctor_no_telp">No. Telepon</label>
                        <input type="text" id="doctor_no_telp" name="no_telp">
                    </div>
                    <div class="form-group">
                        <label for="doctor_status">Status</label>
                        <select id="doctor_status" name="status">
                            <option value="aktif">Aktif</option>
                            <option value="tidak_aktif">Tidak Aktif</option>
                        </select>
                    </div>
                </div>

                <div class="form-group">
                    <label for="doctor_alamat">Alamat</label>
                    <textarea id="doctor_alamat" name="alamat"></textarea>
                </div>

                <div style="text-align: right; margin-top: 20px;">
                    <button type="button" class="btn-cancel" onclick="closeDoctorModal()">Batal</button>
                    <button type="submit" class="btn-save">Simpan</button>
                </div>
            </form>
        </div>
    </div>

    <!-- Modal Pembayaran (Admin) -->
    <div id="paymentModal" class="modal">
        <div class="modal-content">
            <div class="modal-header">
                <h3>Form Pembayaran</h3>
                <span class="close" onclick="closePaymentModal()">&times;</span>
            </div>
            <form id="paymentForm">
                <input type="hidden" id="pemeriksaan_id" name="pemeriksaan_id">
                
                <div id="payment-patient-info" class="card" style="margin-bottom: 20px; background-color: #f8f9fa;">
                    <div class="card-body">
                        <h4 style="color: #1976d2; margin-bottom: 10px;">Informasi Pasien</h4>
                        <div id="payment-patient-details"></div>
                    </div>
                </div>

                <div class="form-group">
                    <label for="biaya_konsultasi">Biaya Konsultasi *</label>
                    <input type="number" id="biaya_konsultasi" name="biaya_konsultasi" value="50000" onchange="calculateTotal()" required>
                </div>

                <div class="form-group">
                    <label for="biaya_tindakan">Biaya Tindakan</label>
                    <input type="number" id="biaya_tindakan" name="biaya_tindakan" value="0" onchange="calculateTotal()">
                </div>

                <div class="form-group">
                    <label for="biaya_obat">Biaya Obat</label>
                    <input type="number" id="biaya_obat" name="biaya_obat" value="0" onchange="calculateTotal()">
                </div>

                <div class="form-group">
                    <label for="total_bayar">Total Biaya</label>
                    <input type="number" id="total_bayar" name="total_bayar" readonly style="background-color: #f9f9f9; font-weight: bold;">
                </div>

                <div style="text-align: right; margin-top: 20px;">
                    <button type="button" class="btn-cancel" onclick="closePaymentModal()">Batal</button>
                    <button type="submit" class="btn-save">Simpan Pembayaran</button>
                </div>
            </form>
        </div>
    </div>

    <!-- Modal Detail Transaksi -->
    <div id="transactionModal" class="modal">
        <div class="modal-content" style="max-width: 800px;">
            <div class="modal-header">
                <h3><i class="fas fa-eye"></i> Detail Transaksi</h3>
                <span class="close" onclick="closeTransactionModal()">&times;</span>
            </div>
            <div id="transaction-details" style="padding: 20px;">
                <!-- Content will be loaded via AJAX -->
            </div>
            <div style="text-align: right; padding: 20px; border-top: 1px solid #eee;">
                <button type="button" class="btn-cancel" onclick="closeTransactionModal()">Tutup</button>
                <button type="button" class="btn btn-primary" onclick="printTransaction()">
                    <i class="fas fa-print"></i> Cetak PDF
                </button>
            </div>
        </div>
    </div>

    <script>
        // Fungsi untuk navigasi menu sidebar
        document.addEventListener('DOMContentLoaded', function() {
            const menuItems = document.querySelectorAll('.menu-item');
            const contentSections = document.querySelectorAll('.content-section');
            
            menuItems.forEach(item => {
                item.addEventListener('click', function(e) {
                    e.preventDefault();
                    
                    // Remove active class from all menu items
                    menuItems.forEach(i => i.classList.remove('active'));
                    // Add active class to clicked menu item
                    this.classList.add('active');
                    
                    // Get target section
                    const targetId = this.getAttribute('data-target');
                    
                    // Hide all content sections
                    contentSections.forEach(section => {
                        section.classList.remove('active');
                    });
                    
                    // Show target section
                    document.getElementById(targetId).classList.add('active');
                });
            });

            // Load patients data when patients section is active
            loadPatients();
        });

        function logout() {
            if (confirm('Apakah Anda yakin ingin keluar?')) {
                window.location.href = 'login.php?action=logout';
            }
        }


        function loadPatients() {
            // Data pasien sudah dimuat dari database melalui PHP
            // Fungsi ini hanya untuk keperluan refresh jika diperlukan
        }

        function addPatient() {
            alert('Fitur tambah pasien akan segera tersedia');
        }


        function deletePatient(id) {
            if (confirm('Apakah Anda yakin ingin menghapus data pasien ini?')) {
                // AJAX call to delete patient
                fetch('process.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                    },
                    body: JSON.stringify({
                        action: 'delete_patient',
                        patient_id: id
                    })
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        alert('Data pasien berhasil dihapus');
                        location.reload();
                    } else {
                        alert('Gagal menghapus data pasien: ' + data.message);
                    }
                });
            }
        }

        // Fungsi untuk dokter - mulai pemeriksaan
        function startExamination(pendaftaranId, pasienId) {
            // Get patient info
            fetch('process.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                },
                body: JSON.stringify({
                    action: 'get_patient_info',
                    patient_id: pasienId
                })
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    const patient = data.patient;
                    document.getElementById('pendaftaran_id').value = pendaftaranId;
                    document.getElementById('pasien_id').value = pasienId;
                    
                    // Fill patient info
                    const patientInfo = `
                        <p><strong>Nama:</strong> ${patient.nama}</p>
                        <p><strong>Usia:</strong> ${patient.usia} tahun</p>
                        <p><strong>Jenis Kelamin:</strong> ${patient.jenis_kelamin === 'L' ? 'Laki-laki' : 'Perempuan'}</p>
                        <p><strong>Alamat:</strong> ${patient.alamat}</p>
                        <p><strong>No. Telepon:</strong> ${patient.no_telp || '-'}</p>
                    `;
                    document.getElementById('patient-details').innerHTML = patientInfo;
                    
                    document.getElementById('examinationModal').style.display = 'block';
                } else {
                    alert('Gagal mengambil data pasien');
                }
            });
        }

        function closeExaminationModal() {
            document.getElementById('examinationModal').style.display = 'none';
            document.getElementById('examinationForm').reset();
        }

        // Handle examination form submission
        document.getElementById('examinationForm').addEventListener('submit', function(e) {
            e.preventDefault();
            
            const formData = new FormData(this);
            formData.append('action', 'save_examination');
            
            fetch('process.php', {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    alert('Pemeriksaan berhasil disimpan');
                    closeExaminationModal();
                    location.reload();
                } else {
                    alert('Gagal menyimpan pemeriksaan: ' + data.message);
                }
            });
        });

        // Fungsi untuk admin - tambah pasien
        function showAddPatientModal() {
            document.getElementById('patientModalTitle').textContent = 'Tambah Pasien Baru';
            document.getElementById('patientForm').reset();
            document.getElementById('patient_id').value = '';
            document.getElementById('patientModal').style.display = 'block';
        }

        function editPatient(id) {
            document.getElementById('patientModalTitle').textContent = 'Edit Data Pasien';
            
            // Get patient data
            fetch('process.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                },
                body: JSON.stringify({
                    action: 'get_patient_info',
                    patient_id: id
                })
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    const patient = data.patient;
                    document.getElementById('patient_id').value = patient.id;
                    document.getElementById('nama').value = patient.nama;
                    document.getElementById('jenis_kelamin').value = patient.jenis_kelamin;
                    document.getElementById('tempat_lahir').value = patient.tempat_lahir || '';
                    document.getElementById('tanggal_lahir').value = patient.tanggal_lahir || '';
                    document.getElementById('usia').value = patient.usia || '';
                    document.getElementById('alamat').value = patient.alamat || '';
                    document.getElementById('no_telp').value = patient.no_telp || '';
                    document.getElementById('pekerjaan').value = patient.pekerjaan || '';
                    document.getElementById('agama').value = patient.agama || '';
                    
                    document.getElementById('patientModal').style.display = 'block';
                }
            });
        }

        function closePatientModal() {
            document.getElementById('patientModal').style.display = 'none';
            document.getElementById('patientForm').reset();
        }

        // Calculate age from birth date
        function calculateAge() {
            const birthDate = document.getElementById('tanggal_lahir').value;
            if (birthDate) {
                const today = new Date();
                const birth = new Date(birthDate);
                let age = today.getFullYear() - birth.getFullYear();
                const monthDiff = today.getMonth() - birth.getMonth();
                if (monthDiff < 0 || (monthDiff === 0 && today.getDate() < birth.getDate())) {
                    age--;
                }
                document.getElementById('usia').value = age;
            }
        }

        // Handle patient form submission
        document.getElementById('patientForm').addEventListener('submit', function(e) {
            e.preventDefault();
            
            const formData = new FormData(this);
            const isEdit = document.getElementById('patient_id').value !== '';
            formData.append('action', isEdit ? 'edit_patient' : 'add_patient');
            
            fetch('process.php', {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    alert(isEdit ? 'Data pasien berhasil diupdate' : 'Pasien berhasil ditambahkan');
                    closePatientModal();
                    location.reload();
                } else {
                    alert('Gagal menyimpan data: ' + data.message);
                }
            });
        });

        // Fungsi untuk admin - dokter management
        function showAddDoctorModal() {
            document.getElementById('doctorModalTitle').textContent = 'Tambah Dokter Baru';
            document.getElementById('doctorForm').reset();
            document.getElementById('doctor_id').value = '';
            document.getElementById('doctor_password').required = true;
            document.getElementById('doctorModal').style.display = 'block';
        }

        function editDoctor(id) {
            document.getElementById('doctorModalTitle').textContent = 'Edit Data Dokter';
            document.getElementById('doctor_password').required = false;
            
            // Get doctor data
            fetch('process.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                },
                body: JSON.stringify({
                    action: 'get_doctor_info',
                    doctor_id: id
                })
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    const doctor = data.doctor;
                    document.getElementById('doctor_id').value = doctor.id;
                    document.getElementById('doctor_nama').value = doctor.nama;
                    document.getElementById('doctor_spesialisasi').value = doctor.spesialisasi;
                    document.getElementById('doctor_username').value = doctor.username;
                    document.getElementById('doctor_no_sip').value = doctor.no_sip || '';
                    document.getElementById('doctor_email').value = doctor.email || '';
                    document.getElementById('doctor_no_telp').value = doctor.no_telp || '';
                    document.getElementById('doctor_alamat').value = doctor.alamat || '';
                    document.getElementById('doctor_status').value = doctor.status;
                    
                    document.getElementById('doctorModal').style.display = 'block';
                }
            });
        }

        function deleteDoctor(id) {
            if (confirm('Apakah Anda yakin ingin menghapus data dokter ini?')) {
                fetch('process.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                    },
                    body: JSON.stringify({
                        action: 'delete_doctor',
                        doctor_id: id
                    })
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        alert('Data dokter berhasil dihapus');
                        location.reload();
                    } else {
                        alert('Gagal menghapus data dokter: ' + data.message);
                    }
                });
            }
        }

        function closeDoctorModal() {
            document.getElementById('doctorModal').style.display = 'none';
            document.getElementById('doctorForm').reset();
        }

        // Handle doctor form submission
        document.getElementById('doctorForm').addEventListener('submit', function(e) {
            e.preventDefault();
            
            const formData = new FormData(this);
            const isEdit = document.getElementById('doctor_id').value !== '';
            formData.append('action', isEdit ? 'edit_doctor' : 'add_doctor');
            
            fetch('process.php', {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    alert(isEdit ? 'Data dokter berhasil diupdate' : 'Dokter berhasil ditambahkan');
                    closeDoctorModal();
                    location.reload();
                } else {
                    alert('Gagal menyimpan data: ' + data.message);
                }
            });
        });

        // Fungsi untuk admin - payment management
        function showPaymentModal(pemeriksaanId, patientName) {
            document.getElementById('pemeriksaan_id').value = pemeriksaanId;
            document.getElementById('payment-patient-details').innerHTML = '<p><strong>Nama Pasien:</strong> ' + patientName + '</p>';
            calculateTotal();
            document.getElementById('paymentModal').style.display = 'block';
        }

        function closePaymentModal() {
            document.getElementById('paymentModal').style.display = 'none';
            document.getElementById('paymentForm').reset();
        }

        function calculateTotal() {
            const konsultasi = parseInt(document.getElementById('biaya_konsultasi').value) || 0;
            const tindakan = parseInt(document.getElementById('biaya_tindakan').value) || 0;
            const obat = parseInt(document.getElementById('biaya_obat').value) || 0;
            const total = konsultasi + tindakan + obat;
            
            document.getElementById('total_bayar').value = total;
        }

        // Handle payment form submission
        document.getElementById('paymentForm').addEventListener('submit', function(e) {
            e.preventDefault();
            
            const formData = new FormData(this);
            formData.append('action', 'update_payment');
            
            fetch('process.php', {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    alert('Pembayaran berhasil disimpan');
                    closePaymentModal();
                    location.reload();
                } else {
                    alert('Gagal menyimpan pembayaran: ' + data.message);
                }
            });
        });

        // Fungsi untuk dokter - mark attendance
        function markAttendance(pendaftaranId) {
            if (confirm('Apakah pasien sudah hadir?')) {
                fetch('process.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                    },
                    body: JSON.stringify({
                        action: 'mark_attendance',
                        pendaftaran_id: pendaftaranId
                    })
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        alert('Status kehadiran berhasil diupdate');
                        location.reload();
                    } else {
                        alert('Gagal mengupdate status: ' + data.message);
                    }
                });
            }
        }

        // Fungsi untuk dokter - show examination form (rename from startExamination)
        function showExaminationForm(pendaftaranId, pasienId) {
            // Same as startExamination but renamed for clarity
            startExamination(pendaftaranId, pasienId);
        }

        // Fungsi untuk admin - show transaction detail
        function showTransactionDetail(pemeriksaanId) {
            fetch('process.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                },
                body: JSON.stringify({
                    action: 'get_transaction_detail',
                    pemeriksaan_id: pemeriksaanId
                })
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    document.getElementById('transaction-details').innerHTML = data.html;
                    document.getElementById('transactionModal').style.display = 'block';
                    
                    // Store pemeriksaan_id for PDF printing
                    window.currentPemeriksaanId = pemeriksaanId;
                } else {
                    alert('Gagal mengambil detail transaksi');
                }
            });
        }

        function closeTransactionModal() {
            document.getElementById('transactionModal').style.display = 'none';
        }

        function printTransaction() {
            if (window.currentPemeriksaanId) {
                window.open('print_transaction.php?id=' + window.currentPemeriksaanId, '_blank');
            }
        }

        // Close modal when clicking outside
        window.onclick = function(event) {
            const examinationModal = document.getElementById('examinationModal');
            const patientModal = document.getElementById('patientModal');
            const doctorModal = document.getElementById('doctorModal');
            const paymentModal = document.getElementById('paymentModal');
            const transactionModal = document.getElementById('transactionModal');
            
            if (event.target === examinationModal) {
                closeExaminationModal();
            }
            if (event.target === patientModal) {
                closePatientModal();
            }
            if (event.target === doctorModal) {
                closeDoctorModal();
            }
            if (event.target === paymentModal) {
                closePaymentModal();
            }
            if (event.target === transactionModal) {
                closeTransactionModal();
            }
        }
    </script>
</body>
</html>
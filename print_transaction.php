<?php
session_start();
include_once 'config.php';

// Cek apakah user sudah login dan role admin
if (!isset($_SESSION['user']) || $_SESSION['user']['role'] !== 'admin') {
    header("Location: login.php");
    exit();
}

$pemeriksaan_id = isset($_GET['id']) ? mysqli_real_escape_string($conn, $_GET['id']) : '';

if (empty($pemeriksaan_id)) {
    die('ID Pemeriksaan tidak ditemukan');
}

// Query data transaksi
$query = "SELECT pe.*, p.no_rm, p.nama as pasien_nama, p.usia, p.jenis_kelamin, p.alamat, p.no_telp,
          d.nama as dokter_nama, d.spesialisasi,
          pb.biaya_konsultasi, pb.biaya_obat, pb.biaya_tindakan, pb.total_biaya, pb.tanggal_bayar,
          pd.keluhan as keluhan_awal
          FROM pemeriksaan pe
          JOIN pasien p ON pe.pasien_id = p.id
          JOIN dokter d ON pe.dokter_id = d.id
          JOIN pendaftaran pd ON pe.pendaftaran_id = pd.id
          LEFT JOIN pembayaran pb ON pe.pendaftaran_id = pb.pendaftaran_id
          WHERE pe.id = '$pemeriksaan_id'";

$result = mysqli_query($conn, $query);

if (!$result || mysqli_num_rows($result) == 0) {
    die('Data transaksi tidak ditemukan');
}

$data = mysqli_fetch_assoc($result);
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Print Transaksi - <?php echo htmlspecialchars($data['pasien_nama']); ?></title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            font-family: 'Arial', sans-serif;
            font-size: 12px;
            line-height: 1.6;
            color: #333;
            background-color: white;
        }
        
        .container {
            max-width: 800px;
            margin: 0 auto;
            padding: 20px;
        }
        
        .header {
            text-align: center;
            border-bottom: 2px solid #1976d2;
            padding-bottom: 20px;
            margin-bottom: 30px;
        }
        
        .header h1 {
            color: #1976d2;
            font-size: 24px;
            margin-bottom: 5px;
        }
        
        .header p {
            color: #666;
            font-size: 14px;
        }
        
        .info-section {
            display: flex;
            gap: 20px;
            margin-bottom: 25px;
        }
        
        .info-box {
            flex: 1;
            border: 1px solid #ddd;
            padding: 15px;
            border-radius: 5px;
        }
        
        .info-box h3 {
            color: #1976d2;
            font-size: 14px;
            margin-bottom: 10px;
            border-bottom: 1px solid #eee;
            padding-bottom: 5px;
        }
        
        .info-table {
            width: 100%;
        }
        
        .info-table td {
            padding: 3px 0;
            vertical-align: top;
        }
        
        .info-table td:first-child {
            width: 40%;
            font-weight: bold;
        }
        
        .examination-section {
            border: 1px solid #ddd;
            padding: 20px;
            border-radius: 5px;
            margin-bottom: 25px;
        }
        
        .examination-section h3 {
            color: #1976d2;
            font-size: 16px;
            margin-bottom: 15px;
            border-bottom: 1px solid #eee;
            padding-bottom: 5px;
        }
        
        .exam-item {
            margin-bottom: 15px;
        }
        
        .exam-item label {
            font-weight: bold;
            display: block;
            margin-bottom: 5px;
        }
        
        .exam-content {
            background-color: #f8f9fa;
            padding: 10px;
            border-radius: 4px;
            min-height: 20px;
        }
        
        .exam-content.diagnosis {
            background-color: #e3f2fd;
            font-weight: bold;
        }
        
        .payment-section {
            border: 1px solid #ddd;
            padding: 20px;
            border-radius: 5px;
        }
        
        .payment-section h3 {
            color: #1976d2;
            font-size: 16px;
            margin-bottom: 15px;
            border-bottom: 1px solid #eee;
            padding-bottom: 5px;
        }
        
        .payment-table {
            width: 100%;
            border-collapse: collapse;
        }
        
        .payment-table td {
            padding: 8px 0;
            border-bottom: 1px solid #eee;
        }
        
        .payment-table td:last-child {
            text-align: right;
            width: 30%;
        }
        
        .payment-total {
            border-top: 2px solid #1976d2;
            background-color: #f8f9fa;
            font-weight: bold;
            font-size: 14px;
        }
        
        .payment-total td {
            padding: 12px 0;
        }
        
        .footer {
            margin-top: 30px;
            text-align: center;
            font-size: 10px;
            color: #666;
            border-top: 1px solid #eee;
            padding-top: 15px;
        }
        
        @media print {
            body { 
                font-size: 11px; 
            }
            .container { 
                padding: 10px; 
            }
            .no-print { 
                display: none; 
            }
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>ORRIMEDIKA</h1>
            <p>Sistem Informasi Layanan Kesehatan</p>
            <p style="margin-top: 10px; font-weight: bold;">KWITANSI PEMBAYARAN</p>
        </div>
        
        <div class="info-section">
            <div class="info-box">
                <h3>📋 Informasi Pasien</h3>
                <table class="info-table">
                    <tr><td>No. RM:</td><td><?php echo htmlspecialchars($data['no_rm']); ?></td></tr>
                    <tr><td>Nama:</td><td><?php echo htmlspecialchars($data['pasien_nama']); ?></td></tr>
                    <tr><td>Usia:</td><td><?php echo $data['usia']; ?> tahun</td></tr>
                    <tr><td>Jenis Kelamin:</td><td><?php echo $data['jenis_kelamin'] === 'L' ? 'Laki-laki' : 'Perempuan'; ?></td></tr>
                    <tr><td>Alamat:</td><td><?php echo htmlspecialchars($data['alamat']); ?></td></tr>
                    <tr><td>No. Telp:</td><td><?php echo htmlspecialchars($data['no_telp']); ?></td></tr>
                </table>
            </div>
            
            <div class="info-box">
                <h3>👨‍⚕️ Informasi Dokter</h3>
                <table class="info-table">
                    <tr><td>Nama Dokter:</td><td><?php echo htmlspecialchars($data['dokter_nama']); ?></td></tr>
                    <tr><td>Spesialisasi:</td><td><?php echo htmlspecialchars($data['spesialisasi']); ?></td></tr>
                    <tr><td>Tanggal Periksa:</td><td><?php echo date('d/m/Y H:i', strtotime($data['tanggal_periksa'])); ?></td></tr>
                    <tr><td>Tanggal Bayar:</td><td><?php echo date('d/m/Y H:i', strtotime($data['tanggal_bayar'])); ?></td></tr>
                </table>
            </div>
        </div>
        
        <div class="examination-section">
            <h3>🩺 Hasil Pemeriksaan</h3>
            
            <div class="exam-item">
                <label>Keluhan Awal:</label>
                <div class="exam-content"><?php echo htmlspecialchars($data['keluhan_awal']); ?></div>
            </div>
            
            <div class="exam-item">
                <label>Anamnesa:</label>
                <div class="exam-content"><?php echo htmlspecialchars($data['anamnesa']); ?></div>
            </div>
            
            <div class="exam-item">
                <label>Pemeriksaan Fisik:</label>
                <div class="exam-content"><?php echo htmlspecialchars($data['pemeriksaan_fisik']); ?></div>
            </div>
            
            <div class="exam-item">
                <label>Diagnosis:</label>
                <div class="exam-content diagnosis"><?php echo htmlspecialchars($data['diagnosa']); ?></div>
            </div>
            
            <div class="exam-item">
                <label>Terapi/Pengobatan:</label>
                <div class="exam-content"><?php echo htmlspecialchars($data['terapi_pengobatan']); ?></div>
            </div>
            
            <?php if (!empty($data['catatan_tambahan'])): ?>
            <div class="exam-item">
                <label>Catatan Tambahan:</label>
                <div class="exam-content"><?php echo htmlspecialchars($data['catatan_tambahan']); ?></div>
            </div>
            <?php endif; ?>
        </div>
        
        <div class="payment-section">
            <h3>💰 Rincian Pembayaran</h3>
            
            <table class="payment-table">
                <tr>
                    <td>Biaya Konsultasi</td>
                    <td>Rp <?php echo number_format($data['biaya_konsultasi'], 0, ',', '.'); ?></td>
                </tr>
                <tr>
                    <td>Biaya Tindakan</td>
                    <td>Rp <?php echo number_format($data['biaya_tindakan'], 0, ',', '.'); ?></td>
                </tr>
                <tr>
                    <td>Biaya Obat</td>
                    <td>Rp <?php echo number_format($data['biaya_obat'], 0, ',', '.'); ?></td>
                </tr>
                <tr class="payment-total">
                    <td><strong>TOTAL PEMBAYARAN</strong></td>
                    <td><strong>Rp <?php echo number_format($data['total_biaya'], 0, ',', '.'); ?></strong></td>
                </tr>
            </table>
        </div>
        
        <div class="footer">
            <p><strong>ORRIMEDIKA</strong> - Sistem Informasi Layanan Kesehatan</p>
            <p>Dicetak pada: <?php echo date('d/m/Y H:i:s'); ?> WIB</p>
            <p style="margin-top: 10px; font-style: italic;">*** Terima kasih atas kepercayaan Anda ***</p>
        </div>
        
        <div class="no-print" style="text-align: center; margin-top: 30px;">
            <button onclick="window.print()" style="padding: 10px 20px; background-color: #1976d2; color: white; border: none; border-radius: 5px; cursor: pointer;">
                🖨️ Cetak Dokumen
            </button>
            <button onclick="window.close()" style="padding: 10px 20px; background-color: #757575; color: white; border: none; border-radius: 5px; cursor: pointer; margin-left: 10px;">
                ❌ Tutup
            </button>
        </div>
    </div>
    
    <script>
        // Auto print when page loads (optional)
        // window.onload = function() { window.print(); }
    </script>
</body>
</html>
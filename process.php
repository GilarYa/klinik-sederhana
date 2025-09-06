<?php
session_start();
include_once 'config.php';

// Set header untuk JSON response
header('Content-Type: application/json');

// Cek apakah user sudah login
if (!isset($_SESSION['user'])) {
    echo json_encode(['success' => false, 'message' => 'Unauthorized access']);
    exit();
}

$user_role = $_SESSION['user']['role'];
$user_id = $_SESSION['user']['id'];

// Handle POST request
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $input = file_get_contents('php://input');
    $data = json_decode($input, true);
    
    // Jika data kosong, coba ambil dari $_POST (untuk form data)
    if ($data === null) {
        $data = $_POST;
        $action = isset($data['action']) ? $data['action'] : '';
    } else {
        $action = isset($data['action']) ? $data['action'] : '';
    }
    
    switch ($action) {
        case 'get_patient_info':
            getPatientInfo($data['patient_id']);
            break;
            
        case 'get_doctor_info':
            if ($user_role !== 'admin') {
                echo json_encode(['success' => false, 'message' => 'Access denied']);
                return;
            }
            getDoctorInfo($data['doctor_id']);
            break;
            
        case 'save_examination':
            if ($user_role !== 'dokter') {
                echo json_encode(['success' => false, 'message' => 'Access denied']);
                return;
            }
            saveExamination($data, $user_id);
            break;
            
        case 'add_patient':
            if ($user_role !== 'admin') {
                echo json_encode(['success' => false, 'message' => 'Access denied']);
                return;
            }
            addPatient($data);
            break;
            
        case 'edit_patient':
            if ($user_role !== 'admin') {
                echo json_encode(['success' => false, 'message' => 'Access denied']);
                return;
            }
            editPatient($data);
            break;
            
        case 'delete_patient':
            if ($user_role !== 'admin') {
                echo json_encode(['success' => false, 'message' => 'Access denied']);
                return;
            }
            deletePatient($data['patient_id']);
            break;
            
        case 'add_doctor':
            if ($user_role !== 'admin') {
                echo json_encode(['success' => false, 'message' => 'Access denied']);
                return;
            }
            addDoctor($data);
            break;
            
        case 'edit_doctor':
            if ($user_role !== 'admin') {
                echo json_encode(['success' => false, 'message' => 'Access denied']);
                return;
            }
            editDoctor($data);
            break;
            
        case 'delete_doctor':
            if ($user_role !== 'admin') {
                echo json_encode(['success' => false, 'message' => 'Access denied']);
                return;
            }
            deleteDoctor($data['doctor_id']);
            break;
            
        case 'update_payment':
            if ($user_role !== 'admin') {
                echo json_encode(['success' => false, 'message' => 'Access denied']);
                return;
            }
            updatePayment($data);
            break;
            
        case 'mark_attendance':
            if ($user_role !== 'dokter') {
                echo json_encode(['success' => false, 'message' => 'Access denied']);
                return;
            }
            markAttendance($data['pendaftaran_id']);
            break;
            
        case 'get_transaction_detail':
            if ($user_role !== 'admin') {
                echo json_encode(['success' => false, 'message' => 'Access denied']);
                return;
            }
            getTransactionDetail($data['pemeriksaan_id']);
            break;
            
        default:
            echo json_encode(['success' => false, 'message' => 'Invalid action']);
            break;
    }
}

function getPatientInfo($patient_id) {
    global $conn;
    
    $patient_id = mysqli_real_escape_string($conn, $patient_id);
    $query = "SELECT * FROM pasien WHERE id = '$patient_id'";
    $result = mysqli_query($conn, $query);
    
    if ($result && mysqli_num_rows($result) > 0) {
        $patient = mysqli_fetch_assoc($result);
        echo json_encode(['success' => true, 'patient' => $patient]);
    } else {
        echo json_encode(['success' => false, 'message' => 'Patient not found']);
    }
}

function saveExamination($data, $dokter_id) {
    global $conn;
    
    $pendaftaran_id = mysqli_real_escape_string($conn, $data['pendaftaran_id']);
    $pasien_id = mysqli_real_escape_string($conn, $data['pasien_id']);
    $keluhan = mysqli_real_escape_string($conn, $data['keluhan']);
    $pemeriksaan_fisik = mysqli_real_escape_string($conn, $data['pemeriksaan_fisik']);
    $diagnosa = mysqli_real_escape_string($conn, $data['diagnosa']);
    $tindakan = mysqli_real_escape_string($conn, $data['tindakan']);
    $resep = mysqli_real_escape_string($conn, $data['resep']);
    $catatan = mysqli_real_escape_string($conn, $data['catatan']);
    
    // Start transaction
    mysqli_begin_transaction($conn);
    
    try {
        // Insert pemeriksaan
        $query = "INSERT INTO pemeriksaan (
            pendaftaran_id, pasien_id, dokter_id, tanggal_periksa, 
            anamnesa, pemeriksaan_fisik, diagnosa, terapi_pengobatan, 
            catatan_tambahan, status
        ) VALUES (
            '$pendaftaran_id', '$pasien_id', '$dokter_id', NOW(),
            '$keluhan', '$pemeriksaan_fisik', '$diagnosa', '$tindakan',
            '$catatan', 'selesai'
        )";
        
        if (!mysqli_query($conn, $query)) {
            throw new Exception("Gagal menyimpan pemeriksaan");
        }
        
        $pemeriksaan_id = mysqli_insert_id($conn);
        
        // Update status pendaftaran
        $update_query = "UPDATE pendaftaran SET status = 'selesai' WHERE id = '$pendaftaran_id'";
        if (!mysqli_query($conn, $update_query)) {
            throw new Exception("Gagal mengupdate status pendaftaran");
        }
        
        // Jika ada resep, simpan ke resep_obat
        if (!empty($resep)) {
            $resep_query = "INSERT INTO resep_obat (
                pemeriksaan_id, pasien_id, dokter_id, total_biaya, status
            ) VALUES (
                '$pemeriksaan_id', '$pasien_id', '$dokter_id', 0, 'belum_diambil'
            )";
            
            if (!mysqli_query($conn, $resep_query)) {
                throw new Exception("Gagal menyimpan resep");
            }
        }
        
        // Commit transaction
        mysqli_commit($conn);
        echo json_encode(['success' => true, 'message' => 'Pemeriksaan berhasil disimpan']);
        
    } catch (Exception $e) {
        // Rollback transaction
        mysqli_rollback($conn);
        echo json_encode(['success' => false, 'message' => $e->getMessage()]);
    }
}

function addPatient($data) {
    global $conn;
    
    $nama = mysqli_real_escape_string($conn, $data['nama']);
    $jenis_kelamin = mysqli_real_escape_string($conn, $data['jenis_kelamin']);
    $tempat_lahir = mysqli_real_escape_string($conn, $data['tempat_lahir']);
    $tanggal_lahir = mysqli_real_escape_string($conn, $data['tanggal_lahir']);
    $usia = mysqli_real_escape_string($conn, $data['usia']);
    $alamat = mysqli_real_escape_string($conn, $data['alamat']);
    $no_telp = mysqli_real_escape_string($conn, $data['no_telp']);
    $pekerjaan = mysqli_real_escape_string($conn, $data['pekerjaan']);
    $agama = mysqli_real_escape_string($conn, $data['agama']);
    $dokter_id = mysqli_real_escape_string($conn, $data['dokter_id']);
    $keluhan_awal = mysqli_real_escape_string($conn, $data['keluhan_awal']);
    
    // Generate nomor rekam medis
    $year = date('Y');
    $query_last_rm = "SELECT no_rm FROM pasien WHERE no_rm LIKE 'RM$year%' ORDER BY no_rm DESC LIMIT 1";
    $result_rm = mysqli_query($conn, $query_last_rm);
    
    if ($result_rm && mysqli_num_rows($result_rm) > 0) {
        $last_rm = mysqli_fetch_assoc($result_rm)['no_rm'];
        $last_number = intval(substr($last_rm, 6)); // ambil 3 digit terakhir
        $new_number = $last_number + 1;
    } else {
        $new_number = 1;
    }
    
    $no_rm = 'RM' . $year . str_pad($new_number, 3, '0', STR_PAD_LEFT);
    
    // Start transaction
    mysqli_begin_transaction($conn);
    
    try {
        // Insert pasien
        $query = "INSERT INTO pasien (
            no_rm, nama, tempat_lahir, tanggal_lahir, usia, jenis_kelamin,
            alamat, no_telp, pekerjaan, agama
        ) VALUES (
            '$no_rm', '$nama', '$tempat_lahir', '$tanggal_lahir', '$usia', '$jenis_kelamin',
            '$alamat', '$no_telp', '$pekerjaan', '$agama'
        )";
        
        if (!mysqli_query($conn, $query)) {
            throw new Exception("Gagal menambah pasien");
        }
        
        $pasien_id = mysqli_insert_id($conn);
        
        // Jika ada dokter yang dipilih, buat pendaftaran
        if (!empty($dokter_id)) {
            // Generate nomor antrian
            $date_today = date('Y-m-d');
            $antrian_query = "SELECT COUNT(*) as total FROM pendaftaran WHERE tanggal_daftar = '$date_today'";
            $antrian_result = mysqli_query($conn, $antrian_query);
            $antrian_count = mysqli_fetch_assoc($antrian_result)['total'];
            $no_antrian = 'A' . str_pad($antrian_count + 1, 3, '0', STR_PAD_LEFT);
            
            // Get pelayanan_id from dokter (ambil pelayanan pertama dokter)
            $pelayanan_query = "SELECT pelayanan_id FROM jadwal_dokter WHERE dokter_id = '$dokter_id' LIMIT 1";
            $pelayanan_result = mysqli_query($conn, $pelayanan_query);
            $pelayanan_id = 1; // default
            
            if ($pelayanan_result && mysqli_num_rows($pelayanan_result) > 0) {
                $pelayanan_id = mysqli_fetch_assoc($pelayanan_result)['pelayanan_id'];
            }
            
            $pendaftaran_query = "INSERT INTO pendaftaran (
                no_antrian, pasien_id, dokter_id, pelayanan_id, tanggal_daftar, keluhan, status
            ) VALUES (
                '$no_antrian', '$pasien_id', '$dokter_id', '$pelayanan_id', '$date_today', '$keluhan_awal', 'menunggu'
            )";
            
            if (!mysqli_query($conn, $pendaftaran_query)) {
                throw new Exception("Gagal membuat pendaftaran");
            }
        }
        
        // Commit transaction
        mysqli_commit($conn);
        echo json_encode(['success' => true, 'message' => 'Pasien berhasil ditambahkan dengan No. RM: ' . $no_rm]);
        
    } catch (Exception $e) {
        // Rollback transaction
        mysqli_rollback($conn);
        echo json_encode(['success' => false, 'message' => $e->getMessage()]);
    }
}

function editPatient($data) {
    global $conn;
    
    $patient_id = mysqli_real_escape_string($conn, $data['patient_id']);
    $nama = mysqli_real_escape_string($conn, $data['nama']);
    $jenis_kelamin = mysqli_real_escape_string($conn, $data['jenis_kelamin']);
    $tempat_lahir = mysqli_real_escape_string($conn, $data['tempat_lahir']);
    $tanggal_lahir = mysqli_real_escape_string($conn, $data['tanggal_lahir']);
    $usia = mysqli_real_escape_string($conn, $data['usia']);
    $alamat = mysqli_real_escape_string($conn, $data['alamat']);
    $no_telp = mysqli_real_escape_string($conn, $data['no_telp']);
    $pekerjaan = mysqli_real_escape_string($conn, $data['pekerjaan']);
    $agama = mysqli_real_escape_string($conn, $data['agama']);
    
    $query = "UPDATE pasien SET 
        nama = '$nama',
        tempat_lahir = '$tempat_lahir',
        tanggal_lahir = '$tanggal_lahir',
        usia = '$usia',
        jenis_kelamin = '$jenis_kelamin',
        alamat = '$alamat',
        no_telp = '$no_telp',
        pekerjaan = '$pekerjaan',
        agama = '$agama',
        updated_at = NOW()
        WHERE id = '$patient_id'";
    
    if (mysqli_query($conn, $query)) {
        echo json_encode(['success' => true, 'message' => 'Data pasien berhasil diupdate']);
    } else {
        echo json_encode(['success' => false, 'message' => 'Gagal mengupdate data pasien']);
    }
}

function deletePatient($patient_id) {
    global $conn;
    
    $patient_id = mysqli_real_escape_string($conn, $patient_id);
    
    // Check if patient has examination records
    $check_query = "SELECT COUNT(*) as total FROM pemeriksaan WHERE pasien_id = '$patient_id'";
    $check_result = mysqli_query($conn, $check_query);
    $total = mysqli_fetch_assoc($check_result)['total'];
    
    if ($total > 0) {
        echo json_encode(['success' => false, 'message' => 'Tidak dapat menghapus pasien yang sudah memiliki riwayat pemeriksaan']);
        return;
    }
    
    // Delete patient
    $query = "DELETE FROM pasien WHERE id = '$patient_id'";
    
    if (mysqli_query($conn, $query)) {
        echo json_encode(['success' => true, 'message' => 'Data pasien berhasil dihapus']);
    } else {
        echo json_encode(['success' => false, 'message' => 'Gagal menghapus data pasien']);
    }
}

function getDoctorInfo($doctor_id) {
    global $conn;
    
    $doctor_id = mysqli_real_escape_string($conn, $doctor_id);
    $query = "SELECT * FROM dokter WHERE id = '$doctor_id'";
    $result = mysqli_query($conn, $query);
    
    if ($result && mysqli_num_rows($result) > 0) {
        $doctor = mysqli_fetch_assoc($result);
        echo json_encode(['success' => true, 'doctor' => $doctor]);
    } else {
        echo json_encode(['success' => false, 'message' => 'Doctor not found']);
    }
}

function addDoctor($data) {
    global $conn;
    
    $username = mysqli_real_escape_string($conn, $data['username']);
    $password = mysqli_real_escape_string($conn, $data['password']);
    $nama = mysqli_real_escape_string($conn, $data['nama']);
    $spesialisasi = mysqli_real_escape_string($conn, $data['spesialisasi']);
    $no_sip = mysqli_real_escape_string($conn, $data['no_sip']);
    $email = mysqli_real_escape_string($conn, $data['email']);
    $no_telp = mysqli_real_escape_string($conn, $data['no_telp']);
    $alamat = mysqli_real_escape_string($conn, $data['alamat']);
    
    // Check if username already exists
    $check_query = "SELECT id FROM dokter WHERE username = '$username'";
    $check_result = mysqli_query($conn, $check_query);
    
    if (mysqli_num_rows($check_result) > 0) {
        echo json_encode(['success' => false, 'message' => 'Username sudah digunakan']);
        return;
    }
    
    $query = "INSERT INTO dokter (
        username, password, nama, spesialisasi, no_sip, email, no_telp, alamat, status
    ) VALUES (
        '$username', '$password', '$nama', '$spesialisasi', '$no_sip', '$email', '$no_telp', '$alamat', 'aktif'
    )";
    
    if (mysqli_query($conn, $query)) {
        echo json_encode(['success' => true, 'message' => 'Dokter berhasil ditambahkan']);
    } else {
        echo json_encode(['success' => false, 'message' => 'Gagal menambah dokter']);
    }
}

function editDoctor($data) {
    global $conn;
    
    $doctor_id = mysqli_real_escape_string($conn, $data['doctor_id']);
    $username = mysqli_real_escape_string($conn, $data['username']);
    $nama = mysqli_real_escape_string($conn, $data['nama']);
    $spesialisasi = mysqli_real_escape_string($conn, $data['spesialisasi']);
    $no_sip = mysqli_real_escape_string($conn, $data['no_sip']);
    $email = mysqli_real_escape_string($conn, $data['email']);
    $no_telp = mysqli_real_escape_string($conn, $data['no_telp']);
    $alamat = mysqli_real_escape_string($conn, $data['alamat']);
    $status = mysqli_real_escape_string($conn, $data['status']);
    
    // Check if username already exists (except current doctor)
    $check_query = "SELECT id FROM dokter WHERE username = '$username' AND id != '$doctor_id'";
    $check_result = mysqli_query($conn, $check_query);
    
    if (mysqli_num_rows($check_result) > 0) {
        echo json_encode(['success' => false, 'message' => 'Username sudah digunakan']);
        return;
    }
    
    $query = "UPDATE dokter SET 
        username = '$username',
        nama = '$nama',
        spesialisasi = '$spesialisasi',
        no_sip = '$no_sip',
        email = '$email',
        no_telp = '$no_telp',
        alamat = '$alamat',
        status = '$status',
        updated_at = NOW()
        WHERE id = '$doctor_id'";
    
    // Update password if provided
    if (!empty($data['password'])) {
        $password = mysqli_real_escape_string($conn, $data['password']);
        $query = str_replace(
            "status = '$status',", 
            "status = '$status', password = '$password',", 
            $query
        );
    }
    
    if (mysqli_query($conn, $query)) {
        echo json_encode(['success' => true, 'message' => 'Data dokter berhasil diupdate']);
    } else {
        echo json_encode(['success' => false, 'message' => 'Gagal mengupdate data dokter']);
    }
}

function deleteDoctor($doctor_id) {
    global $conn;
    
    $doctor_id = mysqli_real_escape_string($conn, $doctor_id);
    
    // Check if doctor has examination records
    $check_query = "SELECT COUNT(*) as total FROM pemeriksaan WHERE dokter_id = '$doctor_id'";
    $check_result = mysqli_query($conn, $check_query);
    $total = mysqli_fetch_assoc($check_result)['total'];
    
    if ($total > 0) {
        echo json_encode(['success' => false, 'message' => 'Tidak dapat menghapus dokter yang sudah memiliki riwayat pemeriksaan']);
        return;
    }
    
    // Delete doctor
    $query = "DELETE FROM dokter WHERE id = '$doctor_id'";
    
    if (mysqli_query($conn, $query)) {
        echo json_encode(['success' => true, 'message' => 'Data dokter berhasil dihapus']);
    } else {
        echo json_encode(['success' => false, 'message' => 'Gagal menghapus data dokter']);
    }
}

function updatePayment($data) {
    global $conn;
    
    $pemeriksaan_id = mysqli_real_escape_string($conn, $data['pemeriksaan_id']);
    $biaya_konsultasi = mysqli_real_escape_string($conn, $data['biaya_konsultasi']);
    $biaya_obat = mysqli_real_escape_string($conn, $data['biaya_obat']);
    $biaya_tindakan = mysqli_real_escape_string($conn, $data['biaya_tindakan']);
    $total_biaya = $biaya_konsultasi + $biaya_obat + $biaya_tindakan;
    
    // Get pendaftaran_id and pasien_id from pemeriksaan
    $get_query = "SELECT pendaftaran_id, pasien_id FROM pemeriksaan WHERE id = '$pemeriksaan_id'";
    $get_result = mysqli_query($conn, $get_query);
    
    if (!$get_result || mysqli_num_rows($get_result) == 0) {
        echo json_encode(['success' => false, 'message' => 'Data pemeriksaan tidak ditemukan']);
        return;
    }
    
    $examination = mysqli_fetch_assoc($get_result);
    $pendaftaran_id = $examination['pendaftaran_id'];
    $pasien_id = $examination['pasien_id'];
    
    // Check if payment record exists
    $check_query = "SELECT id FROM pembayaran WHERE pendaftaran_id = '$pendaftaran_id'";
    $check_result = mysqli_query($conn, $check_query);
    
    if (mysqli_num_rows($check_result) > 0) {
        // Update existing payment
        $query = "UPDATE pembayaran SET 
            biaya_konsultasi = '$biaya_konsultasi',
            biaya_obat = '$biaya_obat',
            biaya_tindakan = '$biaya_tindakan',
            total_biaya = '$total_biaya',
            status = 'sudah_bayar',
            tanggal_bayar = NOW(),
            updated_at = NOW()
            WHERE pendaftaran_id = '$pendaftaran_id'";
    } else {
        // Insert new payment record
        $query = "INSERT INTO pembayaran (
            pendaftaran_id, pasien_id, biaya_konsultasi, biaya_obat, biaya_tindakan,
            total_biaya, metode_pembayaran, status, tanggal_bayar
        ) VALUES (
            '$pendaftaran_id', '$pasien_id', '$biaya_konsultasi', '$biaya_obat', '$biaya_tindakan',
            '$total_biaya', 'tunai', 'sudah_bayar', NOW()
        )";
    }
    
    if (mysqli_query($conn, $query)) {
        // Update status pendaftaran menjadi 'lunas' untuk menghilangkan dari tampilan admin
        $update_pendaftaran = "UPDATE pendaftaran SET status = 'lunas' WHERE id = '$pendaftaran_id'";
        mysqli_query($conn, $update_pendaftaran);
        
        echo json_encode(['success' => true, 'message' => 'Pembayaran berhasil disimpan dan transaksi telah selesai']);
    } else {
        echo json_encode(['success' => false, 'message' => 'Gagal menyimpan pembayaran']);
    }
}

function markAttendance($pendaftaran_id) {
    global $conn;
    
    $pendaftaran_id = mysqli_real_escape_string($conn, $pendaftaran_id);
    
    $query = "UPDATE pendaftaran SET status = 'hadir' WHERE id = '$pendaftaran_id'";
    
    if (mysqli_query($conn, $query)) {
        echo json_encode(['success' => true, 'message' => 'Status kehadiran berhasil diupdate']);
    } else {
        echo json_encode(['success' => false, 'message' => 'Gagal mengupdate status kehadiran']);
    }
}

function getTransactionDetail($pemeriksaan_id) {
    global $conn;
    
    $pemeriksaan_id = mysqli_real_escape_string($conn, $pemeriksaan_id);
    
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
    
    if ($result && mysqli_num_rows($result) > 0) {
        $data = mysqli_fetch_assoc($result);
        
        // Build HTML content
        $html = '
        <div style="text-align: center; margin-bottom: 30px;">
            <h2 style="color: #1976d2; margin: 0;">ORRIMEDIKA</h2>
            <p style="margin: 5px 0;">Sistem Informasi Layanan Kesehatan</p>
            <hr style="margin: 20px 0;">
        </div>
        
        <div style="display: flex; gap: 20px; margin-bottom: 20px;">
            <div style="flex: 1; background-color: #f8f9fa; padding: 15px; border-radius: 8px;">
                <h4 style="color: #1976d2; margin-bottom: 15px;"><i class="fas fa-user"></i> Informasi Pasien</h4>
                <table style="width: 100%; font-size: 14px;">
                    <tr><td><strong>No. RM:</strong></td><td>' . htmlspecialchars($data['no_rm']) . '</td></tr>
                    <tr><td><strong>Nama:</strong></td><td>' . htmlspecialchars($data['pasien_nama']) . '</td></tr>
                    <tr><td><strong>Usia:</strong></td><td>' . $data['usia'] . ' tahun</td></tr>
                    <tr><td><strong>Jenis Kelamin:</strong></td><td>' . ($data['jenis_kelamin'] === 'L' ? 'Laki-laki' : 'Perempuan') . '</td></tr>
                    <tr><td><strong>Alamat:</strong></td><td>' . htmlspecialchars($data['alamat']) . '</td></tr>
                    <tr><td><strong>No. Telp:</strong></td><td>' . htmlspecialchars($data['no_telp']) . '</td></tr>
                </table>
            </div>
            
            <div style="flex: 1; background-color: #f8f9fa; padding: 15px; border-radius: 8px;">
                <h4 style="color: #1976d2; margin-bottom: 15px;"><i class="fas fa-user-md"></i> Informasi Dokter</h4>
                <table style="width: 100%; font-size: 14px;">
                    <tr><td><strong>Nama Dokter:</strong></td><td>' . htmlspecialchars($data['dokter_nama']) . '</td></tr>
                    <tr><td><strong>Spesialisasi:</strong></td><td>' . htmlspecialchars($data['spesialisasi']) . '</td></tr>
                    <tr><td><strong>Tanggal Periksa:</strong></td><td>' . date('d/m/Y H:i', strtotime($data['tanggal_periksa'])) . '</td></tr>
                </table>
            </div>
        </div>
        
        <div style="background-color: #fff; border: 1px solid #ddd; padding: 20px; border-radius: 8px; margin-bottom: 20px;">
            <h4 style="color: #1976d2; margin-bottom: 15px;"><i class="fas fa-stethoscope"></i> Hasil Pemeriksaan</h4>
            
            <div style="margin-bottom: 15px;">
                <strong>Keluhan Awal:</strong><br>
                <div style="background-color: #f8f9fa; padding: 10px; border-radius: 4px; margin-top: 5px;">
                    ' . htmlspecialchars($data['keluhan_awal']) . '
                </div>
            </div>
            
            <div style="margin-bottom: 15px;">
                <strong>Anamnesa:</strong><br>
                <div style="background-color: #f8f9fa; padding: 10px; border-radius: 4px; margin-top: 5px;">
                    ' . htmlspecialchars($data['anamnesa']) . '
                </div>
            </div>
            
            <div style="margin-bottom: 15px;">
                <strong>Pemeriksaan Fisik:</strong><br>
                <div style="background-color: #f8f9fa; padding: 10px; border-radius: 4px; margin-top: 5px;">
                    ' . htmlspecialchars($data['pemeriksaan_fisik']) . '
                </div>
            </div>
            
            <div style="margin-bottom: 15px;">
                <strong>Diagnosis:</strong><br>
                <div style="background-color: #e3f2fd; padding: 10px; border-radius: 4px; margin-top: 5px; font-weight: bold;">
                    ' . htmlspecialchars($data['diagnosa']) . '
                </div>
            </div>
            
            <div style="margin-bottom: 15px;">
                <strong>Terapi/Pengobatan:</strong><br>
                <div style="background-color: #f8f9fa; padding: 10px; border-radius: 4px; margin-top: 5px;">
                    ' . htmlspecialchars($data['terapi_pengobatan']) . '
                </div>
            </div>
            
            <div style="margin-bottom: 15px;">
                <strong>Catatan Tambahan:</strong><br>
                <div style="background-color: #f8f9fa; padding: 10px; border-radius: 4px; margin-top: 5px;">
                    ' . htmlspecialchars($data['catatan_tambahan']) . '
                </div>
            </div>
        </div>
        
        <div style="background-color: #fff; border: 1px solid #ddd; padding: 20px; border-radius: 8px;">
            <h4 style="color: #1976d2; margin-bottom: 15px;"><i class="fas fa-money-bill"></i> Rincian Biaya</h4>
            
            <table style="width: 100%; border-collapse: collapse;">
                <tr style="border-bottom: 1px solid #eee;">
                    <td style="padding: 8px 0;"><strong>Biaya Konsultasi:</strong></td>
                    <td style="text-align: right; padding: 8px 0;">Rp ' . number_format($data['biaya_konsultasi'], 0, ',', '.') . '</td>
                </tr>
                <tr style="border-bottom: 1px solid #eee;">
                    <td style="padding: 8px 0;"><strong>Biaya Tindakan:</strong></td>
                    <td style="text-align: right; padding: 8px 0;">Rp ' . number_format($data['biaya_tindakan'], 0, ',', '.') . '</td>
                </tr>
                <tr style="border-bottom: 1px solid #eee;">
                    <td style="padding: 8px 0;"><strong>Biaya Obat:</strong></td>
                    <td style="text-align: right; padding: 8px 0;">Rp ' . number_format($data['biaya_obat'], 0, ',', '.') . '</td>
                </tr>
                <tr style="border-top: 2px solid #1976d2; background-color: #e3f2fd;">
                    <td style="padding: 12px 0; font-size: 16px;"><strong>TOTAL BIAYA:</strong></td>
                    <td style="text-align: right; padding: 12px 0; font-size: 18px; font-weight: bold; color: #1976d2;">Rp ' . number_format($data['total_biaya'], 0, ',', '.') . '</td>
                </tr>
            </table>
            
            <div style="margin-top: 15px; text-align: center; color: #666;">
                <small>Pembayaran dilakukan pada: ' . date('d/m/Y H:i', strtotime($data['tanggal_bayar'])) . '</small>
            </div>
        </div>';
        
        echo json_encode(['success' => true, 'html' => $html]);
    } else {
        echo json_encode(['success' => false, 'message' => 'Data transaksi tidak ditemukan']);
    }
}
?>
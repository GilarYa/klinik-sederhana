<?php
session_start();

// Jika sudah login, redirect ke dashboard
if (isset($_SESSION['user'])) {
    header("Location: dashboard.php");
    exit();
}

// Handle logout
if (isset($_GET['action']) && $_GET['action'] === 'logout') {
    session_destroy();
    header("Location: login.php");
    exit();
}

include_once 'config.php';

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = mysqli_real_escape_string($conn, $_POST['username']);
    $password = $_POST['password'];
    $role = $_POST['role'];
    
    if (!empty($username) && !empty($password)) {
        // Query berdasarkan role
        if ($role === 'admin') {
            $query = "SELECT * FROM admin WHERE username = '$username'";
        } else {
            $query = "SELECT * FROM dokter WHERE username = '$username'";
        }
        
        $result = mysqli_query($conn, $query);
        
        if ($result && mysqli_num_rows($result) === 1) {
            $user = mysqli_fetch_assoc($result);
            
            // Verifikasi password (dalam implementasi nyata, gunakan password_verify())
            if ($password === $user['password']) {
                // Set session
                $_SESSION['user'] = [
                    'id' => $user['id'],
                    'username' => $user['username'],
                    'nama' => $user['nama'],
                    'role' => $role
                ];
                
                header("Location: dashboard.php");
                exit();
            } else {
                $error = 'Username atau password salah!';
            }
        } else {
            $error = 'Username atau password salah!';
        }
    } else {
        $error = 'Username dan password harus diisi!';
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - ORRIMEDIKA</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }
        
        body {
            background: linear-gradient(135deg, #1e88e5, #0d47a1);
            height: 100vh;
            display: flex;
            justify-content: center;
            align-items: center;
        }
        
        .login-container {
            background-color: white;
            border-radius: 10px;
            box-shadow: 0 10px 25px rgba(0, 0, 0, 0.2);
            width: 100%;
            max-width: 400px;
            padding: 30px;
        }
        
        .login-header {
            text-align: center;
            margin-bottom: 30px;
        }
        
        .login-header h1 {
            color: #1565c0;
            font-size: 28px;
            margin-bottom: 10px;
        }
        
        .login-header p {
            color: #616161;
            font-size: 14px;
        }
        
        .login-header img {
            width: 80px;
            margin-bottom: 15px;
        }
        
        .form-group {
            margin-bottom: 20px;
        }
        
        .form-group label {
            display: block;
            margin-bottom: 8px;
            color: #424242;
            font-weight: 500;
        }
        
        .form-group input, .form-group select {
            width: 100%;
            padding: 12px 15px;
            border: 1px solid #ddd;
            border-radius: 5px;
            font-size: 14px;
            transition: border-color 0.3s;
        }
        
        .form-group input:focus, .form-group select:focus {
            border-color: #1565c0;
            outline: none;
        }
        
        .btn-login {
            width: 100%;
            padding: 12px;
            background-color: #1976d2;
            color: white;
            border: none;
            border-radius: 5px;
            font-size: 16px;
            font-weight: 600;
            cursor: pointer;
            transition: background-color 0.3s;
        }
        
        .btn-login:hover {
            background-color: #1565c0;
        }
        
        .login-footer {
            text-align: center;
            margin-top: 20px;
            font-size: 14px;
            color: #616161;
        }
        
        .login-footer a {
            color: #1976d2;
            text-decoration: none;
        }
        
        .role-selector {
            display: flex;
            margin-bottom: 20px;
            border-radius: 5px;
            overflow: hidden;
            border: 1px solid #ddd;
        }
        
        .role-option {
            flex: 1;
            text-align: center;
            padding: 10px;
            background-color: #f5f5f5;
            cursor: pointer;
            transition: background-color 0.3s;
            border: none;
            font-size: 14px;
        }
        
        .role-option.active {
            background-color: #1976d2;
            color: white;
        }
        
        .error-message {
            background-color: #ffebee;
            color: #c62828;
            padding: 10px;
            border-radius: 5px;
            margin-bottom: 20px;
            text-align: center;
            font-size: 14px;
            display: <?php echo !empty($error) ? 'block' : 'none'; ?>;
        }
        
        @media (max-width: 480px) {
            .login-container {
                margin: 20px;
                padding: 20px;
            }
        }
    </style>
</head>
<body>
    <div class="login-container">
        <div class="login-header">
            <img src="data:image/svg+xml;base64,PHN2ZyB4bWxucz0iaHR0cDovL3d3dy53My5vcmcvMjAwMC9zdmciIHZpZXdCb3g9IjAgMCA1MTIgNTEyIj48cGF0aCBmaWxsPSIjMTk3NmQyIiBkPSJNMzIwIDQ0OGgtMTI4VjY0aDEyOHYzODR6TTQxNiAyNTZoLTEyOFY2NGgxMjh2MTkyek05NiA0NDhWNjRoMTI4djM4NEg5NnoiLz48L3N2Zz4=" alt="Logo Puskesmas">
            <h1>ORRIMEDIKA</h1>
            <p>Sistem Informasi Layanan Kesehatan</p>
        </div>
        
        <?php if (!empty($error)): ?>
        <div class="error-message">
            <?php echo htmlspecialchars($error); ?>
        </div>
        <?php endif; ?>
        
        <form method="POST">
            <div class="role-selector">
                <button type="button" class="role-option active" id="admin-role" onclick="selectRole('admin')">Admin</button>
                <button type="button" class="role-option" id="dokter-role" onclick="selectRole('dokter')">Dokter</button>
            </div>
            
            <input type="hidden" name="role" id="selected-role" value="admin">
            
            <div class="form-group">
                <label for="username">Username</label>
                <input type="text" name="username" id="username" placeholder="Masukkan username" required value="<?php echo isset($_POST['username']) ? htmlspecialchars($_POST['username']) : ''; ?>">
            </div>
            
            <div class="form-group">
                <label for="password">Password</label>
                <input type="password" name="password" id="password" placeholder="Masukkan password" required>
            </div>
            
            <button type="submit" class="btn-login">Masuk</button>
        </form>
        
        <div class="login-footer">
            <p>Lupa password? <a href="#">Hubungi Administrator</a></p>
            <br>
            <p><strong>Demo Login:</strong><br>
               Admin: admin / admin123<br>
               Dokter: dr.sari / dokter123
            </p>
        </div>
    </div>

    <script>
        function selectRole(role) {
            // Update button states
            const adminBtn = document.getElementById('admin-role');
            const dokterBtn = document.getElementById('dokter-role');
            const roleInput = document.getElementById('selected-role');
            
            if (role === 'admin') {
                adminBtn.classList.add('active');
                dokterBtn.classList.remove('active');
                roleInput.value = 'admin';
            } else {
                dokterBtn.classList.add('active');
                adminBtn.classList.remove('active');
                roleInput.value = 'dokter';
            }
        }
        
        // Set initial role state
        document.addEventListener('DOMContentLoaded', function() {
            const urlParams = new URLSearchParams(window.location.search);
            if (urlParams.get('role') === 'dokter') {
                selectRole('dokter');
            }
        });
    </script>
</body>
</html>
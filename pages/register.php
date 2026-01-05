<?php
/**
 * Registration Page
 * Sakinah Style - Customer Registration
 */

require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../functions.php';

$page_title = "Daftar Akun - Sakinah Style";
$page_description = "Buat akun baru untuk pengalaman belanja yang lebih baik";

$errors = [];
$success = false;

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $nama = trim($_POST['nama'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';
    $confirm_password = $_POST['confirm_password'] ?? '';
    $phone = trim($_POST['phone'] ?? '');
    
    // Validation
    if (empty($nama)) {
        $errors[] = "Nama wajib diisi";
    }
    
    if (empty($email)) {
        $errors[] = "Email wajib diisi";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors[] = "Format email tidak valid";
    }
    
    if (empty($password)) {
        $errors[] = "Password wajib diisi";
    } elseif (strlen($password) < 6) {
        $errors[] = "Password minimal 6 karakter";
    }
    
    if ($password !== $confirm_password) {
        $errors[] = "Password dan konfirmasi password tidak sama";
    }
    
    if (empty($phone)) {
        $errors[] = "Nomor HP wajib diisi";
    }
    
    // Check if email already exists
    if (empty($errors)) {
        $pdo = getDBConnection();
        try {
            $checkEmail = $pdo->prepare("SELECT ID_Pelanggan FROM Pelanggan WHERE Email = :email");
            $checkEmail->execute([':email' => $email]);
            if ($checkEmail->fetch()) {
                $errors[] = "Email sudah terdaftar";
            }
        } catch (PDOException $e) {
            error_log("Error checking email: " . $e->getMessage());
            $errors[] = "Terjadi kesalahan. Silakan coba lagi.";
        }
    }
    
    // Check if phone already exists
    if (empty($errors) && !empty($phone)) {
        try {
            $checkPhone = $pdo->prepare("SELECT ID_Pelanggan FROM Pelanggan WHERE No_HP = :phone");
            $checkPhone->execute([':phone' => $phone]);
            if ($checkPhone->fetch()) {
                $errors[] = "Nomor HP sudah terdaftar";
            }
        } catch (PDOException $e) {
            error_log("Error checking phone: " . $e->getMessage());
        }
    }
    
    // Register user
    if (empty($errors)) {
        try {
            // Generate Customer ID
            $year = date('Y');
            $checkLast = $pdo->query("SELECT ID_Pelanggan FROM Pelanggan WHERE ID_Pelanggan LIKE 'PLG-{$year}-%' ORDER BY ID_Pelanggan DESC LIMIT 1");
            $lastId = $checkLast->fetch();
            
            if ($lastId) {
                $lastNum = (int)substr($lastId['ID_Pelanggan'], -3);
                $newNum = str_pad($lastNum + 1, 3, '0', STR_PAD_LEFT);
            } else {
                $newNum = '001';
            }
            
            $id_pelanggan = "PLG-{$year}-{$newNum}";
            
            // Hash password
            $hashed_password = password_hash($password, PASSWORD_DEFAULT);
            
            // Insert into database
            $sql = "INSERT INTO Pelanggan (ID_Pelanggan, Nama_Pelanggan, Email, Password, No_HP, Role) 
                    VALUES (:id, :nama, :email, :password, :phone, 'customer')";
            
            $stmt = $pdo->prepare($sql);
            $stmt->execute([
                ':id' => $id_pelanggan,
                ':nama' => $nama,
                ':email' => $email,
                ':password' => $hashed_password,
                ':phone' => $phone
            ]);
            
            // Auto login after registration
            $_SESSION['user'] = [
                'id' => $id_pelanggan,
                'name' => $nama,
                'email' => $email,
                'role' => 'customer'
            ];
            
            $success = true;
            
            // Redirect to profile page after 2 seconds
            header("Refresh: 2; url=profile");
            
        } catch (PDOException $e) {
            error_log("Error registering user: " . $e->getMessage());
            $errors[] = "Terjadi kesalahan saat mendaftar. Silakan coba lagi.";
        }
    }
}

include __DIR__ . '/../includes/header.php';
?>

<div class="container my-5">
    <div class="row justify-content-center">
        <div class="col-lg-6 col-md-8">
            <div class="card shadow-sm">
                <div class="card-header bg-primary text-white text-center">
                    <h4 class="mb-0">
                        <i class="bi bi-person-plus"></i> Daftar Akun Baru
                    </h4>
                </div>
                <div class="card-body p-4">
                    <?php if ($success): ?>
                        <div class="alert alert-success">
                            <i class="bi bi-check-circle"></i> 
                            <strong>Pendaftaran Berhasil!</strong> Anda akan diarahkan ke halaman profil...
                        </div>
                    <?php else: ?>
                        <?php if (!empty($errors)): ?>
                            <div class="alert alert-danger">
                                <i class="bi bi-exclamation-triangle"></i>
                                <ul class="mb-0">
                                    <?php foreach ($errors as $error): ?>
                                        <li><?php echo htmlspecialchars($error); ?></li>
                                    <?php endforeach; ?>
                                </ul>
                            </div>
                        <?php endif; ?>
                        
                        <form method="POST" action="register.php">
                            <div class="mb-3">
                                <label for="nama" class="form-label">Nama Lengkap <span class="text-danger">*</span></label>
                                <input type="text" 
                                       class="form-control" 
                                       id="nama" 
                                       name="nama" 
                                       value="<?php echo isset($_POST['nama']) ? htmlspecialchars($_POST['nama']) : ''; ?>"
                                       required>
                            </div>
                            
                            <div class="mb-3">
                                <label for="email" class="form-label">Email <span class="text-danger">*</span></label>
                                <input type="email" 
                                       class="form-control" 
                                       id="email" 
                                       name="email" 
                                       value="<?php echo isset($_POST['email']) ? htmlspecialchars($_POST['email']) : ''; ?>"
                                       required>
                            </div>
                            
                            <div class="mb-3">
                                <label for="phone" class="form-label">Nomor HP/WhatsApp <span class="text-danger">*</span></label>
                                <input type="text" 
                                       class="form-control" 
                                       id="phone" 
                                       name="phone" 
                                       value="<?php echo isset($_POST['phone']) ? htmlspecialchars($_POST['phone']) : ''; ?>"
                                       placeholder="08xxxxxxxxxx"
                                       required>
                            </div>
                            
                            <div class="mb-3">
                                <label for="password" class="form-label">Password <span class="text-danger">*</span></label>
                                <input type="password" 
                                       class="form-control" 
                                       id="password" 
                                       name="password" 
                                       minlength="6"
                                       required>
                                <small class="text-muted">Minimal 6 karakter</small>
                            </div>
                            
                            <div class="mb-3">
                                <label for="confirm_password" class="form-label">Konfirmasi Password <span class="text-danger">*</span></label>
                                <input type="password" 
                                       class="form-control" 
                                       id="confirm_password" 
                                       name="confirm_password" 
                                       minlength="6"
                                       required>
                            </div>
                            
                            <div class="d-grid gap-2">
                                <button type="submit" class="btn btn-primary btn-lg">
                                    <i class="bi bi-person-check"></i> Daftar Sekarang
                                </button>
                                <a href="login.php" class="btn btn-outline-primary">
                                    Sudah punya akun? Masuk di sini
                                </a>
                            </div>
                        </form>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</div>

<?php include __DIR__ . '/../includes/footer.php'; ?>


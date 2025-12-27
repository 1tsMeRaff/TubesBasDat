<?php
/**
 * Customer Profile Dashboard
 * Sakinah Style - Profile Management
 */

require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../functions.php';

// Check if user is logged in
if (!isset($_SESSION['user'])) {
    header('Location: login.php?redirect=profile.php');
    exit;
}

$page_title = "Profil Saya - Sakinah Style";
$page_description = "Kelola profil dan informasi akun Anda";

$user_id = $_SESSION['user']['id'];
$errors = [];
$success = false;

// Handle profile update
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['action']) && $_POST['action'] == 'update_profile') {
    $nama = trim($_POST['nama'] ?? '');
    $phone = trim($_POST['phone'] ?? '');
    $alamat = trim($_POST['alamat'] ?? '');
    
    if (empty($nama)) {
        $errors[] = "Nama wajib diisi";
    }
    
    if (empty($errors)) {
        $pdo = getDBConnection();
        try {
            $sql = "UPDATE Pelanggan 
                    SET Nama_Pelanggan = :nama, No_HP = :phone, Alamat_Utama = :alamat 
                    WHERE ID_Pelanggan = :id";
            
            $stmt = $pdo->prepare($sql);
            $stmt->execute([
                ':nama' => $nama,
                ':phone' => $phone,
                ':alamat' => $alamat,
                ':id' => $user_id
            ]);
            
            // Update session
            $_SESSION['user']['name'] = $nama;
            $_SESSION['user']['phone'] = $phone;
            $_SESSION['user']['address'] = $alamat;
            
            $success = true;
        } catch (PDOException $e) {
            error_log("Error updating profile: " . $e->getMessage());
            $errors[] = "Terjadi kesalahan saat memperbarui profil.";
        }
    }
}

// Handle password change
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['action']) && $_POST['action'] == 'change_password') {
    $current_password = $_POST['current_password'] ?? '';
    $new_password = $_POST['new_password'] ?? '';
    $confirm_password = $_POST['confirm_password'] ?? '';
    
    if (empty($current_password)) {
        $errors[] = "Password saat ini wajib diisi";
    }
    
    if (empty($new_password)) {
        $errors[] = "Password baru wajib diisi";
    } elseif (strlen($new_password) < 6) {
        $errors[] = "Password baru minimal 6 karakter";
    }
    
    if ($new_password !== $confirm_password) {
        $errors[] = "Password baru dan konfirmasi tidak sama";
    }
    
    if (empty($errors)) {
        $pdo = getDBConnection();
        try {
            // Verify current password
            $sql = "SELECT Password FROM Pelanggan WHERE ID_Pelanggan = :id";
            $stmt = $pdo->prepare($sql);
            $stmt->execute([':id' => $user_id]);
            $user = $stmt->fetch();
            
            if ($user && password_verify($current_password, $user['Password'])) {
                // Update password
                $hashed_password = password_hash($new_password, PASSWORD_DEFAULT);
                $updateSql = "UPDATE Pelanggan SET Password = :password WHERE ID_Pelanggan = :id";
                $updateStmt = $pdo->prepare($updateSql);
                $updateStmt->execute([
                    ':password' => $hashed_password,
                    ':id' => $user_id
                ]);
                
                $success = true;
                $success_message = "Password berhasil diubah";
            } else {
                $errors[] = "Password saat ini salah";
            }
        } catch (PDOException $e) {
            error_log("Error changing password: " . $e->getMessage());
            $errors[] = "Terjadi kesalahan saat mengubah password.";
        }
    }
}

// Get user data
$pdo = getDBConnection();
try {
    $sql = "SELECT * FROM Pelanggan WHERE ID_Pelanggan = :id";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([':id' => $user_id]);
    $user_data = $stmt->fetch();
} catch (PDOException $e) {
    error_log("Error fetching user data: " . $e->getMessage());
    $user_data = null;
}

include __DIR__ . '/../includes/header.php';
?>

<div class="container my-5">
    <div class="row">
        <!-- Sidebar Menu -->
        <div class="col-lg-3 mb-4">
            <div class="card shadow-sm">
                <div class="card-header bg-primary text-white">
                    <h5 class="mb-0"><i class="bi bi-person-circle"></i> Menu</h5>
                </div>
                <ul class="list-group list-group-flush">
                    <li class="list-group-item active">
                        <i class="bi bi-person"></i> Profil Saya
                    </li>
                    <li class="list-group-item">
                        <a href="my-orders.php" class="text-decoration-none text-dark">
                            <i class="bi bi-bag-check"></i> Riwayat Pesanan
                        </a>
                    </li>
                    <li class="list-group-item">
                        <a href="../cart.php" class="text-decoration-none text-dark">
                            <i class="bi bi-cart"></i> Keranjang
                        </a>
                    </li>
                    <li class="list-group-item">
                        <a href="logout.php" class="text-decoration-none text-danger">
                            <i class="bi bi-box-arrow-right"></i> Keluar
                        </a>
                    </li>
                </ul>
            </div>
        </div>
        
        <!-- Main Content -->
        <div class="col-lg-9">
            <h2 class="mb-4">Profil Saya</h2>
            
            <?php if ($success): ?>
                <div class="alert alert-success alert-dismissible fade show">
                    <i class="bi bi-check-circle"></i> 
                    <?php echo isset($success_message) ? $success_message : 'Profil berhasil diperbarui!'; ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            <?php endif; ?>
            
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
            
            <!-- Edit Profile Tab -->
            <div class="card shadow-sm mb-4">
                <div class="card-header">
                    <h5 class="mb-0"><i class="bi bi-pencil"></i> Edit Profil</h5>
                </div>
                <div class="card-body">
                    <form method="POST" action="profile.php">
                        <input type="hidden" name="action" value="update_profile">
                        
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="nama" class="form-label">Nama Lengkap</label>
                                <input type="text" 
                                       class="form-control" 
                                       id="nama" 
                                       name="nama" 
                                       value="<?php echo htmlspecialchars($user_data['Nama_Pelanggan'] ?? ''); ?>"
                                       required>
                            </div>
                            
                            <div class="col-md-6 mb-3">
                                <label for="email" class="form-label">Email</label>
                                <input type="email" 
                                       class="form-control" 
                                       id="email" 
                                       value="<?php echo htmlspecialchars($user_data['Email'] ?? ''); ?>"
                                       disabled>
                                <small class="text-muted">Email tidak dapat diubah</small>
                            </div>
                        </div>
                        
                        <div class="mb-3">
                            <label for="phone" class="form-label">Nomor HP/WhatsApp</label>
                            <input type="text" 
                                   class="form-control" 
                                   id="phone" 
                                   name="phone" 
                                   value="<?php echo htmlspecialchars($user_data['No_HP'] ?? ''); ?>"
                                   placeholder="08xxxxxxxxxx">
                        </div>
                        
                        <div class="mb-3">
                            <label for="alamat" class="form-label">Alamat Utama</label>
                            <textarea class="form-control" 
                                      id="alamat" 
                                      name="alamat" 
                                      rows="3"><?php echo htmlspecialchars($user_data['Alamat_Utama'] ?? ''); ?></textarea>
                        </div>
                        
                        <button type="submit" class="btn btn-primary">
                            <i class="bi bi-save"></i> Simpan Perubahan
                        </button>
                    </form>
                </div>
            </div>
            
            <!-- Change Password Tab -->
            <div class="card shadow-sm">
                <div class="card-header">
                    <h5 class="mb-0"><i class="bi bi-key"></i> Ganti Password</h5>
                </div>
                <div class="card-body">
                    <form method="POST" action="profile.php">
                        <input type="hidden" name="action" value="change_password">
                        
                        <div class="mb-3">
                            <label for="current_password" class="form-label">Password Saat Ini</label>
                            <input type="password" 
                                   class="form-control" 
                                   id="current_password" 
                                   name="current_password" 
                                   required>
                        </div>
                        
                        <div class="mb-3">
                            <label for="new_password" class="form-label">Password Baru</label>
                            <input type="password" 
                                   class="form-control" 
                                   id="new_password" 
                                   name="new_password" 
                                   minlength="6"
                                   required>
                            <small class="text-muted">Minimal 6 karakter</small>
                        </div>
                        
                        <div class="mb-3">
                            <label for="confirm_password" class="form-label">Konfirmasi Password Baru</label>
                            <input type="password" 
                                   class="form-control" 
                                   id="confirm_password" 
                                   name="confirm_password" 
                                   minlength="6"
                                   required>
                        </div>
                        
                        <button type="submit" class="btn btn-primary">
                            <i class="bi bi-key"></i> Ubah Password
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<?php include __DIR__ . '/../includes/footer.php'; ?>


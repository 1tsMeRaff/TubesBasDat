<?php
/**
 * Login Page
 * Sakinah Style - Customer Login
 */

require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../functions.php';

$page_title = "Masuk - Sakinah Style";
$page_description = "Masuk ke akun Anda untuk melanjutkan belanja";

// Redirect if already logged in
if (isset($_SESSION['user'])) {
    header('Location: index');
    exit;
}

$errors = [];
$redirect = $_GET['redirect'] ?? 'profile.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $email = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';
    $remember = isset($_POST['remember']);
    
    // Validation
    if (empty($email)) {
        $errors[] = "Email wajib diisi";
    }
    
    if (empty($password)) {
        $errors[] = "Password wajib diisi";
    }
    
    // Authenticate
    if (empty($errors)) {
        $pdo = getDBConnection();
        try {
            $sql = "SELECT ID_Pelanggan, Nama_Pelanggan, Email, Password, Role, No_HP, Alamat_Utama 
                    FROM Pelanggan 
                    WHERE Email = :email";
            
            $stmt = $pdo->prepare($sql);
            $stmt->execute([':email' => $email]);
            $user = $stmt->fetch();
            
            if ($user && password_verify($password, $user['Password'])) {
                // Login successful
                $_SESSION['user'] = [
                    'id' => $user['ID_Pelanggan'],
                    'name' => $user['Nama_Pelanggan'],
                    'email' => $user['Email'],
                    'role' => $user['Role'] ?? 'customer',
                    'phone' => $user['No_HP'] ?? null,
                    'address' => $user['Alamat_Utama'] ?? null
                ];
                
                // Handle remember me (optional - set cookie)
                if ($remember) {
                    // You can implement remember me functionality here
                    // For now, we'll just use session
                }
                
                // Redirect berdasarkan role
                if (($user['Role'] ?? 'customer') === 'admin') {
                    header('Location: ../admin/index.php');
                } else {
                header('Location: profile.php');
                }
            exit;
 
            } else {
                $errors[] = "Email atau password salah";
            }
        } catch (PDOException $e) {
            error_log("Error during login: " . $e->getMessage());
            $errors[] = "Terjadi kesalahan. Silakan coba lagi.";
        }
    }
}

include __DIR__ . '/../includes/header.php';
?>

<div class="container my-5">
    <div class="row justify-content-center">
        <div class="col-lg-5 col-md-7">
            <div class="card shadow-sm">
                <div class="card-header bg-primary text-white text-center">
                    <h4 class="mb-0">
                        <i class="bi bi-box-arrow-in-right"></i> Masuk ke Akun
                    </h4>
                </div>
                <div class="card-body p-4">
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
                    
                    <form method="POST" action="login.php">
                        <input type="hidden" name="redirect" value="<?php echo htmlspecialchars($redirect); ?>">
                        
                        <div class="mb-3">
                            <label for="email" class="form-label">Email</label>
                            <input type="email" 
                                   class="form-control" 
                                   id="email" 
                                   name="email" 
                                   value="<?php echo isset($_POST['email']) ? htmlspecialchars($_POST['email']) : ''; ?>"
                                   required
                                   autofocus>
                        </div>
                        
                        <div class="mb-3">
                            <label for="password" class="form-label">Password</label>
                            <input type="password" 
                                   class="form-control" 
                                   id="password" 
                                   name="password" 
                                   required>
                        </div>
                        
                        <div class="mb-3 form-check">
                            <input type="checkbox" 
                                   class="form-check-input" 
                                   id="remember" 
                                   name="remember">
                            <label class="form-check-label" for="remember">
                                Ingat saya
                            </label>
                        </div>
                        
                        <div class="d-grid gap-2">
                            <button type="submit" class="btn btn-primary btn-lg">
                                <i class="bi bi-box-arrow-in-right"></i> Masuk
                            </button>
                            <a href="register.php" class="btn btn-outline-primary">
                                Belum punya akun? Daftar di sini
                            </a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<?php include __DIR__ . '/../includes/footer.php'; ?>

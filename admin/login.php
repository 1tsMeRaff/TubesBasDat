<?php
session_start();
require_once "../config/database.php";

$error = "";

if (isset($_POST['login'])) {
    $email = mysqli_real_escape_string($conn, $_POST['email']);
    $password = $_POST['password'];

    $query = "SELECT * FROM pelanggan 
              WHERE Email='$email' AND Role='admin' LIMIT 1";
    $result = mysqli_query($conn, $query);

    if (mysqli_num_rows($result) === 1) {
        $admin = mysqli_fetch_assoc($result);

        // Jika password masih plaintext (sementara)
        if ($password === $admin['Password'] || password_verify($password, $admin['Password'])) {

            $_SESSION['admin_login'] = true;
            $_SESSION['admin_id']    = $admin['ID_Pelanggan'];
            $_SESSION['admin_nama']  = $admin['Nama_Pelanggan'];

            header("Location: index.php");
            exit;
        } else {
            $error = "Password salah";
        }
    } else {
        $error = "Akun admin tidak ditemukan";
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Login Admin - Sakinah Style</title>
</head>
<body>

<h2>Login Admin Sakinah Style</h2>

<?php if ($error): ?>
    <p style="color:red"><?= $error ?></p>
<?php endif; ?>

<form method="post">
    <label>Email</label><br>
    <input type="email" name="email" required><br><br>

    <label>Password</label><br>
    <input type="password" name="password" required><br><br>

    <button type="submit" name="login">Login</button>
</form>

</body>
</html>

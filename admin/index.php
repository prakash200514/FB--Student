<?php
// admin/index.php
session_start();
$path = "../config.php";
if (file_exists($path)) include $path;
else { $conn = new mysqli("localhost", "root", "", "student_feedback_db"); }

if (isset($_SESSION['admin_logged_in'])) { header("location: dashboard.php"); exit; }

$error = '';
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $user = $_POST['username'];
    $pass = $_POST['password'];
    $stmt = $conn->prepare("SELECT id, password FROM admins WHERE username = ?");
    $stmt->bind_param("s", $user);
    $stmt->execute();
    $stmt->store_result();
    if ($stmt->num_rows > 0) {
        $stmt->bind_result($id, $hashed_password);
        $stmt->fetch();
        if (password_verify($pass, $hashed_password)) {
            $_SESSION['admin_logged_in'] = true;
            header("location: dashboard.php"); exit;
        } else { $error = "Invalid Password"; }
    } else { $error = "Invalid Username"; }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Login – Academic Portal</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="../assets/css/style.css" rel="stylesheet">
</head>
<body>

<!-- Navbar -->
<nav class="ap-navbar">
    <a class="ap-navbar-brand" href="#">
        <span class="brand-icon">🎓</span>
        Academic Portal
    </a>
    <div class="ap-navbar-right">
        <a href="../index.php" class="btn-nav">Student Login</a>
    </div>
</nav>

<!-- Login Wrap -->
<div class="ap-login-wrap">
    <div class="ap-login-card">

        <div style="text-align:center; margin-bottom:1.5rem;">
            <span class="ap-hero-badge">Administrator</span>
        </div>

        <h1 class="ap-login-title">Admin Login</h1>
        <p class="ap-login-subtitle">Restricted access — authorised personnel only</p>

        <?php if ($error): ?>
        <div class="ap-alert-danger"><?php echo $error; ?></div>
        <?php endif; ?>

        <form method="post">
            <div class="mb-3">
                <label class="form-label fw-bold" style="font-size:1.0rem; color:var(--text-mid); letter-spacing:0.5px; text-transform:uppercase;">Username</label>
                <input type="text" name="username" class="form-control" required placeholder="Enter username">
            </div>
            <div class="mb-4">
                <label class="form-label fw-bold" style="font-size:1.0rem; color:var(--text-mid); letter-spacing:0.5px; text-transform:uppercase;">Password</label>
                <input type="password" name="password" class="form-control" required placeholder="Enter password">
            </div>
            <div class="d-grid">
                <button type="submit" class="btn-primary-custom" style="font-size:1.05rem; padding:12px;">
                    Login to Dashboard
                </button>
            </div>
        </form>

    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>

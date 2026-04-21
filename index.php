<?php
// index.php
include 'config.php';

if (isset($_SESSION['register_number'])) {
    header("location: dashboard.php");
    exit;
}

$error = '';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $name     = trim($_POST['name']);
    $reg_no   = trim($_POST['register_number']);
    $program  = isset($_POST['program']) ? trim($_POST['program']) : '';
    $semester = isset($_POST['semester']) ? trim($_POST['semester']) : '';

    if (empty($name) || empty($reg_no) || empty($program) || empty($semester)) {
        $error = "Please fill all fields.";
    } else {
        $_SESSION['student_name']    = $name;
        $_SESSION['register_number'] = $reg_no;
        $_SESSION['program']         = $program;
        $_SESSION['semester']        = $semester;
        header("location: dashboard.php");
        exit;
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Student Login – Academic Portal</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <link href="assets/css/style.css" rel="stylesheet">
</head>
<body>

<!-- ── Navigation Bar ── -->
<nav class="ap-navbar">
    <a class="ap-navbar-brand" href="#">
        <span class="brand-icon">🎓</span>
        Academic Portal
    </a>
    <div class="ap-navbar-right"></div>
</nav>

<!-- ── Login Container ── -->
<div class="ap-login-wrap">
    <div class="ap-login-card">

        <div style="text-align:center; margin-bottom:1.5rem;">
            <span class="ap-hero-badge">Student Portal</span>
        </div>

        <h1 class="ap-login-title">Welcome Back</h1>
        <p class="ap-login-subtitle">Enter your details to access the feedback portal</p>

        <?php if ($error): ?>
        <div class="ap-alert-danger"><?php echo $error; ?></div>
        <?php endif; ?>

        <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post">
            <div class="mb-3">
                <label for="name" class="form-label fw-bold" style="font-size:1.0rem; color:var(--text-mid); letter-spacing:0.5px; text-transform:uppercase;">Full Name</label>
                <input type="text" name="name" id="name" class="form-control" required placeholder="Enter your full name">
            </div>
            <div class="mb-3">
                <label for="register_number" class="form-label fw-bold" style="font-size:1.0rem; color:var(--text-mid); letter-spacing:0.5px; text-transform:uppercase;">Register Number</label>
                <input type="text" name="register_number" id="register_number" class="form-control" required placeholder="E.g. REG12345">
            </div>
            <div class="mb-3">
                <label for="program" class="form-label fw-bold" style="font-size:1.0rem; color:var(--text-mid); letter-spacing:0.5px; text-transform:uppercase;">Academic Level</label>
                <select name="program" id="program" class="form-control form-select" required onchange="updateSemesters()">
                    <option value="" disabled selected>Select Academic Level</option>
                    <option value="UG">UG</option>
                    <option value="PG">PG</option>
                </select>
            </div>
            <div class="mb-4">
                <label for="semester" class="form-label fw-bold" style="font-size:1.0rem; color:var(--text-mid); letter-spacing:0.5px; text-transform:uppercase;">Semester</label>
                <select name="semester" id="semester" class="form-control form-select" required>
                    <option value="" disabled selected>Select Semester</option>
                </select>
            </div>
            <div class="d-grid">
                <button type="submit" class="btn-primary-custom" style="font-size:1.05rem; padding:12px;">
                    Login to Portal
                </button>
            </div>
        </form>

    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script>
function updateSemesters() {
    const program = document.getElementById('program').value;
    const semesterSelect = document.getElementById('semester');
    semesterSelect.innerHTML = '<option value="" disabled selected>Select Semester</option>';
    
    let numSemesters = 0;
    if (program === 'UG') {
        numSemesters = 6;
    } else if (program === 'PG') {
        numSemesters = 4;
    }
    
    for (let i = 1; i <= numSemesters; i++) {
        const option = document.createElement('option');
        option.value = 'Semester ' + i;
        option.textContent = 'Semester ' + i;
        semesterSelect.appendChild(option);
    }
}
</script>
</body>
</html>

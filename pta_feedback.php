<?php
include 'config.php';
if (!isset($_SESSION['register_number'])) { header("location: index.php"); exit; }

$message = "";

$currentMonth = date('n');
$currentYear  = date('Y');
if ($currentMonth >= 6) {
    $academic_year    = $currentYear . '-' . ($currentYear + 1);
    $semester_display = "Odd";
} else {
    $academic_year    = ($currentYear - 1) . '-' . $currentYear;
    $semester_display = "Even";
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $parent_name = $_POST['parent_name'];
    $address     = $_POST['address'];
    $mobile      = $_POST['mobile'];
    $semester    = $_POST['semester'];
    $part1_q1    = $_POST['part1_q1'];
    $part1_q2    = $_POST['part1_q2'];
    $part2_q1    = $_POST['part2_q1'];
    $part2_q2    = $_POST['part2_q2'];
    $part2_q3    = $_POST['part2_q3'];
    $part3_q1    = $_POST['part3_q1'];
    $part4_q1    = $_POST['part4_q1'];

    $program = $_SESSION['program'] ?? 'UG';
    $sql = "INSERT INTO feedback_pta 
(student_name, register_number, program, parent_name, address, mobile_no, semester, part1_q1, part1_q2, part2_q1, part2_q2, part2_q3, part3_q1, part4_q1) 
VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";

    $stmt = $conn->prepare($sql);
    $stmt->bind_param("sssssssiiiiisi",
        $_SESSION['student_name'],
        $_SESSION['register_number'],
        $program,
        $parent_name,
        $address,
        $mobile,
        $semester,
        $part1_q1,
        $part1_q2,
        $part2_q1,
        $part2_q2,
        $part2_q3,
        $part3_q1,
        $part4_q1
    );

    if ($stmt->execute()) {
        echo "<script>alert('PTA Feedback Submitted Successfully!'); window.location='dashboard.php';</script>";
        exit;
    } else {
        $message = "Error: " . $stmt->error;
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>PTA Feedback – Academic Portal</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <link href="assets/css/style.css" rel="stylesheet">
</head>
<body>

<!-- ── Navbar ── -->
<nav class="ap-navbar">
    <a class="ap-navbar-brand" href="dashboard.php">
        <span class="brand-icon">🎓</span>
        Academic Portal
    </a>
    <div class="ap-navbar-right">
        <a href="dashboard.php" class="btn-nav">← Back</a>
        <a href="logout.php" class="btn-nav">Logout</a>
    </div>
</nav>

<!-- ── Page Wrap ── -->
<div class="ap-page-wrap">

    <!-- Page Header -->
    <div class="ap-page-header">
        <span class="ap-hero-badge" style="margin-bottom:0.6rem;">PTA Evaluation</span>
        <h1 class="ap-page-title">PTA Feedback</h1>
        <p class="ap-page-meta">St. John's College, Palayamkottai &nbsp;·&nbsp; Department of Computer Science</p>
    </div>

    <?php if ($message): ?>
    <div class="ap-alert-danger"><?php echo $message; ?></div>
    <?php endif; ?>

    <form method="post">

        <!-- Parent & Student Details -->
        <div class="ap-section-card">
            <h2 class="ap-section-title">Parent &amp; Student Details</h2>
            <div class="row g-3">
                <div class="col-md-6">
                    <label class="form-label fw-bold" style="font-size:0.95rem; text-transform:uppercase; color:var(--text-mid); letter-spacing:0.5px;">Parent Name</label>
                    <input type="text" name="parent_name" class="form-control" required placeholder="Enter parent name">
                </div>
                <div class="col-md-6">
                    <label class="form-label fw-bold" style="font-size:0.95rem; text-transform:uppercase; color:var(--text-mid); letter-spacing:0.5px;">Mobile No.</label>
                    <input type="text" name="mobile" class="form-control" required placeholder="Enter mobile number">
                </div>
                <div class="col-md-6">
                    <label class="form-label fw-bold" style="font-size:0.95rem; text-transform:uppercase; color:var(--text-mid); letter-spacing:0.5px;">Address</label>
                    <input type="text" name="address" class="form-control" required placeholder="Enter address">
                </div>
                <div class="col-md-3">
                    <label class="form-label fw-bold" style="font-size:0.95rem; text-transform:uppercase; color:var(--text-mid); letter-spacing:0.5px;">Student Name</label>
                    <input type="text" class="form-control" value="<?php echo htmlspecialchars($_SESSION['student_name']); ?>" readonly style="background:#faf8f4;">
                </div>
                <div class="col-md-3">
                    <label class="form-label fw-bold" style="font-size:0.95rem; text-transform:uppercase; color:var(--text-mid); letter-spacing:0.5px;">Semester</label>
                    <input type="text" name="semester" class="form-control" value="<?php echo $_SESSION['semester']; ?>" required>
                </div>
                <div class="col-md-3">
                    <label class="form-label fw-bold" style="font-size:0.95rem; text-transform:uppercase; color:var(--text-mid); letter-spacing:0.5px;">Class</label>
                    <input type="text" class="form-control" value="B.Sc Computer Science" readonly style="background:#faf8f4;">
                </div>
                <div class="col-md-3">
                    <label class="form-label fw-bold" style="font-size:0.95rem; text-transform:uppercase; color:var(--text-mid); letter-spacing:0.5px;">Date</label>
                    <input type="text" class="form-control" value="<?php echo date('d-m-Y'); ?>" readonly style="background:#faf8f4;">
                </div>
            </div>
        </div>

        <!-- Part I -->
        <div class="ap-section-sep">Part I</div>
        <div class="ap-exact-scale" style="margin-bottom: 2rem;">
            <div class="ap-exact-nums">
                <span class="ap-num" style="left: 10%; transform: translateX(-50%);">5</span>
                <span class="ap-num" style="left: 30%; transform: translateX(-50%);">4</span>
                <span class="ap-num" style="left: 50%; transform: translateX(-50%);">3</span>
                <span class="ap-num" style="left: 70%; transform: translateX(-50%);">2</span>
                <span class="ap-num" style="left: 90%; transform: translateX(-50%);">1</span>
            </div>
            <div class="ap-exact-bar">
                <div class="ap-exact-bar-segment" style="width: 20%;"></div>
                <div class="ap-exact-bar-segment" style="width: 20%;"></div>
                <div class="ap-exact-bar-segment" style="width: 20%;"></div>
                <div class="ap-exact-bar-segment" style="width: 20%;"></div>
                <div class="ap-exact-bar-segment" style="width: 20%;"></div>
            </div>
            <div class="ap-exact-descs" style="font-size: 1.0rem;">
                <div class="ap-desc-segment" style="width: 20%;">Very Satisfied</div>
                <div class="ap-desc-segment" style="width: 20%;">Somewhat Satisfied</div>
                <div class="ap-desc-segment" style="width: 20%;">Neither</div>
                <div class="ap-desc-segment" style="width: 20%;">Somewhat Dissatisfied</div>
                <div class="ap-desc-segment" style="width: 20%;">Very Dissatisfied</div>
            </div>
        </div>
        <div class="ap-section-card" style="padding:0.5rem;">
            <table class="ap-table table-bordered">
                <thead>
                    <tr>
                        <th style="text-align:left; width:70%;">Particulars (விவரங்கள்)</th>
                        <th>Rating (1–5)</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td style="text-align:left;">Are you satisfied with the discussions you had with the faculty?<br>
                        <small style="color:var(--text-light);">ஆசிரியருடன் கலந்துரையாடியதில் நீங்கள் மனநிறைவு பெற்றீர்களா?</small></td>
                        <td style="text-align:center;"><input type="number" name="part1_q1" class="score-input" min="1" max="5" required></td>
                    </tr>
                    <tr>
                        <td style="text-align:left;">Are you satisfied with the various activities of the department?<br>
                        <small style="color:var(--text-light);">துறைசார்ந்த பல்வேறு செயல்பாடுகள் உங்களுக்கு நிறைவாக இருந்ததா?</small></td>
                        <td style="text-align:center;"><input type="number" name="part1_q2" class="score-input" min="1" max="5" required></td>
                    </tr>
                </tbody>
            </table>
        </div>

        <!-- Part II -->
        <div class="ap-section-sep">Part II</div>
        <div class="ap-exact-scale" style="margin-bottom: 2rem;">
            <div class="ap-exact-nums">
                <span class="ap-num" style="left: 10%; transform: translateX(-50%);">5</span>
                <span class="ap-num" style="left: 30%; transform: translateX(-50%);">4</span>
                <span class="ap-num" style="left: 50%; transform: translateX(-50%);">3</span>
                <span class="ap-num" style="left: 70%; transform: translateX(-50%);">2</span>
                <span class="ap-num" style="left: 90%; transform: translateX(-50%);">1</span>
            </div>
            <div class="ap-exact-bar">
                <div class="ap-exact-bar-segment" style="width: 20%;"></div>
                <div class="ap-exact-bar-segment" style="width: 20%;"></div>
                <div class="ap-exact-bar-segment" style="width: 20%;"></div>
                <div class="ap-exact-bar-segment" style="width: 20%;"></div>
                <div class="ap-exact-bar-segment" style="width: 20%;"></div>
            </div>
            <div class="ap-exact-descs" style="font-size: 1.0rem;">
                <div class="ap-desc-segment" style="width: 20%;">Always</div>
                <div class="ap-desc-segment" style="width: 20%;">Almost Always</div>
                <div class="ap-desc-segment" style="width: 20%;">Frequently</div>
                <div class="ap-desc-segment" style="width: 20%;">Rarely</div>
                <div class="ap-desc-segment" style="width: 20%;">Never</div>
            </div>
        </div>
        <div class="ap-section-card" style="padding:0.5rem;">
            <table class="ap-table table-bordered">
                <thead>
                    <tr>
                        <th style="text-align:left; width:70%;">Particulars (விவரங்கள்)</th>
                        <th>Rating (1–5)</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td style="text-align:left;">Do you monitor your son/daughter's attendance?<br>
                        <small style="color:var(--text-light);">உங்கள் மகன்/மகளின் வருகைப் பதிவைக் கண்காணிக்கிறீர்களா?</small></td>
                        <td style="text-align:center;"><input type="number" name="part2_q1" class="score-input" min="1" max="5" required></td>
                    </tr>
                    <tr>
                        <td style="text-align:left;">Do you monitor your son/daughter's internal/summative exam marks?<br>
                        <small style="color:var(--text-light);">உங்கள் மகன்/மகளின் அகமதிப்பீட்டு/புறமதிப்பீட்டு மதிப்பெண்களைக் கண்காணிக்கிறீர்களா?</small></td>
                        <td style="text-align:center;"><input type="number" name="part2_q2" class="score-input" min="1" max="5" required></td>
                    </tr>
                    <tr>
                        <td style="text-align:left;">Does your son/daughter discuss department activities with you?<br>
                        <small style="color:var(--text-light);">உங்களிடம் துறை சார்ந்த பல்வேறு செயல்பாடுகள் குறித்து உங்கள் மகன்/மகள் கலந்துரையாடுவாரா?</small></td>
                        <td style="text-align:center;"><input type="number" name="part2_q3" class="score-input" min="1" max="5" required></td>
                    </tr>
                </tbody>
            </table>
        </div>

        <!-- Part III -->
        <div class="ap-section-sep">Part III</div>
        <div class="ap-section-card" style="padding:0.5rem;">
            <table class="ap-table table-bordered">
                <thead>
                    <tr>
                        <th style="text-align:left; width:70%;">Particulars (விவரங்கள்)</th>
                        <th>Your Response</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td style="text-align:left;">After graduation what is your immediate plan for your son/daughter?<br>
                        <small style="color:var(--text-light);">உங்கள் மகன்/மகள், கல்லூரி படிப்பு முடித்த பின் உங்களின் உடனடித்ததிட்டம் என்ன?</small><br>
                        <small style="color:var(--navy); font-weight:700;">Options: Higher Studies, Entrepreneurship, Job, Marriage, etc.</small></td>
                        <td style="text-align:center;"><input type="text" name="part3_q1" class="form-control text-center" placeholder="E.g. Higher Studies" required style="width:180px; margin:0 auto;"></td>
                    </tr>
                </tbody>
            </table>
        </div>

        <!-- Part IV -->
        <div class="ap-section-sep">Part IV</div>
        <div class="ap-exact-scale" style="margin-bottom: 2rem;">
            <div class="ap-exact-nums">
                <span class="ap-num" style="left: 10%; transform: translateX(-50%);">5</span>
                <span class="ap-num" style="left: 30%; transform: translateX(-50%);">4</span>
                <span class="ap-num" style="left: 50%; transform: translateX(-50%);">3</span>
                <span class="ap-num" style="left: 70%; transform: translateX(-50%);">2</span>
                <span class="ap-num" style="left: 90%; transform: translateX(-50%);">1</span>
            </div>
            <div class="ap-exact-bar">
                <div class="ap-exact-bar-segment" style="width: 20%;"></div>
                <div class="ap-exact-bar-segment" style="width: 20%;"></div>
                <div class="ap-exact-bar-segment" style="width: 20%;"></div>
                <div class="ap-exact-bar-segment" style="width: 20%;"></div>
                <div class="ap-exact-bar-segment" style="width: 20%;"></div>
            </div>
            <div class="ap-exact-descs" style="font-size: 1.0rem;">
                <div class="ap-desc-segment" style="width: 20%;">Strongly Agree</div>
                <div class="ap-desc-segment" style="width: 20%;">Agree</div>
                <div class="ap-desc-segment" style="width: 20%;">Neither</div>
                <div class="ap-desc-segment" style="width: 20%;">Disagree</div>
                <div class="ap-desc-segment" style="width: 20%;">Strongly Disagree</div>
            </div>
        </div>
        <div class="ap-section-card" style="padding:0.5rem;">
            <table class="ap-table table-bordered">
                <thead>
                    <tr>
                        <th style="text-align:left; width:70%;">Particulars (விவரங்கள்)</th>
                        <th>Rating (1–5)</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td style="text-align:left;">Do you agree that the department has helped in overall development of your ward's personality?<br>
                        <small style="color:var(--text-light);">உங்கள் மகன்/மகள் ஆளுமையை வளர்த்துக் கொள்வதற்கு துறை உதவுகிறது</small></td>
                        <td style="text-align:center;"><input type="number" name="part4_q1" class="score-input" min="1" max="5" required></td>
                    </tr>
                </tbody>
            </table>
        </div>

        <!-- Actions -->
        <div class="d-flex justify-content-between align-items-center mt-2">
            <a href="dashboard.php" class="btn-secondary-custom">Cancel</a>
            <button type="submit" class="btn-primary-custom">Submit PTA Feedback</button>
        </div>

    </form>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>

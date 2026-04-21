<?php
include 'config.php';
if (!isset($_SESSION['register_number'])) { header("location: index.php"); exit; }

$message = "";

$currentMonth = date('n');
$currentYear  = date('Y');
$academic_year = ($currentMonth >= 6)
    ? $currentYear . '-' . ($currentYear + 1)
    : ($currentYear - 1) . '-' . $currentYear;

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $semester_val   = $_SESSION['semester'];
    $suggestions    = $_POST['suggestions'];
    $feedback_matrix = $_POST['feedback'];
    $success_count  = 0;

    for ($t_index = 1; $t_index <= 5; $t_index++) {
        $ratings = [];
        $total_score_calc = 0;
        $has_data = false;
        for ($q_index = 0; $q_index < 14; $q_index++) {
            if (isset($feedback_matrix[$q_index]['teacher_' . $t_index]) && $feedback_matrix[$q_index]['teacher_' . $t_index] !== '') {
                $val = floatval($feedback_matrix[$q_index]['teacher_' . $t_index]);
                $ratings[] = $val;
                $total_score_calc += $val;
                $has_data = true;
            } else {
                $ratings[] = 0;
            }
        }
        if ($has_data) {
            $teacher_name = "Teacher " . $t_index;
            $subject_code = "General";
            $max_score  = 56;
            $percentage = ($total_score_calc / $max_score) * 100;
            $program = $_SESSION['program'] ?? 'UG';
            $sql = "INSERT INTO feedback_teacher 
(student_name, register_number, program, semester, teacher_name, subject_code, 
q1,q2,q3,q4,q5,q6,q7,q8,q9,q10,q11,q12,q13,q14, suggestions)
VALUES (?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?)";
            if ($stmt = $conn->prepare($sql)) {  
                $q_params = array_map('strval', $ratings);  
                $types  = str_repeat("s", 6) . str_repeat("s", 14) . "s";  
                $params = array_merge(  
                    [$_SESSION['student_name'], $_SESSION['register_number'], $program, $semester_val, $teacher_name, $subject_code],  
                    $q_params,  
                    [$suggestions]  
            );  
            $stmt->bind_param($types, ...$params);  
            if ($stmt->execute()) $success_count++;  
            }
        }
    }
    if ($success_count > 0) {
        echo "<script>alert('Feedback Submitted Successfully for $success_count teachers!'); window.location='dashboard.php';</script>";
    } else {
        $message = "Please enter ratings for at least one teacher.";
    }
}

$questions = [
    "Teaching efficiency",
    "Communication skill of teachers",
    "Focus on Syllabi",
    "Punctuality in class",
    "Regularity in taking class",
    "Control mechanism in effectively conducting the class",
    "Completes the syllabus of the course in time",
    "Internal evaluation system",
    "Usage of innovative methods",
    "Student friendly approach by teacher",
    "Conducting the classroom discussion",
    "Helping approach in varied academic interest of the student",
    "Career guidance provided by teacher",
    "Overall quality of teaching learning process"
];
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Teacher Feedback – Academic Portal</title>
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
        <span class="ap-hero-badge" style="margin-bottom:0.6rem;">Faculty Evaluation</span>
        <h1 class="ap-page-title">Teacher Feedback</h1>
        <p class="ap-page-meta">
            Semester <?php echo $_SESSION['semester']; ?> &nbsp;·&nbsp;
            Academic Year: <?php echo $academic_year; ?> &nbsp;·&nbsp;
            Department of Computer Science
        </p>
    </div>

    <!-- Student Info Bar -->
    <div class="ap-info-bar">
        <div><span style="font-size:0.9rem; text-transform:uppercase; letter-spacing:0.5px; color:var(--text-light);">Student Name</span><br>
        <strong><?php echo htmlspecialchars($_SESSION['student_name']); ?></strong></div>
        <div><span style="font-size:0.9rem; text-transform:uppercase; letter-spacing:0.5px; color:var(--text-light);">Register Number</span><br>
        <strong><?php echo htmlspecialchars($_SESSION['register_number']); ?></strong></div>
    </div>

    <?php if ($message): ?>
    <div class="ap-alert-danger"><?php echo $message; ?></div>
    <?php endif; ?>

    <form method="post">

        <!-- Grading Scale -->
        <div class="ap-section-card">
            <h2 class="ap-section-title">Grading Scale</h2>
            <div class="ap-exact-scale">
                <div class="ap-exact-nums">
                    <span class="ap-num" style="left: 0%;">4.00</span>
                    <span class="ap-num" style="left: 25%; transform: translateX(-50%);">3.00</span>
                    <span class="ap-num" style="left: 50%; transform: translateX(-50%);">2.00</span>
                    <span class="ap-num" style="left: 62.5%; transform: translateX(-50%);">1.00</span>
                    <span class="ap-num" style="right: 0%;">0.0</span>
                </div>
                <div class="ap-exact-bar">
                    <div class="ap-exact-bar-segment" style="width: 25%;">A</div>
                    <div class="ap-exact-bar-segment" style="width: 25%;">B</div>
                    <div class="ap-exact-bar-segment" style="width: 12.5%;">C</div>
                    <div class="ap-exact-bar-segment" style="width: 37.5%;">D</div>
                </div>
                <div class="ap-exact-descs">
                    <div class="ap-desc-segment" style="width: 25%;">Very Good</div>
                    <div class="ap-desc-segment" style="width: 25%;">Good</div>
                    <div class="ap-desc-segment" style="width: 12.5%;">Satisfactory</div>
                    <div class="ap-desc-segment" style="width: 37.5%;">Unsatisfactory</div>
                </div>
            </div>
        </div>

        <!-- Info -->
        <div class="ap-alert-info">
            Rate each parameter from <strong>0.00 to 4.00</strong> for the respective teachers.
        </div>

        <!-- Feedback Matrix -->
        <div class="ap-section-card" style="padding:1.2rem 0.5rem;">
            <div class="table-responsive">
                <table class="ap-table table-bordered">
                    <thead>
                        <tr>
                            <th style="text-align:left; width:30%;">Particulars</th>
                            <th>Teacher 1</th>
                            <th>Teacher 2</th>
                            <th>Teacher 3</th>
                            <th>Teacher 4</th>
                            <th>Teacher 5</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($questions as $q_index => $q): ?>
                        <tr>
                            <td style="text-align:left;">
                                <span style="font-weight:700; color:var(--navy); margin-right:6px;"><?php echo $q_index + 1; ?>.</span>
                                <?php echo $q; ?>
                            </td>
                            <?php for ($t = 1; $t <= 5; $t++): ?>
                            <td style="text-align:center; padding:8px;">
                                <input type="number"
                                       name="feedback[<?php echo $q_index; ?>][teacher_<?php echo $t; ?>]"
                                       class="score-input"
                                       step="0.01" min="0" max="4"
                                       placeholder="–"
                                       oninput="if(this.value>4)this.value=4; if(this.value<0)this.value=0;">
                            </td>
                            <?php endfor; ?>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Suggestions -->
        <div class="ap-section-card">
            <h2 class="ap-section-title">Suggestions / Comments</h2>
            <textarea name="suggestions" class="form-control" rows="4" placeholder="Share your thoughts..."></textarea>
        </div>

        <!-- Actions -->
        <div class="d-flex justify-content-between align-items-center mt-2">
            <a href="dashboard.php" class="btn-secondary-custom">Cancel</a>
            <button type="submit" class="btn-primary-custom">Submit Evaluation</button>
        </div>

    </form>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>

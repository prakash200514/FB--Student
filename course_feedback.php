<?php
include 'config.php';
if (!isset($_SESSION['register_number'])) { header("location: index.php"); exit; }

$message = "";
$reg_no  = $_SESSION['register_number'];
$semester = $_SESSION['semester'];

$check_sql = "SELECT id FROM feedback_course WHERE register_number = ? AND semester = ?";
$stmt = $conn->prepare($check_sql);
$stmt->bind_param("ss", $reg_no, $semester);
$stmt->execute();
$stmt->store_result();
if ($stmt->num_rows > 0) {
    echo "<script>alert('You have already submitted Course Feedback for this semester.'); window.location='dashboard.php';</script>";
    exit;
}
$stmt->close();

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $feedback_data = json_encode($_POST['feedback']);
    $student_name  = $_SESSION['student_name'];
    $program       = $_SESSION['program'] ?? 'UG';
    $sql = "INSERT INTO feedback_course (student_name, register_number, program, semester, feedback_data) VALUES (?, ?, ?, ?, ?)";
    if ($stmt = $conn->prepare($sql)) {
        $stmt->bind_param("sssss", $student_name, $reg_no, $program, $semester, $feedback_data);
        if ($stmt->execute()) {
            echo "<script>alert('Feedback Submitted Successfully!'); window.location='dashboard.php';</script>";
        } else {
            $message = "Error: " . $stmt->error;
        }
        $stmt->close();
    }
}

$clean_questions = [
    "The syllabus prescribed by the University",
    "The availability of Text Books and Reference",
    "Objective clearly stated or the Course",
    "Scope for employability",
    "Depth of the course content",
    "Availability of E-learning resources",
    "Knowledge base of the teacher about the course",
    "Pattern of the examination in relation with the curriculum ",
    "Over-all rating"
];

$program = $_SESSION['program'] ?? 'UG';
$semester_str = $_SESSION['semester'] ?? 'Semester 1';
$sem_num = str_replace('Semester ', '', $semester_str);

$subject_map = [
    'UG' => [
        '1' => ['Tamil', 'English', 'Python Programming', 'Digital Logic Fundamental', 'Problem Solving Techniques'],
        '2' => ['Tamil', 'English', 'Data Structure and Algorithm', 'Discreate Mathematics', 'Computer Architecture'],
        '3' => ['Tamil', 'English', 'Programming in C++', 'Introduction to Data Structure', 'Environmental Studies'],
        '4' => ['Tamil', 'English', 'Java Programming', 'Biometrics', 'Value Education'],
        '5' => ['Software Engineering', 'Database Management System', 'Image Processing', 'Data Analytics using R', 'NM'],
        '6' => ['Computer Networks', '.NET Programming', 'Operating System', 'Artificial Neural Network', 'NM']
    ],
    'PG' => [
        '1' => ['Analysis & Design of Algorithms', 'Object Oriented Analysis and Dwsign & c++', 'Python Programming', 'Advanced Sotware Engineering', 'Advanced Computer Network'],
        '2' => ['Data Mining  and Warehousing', 'Advanced Java Programming', 'Artificial Intelligence & Machine Learning', 'Block Chain Technology', 'Statistical Tools']
    ]
];

$subjects = ['Subject 1', 'Subject 2', 'Subject 3', 'Subject 4', 'Subject 5'];
if (isset($subject_map[$program][$sem_num])) {
    $subjects = $subject_map[$program][$sem_num];
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Course Feedback – Academic Portal</title>
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
    <div style="text-align:center; margin-bottom:0.6rem;">
        <span class="ap-hero-badge">Student Evaluation</span>
    </div>

    <form method="post">
        <!-- Exact Layout Header -->
        <div style="margin-bottom:2rem;">
            <h3 style="text-align:center; font-weight:bold; color:#0e1e38; font-family:'Times New Roman', Times, serif; font-size:1.5rem; text-transform:uppercase; margin-bottom:1rem; letter-spacing:0.5px;">
                STUDENT FEEDBACK FORM ON COURSES
            </h3>
            
            <p style="font-family:'Times New Roman', Times, serif; font-size:1.15rem; color:#000; line-height:1.5; margin-bottom:2.5rem; text-align:justify;">
                <strong style="font-size:1.15rem;">Note:</strong> This questionnaire has been designed to seek a feedback from the student about the courses to analyze the quality of curriculum in the perspective of the students.
            </p>
            
            <div class="row" style="font-family:'Times New Roman', Times, serif; font-size:1.2rem; color:#000;">
                <!-- Left Side -->
                <div class="col-md-7" style="display:flex; flex-direction:column; gap:1.5rem;">
                    <div style="display:flex; align-items:center;">
                        <span style="font-weight:bold; margin-right:1rem; min-width:180px;">Name of the Student:</span>
                        <span style="border-bottom:none; flex-grow:1;"><?php echo htmlspecialchars($_SESSION['student_name']); ?></span>
                    </div>
                    <div style="display:flex; align-items:center;">
                        <span style="font-weight:bold; margin-right:1rem; min-width:180px;">Register No:</span>
                        <span style="border-bottom:none; flex-grow:1;"><?php echo htmlspecialchars($reg_no); ?></span>
                    </div>
                </div>
                
                <!-- Right Side -->
                <div class="col-md-5" style="display:flex; flex-direction:column; gap:1.5rem; align-items:flex-end;">
                    <div style="display:flex; align-items:center; width:100%; justify-content:flex-end;">
                        <span style="font-weight:bold; margin-right:1rem;">Academic Level:</span>
                        <span style="border-bottom:none; display:inline-block; min-width:140px; text-align:left;">
                            <?php echo htmlspecialchars($_SESSION['program'] ?? 'UG'); ?>
                        </span>
                    </div>
                    <div style="display:flex; align-items:center; width:100%; justify-content:flex-end;">
                        <span style="font-weight:bold; margin-right:1rem;">Semester:</span>
                        <span style="border-bottom:none; display:inline-block; min-width:140px; text-align:left;">
                            <?php echo htmlspecialchars(str_replace('Semester ', '', $semester)); ?>
                        </span>
                    </div>
                </div>
            </div>
        </div>

    <?php if ($message): ?>
    <div class="ap-alert-danger"><?php echo $message; ?></div>
    <?php endif; ?>

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

        <!-- Info banner -->
        <div class="ap-alert-info">
            Rate each parameter from <strong>0.00 to 4.00</strong> for each subject.
        </div>

        <!-- Feedback Matrix -->
        <div class="ap-section-card" style="padding:1.2rem 0.5rem;">
            <div class="table-responsive">
                <table class="ap-table table-bordered">
                    <thead>
                        <tr>
                            <th style="text-align:left; width:38%;">Parameters</th>
                            <?php foreach ($subjects as $subject_name): ?>
                                <th><?php echo htmlspecialchars($subject_name); ?></th>
                            <?php endforeach; ?>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($clean_questions as $index => $q): ?>
                        <tr <?php if ($index == 8) echo 'style="background:#f0f8f0;"'; ?>>
                            <td style="text-align:left;">
                                <span style="font-weight:700; color:var(--navy); margin-right:6px;"><?php echo $index + 1; ?>.</span>
                                <?php echo $q; ?>
                                <?php if ($index == 8): ?>
                                    <span style="font-size:0.82rem; color:#1dd1a1; font-weight:600; margin-left:6px;">(Auto-calculated)</span>
                                <?php endif; ?>
                            </td>
                            <?php for ($s = 1; $s <= count($subjects); $s++): ?>
                            <td style="text-align:center; padding:8px;">
                                <?php if ($index == 8): ?>
                                    <input type="number"
                                           id="q9_s<?php echo $s; ?>"
                                           name="feedback[<?php echo $index; ?>][subject_<?php echo $s; ?>]"
                                           class="score-input q9-auto"
                                           step="0.01" min="0" max="4"
                                           readonly
                                           style="background:#e8f8f0; color:var(--navy); font-weight:700; cursor:default;">
                                <?php else: ?>
                                    <input type="number"
                                           name="feedback[<?php echo $index; ?>][subject_<?php echo $s; ?>]"
                                           class="score-input q-row"
                                           data-subject="<?php echo $s; ?>"
                                           data-row="<?php echo $index; ?>"
                                           step="0.01" min="0" max="4" required
                                           oninput="if(this.value>4)this.value=4; if(this.value<0)this.value=0; calcQ9(<?php echo $s; ?>);">
                                <?php endif; ?>
                            </td>
                            <?php endfor; ?>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Actions -->
        <div class="d-flex justify-content-between align-items-center mt-2">
            <a href="dashboard.php" class="btn-secondary-custom">Cancel</a>
            <button type="submit" class="btn-primary-custom">Submit Feedback</button>
        </div>

    </form>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script>
const numSubjects = <?php echo count($subjects); ?>;

function calcQ9(subjectIndex) {
    let sum = 0;
    document.querySelectorAll('.q-row[data-subject="' + subjectIndex + '"]').forEach(function(inp) {
        const v = parseFloat(inp.value);
        if (!isNaN(v)) { sum += v; }
    });
    const q9 = document.getElementById('q9_s' + subjectIndex);
    if (q9) q9.value = sum.toFixed(2);
}
</script>
</body>
</html>

<?php
// dashboard.php
include 'config.php';

if (!isset($_SESSION['register_number'])) {
    header("location: index.php");
    exit;
}



$first_name = explode(' ', $_SESSION['student_name'])[0];
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard – Academic Portal</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <link href="assets/css/style.css" rel="stylesheet">
</head>
<body>

<!-- ── Navigation Bar ── -->
<nav class="ap-navbar">
    <a class="ap-navbar-brand" href="dashboard.php">
        <span class="brand-icon">🎓</span>
        Academic Portal
    </a>
    <div class="ap-navbar-right">
        <a href="logout.php" class="btn-nav">Logout</a>
    </div>
</nav>

<!-- ── Student Info Bar ── -->
<div class="ap-semester-bar" style="justify-content: center; gap: 2rem;">
    <span class="student-info" style="border-left: none; margin-left: 0; padding-left: 0;">
        Student Name: <strong><?php echo htmlspecialchars($_SESSION['student_name'] ?? ''); ?></strong>
    </span>
    <span class="student-info" style="border-left: 1px solid #ddd; padding-left: 1rem; margin-left: 0;">
        Reg No: <strong><?php echo htmlspecialchars($_SESSION['register_number'] ?? ''); ?></strong>
    </span>
    <span class="student-info" style="border-left: 1px solid #ddd; padding-left: 1rem; margin-left: 0;">
        Academic Level: <strong><?php echo htmlspecialchars($_SESSION['program'] ?? ''); ?></strong>
    </span>
    <span class="student-info" style="border-left: 1px solid #ddd; padding-left: 1rem; margin-left: 0;">
        Current Semester: <strong><?php echo htmlspecialchars($_SESSION['semester'] ?? ''); ?></strong>
    </span>
</div>

<!-- ── Hero Section ── -->
<div class="ap-hero">
    <span class="ap-hero-badge">Academic Evaluation System</span>
    <h1 class="ap-hero-title">Feedback</h1>
    <hr class="ap-hero-divider">
    <p class="ap-hero-subtitle">Select a feedback category to begin your evaluation</p>
</div>

<!-- ── Feedback Cards ── -->
<div class="ap-cards-row">

    <!-- Course Feedback -->
    <a href="course_feedback.php" class="ap-card">
        <span class="ap-card-emoji">📚</span>
        <h3 class="ap-card-title">Course Feedback</h3>
        <p class="ap-card-subtitle">Evaluate course content &amp; curriculum</p>
    </a>

    <!-- Teacher Feedback -->
    <a href="teacher_feedback.php" class="ap-card">
        <span class="ap-card-emoji">👨‍🏫</span>
        <h3 class="ap-card-title">Teacher Feedback</h3>
        <p class="ap-card-subtitle">Rate faculty teaching effectiveness</p>
    </a>

    <!-- PTA Feedback -->
    <a href="pta_feedback.php" class="ap-card">
        <span class="ap-card-emoji">🤝</span>
        <h3 class="ap-card-title">PTA Feedback</h3>
        <p class="ap-card-subtitle">Parent-Teacher Association review</p>
    </a>

    <!-- Statistics -->
    <a href="statistics.php" class="ap-card">
        <span class="ap-card-emoji">📊</span>
        <h3 class="ap-card-title">Statistics</h3>
        <p class="ap-card-subtitle">View overall feedback analytics</p>
    </a>

</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>

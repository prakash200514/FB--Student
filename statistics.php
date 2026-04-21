<?php
session_start();
include 'config.php';
if (!isset($_SESSION['register_number'])) { header("location: index.php"); exit; }

$type_id = isset($_GET['type']) ? $_GET['type'] : 'all';
$program_filter = isset($_GET['program']) ? $_GET['program'] : 'all';
$semester_filter = isset($_GET['semester']) ? $_GET['semester'] : 'all';

$where_clauses = [];
if ($program_filter !== 'all') {
    $where_clauses[] = "program = '" . $conn->real_escape_string($program_filter) . "'";
}
if ($semester_filter !== 'all') {
    $where_clauses[] = "semester = '" . $conn->real_escape_string($semester_filter) . "'";
}
$where_sql = count($where_clauses) > 0 ? " WHERE " . implode(" AND ", $where_clauses) : "";

$feedback_types = [
    ['id' => 'course', 'name' => 'Course Feedback'],
    ['id' => 'teacher', 'name' => 'Teacher Feedback'],
    ['id' => 'pta', 'name' => 'PTA Feedback']
];

// 1. Total Submissions & Overall Averages
$c_subs = $conn->query("SELECT COUNT(*) as cnt FROM feedback_course" . $where_sql)->fetch_assoc()['cnt'];
$t_subs = $conn->query("SELECT COUNT(*) as cnt FROM feedback_teacher" . $where_sql)->fetch_assoc()['cnt'];
$pta_subs = $conn->query("SELECT COUNT(*) as cnt FROM feedback_pta" . $where_sql)->fetch_assoc()['cnt'];

// Course detailed avgs
$res = $conn->query("SELECT feedback_data FROM feedback_course" . $where_sql);
$c_q_sums = array_fill(0, 9, 0);
$c_q_counts = array_fill(0, 9, 0);
$c_sum = 0; $c_cnt = 0;
// Data for distribution Chart
$course_dist = array_fill(0, 9, ['0'=>0, '1'=>0, '2'=>0, '3'=>0, '4'=>0]);

while ($row = $res->fetch_assoc()) {
    $fb = json_decode($row['feedback_data'], true);
    if (is_array($fb)) {
        for ($i = 0; $i < 9; $i++) {
            if (isset($fb[$i])) {
                foreach ($fb[$i] as $subject => $score) {
                    if (is_numeric($score)) {
                        $val = floatval($score);
                        $c_q_sums[$i] += $val;
                        $c_q_counts[$i]++;
                        $c_sum += $val;
                        $c_cnt++;
                        
                        // Round score to nearest integer for distribution (0-4 scale)
                        $round_val = strval(round($val));
                        if(isset($course_dist[$i][$round_val])) {
                            $course_dist[$i][$round_val]++;
                        }
                    }
                }
            }
        }
    }
}
$c_overall_avg = $c_cnt > 0 ? ($c_sum / $c_cnt) : 0;
$c_q_avgs = [];
for ($i=0; $i<9; $i++) $c_q_avgs[] = $c_q_counts[$i] > 0 ? ($c_q_sums[$i] / $c_q_counts[$i]) : 0;

// Teacher detailed avgs
$t_avg_row = $conn->query("SELECT AVG(q1) as q1, AVG(q2) as q2, AVG(q3) as q3, AVG(q4) as q4, AVG(q5) as q5, AVG(q6) as q6, AVG(q7) as q7, AVG(q8) as q8, AVG(q9) as q9, AVG(q10) as q10, AVG(q11) as q11, AVG(q12) as q12, AVG(q13) as q13, AVG(q14) as q14 FROM feedback_teacher" . $where_sql)->fetch_assoc();
$t_q_avgs = [];
$t_sum = 0; $t_cnt = 0;
for($i=1; $i<=14; $i++) {
    $val = isset($t_avg_row["q$i"]) ? floatval($t_avg_row["q$i"]) : 0;
    $t_q_avgs[] = $val;
    if($val > 0) { $t_sum += $val; $t_cnt++; }
}
$t_overall_avg = $t_cnt > 0 ? ($t_sum / $t_cnt) : 0;

// Teacher distribution
$teacher_dist = array_fill(0, 14, ['0'=>0, '1'=>0, '2'=>0, '3'=>0, '4'=>0]);
$t_res = $conn->query("SELECT q1,q2,q3,q4,q5,q6,q7,q8,q9,q10,q11,q12,q13,q14 FROM feedback_teacher" . $where_sql);
while ($row = $t_res->fetch_assoc()) {
    for($i=1; $i<=14; $i++) {
        if (isset($row["q$i"]) && is_numeric($row["q$i"])) {
            $round_val = strval(round(floatval($row["q$i"])));
            if(isset($teacher_dist[$i-1][$round_val])) $teacher_dist[$i-1][$round_val]++;
        }
    }
}


// PTA detailed avgs (Updated for new schema)
$pta_avg_row = $conn->query("SELECT AVG(part1_q1) as q1, AVG(part1_q2) as q2, AVG(part2_q1) as q3, AVG(part2_q2) as q4, AVG(part2_q3) as q5, AVG(part4_q1) as q6 FROM feedback_pta" . $where_sql);
$pta_avg_data = $pta_avg_row ? $pta_avg_row->fetch_assoc() : null;

$keys = ["part1_q1", "part1_q2", "part2_q1", "part2_q2", "part2_q3", "part4_q1"];
$pta_sum = 0; $pta_cnt = 0;
$pta_q_avgs = [];

$pta_num_res = $conn->query("SELECT " . implode(", ", $keys) . " FROM feedback_pta" . $where_sql);
$pta_acc = array_fill_keys($keys, 0);
$pta_ccnt = array_fill_keys($keys, 0);

if ($pta_num_res) {
    while ($row = $pta_num_res->fetch_assoc()) {
        foreach($keys as $k) {
            if (isset($row[$k]) && is_numeric($row[$k])) {
                $pta_acc[$k] += floatval($row[$k]);
                $pta_ccnt[$k]++;
            }
        }
    }
}

foreach($keys as $k) {
    if ($pta_ccnt[$k] > 0) {
        $val = $pta_acc[$k] / $pta_ccnt[$k];
        $pta_q_avgs[] = $val;
        $pta_sum += $val; 
        $pta_cnt++;
    } else {
        $pta_q_avgs[] = 0;
    }
}
$pta_overall_avg = $pta_cnt > 0 ? ($pta_sum / $pta_cnt) : 0;

// PTA distribution
$pta_dist = array_fill(0, 6, ['1'=>0, '2'=>0, '3'=>0, '4'=>0, '5'=>0]);
$pta_res = $conn->query("SELECT " . implode(", ", $keys) . " FROM feedback_pta" . $where_sql);
if ($pta_res) {
    while ($row = $pta_res->fetch_assoc()) {
        $i = 0;
        foreach($keys as $k) {
            if (isset($row[$k]) && is_numeric($row[$k])) {
                $round_val = strval(floor(floatval($row[$k])));
                if(isset($pta_dist[$i][$round_val])) $pta_dist[$i][$round_val]++;
            }
            $i++;
        }
    }
}


// Overall Performance Data
if ($type_id == 'all') {
    $perf_labels = ['Course', 'Teacher', 'PTA'];
    $perf_data = [round($c_overall_avg,2), round($t_overall_avg,2), round($pta_overall_avg,2)];
} elseif ($type_id == 'course') {
    $perf_labels = ['Course'];
    $perf_data = [round($c_overall_avg,2)];
} elseif ($type_id == 'teacher') {
    $perf_labels = ['Teacher'];
    $perf_data = [round($t_overall_avg,2)];
} elseif ($type_id == 'pta') {
    $perf_labels = ['PTA'];
    $perf_data = [round($pta_overall_avg,2)];
}

// Selection specific variables
$total_subs = 0;
$overall_avg = 0;
$questions_tracked = 0;

$q_labels = [];
$dist_datasets = [];

if ($type_id == 'all') {
    $total_subs = $c_subs + $t_subs + $pta_subs;
    $sum_avgs = 0; $cnt_avgs = 0;
    foreach($perf_data as $v) { if($v > 0) { $sum_avgs += $v; $cnt_avgs++; } }
    $overall_avg = $cnt_avgs > 0 ? round($sum_avgs / $cnt_avgs, 2) : 0;
    $questions_tracked = 9 + 14 + 6;
} else {
    // Generate Distribution chart data based on selected type
    $active_dist = [];
    $possible_answers = [];
    
    if ($type_id == 'course') {
        $total_subs = $c_subs;
        $overall_avg = round($c_overall_avg, 2);
        $questions_tracked = 9;
        $active_dist = $course_dist;
        $possible_answers = ['0', '1', '2', '3', '4'];
        
        $questions = ["Depth of content", "Coverage", "Relevance", "Learning value", "Reading material", "Additional source", "Student effort", "Overall rating", "Usefulness"];
        foreach($questions as $i => $q) { $q_labels[] = "Q".($i+1).": ".substr($q,0,15); }
    } elseif ($type_id == 'teacher') {
        $total_subs = $t_subs;
        $overall_avg = round($t_overall_avg, 2);
        $questions_tracked = 14;
        $active_dist = $teacher_dist;
        $possible_answers = ['0', '1', '2', '3', '4'];
        
        $questions = ["Teaching efficiency", "Communication skill", "Focus on Syllabi", "Punctuality", "Regularity", "Control mechanism", "Completes syllabus", "Internal evaluation", "Innovative methods", "Student friendly", "Classroom discussion", "Helping approach", "Career guidance", "Overall quality"];
        foreach($questions as $i => $q) { $q_labels[] = "Q".($i+1).": ".substr($q,0,15); }

    } elseif ($type_id == 'pta') {
        $total_subs = $pta_subs;
        $overall_avg = round($pta_overall_avg, 2); 
        $questions_tracked = 6;
        $active_dist = $pta_dist;
        $possible_answers = ['1', '2', '3', '4', '5']; // PTA Uses 1-5 scale
        
        $questions = ["Faculty Disc", "Dept Activity", "Attendance", "Exam Marks", "Dept Discuss", "Overall Dev"];
        foreach($questions as $i => $q) { $q_labels[] = "Q".($i+1).": ".substr($q,0,15); }
    }
    
    $active_avgs = [];
    if ($type_id == 'course') $active_avgs = $c_q_avgs;
    elseif ($type_id == 'teacher') $active_avgs = $t_q_avgs;
    elseif ($type_id == 'pta') $active_avgs = $pta_q_avgs;

    // Build the single dataset for the average scores
    $avg_data = [];
    for($q=0; $q<$questions_tracked; $q++) {
        $avg_data[] = isset($active_avgs[$q]) ? round($active_avgs[$q], 2) : 0;
    }
    
    $dist_datasets[] = [
        'label' => 'Average Rating',
        'data' => $avg_data,
        'backgroundColor' => 'rgba(29, 209, 161, 0.85)', // Clean Modern Teal/Green
        'hoverBackgroundColor' => 'rgba(16, 172, 132, 1)',
        'borderRadius' => 8,
        'borderSkipped' => false,
        'barPercentage' => 0.5,
        'categoryPercentage' => 0.6
    ];
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Feedback Statistics – Academic Portal</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <link href="assets/css/style.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <style>
        .stat-page-title {
            text-align: center;
            font-family: 'Playfair Display', Georgia, serif;
            color: var(--navy);
            font-size: 2.2rem;
            font-weight: 700;
            margin: 2rem 0 1rem;
        }
        .stat-page-divider {
            width: 50px;
            height: 3px;
            background: var(--gold);
            margin: 0 auto 2rem;
            border: none;
        }
        .stat-filter-row {
            display: flex;
            justify-content: center;
            gap: 20px;
            align-items: flex-end;
            background: #fff;
            padding: 1.5rem 2.5rem;
            border-radius: 12px;
            box-shadow: var(--card-shadow);
            margin-bottom: 2rem;
        }
        .stat-filter-group {
            display: flex;
            flex-direction: column;
        }
        .stat-filter-label {
            font-size: 0.9rem;
            font-weight: 700;
            color: var(--text-light);
            text-transform: uppercase;
            letter-spacing: 1px;
            margin-bottom: 8px;
        }
        .stat-filter-select {
            border: 1px solid #e0dcd3;
            background: #faf8f4;
            padding: 10px 16px;
            border-radius: 8px;
            font-weight: 600;
            color: #0d1f3c; /* var(--navy) but direct color is safer */
            font-size: 1.05rem;
            margin-bottom: 0.5rem;
            outline: none;
        }
        .stat-filter-select:focus {
            border-color: var(--navy);
        }
        .stat-btn {
            background: var(--navy);
            color: #fff;
            font-weight: 700;
            border: none;
            padding: 10px 24px;
            font-size: 1.05rem;
            border-radius: 8px;
            cursor: pointer;
            transition: 0.2s;
            height: 43px;
        }
        .stat-btn:hover { background: #1a2e50; }
        .stat-btn-reset {
            background: #fff;
            border: 1px solid #e0dcd3;
            color: var(--text-mid);
            text-decoration: none;
            padding: 11px 24px;
            border-radius: 8px;
            font-size: 1.05rem;
            font-weight: 700;
            height: 43px;
            display: inline-flex;
            align-items: center;
            transition: 0.2s;
        }
        .stat-btn-reset:hover { background: #f5f0e8; color: var(--text-dark); }

        .stat-cards-row {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 20px;
            margin-bottom: 2rem;
        }
        .stat-summary-card {
            background: #fff;
            border-radius: 12px;
            box-shadow: var(--card-shadow);
            padding: 2.2rem;
            text-align: center;
            border: 1px solid rgba(0,0,0,0.04);
            transition: transform 0.2s ease;
        }
        .stat-summary-card:hover { transform: translateY(-4px); }
        .stat-card-icon {
            font-size: 2.2rem;
            margin-bottom: 12px;
        }
        .stat-card-value {
            font-size: 2.5rem;
            font-weight: 900;
            color: var(--navy);
            font-family: 'Playfair Display', serif;
            margin: 0 0 5px;
        }
        .stat-card-title {
            font-size: 0.9rem;
            font-weight: 700;
            color: var(--text-light);
            text-transform: uppercase;
            letter-spacing: 1px;
            margin: 0;
        }

        .stat-charts-row {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 20px;
            margin-bottom: 3rem;
        }
        .stat-chart-card {
            background: #fafafc;
            border-radius: 16px;
            box-shadow: 0 10px 30px rgba(0,0,0,0.05);
            padding: 2rem;
            border: 1px solid rgba(0,0,0,0.02);
        }
        .stat-chart-title {
            font-size: 1.2rem;
            font-weight: 700;
            color: var(--navy);
            font-family: 'Playfair Display', serif;
            margin: 0 0 1.5rem;
            padding-bottom: 0.8rem;
            border-bottom: 1px solid #f0ede6;
        }
        .chart-container-fluid {
            position: relative;
            height: 320px;
            width: 100%;
        }
        
        @media(max-width:992px){
            .stat-cards-row, .stat-charts-row { grid-template-columns: 1fr; }
            .stat-filter-row { flex-wrap: wrap; justify-content: flex-start; }
        }
    </style>
</head>
<body>

<!-- ── Navbar ── -->
<nav class="ap-navbar">
    <a class="ap-navbar-brand" href="dashboard.php">
        <span class="brand-icon">🎓</span>
        Academic Portal
    </a>
    <div class="ap-navbar-right">
        <a href="dashboard.php" class="btn-nav" style="border:none;">
            <i class="fas fa-home" style="margin-right:6px;"></i> Home
        </a>
    </div>
</nav>

<!-- ── Page Wrap ── -->
<div class="ap-page-wrap" style="max-width: 1200px;">
    
    <h1 class="stat-page-title">Feedback Statistics</h1>
    <hr class="stat-page-divider">

    <!-- Filter Bar -->
    <form method="GET">
        <div class="stat-filter-row">
            <div class="stat-filter-group">
                <label class="stat-filter-label">FEEDBACK TYPE</label>
                <select name="type" class="stat-filter-select" onchange="this.form.submit()">
                    <option value="all" <?php echo $type_id == 'all' ? 'selected' : ''; ?>>All Types</option>
                    <?php foreach ($feedback_types as $ft): ?>
                        <option value="<?php echo htmlspecialchars($ft['id']); ?>" <?php echo $type_id == $ft['id'] ? 'selected' : ''; ?>>
                            <?php echo htmlspecialchars($ft['name']); ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
            
            <div class="stat-filter-group">
                <label class="stat-filter-label">ACADEMIC LEVEL</label>
                <select name="program" class="stat-filter-select" onchange="this.form.submit()">
                    <option value="all" <?php echo $program_filter == 'all' ? 'selected' : ''; ?>>All Levels</option>
                    <option value="UG" <?php echo $program_filter == 'UG' ? 'selected' : ''; ?>>UG</option>
                    <option value="PG" <?php echo $program_filter == 'PG' ? 'selected' : ''; ?>>PG</option>
                </select>
            </div>
            
            <div class="stat-filter-group">
                <label class="stat-filter-label">SEMESTER</label>
                <select name="semester" class="stat-filter-select" onchange="this.form.submit()">
                    <option value="all" <?php echo $semester_filter == 'all' ? 'selected' : ''; ?>>All Semesters</option>
                    <?php for($s=1; $s<=6; $s++): ?>
                        <option value="Semester <?php echo $s; ?>" <?php echo $semester_filter == "Semester $s" ? 'selected' : ''; ?>>Semester <?php echo $s; ?></option>
                    <?php endfor; ?>
                </select>
            </div>
            
            <button type="submit" class="stat-btn">Apply Filters</button>
            <a href="statistics.php" class="stat-btn-reset">Reset</a>
        </div>
    </form>

    <!-- Summary Cards -->
    <div class="stat-cards-row">
        <div class="stat-summary-card">
            <div class="stat-card-icon">📝</div>
            <h2 class="stat-card-value" id="total-submissions"><?php echo number_format($total_subs); ?></h2>
            <p class="stat-card-title">TOTAL SUBMISSIONS</p>
        </div>
        <div class="stat-summary-card">
            <div class="stat-card-icon">⭐</div>
            <h2 class="stat-card-value"><?php echo number_format($overall_avg, 2); ?></h2>
            <p class="stat-card-title">OVERALL AVERAGE</p>
        </div>
        <div class="stat-summary-card">
            <div class="stat-card-icon">📊</div>
            <h2 class="stat-card-value"><?php echo number_format($questions_tracked); ?></h2>
            <p class="stat-card-title">QUESTIONS TRACKED</p>
        </div>
    </div>

    <!-- Charts -->
    <div class="stat-charts-row">
        <!-- Chart 1: Performance by Type -->
        <div class="stat-chart-card">
            <h3 class="stat-chart-title">Performance by Feedback Type</h3>
            <div class="chart-container-fluid">
                <canvas id="perfChart"></canvas>
            </div>
        </div>

        <!-- Chart 2: Question-wise Percentage Distribution -->
        <div class="stat-chart-card" <?php if ($type_id == 'all') echo 'style="display:flex; flex-direction:column; justify-content:center;"'; ?>>
            <h3 class="stat-chart-title">Question-wise Average Rating</h3>
            <div class="chart-container-fluid">
                <?php if ($type_id == 'all'): ?>
                    <div style="display:flex; height:100%; align-items:center; justify-content:center; color:var(--text-light); text-align:center;">
                        Select a specific feedback type<br>to view question-wise average ratings.
                    </div>
                <?php else: ?>
                    <canvas id="distChart"></canvas>
                <?php endif; ?>
            </div>
        </div>
    </div>

</div>

<script>
// Chart Defaults
Chart.defaults.font.family = "'Lato', 'Poppins', sans-serif";
Chart.defaults.color = "#718096";

const commonOptions = {
    responsive: true,
    maintainAspectRatio: false,
    plugins: {
        tooltip: {
            backgroundColor: 'rgba(13, 31, 60, 0.95)',
            titleFont: { size: 13, family: "'Lato', sans-serif" },
            bodyFont: { size: 14, weight: 'bold' },
            padding: 12,
            displayColors: true,
            cornerRadius: 8
        }
    }
};

// Chart 1: Performance By Type (Average)
const perfCtx = document.getElementById('perfChart').getContext('2d');
new Chart(perfCtx, {
    type: 'bar',
    data: {
        labels: <?php echo json_encode($perf_labels); ?>,
        datasets: [{
            label: 'Average Score',
            data: <?php echo json_encode($perf_data); ?>,
            backgroundColor: 'rgba(84, 160, 255, 0.85)', 
            hoverBackgroundColor: 'rgba(46, 134, 222, 1)', 
            borderRadius: 8,
            borderSkipped: false,
            barPercentage: 0.5,
            categoryPercentage: 0.7
        }]
    },
    options: {
        ...commonOptions,
        scales: {
            y: {
                beginAtZero: true,
                max: 4.0, // Assumes a generic 4-point scale average max
                grid: { color: 'rgba(0,0,0,0.04)', drawBorder: false },
                ticks: { font: { size: 11 }, stepSize: 0.5 }
            },
            x: {
                grid: { display: false, drawBorder: false },
                ticks: { font: { size: 11 } }
            }
        },
        plugins: {
            ...commonOptions.plugins,
            legend: { display: false }
        }
    }
});

// Chart 2: Question-wise Average (Simple Bar)
<?php if ($type_id !== 'all' && !empty($q_labels)): ?>
const distCtx = document.getElementById('distChart').getContext('2d');
new Chart(distCtx, {
    type: 'bar',
    data: {
        labels: <?php echo json_encode($q_labels); ?>,
        datasets: <?php echo json_encode($dist_datasets); ?>
    },
    options: {
        ...commonOptions,
        scales: {
            y: {
                beginAtZero: true,
                max: <?php echo ($type_id == 'pta') ? '5.0' : '4.0'; ?>, 
                grid: { color: 'rgba(0,0,0,0.04)', drawBorder: false },
                ticks: { font: { size: 11 }, stepSize: 0.5 }
            },
            x: {
                grid: { display: false, drawBorder: false },
                ticks: { font: { size: 11 } }
            }
        },
        plugins: {
            ...commonOptions.plugins,
            legend: { display: false }
        }
    }
});
<?php endif; ?>

// Auto Refresh Total Submissions Count
setInterval(() => {
    const params = new URLSearchParams(window.location.search);
    const typeId = params.get('type') || 'all';
    const program = params.get('program') || 'all';
    const semester = params.get('semester') || 'all';
    
    fetch(`get_live_stats.php?type=${typeId}&program=${program}&semester=${semester}`)
        .then(res => res.json())
        .then(data => {
            if (data.total_submissions !== undefined) {
                const counter = document.getElementById('total-submissions');
                if (counter && counter.innerText !== Number(data.total_submissions).toLocaleString()) {
                    // Update value
                    counter.innerText = Number(data.total_submissions).toLocaleString();
                    // Add a tiny animation pop
                    counter.style.transform = 'scale(1.1)';
                    counter.style.color = '#1dd1a1';
                    setTimeout(() => {
                        counter.style.transform = 'scale(1)';
                        counter.style.color = 'var(--navy)';
                    }, 300);
                }
            }
        })
        .catch(err => console.error('Error fetching live stats:', err));
}, 2000); // Poll every 2 seconds

</script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>

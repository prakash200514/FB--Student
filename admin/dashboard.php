<?php
session_start();
if (!isset($_SESSION['admin_logged_in'])) { header("location: index.php"); exit; }

$conn = new mysqli("localhost", "root", "", "student_feedback_db");

$type = isset($_GET['type']) ? $_GET['type'] : 'course';
$table_map = [
    'course'  => 'feedback_course',
    'teacher' => 'feedback_teacher',
    'pta'     => 'feedback_pta'
];
$table = $table_map[$type];

$sql    = "SELECT * FROM $table ORDER BY submitted_at DESC";
$result = $conn->query($sql);

// Export CSV
if (isset($_POST['export'])) {
    header('Content-Type: text/csv');
    header('Content-Disposition: attachment; filename="feedback_export_' . $type . '.csv"');
    $output = fopen("php://output", "w");
    $fields = $conn->query("SHOW COLUMNS FROM $table");
    $headers = [];
    while ($row = $fields->fetch_assoc()) $headers[] = $row['Field'];
    fputcsv($output, $headers);
    $data_res = $conn->query($sql);
    while ($row = $data_res->fetch_assoc()) fputcsv($output, $row);
    fclose($output);
    exit;
}

$labels = [
    'course'  => ['icon'=>'📚', 'label'=>'Course Feedback'],
    'teacher' => ['icon'=>'👨‍🏫', 'label'=>'Teacher Feedback'],
    'pta'     => ['icon'=>'🤝', 'label'=>'PTA Feedback'],
];
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard – Academic Portal</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <link href="../assets/css/style.css" rel="stylesheet">
    <style>
        /* Override card hover for admin tables */
        .ap-data-card { background:#fff; border-radius:10px; box-shadow:0 4px 20px rgba(0,0,0,0.08); overflow:hidden; }
        .ap-data-card .table { margin:0; font-size:1.0rem; }
        .ap-data-card .table thead th { background:var(--navy); color:#fff; border:none; padding:12px 14px; font-size:0.95rem; letter-spacing:0.5px; font-weight:700; }
        .ap-data-card .table tbody td { padding:10px 14px; vertical-align:middle; border-color:#f0ede6; }
        .ap-data-card .table tbody tr:hover { background:#faf8f4; }
        .ap-badge-count { background:var(--gold); color:var(--navy); font-weight:700; font-size:0.9rem; padding:3px 10px; border-radius:20px; }
    </style>
</head>
<body>

<!-- Sidebar -->
<div class="ap-sidebar">
    <div class="ap-sidebar-brand">🎓 <span>Academic</span> Portal<br><span style="font-size:0.85rem; opacity:0.9; letter-spacing:1px;">ADMIN PANEL</span></div>
    <nav class="ap-sidebar-nav">
        <a href="?type=course"  class="<?php echo $type=='course'  ? 'active':'' ?>">📚 Course Feedback</a>
        <a href="?type=teacher" class="<?php echo $type=='teacher' ? 'active':'' ?>">👨‍🏫 Teacher Feedback</a>
        <a href="?type=pta"     class="<?php echo $type=='pta'     ? 'active':'' ?>">🤝 PTA Feedback</a>
        <a href="logout.php" class="danger" style="margin-top:2rem;">⬅ Logout</a>
    </nav>
</div>

<!-- Main Content -->
<div class="ap-admin-content">
    <div class="ap-admin-header">
        <div class="d-flex justify-content-between align-items-center flex-wrap gap-3">
            <div>
                <span class="ap-hero-badge" style="margin-bottom:0.4rem; font-size:0.85rem;">Feedback Reports</span>
                <h1 style="font-family:'Playfair Display',Georgia,serif; font-weight:700; font-size:1.6rem; color:var(--navy); margin:0;">
                    <?php echo $labels[$type]['icon']; ?> <?php echo $labels[$type]['label']; ?>
                </h1>
            </div>
            <form method="post">
                <button type="submit" name="export" class="btn-primary-custom" style="display:flex; align-items:center; gap:8px;">
                    <i class="fas fa-file-csv"></i> Export CSV
                </button>
            </form>
        </div>
    </div>

    <!-- Stats badge -->
    <?php
    $count_res = $conn->query("SELECT COUNT(*) as cnt FROM $table");
    $count_row = $count_res->fetch_assoc();
    ?>
    <div style="margin-bottom:1rem;">
        Total Submissions: <span class="ap-badge-count"><?php echo $count_row['cnt']; ?></span>
    </div>

    <!-- Data Table -->
    <div class="ap-data-card">
        <div class="table-responsive">
            <table class="table table-bordered table-hover">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Student Name</th>
                        <th>Register No.</th>
                        <?php if ($type == 'teacher'): ?><th>Teacher</th><?php endif; ?>
                        <th>Submitted At</th>
                        <th>Details</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if ($result && $result->num_rows > 0): ?>
                        <?php while ($row = $result->fetch_assoc()): ?>
                        <tr>
                            <td><?php echo $row['id']; ?></td>
                            <td><?php echo htmlspecialchars($row['student_name']); ?></td>
                            <td><?php echo htmlspecialchars($row['register_number']); ?></td>
                            <?php if ($type == 'teacher'): ?>
                                <td><?php echo htmlspecialchars($row['teacher_name']); ?></td>
                            <?php endif; ?>
                            <td><?php echo $row['submitted_at']; ?></td>
                            <td>
                                <button class="btn btn-sm" style="background:var(--navy); color:#fff; font-size:0.9rem; padding:4px 12px; border:none; border-radius:5px;"
                                        data-bs-toggle="modal" data-bs-target="#viewModal<?php echo $row['id']; ?>">
                                    View
                                </button>

                                <!-- Modal -->
                                <div class="modal fade" id="viewModal<?php echo $row['id']; ?>" tabindex="-1">
                                    <div class="modal-dialog modal-lg">
                                        <div class="modal-content" style="border-radius:12px; border:none;">
                                            <div class="modal-header" style="background:var(--navy); color:#fff; border-radius:12px 12px 0 0;">
                                                <h5 class="modal-title" style="font-family:'Playfair Display',serif; font-size:1.15rem;">Feedback #<?php echo $row['id']; ?></h5>
                                                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                                            </div>
                                            <div class="modal-body" style="background:var(--cream);">
                                                <table class="table table-sm table-bordered" style="font-size:1.0rem;">
                                                    <tbody>
                                                    <?php foreach ($row as $key => $val): ?>
                                                        <tr>
                                                            <td style="font-weight:600; color:var(--navy); width:35%; background:#faf8f4;"><?php echo ucfirst(str_replace('_',' ',$key)); ?></td>
                                                            <td><?php echo htmlspecialchars($val ?? '–'); ?></td>
                                                        </tr>
                                                    <?php endforeach; ?>
                                                    </tbody>
                                                </table>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                            </td>
                        </tr>
                        <?php endwhile; ?>
                    <?php else: ?>
                        <tr><td colspan="6" class="text-center" style="padding:2rem; color:var(--text-light);">No feedback submissions found.</td></tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>

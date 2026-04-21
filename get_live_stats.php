<?php
session_start();
include 'config.php';
if (!isset($_SESSION['register_number'])) { exit; }

header('Content-Type: application/json');

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

$c_subs = $conn->query("SELECT COUNT(*) as cnt FROM feedback_course" . $where_sql)->fetch_assoc()['cnt'];
$t_subs = $conn->query("SELECT COUNT(*) as cnt FROM feedback_teacher" . $where_sql)->fetch_assoc()['cnt'];
$pta_subs = $conn->query("SELECT COUNT(*) as cnt FROM feedback_pta" . $where_sql)->fetch_assoc()['cnt'];

$total_subs = 0;

if ($type_id == 'all') {
    $total_subs = $c_subs + $t_subs + $pta_subs;
} elseif ($type_id == 'course') {
    $total_subs = $c_subs;
} elseif ($type_id == 'teacher') {
    $total_subs = $t_subs;
} elseif ($type_id == 'pta') {
    $total_subs = $pta_subs;
}

echo json_encode(['total_submissions' => $total_subs]);
?>

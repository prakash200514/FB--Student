<?php
include 'config.php';

// Drop old table to ensure clean structure for new format 
// (Assuming dev environment where we can reset this specific table for the new design)
$sql_drop = "DROP TABLE IF EXISTS feedback_pta";
if($conn->query($sql_drop)) {
    echo "Old feedback_pta table dropped.\n";
}

$sql_create = "CREATE TABLE feedback_pta (
    id INT AUTO_INCREMENT PRIMARY KEY,
    student_name VARCHAR(100) NOT NULL,
    register_number VARCHAR(50) NOT NULL,
    program VARCHAR(50),
    parent_name VARCHAR(100),
    address TEXT,
    mobile_no VARCHAR(20),
    semester VARCHAR(20),
    part1_q1 INT COMMENT 'Faculty Discussions',
    part1_q2 INT COMMENT 'Dept Activities',
    part2_q1 INT COMMENT 'Monitor Attendance',
    part2_q2 INT COMMENT 'Monitor Marks',
    part2_q3 INT COMMENT 'Discuss Activities',
    part3_q1 VARCHAR(50) COMMENT 'Graduation Plan',
    part4_q1 INT COMMENT 'Personality Dev Agreement',
    submitted_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
)";

if($conn->query($sql_create)) {
    echo "New feedback_pta table created successfully.\n";
} else {
    echo "Error creating table: " . $conn->error . "\n";
}
?>

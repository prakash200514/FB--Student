<?php
include 'config.php';

$sql = "ALTER TABLE feedback_course 
        DROP COLUMN q1, DROP COLUMN q2, DROP COLUMN q3, DROP COLUMN q4, DROP COLUMN q5, 
        DROP COLUMN q6, DROP COLUMN q7, DROP COLUMN q8, DROP COLUMN q9,
        ADD COLUMN feedback_data TEXT NOT NULL AFTER semester";

if ($conn->query($sql) === TRUE) {
    echo "Table 'feedback_course' updated successfully for Matrix format.";
} else {
    echo "Error updating table: " . $conn->error;
}

$conn->close();
?>

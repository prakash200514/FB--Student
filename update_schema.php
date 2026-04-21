<?php
include 'config.php';

// Check current schema
$result = $conn->query("DESCRIBE feedback_teacher");
$columns = [];
while($row = $result->fetch_assoc()) {
    $columns[$row['Field']] = $row;
}

// Update q1-q14 to VARCHAR(2)
for($i=1; $i<=14; $i++) {
    $col = "q$i";
    if(isset($columns[$col])) {
        // Change to VARCHAR(2)
        if(strpos($columns[$col]['Type'], 'int') !== false) {
            $conn->query("ALTER TABLE feedback_teacher MODIFY $col VARCHAR(2)");
            echo "Updated $col to VARCHAR(2)\n";
        }
    }
}

// Add total_score if not exists
if(!isset($columns['total_score'])) {
    $conn->query("ALTER TABLE feedback_teacher ADD COLUMN total_score INT");
    echo "Added total_score column\n";
}

// Add percentage if not exists
if(!isset($columns['percentage'])) {
    $conn->query("ALTER TABLE feedback_teacher ADD COLUMN percentage DECIMAL(5,2)");
    echo "Added percentage column\n";
}

echo "Schema update complete.\n";
?>

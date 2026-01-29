<?php
include "../includes/auth.php";
requireRole('trainer');
include "../config/database.php";
include "../includes/trainer_header.php";

$trainer_id = $_SESSION['user_id'];

// Fetch all attendance records by this trainer
$records = mysqli_query($conn, "
    SELECT 
        c.title AS course_title,
        l.title AS lesson_title,
        a.status,
        a.attendance_date
    FROM attendance a
    JOIN lessons l ON l.id = a.lesson_id
    JOIN courses c ON c.id = l.course_id
    WHERE a.trainer_id = $trainer_id
");

// Stats arrays
$courseStats = [];
$overall = ["present"=>0, "absent"=>0, "late"=>0, "excused"=>0];

while ($r = mysqli_fetch_assoc($records)) {
    $course = $r['course_title'];
    $status = $r['status'];

    if (!isset($courseStats[$course])) {
        $courseStats[$course] = ["present"=>0, "absent"=>0, "late"=>0, "excused"=>0, "total"=>0];
    }

    $courseStats[$course][$status]++;
    $courseStats[$course]["total"]++;

    $overall[$status]++;
}

?>
<!DOCTYPE html>
<html>
<head>
<title>Attendance Statistics</title>

<style>
.stats-box {
    background: white;
    padding: 25px;
    border-radius: 12px;
    box-shadow: 0 4px 12px rgba(0,0,0,0.1);
    margin-bottom: 25px;
}

.progress-bar {
    height: 18px;
    background: #28a745;
    border-radius: 6px;
}

.progress-container {
    width: 100%;
    background: #eee;
    border-radius: 6px;
}
</style>

</head>
<body>

<div class="page-container">

    <h2>📊 Attendance Statistics</h2>
    <p class="muted">Overview of attendance marked by you.</p>

    <!-- Overall Stats -->
    <div class="stats-box">
        <h3>Overall Attendance</h3>
        <p><strong>Total Records:</strong> <?= array_sum($overall) ?></p>

        <table class="styled-table">
            <thead>
                <tr>
                    <th>Status</th>
                    <th>Count</th>
                </tr>
            </thead>
            <tbody>
                <tr><td>Present</td><td><?= $overall["present"] ?></td></tr>
                <tr><td>Absent</td><td><?= $overall["absent"] ?></td></tr>
                <tr><td>Late</td><td><?= $overall["late"] ?></td></tr>
                <tr><td>Excused</td><td><?= $overall["excused"] ?></td></tr>
            </tbody>
        </table>
    </div>

    <!-- Per Course Stats -->
    <?php foreach ($courseStats as $course => $stats): 
        $percent = $stats["total"] > 0 ? round(($stats["present"] / $stats["total"]) * 100) : 0;
    ?>
    <div class="stats-box">
        <h3><?= htmlspecialchars($course) ?></h3>
        <p><strong>Total Records:</strong> <?= $stats["total"] ?></p>
        <p><strong>Present Rate:</strong> <?= $percent ?>%</p>

        <div class="progress-container">
            <div class="progress-bar" style="width: <?= $percent ?>%"></div>
        </div>

        <br>

        <table class="styled-table">
            <thead>
                <tr>
                    <th>Status</th>
                    <th>Count</th>
                </tr>
            </thead>
            <tbody>
                <tr><td>Present</td><td><?= $stats["present"] ?></td></tr>
                <tr><td>Absent</td><td><?= $stats["absent"] ?></td></tr>
                <tr><td>Late</td><td><?= $stats["late"] ?></td></tr>
                <tr><td>Excused</td><td><?= $stats["excused"] ?></td></tr>
            </tbody>
        </table>

    </div>
    <?php endforeach; ?>

</div>

</body>
</html>
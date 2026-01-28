<?php
include "../includes/admin_header.php";
include "../includes/auth.php";
requireRole('admin');
include "../config/database.php";

$course_id = isset($_GET['id']) ? intval($_GET['id']) : 0;

if ($course_id <= 0) {
    die("Invalid course ID.");
}

// Fetch course details
$courseQuery = mysqli_query($conn, "
    SELECT *
    FROM courses
    WHERE id = $course_id
");

$course = mysqli_fetch_assoc($courseQuery);

if (!$course) {
    die("Course not found.");
}

// FIX: Define is_active
$is_active = intval($course['is_active']);  // 1 or 0

// Fetch number of lessons
$lessonCount = mysqli_fetch_assoc(mysqli_query($conn, "
    SELECT COUNT(*) AS total FROM lessons WHERE course_id = $course_id
"))['total'];

// Fetch trainer(s)
$trainers = mysqli_query($conn, "
    SELECT u.name 
    FROM trainer_courses tc
    JOIN users u ON u.id = tc.trainer_id
    WHERE tc.course_id = $course_id
");

?>
<!DOCTYPE html>
<html>
<head>
<title>Course Details</title>

<style>
    body {
        font-family: Arial, sans-serif;
        background: #f4f6f8;
        padding: 40px;
    }

    .container {
        width: 70%;
        margin: auto;
        background: white;
        padding: 25px;
        border-radius: 10px;
        box-shadow: 0 4px 10px rgba(0,0,0,0.1);
    }

    h2 { 
        margin-bottom: 20px; 
        text-align: center;
    }

    .details-table {
        width: 100%;
        border-collapse: collapse;
        margin-top: 15px;
        border-radius: 8px;
        overflow: hidden;
        background: #fff;
        box-shadow: 0 2px 8px rgba(0,0,0,0.08);
    }

    .details-table th {
        width: 25%;
        background: #f1f3f5;
        padding: 12px;
        text-align: left;
        font-weight: bold;
        border-bottom: 1px solid #ddd;
    }

    .details-table td {
        padding: 12px;
        border-bottom: 1px solid #ddd;
    }

    .status-active {
        color: green;
        font-weight: bold;
    }

    .status-inactive {
        color: red;
        font-weight: bold;
    }

    .btn {
        padding: 10px 18px;
        border-radius: 6px;
        text-decoration: none;
        color: white;
        margin-top: 20px;
        display: inline-block;
    }

    .btn-danger { background: #d9534f; }
    .btn-success { background: #28a745; }
    .btn-secondary { background: #6c757d; }
</style>

</head>

<body>

<div class="container">
    <h2>Course Details</h2>

    <table class="details-table">
        <tr>
            <th>Title</th>
            <td><?= htmlspecialchars($course['title']) ?></td>
        </tr>

        <tr>
            <th>Description</th>
            <td><?= htmlspecialchars($course['description']) ?></td>
        </tr>

        <tr>
            <th>Price</th>
            <td><?= htmlspecialchars($course['price']) ?>$</td>
        </tr>

        <tr>
            <th>Status</th>
            <td>
                <?= $is_active 
                    ? "<span class='status-active'>Active</span>" 
                    : "<span class='status-inactive'>Inactive</span>" ?>
            </td>
        </tr>

        <tr>
            <th>Total Lessons</th>
            <td><?= $lessonCount ?></td>
        </tr>

        <tr>
            <th>Trainer(s)</th>
            <td>
                <?php 
                    if (mysqli_num_rows($trainers) == 0) {
                        echo "No trainer assigned.";
                    } else {
                        while ($t = mysqli_fetch_assoc($trainers)) {
                            echo "• " . htmlspecialchars($t['name']) . "<br>";
                        }
                    }
                ?>
            </td>
        </tr>
    </table>

    <br>

    <?php if ($is_active): ?>
        <a class="btn btn-danger" href="toggle_course.php?id=<?= $course_id ?>&action=deactivate">
            Deactivate Course
        </a>
    <?php else: ?>
        <a class="btn btn-success" href="toggle_course.php?id=<?= $course_id ?>&action=activate">
            Activate Course
        </a>
    <?php endif; ?>

    <a href="courses.php" class="btn btn-secondary" style="margin-left: 10px;">
        ← Back to Courses
    </a>
</div>

</body>
</html>
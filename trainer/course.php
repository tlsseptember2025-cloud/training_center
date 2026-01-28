<?php
include "../includes/auth.php";
requireRole('trainer');
include "../config/database.php";
include "../includes/trainer_header.php";

$trainer_id = $_SESSION['user_id'];
$course_id = intval($_GET['id'] ?? 0);

if ($course_id <= 0) {
    die("Invalid course ID.");
}

// VERIFY TRAINER OWNS THIS COURSE
$course = mysqli_fetch_assoc(mysqli_query($conn, "
    SELECT c.*
    FROM courses c
    JOIN trainer_courses tc ON tc.course_id = c.id
    WHERE c.id = $course_id AND tc.trainer_id = $trainer_id
"));

if (!$course) {
    die("Access denied.");
}

// FETCH LESSONS
$lessons = mysqli_query($conn, "
    SELECT *
    FROM lessons
    WHERE course_id = $course_id
    ORDER BY id ASC
");
?>
<!DOCTYPE html>
<html>
<head>
<title>Manage Course</title>

<style>
.page-container {
    width: 90%;
    margin: auto;
    margin-top: 30px;
}

.table-card {
    background: #fff;
    padding: 25px;
    border-radius: 12px;
    box-shadow: 0 4px 12px rgba(0,0,0,0.08);
    margin-top: 25px;
}

.styled-table {
    width: 100%;
    border-collapse: collapse;
    margin-top: 15px;
}

.styled-table th {
     background: #1a2238;
    padding: 12px;
    text-align: left;
    color: white;
    font-weight: bold;
    border-bottom: 1px solid #ddd;
    white-space: nowrap; /* keeps labels tidy */
}

.styled-table td {
    padding: 12px;
    border-bottom: 1px solid #ddd;
}

.btn {
    padding: 8px 14px;
    text-decoration: none;
    color: white;
    border-radius: 6px;
}

.btn-primary { background: #007bff; }
.btn-secondary { background: #6c757d; }
.btn-success { background: #28a745; }
.btn-warning { background: #f0ad4e; }
.btn-danger { background: #dc3545; }

h2 { margin-bottom: 10px; }
.section-title { margin-top: 40px; }


</style>

</head>
<body>

<div class="page-container">

    <div class="table-card">
        <h2>Course Details</h2>

        <table class="styled-table">
            <thead>
                <tr>
                    <th>Title</th>
                    <th>Description</th>
                    <th>Price</th>
                </tr>
            </thead>

            <tbody>
                <tr>
                    <td><?= htmlspecialchars($course['title']) ?></td>
                    <td><?= htmlspecialchars($course['description']) ?></td>
                    <td>$<?= htmlspecialchars($course['price']) ?></td>
                </tr>
            </tbody>
        </table>

        <br>
        <a href="dashboard.php" class="btn btn-secondary">← Back to Main</a>
        <a href="add_lesson.php?course_id=<?= $course_id ?>" class="btn btn-primary">+ Add New Lesson</a>
    </div>

    <!-- LESSON LIST -->
    <div class="table-card">
        <h2 class="section-title">Course Lessons</h2>

        <?php if (mysqli_num_rows($lessons) > 0): ?>
        <table class="styled-table">
            <thead>
                <tr>
                    <th>#</th>
                    <th>Lesson Title</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
            <?php $i = 1; while ($l = mysqli_fetch_assoc($lessons)): ?>
                <tr>
                    <td><?= $i++ ?></td>
                    <td><?= htmlspecialchars($l['title']) ?></td>
                    <td>
                        <a href="lesson.php?id=<?= $l['id'] ?>&course_id=<?= $course_id ?>" 
                        class="btn btn-warning btn-sm">Open</a>

                        <a href="attendance.php?lesson_id=<?= $l['id'] ?>&course_id=<?= $course_id ?>" 
                        class="btn btn-success btn-sm">Take Attendance</a>
                        <a href="attendance_history.php?lesson_id=<?= $l['id'] ?>&course_id=<?= $course_id ?>" 
                        class="btn btn-danger btn-sm">Attendance History</a>
                    </td>
                </tr>
            <?php endwhile; ?>
            </tbody>
        </table>

        <?php else: ?>
            <p>No lessons added yet.</p>
        <?php endif; ?>

    </div>

</div>

</body>
</html>
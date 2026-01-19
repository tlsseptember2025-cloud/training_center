<?php
include "../includes/auth.php";
requireRole('trainer');
include "../config/database.php";
include "../includes/trainer_header.php";

$trainer_id = $_SESSION['user_id'];
$course_id  = (int)($_GET['id'] ?? 0);

// 🔍 SAFETY CHECK
if ($course_id <= 0) {
    die("Invalid course ID.");
}

/* ------------------------------------------
   CHECK IF TRAINER IS ASSIGNED TO THIS COURSE
   Works even if table uses trainer_id OR user_id
------------------------------------------- */

// Detect actual column in trainer_courses
$colCheck = mysqli_query($conn, "SHOW COLUMNS FROM trainer_courses LIKE 'trainer_id'");
$trainerColumn = mysqli_num_rows($colCheck) > 0 ? "trainer_id" : "user_id";

$check = mysqli_query($conn, "
    SELECT 1 FROM trainer_courses
    WHERE $trainerColumn = $trainer_id
      AND course_id = $course_id
");

if (mysqli_num_rows($check) == 0) {
    die("<h2 style='color:red;text-align:center;margin-top:40px;'>Access denied — you are not assigned to this course.</h2>");
}

/* ----------------------------------------
   FETCH COURSE DETAILS
----------------------------------------- */

$course = mysqli_fetch_assoc(mysqli_query($conn, "
    SELECT * FROM courses WHERE id = $course_id
"));

if (!$course) {
    die("Course not found.");
}

/* ----------------------------------------
   FETCH LESSONS
----------------------------------------- */

$lessons = mysqli_query($conn, "
    SELECT * FROM lessons
    WHERE course_id = $course_id
    ORDER BY id ASC
");
?>

<div class="admin-container">

    <!-- Back Button -->
    <a href="dashboard.php" class="back-btn">
        <i class="fa fa-arrow-left"></i> Back to Dashboard
    </a>

    <div class="page-header" style="margin-top:20px;">
        <h1 class="page-title">Manage Course: <?= htmlspecialchars($course['title']) ?></h1>
        <p class="muted"><?= htmlspecialchars($course['description']) ?></p>
    </div>


    <!-- Add Lesson Button -->
    <div class="page-actions">
        <a href="add_lesson.php?course_id=<?= $course_id ?>" class="btn btn-primary">
            <i class="fa fa-plus"></i> Add Lesson
        </a>
    </div>


    <!-- Lessons Table -->
    <div class="table-card">
        <table class="styled-table">
            <thead>
                <tr>
                    <th>#</th>
                    <th>Lesson Title</th>
                    <th>File</th>
                    <th>Manage Attendance</th>
                    <th>Edit</th>
                </tr>
            </thead>

            <tbody>
            <?php 
            if (mysqli_num_rows($lessons) == 0): ?>
                <tr>
                    <td colspan="5" style="text-align:center;padding:20px;">
                        No lessons created yet.
                    </td>
                </tr>
            <?php else:
                $i = 1;
                while ($l = mysqli_fetch_assoc($lessons)): ?>
                <tr>
                    <td><?= $i++ ?></td>

                    <td><?= htmlspecialchars($l['title']) ?></td>

                    <td>
                        <?php if (!empty($l['file'])): ?>
                            <a href="../uploads/lessons/<?= $l['file'] ?>" target="_blank">
                                View File
                            </a>
                        <?php else: ?>
                            No file
                        <?php endif; ?>
                    </td>

                    <td>
                        <a href="attendance.php?lesson_id=<?= $l['id'] ?>&course_id=<?= $course_id ?>"
                           class="btn btn-secondary">
                           <i class="fa fa-users"></i> Attendance
                        </a>
                    </td>

                    <td>
                        <a href="edit_lesson.php?id=<?= $l['id'] ?>"
                           class="btn btn-edit">
                           <i class="fa fa-edit"></i> Edit
                        </a>
                    </td>
                </tr>
            <?php endwhile; endif; ?>
            </tbody>

        </table>
    </div>

</div>

<?php include "../includes/footer.php"; ?>

<?php
include "../includes/auth.php";
requireRole('trainer');
include "../config/database.php";
include "../includes/admin_header.php"; // Trainer uses same layout

// -------------------------
// VALIDATE COURSE ACCESS
// -------------------------
$course_id = intval($_GET['course_id']);
$trainer_id = $_SESSION['user_id'];

$check = mysqli_query($conn, "
    SELECT 1 FROM course_trainers 
    WHERE course_id = $course_id AND trainer_id = $trainer_id
");

if (mysqli_num_rows($check) === 0) {
    echo "<div class='error-box'>❌ Access denied — you are not assigned to this course.</div>";
    include "../includes/footer.php";
    exit;
}

// Get course info
$course = mysqli_fetch_assoc(mysqli_query($conn, "
    SELECT * FROM courses WHERE id = $course_id
"));

// Get lessons
$lessons = mysqli_query($conn, "
    SELECT * FROM lessons 
    WHERE course_id = $course_id
    ORDER BY id ASC
");
?>

<div class="admin-container">

    <div class="page-header">
        <h1 class="page-title">Manage Course: <?= htmlspecialchars($course['title']) ?></h1>
        <p class="muted">Trainer access – Add, edit, and update course lessons.</p>
    </div>

    <!-- BACK BUTTON -->
    <a href="dashboard.php" class="back-btn">
        <i class="fa fa-arrow-left"></i> Back to Dashboard
    </a>

    <div class="page-actions">
        <a href="trainer_add_lesson.php?course_id=<?= $course_id ?>" class="btn btn-primary">
            <i class="fa fa-plus"></i> Add Lesson
        </a>
    </div>

    <div class="table-card">
        <table class="styled-table">
            <thead>
                <tr>
                    <th>#</th>
                    <th>Lesson Title</th>
                    <th>Description</th>
                    <th>Actions</th>
                </tr>
            </thead>

            <tbody>
            <?php $i = 1; while ($row = mysqli_fetch_assoc($lessons)): ?>
                <tr>
                    <td><?= $i++ ?></td>
                    <td><?= htmlspecialchars($row['title']) ?></td>
                    <td><?= htmlspecialchars($row['description']) ?></td>

                    <td class="actions">
                        <a href="trainer_edit_lesson.php?id=<?= $row['id'] ?>" title="Edit">
                            <i class="fa fa-edit"></i>
                        </a>

                        <a href="trainer_delete_lesson.php?id=<?= $row['id'] ?>&course_id=<?= $course_id ?>"
                           onclick="showConfirm('Delete this lesson?', this.href); return false;"
                           title="Delete">
                            <i class="fa fa-trash" style="color:#c0392b;"></i>
                        </a>
                    </td>
                </tr>
            <?php endwhile; ?>
            </tbody>
        </table>
    </div>

</div>

<?php include "../includes/footer.php"; ?>

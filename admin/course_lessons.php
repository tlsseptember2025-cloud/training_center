<?php
include "../includes/admin_header.php";
include "../includes/auth.php";
requireRole('admin');
include "../config/database.php";

// Get course ID
$course_id = isset($_GET['course_id']) ? (int)$_GET['course_id'] : 0;

// Fetch course
$course = mysqli_fetch_assoc(
    mysqli_query($conn, "SELECT * FROM courses WHERE id = $course_id")
);

if (!$course) {
    die("<h2>Course not found.</h2>");
}

// Fetch lessons
$lessons = mysqli_query($conn, "
    SELECT * FROM lessons 
    WHERE course_id = $course_id 
    ORDER BY id DESC
");
?>

<div class="admin-container">

    <div class="page-header">
        <h1 class="page-title">
            Lessons for: <?= htmlspecialchars($course['title']) ?>
        </h1>
        <p class="muted">Manage lessons for this course</p>
    </div>

    <div class="page-actions">
        <a href="add_lesson.php?course_id=<?= $course_id ?>" class="btn btn-primary">
            <i class="fa fa-plus"></i> Add Lesson
        </a>

        <a href="courses.php" class="btn btn-secondary">
            <i class="fa fa-arrow-left"></i> Back to Courses
        </a>
    </div>

    <div class="table-card">
        <table class="styled-table">
            <thead>
                <tr>
                    <th>Lesson Title</th>
                    <th>File</th>
                    <th style="width:160px;">Actions</th>
                </tr>
            </thead>

            <tbody>
                <?php if (mysqli_num_rows($lessons) === 0): ?>
                    <tr>
                        <td colspan="3" style="text-align:center; color:#777; padding:25px;">
                            No lessons added yet.
                        </td>
                    </tr>
                <?php endif; ?>

                <?php while ($lesson = mysqli_fetch_assoc($lessons)): ?>
                    <tr>
                        <td><?= htmlspecialchars($lesson['title']) ?></td>

                        <td>
                            <?php if (!empty($lesson['file'])): ?>
                                <a href="../uploads/lessons/<?= $lesson['file'] ?>" target="_blank">
                                    <i class="fa fa-file"></i> View File
                                </a>
                            <?php else: ?>
                                <span class="muted">No file</span>
                            <?php endif; ?>
                        </td>

                        <td class="actions">
                            <a href="delete_lesson.php?id=<?= $lesson['id'] ?>&course_id=<?= $course_id ?>"
                               class="btn btn-delete"
                               onclick="return confirm('Delete this lesson?')">
                               <i class="fa fa-trash"></i> Delete
                           </a>
                        </td>

                    </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
    </div>

</div>

<?php include "../includes/footer.php"; ?>

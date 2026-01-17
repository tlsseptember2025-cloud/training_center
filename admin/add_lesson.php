<?php
include "../includes/admin_header.php";
include "../includes/auth.php";
requireRole('admin');
include "../config/database.php";

$course_id = (int)($_GET['course_id'] ?? 0);
$course = mysqli_fetch_assoc(mysqli_query($conn, "SELECT title FROM courses WHERE id=$course_id"));

if (!$course) {
    die("<h2>Course not found.</h2>");
}

$message = "";

if (isset($_POST['add_lesson'])) {

    $title = mysqli_real_escape_string($conn, $_POST['title']);
    $file_name = "";

    if (!empty($_FILES['lesson_file']['name'])) {
        $file_name = time() . "_" . basename($_FILES['lesson_file']['name']);
        $tmp = $_FILES['lesson_file']['tmp_name'];
        $upload_dir = "../uploads/lessons/";

        if (!is_dir($upload_dir)) mkdir($upload_dir, 0777, true);

        move_uploaded_file($tmp, $upload_dir . $file_name);
    }

    mysqli_query($conn, "
        INSERT INTO lessons (course_id, title, file)
        VALUES ($course_id, '$title', '$file_name')
    ");

    header("Location: course_lessons.php?course_id=$course_id&added=1");
    exit;
}
?>

<link rel="stylesheet" href="/training_center/assets/css/forms.css">

<div class="form-wrapper">
    <h2>Add Lesson — <?= htmlspecialchars($course['title']) ?></h2>

    <form method="POST" enctype="multipart/form-data">

        <div class="form-group">
            <label>Lesson Title</label>
            <input type="text" name="title" required>
        </div>

        <div class="form-group">
            <label>Lesson File (PDF / Video / Any)</label>
            <input type="file" name="lesson_file">
        </div>

        <div class="form-actions">
            <button name="add_lesson" class="btn-primary">
                <i class="fa fa-upload"></i> Add Lesson
            </button>

            <a href="course_lessons.php?course_id=<?= $course_id ?>" class="btn-secondary">Cancel</a>
        </div>

    </form>
</div>

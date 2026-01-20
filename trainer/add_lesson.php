<?php
include "../includes/auth.php";
requireRole('trainer');
include "../config/database.php";
include "../includes/trainer_header.php";

$trainer_id = $_SESSION['user_id'];
$course_id  = (int)($_GET['course_id'] ?? 0);

if ($course_id <= 0) {
    die("<h2 style='color:red;text-align:center;margin-top:40px;'>Invalid course.</h2>");
}

// --- Check trainer assignment ---
$colCheck = mysqli_query($conn, "SHOW COLUMNS FROM trainer_courses LIKE 'trainer_id'");
$trainerColumn = mysqli_num_rows($colCheck) ? "trainer_id" : "user_id";

$assigned = mysqli_query($conn, "
    SELECT 1 FROM trainer_courses
    WHERE $trainerColumn = $trainer_id
      AND course_id = $course_id
");

if (mysqli_num_rows($assigned) == 0) {
    die("<h2 style='color:red;text-align:center;margin-top:40px;'>Access denied — you are not assigned to this course.</h2>");
}

$message = "";

if (isset($_POST['add_lesson'])) {

    $title = mysqli_real_escape_string($conn, $_POST['title']);
    $file_name = "";

    if (!empty($_FILES['lesson_file']['name'])) {

        $file_name = time() . "_" . basename($_FILES['lesson_file']['name']);
        $target = "../uploads/lessons/" . $file_name;

        if (!move_uploaded_file($_FILES['lesson_file']['tmp_name'], $target)) {
            $message = "<div class='alert error'>❌ File upload failed.</div>";
        }
    }

    $insert = mysqli_query($conn, "
        INSERT INTO lessons (course_id, title, file)
        VALUES ($course_id, '$title', '$file_name')
    ");

    if ($insert) {
        header("Location: course.php?id=$course_id&added=1");
        exit;
    } else {
        $message = "<div class='alert error'>❌ Database error — cannot add lesson.</div>";
    }
}
?>

<style>
/* Beautiful Form Container */
.lesson-form-wrapper {
    max-width: 650px;
    margin: 40px auto;
    padding: 35px;
    background: #fff;
    border-radius: 12px;
    box-shadow: 0 8px 25px rgba(0,0,0,0.08);
}

/* Header */
.lesson-form-wrapper h2 {
    text-align: center;
    margin-bottom: 25px;
    font-size: 26px;
}

/* Form Inputs */
.lesson-form-wrapper input[type="text"],
.lesson-form-wrapper input[type="file"] {
    width: 100%;
    padding: 14px;
    border-radius: 8px;
    border: 1px solid #ccc;
    margin-bottom: 20px;
    font-size: 15px;
}

/* File input styling */
input[type="file"] {
    background: #f8f9fa;
    cursor: pointer;
}

/* Buttons */
.btn-submit {
    width: 100%;
    padding: 12px;
    background: #2c7be5;
    border: none;
    color: white;
    font-size: 16px;
    border-radius: 8px;
    cursor: pointer;
}

.btn-submit:hover {
    background: #1a68d1;
}

/* Back button */
.back-btn {
    display: inline-flex;
    align-items: center;
    gap: 6px;
    margin-bottom: 20px;
    background: #e9ecef;
    color: #333 !important;
    padding: 10px 16px;
    border-radius: 8px;
    text-decoration: none;
}

.back-btn:hover {
    background: #dfe3e6;
}
</style>

<div class="admin-container">

    <a href="course.php?id=<?= $course_id ?>" class="back-btn">
        <i class="fa fa-arrow-left"></i> Back to Course
    </a>

    <div class="lesson-form-wrapper">

        <h2>Add New Lesson</h2>

        <?= $message ?>

        <form method="POST" enctype="multipart/form-data">

            <label>Lesson Title</label>
            <input type="text" name="title" placeholder="Enter lesson title..." required>

            <label>Lesson File (PDF, MP4, Images, etc.)</label>
            <input type="file" name="lesson_file">

            <button type="submit" name="add_lesson" class="btn-submit">
                <i class="fa fa-save"></i> Add Lesson
            </button>

        </form>
    </div>

</div>

<?php include "../includes/footer.php"; ?>

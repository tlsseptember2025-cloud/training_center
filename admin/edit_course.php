<?php
include "../includes/auth.php";
requireRole('admin');
include "../config/database.php";

$id = (int)$_GET['id'];
$message = "";

$course = mysqli_fetch_assoc(mysqli_query($conn, "SELECT * FROM courses WHERE id=$id"));
if (!$course) {
    die("<h2>Course not found.</h2>");
}

if (isset($_POST['update_course'])) {
    $title = mysqli_real_escape_string($conn, $_POST['title']);
    $description = mysqli_real_escape_string($conn, $_POST['description']);
    $price = (float)$_POST['price'];

    mysqli_query($conn, "
        UPDATE courses 
        SET title='$title', description='$description', price=$price 
        WHERE id=$id
    ");

    header("Location: courses.php?updated=1");
    exit;
}
include "../includes/admin_header.php";
?>

<link rel="stylesheet" href="/training_center/assets/css/forms.css">

<div class="form-wrapper">
    <h2>Edit Course</h2>

    <a href="courses.php" class="back-btn">
        <i class="fa fa-arrow-left"></i> Back to Courses
    </a>

    <?= $message ?>

    <form method="POST">

        <div class="form-group">
            <label>Course Title</label>
            <input type="text" name="title" value="<?= htmlspecialchars($course['title']) ?>" required>
        </div>

        <div class="form-group">
            <label>Description</label>
            <textarea name="description" required><?= htmlspecialchars($course['description']) ?></textarea>
        </div>

        <div class="form-group">
            <label>Price (USD)</label>
            <input type="number" name="price" step="0.01"
                   value="<?= htmlspecialchars($course['price']) ?>" required>
        </div>

        <div class="form-actions">
            <button type="submit" name="update_course" class="btn-primary">
                <i class="fa fa-save"></i> Save Changes
            </button>

            <a href="courses.php" class="btn-secondary">Cancel</a>
        </div>

    </form>
</div>

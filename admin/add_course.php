<?php
include "../includes/admin_header.php";
include "../includes/auth.php";
requireRole('admin');
include "../config/database.php";

$message = "";

if (isset($_POST['add_course'])) {
    $title = mysqli_real_escape_string($conn, $_POST['title']);
    $description = mysqli_real_escape_string($conn, $_POST['description']);
    $price = (float) $_POST['price'];

    $sql = "INSERT INTO courses (title, description, price)
            VALUES ('$title', '$description', $price)";

    if (mysqli_query($conn, $sql)) {
        header("Location: courses.php?added=1");
        exit;
    } else {
        $message = "<p style='color:red;text-align:center;'>Database error.</p>";
    }
}
?>

<link rel="stylesheet" href="/training_center/assets/css/forms.css">

<div class="form-wrapper">
    <h2>Add New Course</h2>

    <?= $message ?>

    <form method="POST">

        <div class="form-group">
            <label>Course Title</label>
            <input type="text" name="title" required>
        </div>

        <div class="form-group">
            <label>Description</label>
            <textarea name="description" required></textarea>
        </div>

        <div class="form-group">
            <label>Price (USD)</label>
            <input type="number" name="price" step="0.01" required>
        </div>

        <div class="form-actions">
            <button type="submit" name="add_course" class="btn-primary">
                <i class="fa fa-check"></i> Add Course
            </button>

            <a href="courses.php" class="btn-secondary">Cancel</a>
        </div>

    </form>
</div>

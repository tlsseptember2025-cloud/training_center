<?php
include "../includes/admin_header.php";
include "../includes/auth.php";
requireRole('admin');
include "../config/database.php";

if (isset($_POST['save'])) {
    $title = $_POST['title'];
    $description = $_POST['description'];
    $price = $_POST['price'];

    mysqli_query($conn, "
        INSERT INTO courses (title, description, price)
        VALUES ('$title', '$description', '$price')
    ");

    header("Location: courses.php");
    exit;
}
?>

<div class="form-container">
    <h2>Add New Course</h2>

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
            <label>Price</label>
            <input type="number" name="price" required>
        </div>

        <div class="form-actions">
            <button type="submit" name="save" class="btn btn-primary">
                <i class="fa fa-save"></i> Save Course
            </button>
        </div>

    </form>
</div>

<?php include "../includes/footer.php"; ?>

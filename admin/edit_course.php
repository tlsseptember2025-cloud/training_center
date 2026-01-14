<?php
include "../includes/admin_header.php";
include "../includes/auth.php";
requireRole('admin');
include "../config/database.php";

$id = (int)($_GET['id'] ?? 0);

$course = mysqli_fetch_assoc(
    mysqli_query($conn, "SELECT * FROM courses WHERE id = $id")
);

if (!$course) {
    echo "Course not found.";
    exit;
}

if (isset($_POST['update'])) {
    $title = $_POST['title'];
    $description = $_POST['description'];
    $price = $_POST['price'];

    mysqli_query($conn, "
        UPDATE courses 
        SET title='$title', description='$description', price='$price'
        WHERE id=$id
    ");

    header("Location: courses.php");
    exit;
}
?>

<div class="form-container">
    <h2>Edit Course</h2>

    <form method="POST">

        <div class="form-group">
            <label>Course Title</label>
            <input type="text" name="title"
                   value="<?= htmlspecialchars($course['title']) ?>" required>
        </div>

        <div class="form-group">
            <label>Description</label>
            <textarea name="description" required><?= htmlspecialchars($course['description']) ?></textarea>
        </div>

        <div class="form-group">
            <label>Price</label>
            <input type="number" name="price"
                   value="<?= $course['price'] ?>" required>
        </div>

        <div class="form-actions">
            <button type="submit" name="update" class="btn btn-primary">
                <i class="fa fa-save"></i> Update Course
            </button>
        </div>

    </form>
</div>

<?php include "../includes/footer.php"; ?>

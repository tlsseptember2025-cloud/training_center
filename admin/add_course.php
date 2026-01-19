<?php
include "../includes/admin_header.php";
include "../includes/auth.php";
requireRole('admin');
include "../config/database.php";

// GET TRAINERS
$trainers = mysqli_query($conn, "SELECT id, name FROM users WHERE role='trainer'");

if (isset($_POST['add_course'])) {

    $title = $_POST['title'];
    $description = $_POST['description'];
    $price = $_POST['price'];
    $trainer_id = $_POST['trainer_id'];

    $insert = mysqli_query($conn, "
        INSERT INTO courses (title, description, price)
        VALUES ('$title', '$description', '$price')
    ");

    if ($insert) {
        $course_id = mysqli_insert_id($conn);

        // Assign trainer
        mysqli_query($conn, "
            INSERT INTO trainer_courses (trainer_id, course_id)
            VALUES ($trainer_id, $course_id)
        ");

        header("Location: courses.php?success=1");
        exit;
    }
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
            <label>Price (USD)</label>
            <input type="number" name="price" step="0.01" required>
        </div>

        <div class="form-group">
            <label>Assign Trainer</label>
            <select name="trainer_id" required>
                <option value="">-- Select Trainer --</option>
                <?php while ($t = mysqli_fetch_assoc($trainers)): ?>
                    <option value="<?= $t['id'] ?>"><?= htmlspecialchars($t['name']) ?></option>
                <?php endwhile; ?>
            </select>
        </div>

        <div class="form-actions">
            <button class="btn btn-primary" name="add_course">Save Course</button>
        </div>

    </form>

</div>

<?php include "../includes/footer.php"; ?>


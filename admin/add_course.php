<?php
include "../includes/admin_header.php";
include "../includes/auth.php";
requireRole('admin');
include "../config/database.php";

$success = "";
$error = "";

// Fetch trainers for dropdown
$trainers = mysqli_query($conn, "SELECT id, name FROM users WHERE role='trainer' AND status='active'");

if ($_SERVER["REQUEST_METHOD"] === "POST") {

    $title       = mysqli_real_escape_string($conn, $_POST['title']);
    $description = mysqli_real_escape_string($conn, $_POST['description']);
    $price       = floatval($_POST['price']);
    $trainer_id  = intval($_POST['trainer_id']);

    if (empty($title) || empty($description) || $price <= 0 || $trainer_id <= 0) {
        $error = "Please fill all fields correctly.";
    } else {

        // Insert course
        $insert = mysqli_query($conn, "
            INSERT INTO courses (title, description, price, trainer_id)
            VALUES ('$title', '$description', $price, $trainer_id)
        ");

        if ($insert) {
            $course_id = mysqli_insert_id($conn);

            // Assign trainer
            mysqli_query($conn, "
                INSERT INTO trainer_courses (trainer_id, course_id)
                VALUES ($trainer_id, $course_id)
            ");

            $success = "Course added successfully!";
        } else {
            $error = "Database error. Could not add course.";
        }
    }
}
?>

<style>

.form-group input {
    width: 100%;
    padding: 13px;
    border-radius: 8px;
    border: 1px solid #ccc;
    font-size: 15px;
}

.form-group textarea {
    width: 100%;
    padding: 13px;
    border-radius: 8px;
    border: 1px solid #ccc;
    font-size: 15px;
    resize: vertical;
}

.page-wrapper {
    max-width: 900px;
    margin: 40px auto;
    padding: 20px;
}

.form-card {
    background: #fff;
    padding: 35px;
    border-radius: 12px;
    box-shadow: 0 4px 25px rgba(0,0,0,0.08);
}

.page-header h1 {
    font-size: 28px;
    margin-bottom: 5px;
}

.page-header p {
    color: #6c757d;
    margin-bottom: 25px;
}

.form-group {
    margin-bottom: 18px;
}

.form-group label {
    font-weight: 600;
    display: block;
    margin-bottom: 6px;
}

.form-group input {
    width: 100%;
    padding: 13px;
    border-radius: 8px;
    border: 1px solid #ccc;
    font-size: 15px;
}

.alert {
    padding: 12px;
    margin-bottom: 20px;
    border-radius: 8px;
    font-size: 15px;
}

.alert-error {
    background: #f8d7da;
    color: #b32b37;
}

.alert-success {
    background: #d4edda;
    color: #2f6e41;
}

.form-actions {
    margin-top: 20px;
}

.btn-primary {
    background: #2c7be5;
    padding: 10px 18px;
    color: #fff;
    text-decoration: none;
    border: none;
    border-radius: 8px;
    cursor: pointer;
}

.btn-primary:hover {
    background: #1a68d1;
}

.btn-secondary {
    background: #e0e0e0;
    padding: 10px 18px;
    color: #333;
    border-radius: 8px;
    text-decoration: none;
    margin-left: 10px;
}

.btn-secondary:hover {
    background: #cacaca;
}
</style>

<div class="admin-container">

    <div class="page-header">
        <h1 class="page-title">Add Course</h1>
        <p class="muted">Create a new course and assign a trainer</p>
    </div>

    <a href="courses.php" class="back-btn">
        <i class="fa fa-arrow-left"></i> Back to Courses
    </a>

    <!-- Correct card wrapper for styling -->
    <div class="form-card">

        <?php if ($error): ?>
            <div class="alert alert-error"><?= $error ?></div>
        <?php endif; ?>

        <?php if ($success): ?>
            <div class="alert alert-success"><?= $success ?></div>
        <?php endif; ?>

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
                <label>Price ($)</label>
                <input type="number" step="0.01" name="price" required>
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
                <button type="submit" class="btn btn-primary">
                    <i class="fa fa-save"></i> Save Course
                </button>

                <a href="courses.php" class="btn btn-secondary">Cancel</a>
            </div>

        </form>

    </div>
</div>

<?php include "../includes/footer.php"; ?>
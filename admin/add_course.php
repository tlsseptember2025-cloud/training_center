<?php
include "../includes/auth.php";
requireRole('admin');
include "../config/database.php";
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Add Course</title>
    <link rel="stylesheet" href="../assets/css/admin.css">
</head>
<body>

<?php include "../includes/admin_header.php"; ?>

<div class="container">
    <h2 class="page-title">Add New Course</h2>

    <div class="card">
        <form action="save_course.php" method="POST">

            <div class="form-group">
                <label for="title">Course Title</label>
                <input type="text" id="title" name="title" required class="form-control">
            </div>

            <div class="form-group">
                <label>Description</label>
                <textarea name="description" class="form-control" required></textarea>
            </div>

            <div class="form-group">
                <label>Price (USD)</label>
                <input type="number" step="0.01" name="price" required class="form-control">
            </div>

            <div class="form-group">
                <label>Assign Trainer</label>
                <select name="trainer_id" class="form-control" required>
                    <option value="">-- Select Trainer --</option>

                    <?php
                    $trainers = mysqli_query($conn, "SELECT id, name FROM users WHERE role='trainer'");
                    while ($t = mysqli_fetch_assoc($trainers)) {
                        echo "<option value='{$t['id']}'>{$t['name']}</option>";
                    }
                    ?>
                </select>
            </div>

            <button type="submit" class="btn btn-primary">Save Course</button>
        </form>
    </div>
</div>

</body>
</html>

<?php
include "../includes/admin_header.php";
include "../includes/auth.php";
requireRole('admin');
include "../config/database.php";

$result = mysqli_query($conn, "SELECT * FROM courses");
?>

<div class="admin-container">

    <h1 class="page-title">All Courses</h1>

    <div class="page-actions">
    <a href="add_course.php" class="btn btn-primary">
        <i class="fa fa-plus"></i> Add Course
    </a>
    </div>
    <br>
    <table class="admin-table">
        <thead>
            <tr>
                <th>Course Title</th>
                <th>Description</th>
                <th>Price</th>
                <th>Actions</th>
            </tr>
        </thead>

        <tbody>
        <?php while ($course = mysqli_fetch_assoc($result)): ?>
            <tr>
                <td><?= htmlspecialchars($course['title']) ?></td>
                <td><?= htmlspecialchars($course['description']) ?></td>
                <td>$<?= htmlspecialchars($course['price']) ?></td>
                <td>
                    <a class="btn btn-edit"
                       href="edit_course.php?id=<?= $course['id'] ?>">
                        Edit
                    </a>

                    <a class="btn btn-delete"
                       href="delete_course.php?id=<?= $course['id'] ?>"
                       onclick="return confirm('Delete this course?')">
                        Delete
                    </a>
                </td>
            </tr>
        <?php endwhile; ?>
        </tbody>
    </table>

</div>

<?php include "../includes/footer.php"; ?>

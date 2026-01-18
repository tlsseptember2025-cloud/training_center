<?php
include "../includes/admin_header.php";
include "../includes/auth.php";
requireRole('admin');
include "../config/database.php";

$result = mysqli_query($conn, "SELECT * FROM courses");
?>

<div class="admin-container">

    <h1 class="page-title">Courses</h1>

    <!-- BACK BUTTON -->
    <a href="dashboard.php" class="back-btn">
        <i class="fa fa-arrow-left"></i> Back to Dashboard
    </a>

    <div class="page-actions">
        <a href="add_course.php" class="btn btn-primary">
            <i class="fa fa-plus"></i> Add Course
        </a>
    </div>

    <div class="table-card">
        <table class="styled-table">
            <thead>
                <tr>
                    <th>Title</th>
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
                    <td><?= htmlspecialchars($course['price']) ?>$</td>

                    <td class="actions">
                        <a href="course_view.php?id=<?= $course['id'] ?>" title="View">
                            <i class="fa fa-eye"></i>
                        </a>

                        <a href="edit_course.php?id=<?= $course['id'] ?>" class="btn-edit">
                            Edit
                        </a>

                        <a href="delete_course.php?id=<?= $course['id'] ?>"
                           class="btn-delete"
                           onclick="return confirm('Delete this course?')">
                           Delete
                        </a>
                    </td>
                </tr>
            <?php endwhile; ?>
            </tbody>
        </table>
    </div>

</div>

<?php include "../includes/footer.php"; ?>

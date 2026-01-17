<?php
include "../includes/admin_header.php";
include "../includes/auth.php";
requireRole('admin');
include "../config/database.php";

$result = mysqli_query($conn, "SELECT * FROM courses ORDER BY id DESC");
?>

<div class="admin-container">

    <div class="page-header">
        <h1 class="page-title">All Courses</h1>
        <p class="muted">Manage your course catalog and lessons</p>
    </div>

    <div class="page-actions">
        <a href="add_course.php" class="btn btn-primary">
            <i class="fa fa-plus"></i> Add Course
        </a>
    </div>

    <div class="table-card">
        <table class="styled-table">
            <thead>
                <tr>
                    <th>Course Title</th>
                    <th>Description</th>
                    <th>Price</th>
                    <th style="width: 260px;">Actions</th>
                </tr>
            </thead>

            <tbody>
            <?php while ($course = mysqli_fetch_assoc($result)): ?>
                <tr>
                    <td><?= htmlspecialchars($course['title']) ?></td>
                    <td><?= htmlspecialchars($course['description']) ?></td>
                    <td>$<?= htmlspecialchars($course['price']) ?></td>

                    <td class="actions">

                        <!-- Edit Course -->
                        <a href="edit_course.php?id=<?= $course['id'] ?>"
                           class="btn btn-edit"
                           title="Edit Course">
                            <i class="fa fa-edit"></i> Edit
                        </a>

                        <!-- Delete Course -->
                        <a href="delete_course.php?id=<?= $course['id'] ?>"
                           class="btn btn-delete"
                           onclick="return confirm('Delete this course AND all its lessons?')"
                           title="Delete Course">
                            <i class="fa fa-trash"></i> Delete
                        </a>

                        <!-- Manage Lessons -->
                        <a href="course_lessons.php?course_id=<?= $course['id'] ?>"
                           class="btn btn-primary"
                           title="Manage Lessons">
                            <i class="fa fa-book"></i> Lessons
                        </a>

                    </td>
                </tr>
            <?php endwhile; ?>
            </tbody>
        </table>
    </div>

</div>

<?php include "../includes/footer.php"; ?>

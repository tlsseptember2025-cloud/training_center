<?php
include "../includes/auth.php";
requireRole('trainer');
include "../config/database.php";
include "../includes/trainer_header.php";

$trainer_id = $_SESSION['user_id'];

$courses = mysqli_query($conn, "
    SELECT * 
    FROM courses
    WHERE trainer_id = $trainer_id
    ORDER BY created_at DESC
");

?>

<div class="admin-container">

    <h1 class="page-title">Trainer Dashboard</h1>
    <p class="muted">Courses you are assigned to manage</p>

    <div class="table-card">
        <table class="styled-table">
            <thead>
                <tr>
                    <th>Course Name</th>
                    <th>Description</th>
                    <th>Price</th>
                    <th>Manage</th>
                </tr>
            </thead>

            <tbody>
            <?php if (mysqli_num_rows($courses) > 0): ?>
                <?php while ($row = mysqli_fetch_assoc($courses)): ?>
                    <tr>
                        <td><?= htmlspecialchars($row['title']) ?></td>
                        <td><?= htmlspecialchars($row['description']) ?></td>
                        <td>$<?= htmlspecialchars($row['price']) ?></td>

                        <td>
                            <a class="btn btn-primary" 
                               href="course.php?id=<?= $row['id'] ?>">
                               <i class="fa fa-folder-open"></i> Open
                            </a>
                        </td>
                    </tr>
                <?php endwhile; ?>
            <?php else: ?>
                <tr>
                    <td colspan="4" style="text-align:center; padding:20px;">
                        <strong>No courses assigned yet.</strong>
                    </td>
                </tr>
            <?php endif; ?>
            </tbody>

        </table>
    </div>

</div>

<?php include "../includes/footer.php"; ?>



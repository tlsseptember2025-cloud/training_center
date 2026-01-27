
<?php
include "../includes/student_header.php";
include "../includes/auth.php";
requireRole('student');
include "../config/database.php";

if (isset($_GET['msg'])) {
    if ($_GET['msg'] == "enrolled_success") {
        echo "<div class='alert success'>Successfully enrolled!</div>";
    }
    if ($_GET['msg'] == "already_enrolled") {
        echo "<div class='alert warning'>You are already enrolled.</div>";
    }
}

$student_id = $_SESSION['user_id'];

// Fetch enrolled courses
$enrolled = mysqli_query($conn, "
    SELECT c.id, c.title, c.description
    FROM courses c
    JOIN enrollments e ON e.course_id = c.id
    WHERE e.student_id = $student_id
");

// Fetch available courses
$available = mysqli_query($conn, "
    SELECT id, title, description
    FROM courses
    WHERE id NOT IN (SELECT course_id FROM enrollments WHERE student_id = $student_id)
");
?>

<div class="admin-container">

    <div class="page-header">
        <h1 class="page-title">My Courses</h1>
        <p class="muted">Courses you are enrolled in</p>
    </div>

    <!-- ======= ENROLLED COURSES ======= -->
    <div class="table-card">
        <h2 style="margin-bottom: 10px;">Enrolled Courses</h2>

        <table class="styled-table">
            <thead>
                <tr>
                    <th>Course</th>
                    <th>Description</th>
                    <th>Open</th>
                </tr>
            </thead>

            <tbody>
            <?php if (mysqli_num_rows($enrolled) > 0): ?>
                <?php while ($row = mysqli_fetch_assoc($enrolled)): ?>
                    <tr>
                        <td><?= htmlspecialchars($row['title']) ?></td>
                        <td><?= htmlspecialchars($row['description']) ?></td>
                        <td>
                            <a href="lessons.php?course_id=<?= $row['id'] ?>">
                                Open
                            </a>
                        </td>
                    </tr>
                <?php endwhile; ?>
            <?php else: ?>
                <tr>
                    <td colspan="3" class="muted" style="text-align:center; padding:20px;">
                        You are not enrolled in any courses.
                    </td>
                </tr>
            <?php endif; ?>
            </tbody>
        </table>
    </div>

    <br><br>

    <!-- ======= AVAILABLE COURSES ======= -->
    <div class="table-card">
        <h2 style="margin-bottom: 10px;">Available Courses</h2>

        <table class="styled-table">
            <thead>
            <tr>
                <th>Course</th>
                <th>Description</th>
                <th>Enroll</th>
            </tr>
            </thead>

            <tbody>
            <?php while ($row = mysqli_fetch_assoc($available)): ?>
                <tr>
                    <td><?= htmlspecialchars($row['title']) ?></td>
                    <td><?= htmlspecialchars($row['description']) ?></td>
                    <td>
                        <a href="enroll.php?course_id=<?= $row['id'] ?>" class="btn btn-primary">Enroll</a>
                    </td>
                </tr>
            <?php endwhile; ?>
            </tbody>
        </table>
    </div>

</div>

<?php include "../includes/footer.php"; ?>

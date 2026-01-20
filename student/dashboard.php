<?php
include "../includes/student_header.php";
include "../includes/auth.php";
requireRole('student');
include "../config/database.php";

$student_id = $_SESSION['user_id'];

// Count enrolled courses
$enrolled = mysqli_fetch_row(mysqli_query($conn,
    "SELECT COUNT(*) FROM enrollments WHERE student_id = $student_id"
))[0];

// Count completed (placeholder)
$completed = 0;

// Count certificates
$certificates = mysqli_fetch_row(mysqli_query($conn,
    "SELECT COUNT(*) FROM certificates WHERE student_id = $student_id"
))[0];

// Fetch one enrolled course for "Continue Learning"
$course_query = mysqli_query($conn, "
    SELECT c.id, c.title 
    FROM courses c
    JOIN enrollments e ON e.course_id = c.id
    WHERE e.student_id = $student_id
    LIMIT 1
");
$next_course = mysqli_fetch_assoc($course_query);
?>

<div class="admin-container">

    <!-- PAGE HEADER -->
    <div class="page-header">
        <h1 class="page-title">Welcome back 👋</h1>
        <p class="muted">Your learning overview</p>
    </div>

    <!-- ======= DASHBOARD STAT CARDS ======= -->
    <div class="dashboard-cards" style="
        display: grid;
        grid-template-columns: repeat(3, 1fr);
        gap: 25px;
    ">
        <div class="card" style="
            background: #fff;
            padding: 30px;
            border-radius: 14px;
            box-shadow: 0 8px 20px rgba(0,0,0,0.06);
            text-align: center;
        ">
            <div style="font-size: 40px;">📘</div>
            <h2><?= $enrolled ?></h2>
            <p class="muted">Enrolled Courses</p>
        </div>

        <div class="card" style="
            background: #fff;
            padding: 30px;
            border-radius: 14px;
            box-shadow: 0 8px 20px rgba(0,0,0,0.06);
            text-align: center;
        ">
            <div style="font-size: 40px;">✔️</div>
            <h2><?= $completed ?></h2>
            <p class="muted">Completed Courses</p>
        </div>

        <div class="card" style="
            background: #fff;
            padding: 30px;
            border-radius: 14px;
            box-shadow: 0 8px 20px rgba(0,0,0,0.06);
            text-align: center;
        ">
            <div style="font-size: 40px;">🏅</div>
            <h2><?= $certificates ?></h2>
            <p class="muted">Certificates Earned</p>
        </div>
    </div>

    <br><br>

    <!-- ======= QUICK ACTIONS ======= -->
    <h2 style="margin-bottom: 15px;">Quick Actions</h2>

    <div class="dashboard-actions" style="
        display: grid;
        grid-template-columns: repeat(2, 1fr);
        gap: 25px;
    ">

        <!-- CONTINUE LEARNING -->
        <div class="action-card" style="
            background: #fff;
            padding: 30px;
            border-radius: 14px;
            box-shadow: 0 8px 20px rgba(0,0,0,0.06);
        ">
            <div style="font-size: 35px;">▶️</div>
            <h3 style="margin-top: 10px;">Continue Learning</h3>

            <?php if ($next_course): ?>
                <p class="muted">Resume your course</p>
                <a href="course.php?id=<?= $next_course['id'] ?>"
                   class="btn btn-primary">
                    Open Course
                </a>
            <?php else: ?>
                <p class="muted">You have no enrolled courses</p>
                <a href="courses.php" class="btn btn-primary">
                    Browse Courses
                </a>
            <?php endif; ?>
        </div>

        <!-- CERTIFICATES -->
        <div class="action-card" style="
            background: #fff;
            padding: 30px;
            border-radius: 14px;
            box-shadow: 0 8px 20px rgba(0,0,0,0.06);
        ">
            <div style="font-size: 35px;">📄</div>
            <h3 style="margin-top: 10px;">My Certificates</h3>
            <p class="muted">Download certificates</p>

            <a href="certificates.php" class="btn btn-secondary">
                View Certificates
            </a>
        </div>

    </div>

</div>

<?php include "../includes/footer.php"; ?>

<?php
include "../includes/auth.php";
requireRole('student');
include "../config/database.php";

$student_id = $_SESSION['user_id'];

$enrolled = mysqli_fetch_assoc(mysqli_query($conn,
    "SELECT COUNT(*) total FROM enrollments WHERE student_id=$student_id"
))['total'];

$completed = mysqli_fetch_assoc(mysqli_query($conn,
    "SELECT COUNT(*) total FROM certificates WHERE student_id=$student_id"
))['total'];

include "../includes/student_header.php";
?>

<div class="dashboard-container">

    <h2>Welcome back 👋</h2>
    <p>Your learning overview</p>

    <!-- STATS -->
    <div class="grid-3">
        <div class="card">
            <i class="fa-solid fa-book-open" style="color:#2c7be5;"></i>
            <h2><?= $enrolled ?></h2>
            <p>Enrolled Courses</p>
        </div>

        <div class="card">
            <i class="fa-solid fa-check-circle" style="color:#28a745;"></i>
            <h2><?= $completed ?></h2>
            <p>Completed Courses</p>
        </div>

        <div class="card">
            <i class="fa-solid fa-award" style="color:#fd7e14;"></i>
            <h2><?= $completed ?></h2>
            <p>Certificates Earned</p>
        </div>
    </div>

    <!-- ACTIONS -->
    <h3 style="margin-top:50px;">Quick Actions</h3>

    <div class="grid-2">
        <a href="my_courses.php" class="card card-link">
            <i class="fa-solid fa-play-circle" style="color:#2c7be5;"></i>
            <h3>Continue Learning</h3>
            <p>Resume your courses</p>
        </a>

        <a href="my_certificates.php" class="card card-link">
            <i class="fa-solid fa-file-pdf" style="color:#28a745;"></i>
            <h3>My Certificates</h3>
            <p>Download certificates</p>
        </a>
    </div>

</div>

<?php include "../includes/footer.php"; ?>

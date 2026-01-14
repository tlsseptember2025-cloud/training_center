<?php
include "../includes/auth.php";
requireRole('admin');
include "../config/database.php";

$users = mysqli_fetch_assoc(mysqli_query($conn,
    "SELECT COUNT(*) total FROM users"
))['total'];

$courses = mysqli_fetch_assoc(mysqli_query($conn,
    "SELECT COUNT(*) total FROM courses"
))['total'];

$enrollments = mysqli_fetch_assoc(mysqli_query($conn,
    "SELECT COUNT(*) total FROM enrollments"
))['total'];

include "../includes/admin_header.php";
?>

<div class="dashboard-container">

    <h2>Admin Dashboard 🛠️</h2>
    <p>System overview</p>

    <div class="grid-3">
        <div class="card">
            <i class="fa-solid fa-users" style="color:#2c7be5;"></i>
            <h2><?= $users ?></h2>
            <p>Total Users</p>
        </div>

        <div class="card">
            <i class="fa-solid fa-book" style="color:#28a745;"></i>
            <h2><?= $courses ?></h2>
            <p>Total Courses</p>
        </div>

        <div class="card">
            <i class="fa-solid fa-chart-line" style="color:#fd7e14;"></i>
            <h2><?= $enrollments ?></h2>
            <p>Total Enrollments</p>
        </div>
    </div>

    <h3 style="margin-top:50px;">Management</h3>

    <div class="grid-2">
        <a href="courses.php" class="card card-link">
            <i class="fa-solid fa-folder-plus" style="color:#2c7be5;"></i>
            <h3>Manage Courses</h3>
            <p>Create & edit courses</p>
        </a>

        <a href="students.php" class="card card-link">
            <i class="fa-solid fa-user-graduate" style="color:#28a745;"></i>
            <h3>Manage Students</h3>
            <p>View enrollments</p>
        </a>
    </div>

</div>

<?php include "../includes/footer.php"; ?>

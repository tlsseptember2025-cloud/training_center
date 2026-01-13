<?php
include "../includes/auth.php";
requireRole('admin');
include "../config/database.php";
include "../includes/admin_header.php";

// Stats
$students = mysqli_fetch_assoc(mysqli_query($conn,
    "SELECT COUNT(*) total FROM users WHERE role='student'"
))['total'];

$courses = mysqli_fetch_assoc(mysqli_query($conn,
    "SELECT COUNT(*) total FROM courses"
))['total'];

$certificates = mysqli_fetch_assoc(mysqli_query($conn,
    "SELECT COUNT(*) total FROM certificates"
))['total'];
?>

<h2>Admin Dashboard</h2>

<div style="display:flex; gap:20px; margin-top:20px;">
    <div style="flex:1; background:white; padding:20px; border-radius:8px;">
        <h3>👨‍🎓 Students</h3>
        <p style="font-size:24px;"><?= $students ?></p>
    </div>

    <div style="flex:1; background:white; padding:20px; border-radius:8px;">
        <h3>📚 Courses</h3>
        <p style="font-size:24px;"><?= $courses ?></p>
    </div>

    <div style="flex:1; background:white; padding:20px; border-radius:8px;">
        <h3>🎓 Certificates Issued</h3>
        <p style="font-size:24px;"><?= $certificates ?></p>
    </div>
</div>

<h3 style="margin-top:40px;">Quick Actions</h3>

<ul>
    <li><a href="courses.php">➕ Manage Courses</a></li>
    <li><a href="certificates.php">📜 View Certificates</a></li>
    <li><a href="certificate_downloads.php">⬇ Download Logs</a></li>
</ul>

<?php include "../includes/footer.php"; ?>

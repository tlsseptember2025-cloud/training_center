<?php
include "../includes/auth.php";
requireRole('student');
?>

<h1>Student Dashboard</h1>

<ul>
    <li><a href="courses.php">Browse Courses</a></li>
    <li><a href="my_courses.php">My Courses</a></li>
    <li><a href="../logout.php">Logout</a></li>
</ul>

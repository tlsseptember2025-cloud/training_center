<?php
include "../includes/auth.php";
requireRole('admin');
?>

<!DOCTYPE html>
<html>
<head>
    <title>Admin Dashboard</title>
</head>
<body>

<h1>Admin Dashboard</h1>

<p>Welcome, Admin</p>

<hr>

<ul>
    <li>
        <a href="courses.php">Manage Courses</a>
        <br>
        <small>View, edit, or delete existing courses</small>
    </li>
    <br>

    <li>
        <a href="add_course.php">Add New Course</a>
        <br>
        <small>Create a new course</small>
    </li>
    <br>

    <li>
        <a href="../student/courses.php">Preview Student Course View</a>
        <br>
        <small>See how students see courses</small>
    </li>
    <br>

    <li>
        <a href="../logout.php">Logout</a>
    </li>
</ul>

</body>
</html>


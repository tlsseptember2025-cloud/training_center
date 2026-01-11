<?php
include "../config/database.php";
include "../includes/auth.php";
requireRole('admin');


// Handle form
if (isset($_POST['add_course'])) {

    $title = $_POST['title'];
    $description = $_POST['description'];
    $price = $_POST['price'];

    $sql = "INSERT INTO courses (title, description, price)
            VALUES ('$title', '$description', '$price')";

    if (mysqli_query($conn, $sql)) {
        echo "Course added successfully!";
    } else {
        echo "Error adding course";
    }
}
?>

<h2>Add Course</h2>

<form method="POST">
    Course Title:<br>
    <input type="text" name="title"><br><br>

    Description:<br>
    <textarea name="description"></textarea><br><br>

    Price:<br>
    <input type="number" step="0.01" name="price"><br><br>

    <button name="add_course">Add Course</button>
</form>

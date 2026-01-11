<?php
include "../includes/auth.php";
requireRole('admin');
include "../config/database.php";

if (isset($_POST['add_lesson'])) {

    $title = $_POST['title'];
    $course_id = $_POST['course_id'];

    $file_name = $_FILES['lesson_file']['name'];
    $tmp_name  = $_FILES['lesson_file']['tmp_name'];

    $upload_path = "../uploads/lessons/" . $file_name;

    if (move_uploaded_file($tmp_name, $upload_path)) {

        $sql = "INSERT INTO lessons (course_id, title, file)
                VALUES ($course_id, '$title', '$file_name')";

        if (mysqli_query($conn, $sql)) {
            echo "Lesson added successfully!";
        } else {
            echo "Database error.";
        }

    } else {
        echo "File upload failed.";
    }
}
?>

<h1>Add Lesson</h1>

<form method="POST" enctype="multipart/form-data">

    Lesson Title:<br>
    <input type="text" name="title"><br><br>

    Select Course:<br>
    <select name="course_id">
        <?php
        $courses = mysqli_query($conn, "SELECT * FROM courses");
        while ($course = mysqli_fetch_assoc($courses)) {
            echo "<option value='{$course['id']}'>{$course['title']}</option>";
        }
        ?>
    </select><br><br>

    Lesson File (PDF / Video):<br>
    <input type="file" name="lesson_file"><br><br>

    <button name="add_lesson">Add Lesson</button>

</form>

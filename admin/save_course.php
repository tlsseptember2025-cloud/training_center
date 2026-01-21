<?php
include "../includes/auth.php";
requireRole('admin');
include "../config/database.php";

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    $title  = mysqli_real_escape_string($conn, $_POST['title']);
    $desc   = mysqli_real_escape_string($conn, $_POST['description']);
    $price  = mysqli_real_escape_string($conn, $_POST['price']);
    $trainer_id = mysqli_real_escape_string($conn, $_POST['trainer_id']); // FIXED

    $sql = "
        INSERT INTO courses (title, description, price, trainer_id)
        VALUES ('$title', '$desc', '$price', '$trainer_id')
    ";

    if (mysqli_query($conn, $sql)) {
        header("Location: courses.php?success=1");
        exit();
    } else {
        echo "Database Error: " . mysqli_error($conn);
    }
}
?>

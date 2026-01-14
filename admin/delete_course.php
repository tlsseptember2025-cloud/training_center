<?php
include "../includes/auth.php";
requireRole('admin');
include "../config/database.php";

$id = (int)($_GET['id'] ?? 0);

mysqli_query($conn, "DELETE FROM courses WHERE id = $id");

header("Location: courses.php");
exit;

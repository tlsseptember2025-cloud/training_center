<?php
include "../includes/auth.php";
requireRole('admin');
include "../config/database.php";

$result = mysqli_query($conn, "SELECT * FROM courses");
?>

<h1>All Courses (Admin)</h1>

<table border="1" cellpadding="10">
    <tr>
        <th>Title</th>
        <th>Description</th>
        <th>Price</th>
        <th>Actions</th>
    </tr>

<?php
while ($course = mysqli_fetch_assoc($result)) {
    echo "<tr>";
    echo "<td>{$course['title']}</td>";
    echo "<td>{$course['description']}</td>";
    echo "<td>{$course['price']}</td>";
    echo "<td>
            <a href='edit_course.php?id={$course['id']}'>Edit</a> |
            <a href='delete_course.php?id={$course['id']}'>Delete</a>
          </td>";
    echo "</tr>";
}
?>
</table>

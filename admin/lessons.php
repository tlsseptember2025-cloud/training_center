<?php
include "../includes/auth.php";
requireRole('admin');
include "../config/database.php";

$sql = "
SELECT lessons.id, lessons.title AS lesson_title, lessons.file,
       courses.title AS course_title
FROM lessons
JOIN courses ON lessons.course_id = courses.id
";

$result = mysqli_query($conn, $sql);
?>
<h1>Manage Lessons</h1>

<table border="1" cellpadding="10">
    <tr>
        <th>Lesson Title</th>
        <th>Course</th>
        <th>File</th>
        <th>Action</th>
    </tr>

<?php
while ($lesson = mysqli_fetch_assoc($result)) {
    echo "<tr>";
    echo "<td>{$lesson['lesson_title']}</td>";
    echo "<td>{$lesson['course_title']}</td>";
    echo "<td>{$lesson['file']}</td>";
    echo "<td>
            <a href='delete_lesson.php?id={$lesson['id']}'>Delete</a>
          </td>";
    echo "</tr>";
}
?>
</table>

<br>
<a href='dashboard.php'>Back to Dashboard</a>

<?php
include "../includes/student_header.php";
include "../includes/auth.php";
requireRole('student');
include "../config/database.php";

$student_id = $_SESSION['user_id'];

$query = mysqli_query($conn, "
    SELECT 
        c.certificate_code,
        c.course_id,
        c.created_at,
        co.title AS course_title
    FROM certificates c
    JOIN courses co ON co.id = c.course_id
    WHERE c.student_id = $student_id
    ORDER BY c.created_at DESC
");
?>

<!DOCTYPE html>
<html>
<head>
    <title>My Certificates</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background: #f4f6f8;
            padding: 40px;
        }
        h2 {
            text-align: center;
            margin-bottom: 30px;
        }
        table {
            width: 90%;
            margin: auto;
            border-collapse: collapse;
            background: #fff;
        }
        th, td {
            padding: 12px;
            border: 1px solid #ddd;
            text-align: center;
        }
        th {
            background: #2c7be5;
            color: white;
        }
        a.button {
            padding: 6px 12px;
            background: #28a745;
            color: white;
            text-decoration: none;
            border-radius: 4px;
        }
    </style>
</head>

<body>

<h2>My Certificates</h2>

<?php if (mysqli_num_rows($query) === 0): ?>
    <p style="text-align:center;">You have not earned any certificates yet.</p>
<?php else: ?>
<table>
    <tr>
        <th>Course</th>
        <th>Certificate Code</th>
        <th>Issued On</th>
        <th>Action</th>
    </tr>

    <?php while ($row = mysqli_fetch_assoc($query)): ?>
    <tr>
        <td><?= htmlspecialchars($row['course_title']) ?></td>
        <td><?= htmlspecialchars($row['certificate_code']) ?></td>
        <td><?= date("F j, Y", strtotime($row['created_at'])) ?></td>
        <td>
            <a class="button" 
               href="certificate.php?course_id=<?= $row['course_id'] ?>">
               Download
            </a>
        </td>
    </tr>
    <?php endwhile; ?>
</table>
<?php endif; ?>

</body>
</html>

<?php
include "../includes/footer.php";
?>
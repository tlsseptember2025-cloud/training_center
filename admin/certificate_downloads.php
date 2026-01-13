<?php
include "../includes/admin_header.php";
include "../includes/auth.php";
requireRole('admin');
include "../config/database.php";

$query = mysqli_query($conn, "
    SELECT 
        u.name AS student,
        c.title AS course,
        d.downloaded_at
    FROM certificate_downloads d
    JOIN users u ON u.id = d.student_id
    JOIN courses c ON c.id = d.course_id
    ORDER BY d.downloaded_at DESC
");
?>

<!DOCTYPE html>
<html>
<head>
    <title>Certificate Downloads</title>
    <style>
        body { font-family: Arial; background:#f4f6f8; padding:40px; }
        table {
            width: 90%;
            margin:auto;
            border-collapse: collapse;
            background:#fff;
        }
        th, td {
            padding: 12px;
            border: 1px solid #ddd;
            text-align:center;
        }
        th { background:#6c757d; color:white; }
    </style>
</head>

<body>
<h2 style="text-align:center;">Certificate Download History</h2>

<?php if (mysqli_num_rows($query) === 0): ?>
    <p style="text-align:center;">No downloads yet.</p>
<?php else: ?>
<table>
    <tr>
        <th>Student</th>
        <th>Course</th>
        <th>Downloaded At</th>
    </tr>
    <?php while ($row = mysqli_fetch_assoc($query)): ?>
    <tr>
        <td><?= htmlspecialchars($row['student']) ?></td>
        <td><?= htmlspecialchars($row['course']) ?></td>
        <td><?= date("F j, Y H:i", strtotime($row['downloaded_at'])) ?></td>
    </tr>
    <?php endwhile; ?>
</table>
<?php endif; ?>
</body>
</html>

<?php
include "../includes/footer.php";
?>
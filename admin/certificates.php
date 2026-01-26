<?php
include "../includes/admin_header.php";
include "../includes/auth.php";
requireRole('admin');
include "../config/database.php";

$query = mysqli_query($conn, "
    SELECT 
        c.certificate_code,
        c.created_at,
        u.name AS student_name,
        co.title AS course_title
    FROM certificates c
    JOIN users u ON u.id = c.student_id
    JOIN courses co ON co.id = c.course_id
    ORDER BY c.created_at DESC
");
?>

<!DOCTYPE html>
<html>
<head>
    <title>Issued Certificates</title>
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
            width: 95%;
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
            background: #343a40;
            color: white;
        }
        a.verify {
            padding: 6px 10px;
            background: #2c7be5;
            color: white;
            text-decoration: none;
            border-radius: 4px;
        }
    </style>
</head>

<body>

<h2>Issued Certificates</h2>

<a href="dashboard.php" class="back-btn">
        <i class="fa fa-arrow-left"></i> Back to Dashboard
    </a>

<?php if (mysqli_num_rows($query) === 0): ?>
    <p style="text-align:center;">No certificates issued yet.</p>
<?php else: ?>
<table>
    <tr>
        <th>Student</th>
        <th>Course</th>
        <th>Certificate Code</th>
        <th>Issued On</th>
        <th>Verify</th>
    </tr>

    <?php while ($row = mysqli_fetch_assoc($query)): ?>
    <tr>
        <td><?= htmlspecialchars($row['student_name']) ?></td>
        <td><?= htmlspecialchars($row['course_title']) ?></td>
        <td><?= htmlspecialchars($row['certificate_code']) ?></td>
        <td><?= date("F j, Y", strtotime($row['created_at'])) ?></td>
        <td>
            <a class="verify"
               href="../verify.php?code=<?= urlencode($row['certificate_code']) ?>"
               target="_blank">
               Verify
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
<?php
include "../includes/admin_header.php";
include "../includes/auth.php";
requireRole('admin');
include "../config/database.php";

// Fetch certificates WITHOUT JOIN (deleted courses still show)
$query = mysqli_query($conn, "
    SELECT *
    FROM certificates
    ORDER BY issued_at DESC
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
        .badge {
            padding: 6px 10px;
            border-radius: 4px;
            color: white;
        }
        .bg-danger { background: #dc3545; }
        .bg-success { background: #28a745; }
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
        <th>Expiry</th>
        <th>Status</th>
        <th>Verify</th>
    </tr>

    <?php while ($row = mysqli_fetch_assoc($query)): ?>

    <?php
        // Safe values
        $student = htmlspecialchars($row['student_name']);
        $course  = !empty($row['course_title']) ? htmlspecialchars($row['course_title']) : "<i>Deleted Course</i>";

        // Handle dates safely
        $issued_at = !empty($row['issued_at']) ? date("F j, Y", strtotime($row['issued_at'])) : "—";
        $expires_at = !empty($row['expires_at']) ? date("F j, Y", strtotime($row['expires_at'])) : "—";

        // Status (expired / active / unknown)
        if (empty($row['expires_at'])) {
            $status = "—";
            $badge = "";
        } else {
            $isExpired = strtotime($row['expires_at']) < time();
            $status = $isExpired ? "Expired" : "Active";
            $badge = $isExpired ? "bg-danger" : "bg-success";
        }
    ?>

    <tr>
        <td><?= $student ?></td>
        <td><?= $course ?></td>
        <td><?= htmlspecialchars($row['certificate_code']) ?></td>

        <td><?= $issued_at ?></td>
        <td><?= $expires_at ?></td>

        <td>
            <?php if ($status !== "—"): ?>
                <span class="badge <?= $badge ?>"><?= $status ?></span>
            <?php else: ?>
                —
            <?php endif; ?>
        </td>

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

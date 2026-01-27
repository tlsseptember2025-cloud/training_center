<?php
include "../includes/auth.php";
requireRole('student');
include "../config/database.php";

$student_id = $_SESSION['user_id'];

// Fetch certificates
$q = mysqli_query($conn, "
    SELECT 
        id,
        course_title,
        certificate_code,
        issued_at,
        expires_at
    FROM certificates
    WHERE student_id = $student_id
    ORDER BY issued_at DESC
");
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>My Certificates</title>

<style>
/* Page container */
.container {
    max-width: 1100px;
    margin: 40px auto;
    padding: 10px;
}

/* Table Card */
.table-card {
    background: #ffffff;
    padding: 20px;
    border-radius: 12px;
    box-shadow: 0 4px 12px rgba(0,0,0,0.08);
}

/* Styled Table */
.styled-table {
    width: 100%;
    border-collapse: collapse;
    font-size: 16px;
    border-radius: 12px;
    overflow: hidden;
}

.styled-table thead tr {
    background: #1a1f36;
    color: #ffffff;
    text-align: left;
    font-size: 15px;
}

.styled-table th, 
.styled-table td {
    padding: 14px 18px;
}

.styled-table tr {
    border-bottom: 1px solid #e3e3e3;
}

.styled-table tbody tr:hover {
    background: #f8f9fc;
}

/* Download button */
.btn-download {
    padding: 8px 16px;
    background: #1a73e8;
    color: #fff;
    border-radius: 6px;
    text-decoration: none;
    font-size: 14px;
}

.btn-download:hover {
    background: #0c57b3;
}

.badge {
    padding: 6px 12px;
    border-radius: 20px;
    color: #fff;
    font-size: 13px;
}
.bg-success { background:#28a745; }
.bg-danger { background:#dc3545; }

</style>
</head>

<body>

<?php include "../includes/student_header.php"; ?>

<div class="container">

    <h2 style="font-size:28px; font-weight:600; margin-bottom:10px;">My Certificates</h2>
    <p style="color:#555; margin-bottom:25px;">Your earned certificates are listed below.</p>

<div class="table-card">

<?php if (mysqli_num_rows($q) == 0): ?>
    <p>You have not earned any certificates yet.</p>
<?php else: ?>

<table class="styled-table">
    <thead>
        <tr>
            <th>Course</th>
            <th>Certificate Code</th>
            <th>Issued At</th>
            <th>Expired At</th>
            <th>Status</th>
            <th>Download</th>
        </tr>
    </thead>

    <tbody>
    <?php while ($row = mysqli_fetch_assoc($q)): ?>
        <?php
            $isExpired = strtotime($row['expires_at']) < time();
            $statusText = $isExpired ? "Expired" : "Active";
            $statusClass = $isExpired ? "bg-danger" : "bg-success";
        ?>
        <tr>
            <td><?= htmlspecialchars($row['course_title']) ?></td>
            <td><strong><?= $row['certificate_code'] ?></strong></td>
            <td><?= date("F j, Y", strtotime($row['issued_at'])) ?></td>
            <td><?= date("F j, Y", strtotime($row['expires_at'])) ?></td>
            <td><span class="badge <?= $statusClass ?>"><?= $statusText ?></span></td>
            <td>
                <a href="certificate.php?id=<?= $row['id'] ?>" 
                class="btn-download"
                target="_blank">
                Download
                </a>
            </td>
        </tr>
    <?php endwhile; ?>
    </tbody>
</table>

<?php endif; ?>

</div>
</div>

</body>
</html>
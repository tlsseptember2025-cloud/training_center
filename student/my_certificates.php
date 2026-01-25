<?php
include "../includes/auth.php";
requireRole('student');
include "../config/database.php";

$student_id = $_SESSION['user_id'];

// =========================
// FETCH CERTIFICATES
// =========================
$q = mysqli_query($conn, "
    SELECT c.id, c.certificate_code, c.course_id, c.issued_at, co.title
    FROM certificates c
    INNER JOIN courses co ON co.id = c.course_id
    WHERE c.student_id = $student_id
    ORDER BY c.issued_at DESC
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

/* Certificate Card */
.certificate-card {
    background: #ffffff;
    padding: 25px 30px;
    margin-bottom: 22px;
    border-radius: 12px;
    box-shadow: 0 4px 15px rgba(0,0,0,0.08);
    transition: 0.2s ease-in-out;
}

.certificate-card:hover {
    transform: translateY(-3px);
    box-shadow: 0 8px 20px rgba(0,0,0,0.12);
}

/* Title */
.certificate-title {
    font-size: 22px;
    font-weight: 600;
    margin-bottom: 8px;
}

/* Secondary text */
.small-text {
    color: #555;
    font-size: 15px;
    margin-bottom: 4px;
}

/* Download Button */
.btn-primary {
    display: inline-block;
    background: #2d7bf4;
    color: #fff !important;
    padding: 10px 18px;
    border-radius: 8px;
    text-decoration: none;
    font-size: 15px;
    font-weight: 500;
}

.btn-primary:hover {
    background: #1b63d1;
}

/* TABLE STYLE FOR CERTIFICATES */
.cert-table-wrapper {
    width: 100%;
    margin-top: 25px;
}

.cert-table {
    width: 100%;
    border-collapse: collapse;
    background: #fff;
    border-radius: 10px;
    overflow: hidden;
    box-shadow: 0 2px 8px rgba(0,0,0,0.1);
}

.cert-table th {
    background: #1a1f36;
    color: #fff;
    padding: 15px;
    text-align: left;
    font-size: 15px;
}

.cert-table td {
    padding: 15px;
    border-bottom: 1px solid #eee;
    font-size: 15px;
}

.cert-table tr:hover {
    background: #f7f9fc;
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

</style>
</head>

<body>

<?php include "../includes/student_header.php"; ?>

<div class="container">
    <h2 style="font-size:28px; font-weight:600; margin-bottom:10px;">My Certificates</h2>
    <p style="color:#555; margin-bottom:25px;">Your earned certificates are listed below.</p>

    <?php if (mysqli_num_rows($q) == 0): ?>

    <p>You have not earned any certificates yet.</p>

<?php else: ?>

<div class="cert-table-wrapper">
    <table class="cert-table">
        <thead>
            <tr>
                <th>Course</th>
                <th>Certificate Code</th>
                <th>Issued At</th>
                <th>Download</th>
            </tr>
        </thead>

        <tbody>
            <?php while ($row = mysqli_fetch_assoc($q)): ?>
            <tr>
                <td><?= htmlspecialchars($row['title']) ?></td>

                <td><strong><?= $row['certificate_code'] ?></strong></td>

                <td><?= date("F j, Y", strtotime($row['issued_at'])) ?></td>

                <td>
                    <a href="certificate.php?course_id=<?= $row['course_id'] ?>" 
                       class="btn-download">Download</a>
                </td>
            </tr>
            <?php endwhile; ?>
        </tbody>
    </table>
</div>

<?php endif; ?>


</div>

</body>
</html>

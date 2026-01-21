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
.container { padding: 40px; }
.certificate-card {
    background: #fff;
    padding: 20px;
    margin-bottom: 15px;
    border-radius: 8px;
    box-shadow: 0 2px 8px rgba(0,0,0,0.08);
}
.certificate-title { font-size: 20px; font-weight: bold; }
.small-text { color: #666; font-size: 14px; }
.btn-download {
    background: #007bff;
    color: #fff !important;
    padding: 8px 14px;
    border-radius: 5px;
    text-decoration: none;
}
.btn-download:hover { background: #0056c7; }
</style>
</head>

<body>

<?php include "../includes/student_header.php"; ?>

<div class="container">
    <h2>My Certificates</h2>
    <p>Your earned certificates are listed below.</p>

    <?php if (mysqli_num_rows($q) == 0): ?>
        <p>You have not earned any certificates yet.</p>
    <?php else: ?>
        <?php while ($row = mysqli_fetch_assoc($q)): ?>
        <div class="certificate-card">
            <div class="certificate-title"><?= htmlspecialchars($row['title']) ?></div>

            <div class="small-text">
                Certificate Code: <strong><?= $row['certificate_code'] ?></strong>
            </div>

            <div class="small-text">
                Issued At: <?= date("F j, Y", strtotime($row['issued_at'])) ?>
            </div>

            <br>

            <a href="certificate.php?course_id=<?= $row['course_id'] ?>" class="btn btn-primary">
    Download
</a>

        </div>
        <?php endwhile; ?>
    <?php endif; ?>

</div>

</body>
</html>

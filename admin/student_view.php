
<head>

<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">

<style>

.cert-status {
    font-size: 13px;
    padding: 6px 10px;
    display: inline-block;
}

</style>

</head>

<?php
include "../includes/admin_header.php";
include "../includes/auth.php";
requireRole('admin');
include "../config/database.php";

$student_id = (int)($_GET['id'] ?? 0);

// =======================
// FETCH STUDENT
// =======================
$student = mysqli_fetch_assoc(mysqli_query($conn, "
    SELECT id, name, email, status
    FROM users
    WHERE id = $student_id AND role = 'student'
"));

if (!$student) {
    echo "<div class='admin-container'><p>Student not found.</p></div>";
    include "../includes/footer.php";
    exit;
}

// =======================
// FETCH ENROLLMENTS
// =======================
$enrollments = mysqli_query($conn, "
    SELECT c.title
    FROM enrollments e
    JOIN courses c ON c.id = e.course_id
    WHERE e.student_id = $student_id
");

// =======================
// FETCH CERTIFICATES (NO DEPENDENCE ON COURSE TABLE)
// =======================
$certificates = mysqli_query($conn, "
    SELECT id, course_title, certificate_code, issued_at, expires_at
    FROM certificates
    WHERE student_id = $student_id
    ORDER BY issued_at DESC
");
?>

<div class="admin-container">

    <!-- PAGE HEADER -->
    <div class="page-header">
        <h1>Student Profile</h1>
        <p>View student details, enrollments, and certificates</p>
    </div>

    <!-- ACTIONS -->
    <div class="page-actions">
        <a href="students.php" class="btn btn-secondary">
            ← Back to Students
        </a>
    </div>

    <!-- BASIC INFO + ENROLLMENTS -->
    <div class="grid-2">

        <!-- BASIC INFO -->
        <div class="card">
            <h3>Basic Information</h3>
            <p><strong>Name:</strong> <?= htmlspecialchars($student['name']) ?></p>
            <p><strong>Email:</strong> <?= htmlspecialchars($student['email']) ?></p>
            <p>
                <strong>Status:</strong>
                <span class="status <?= $student['status'] ?>">
                    <?= ucfirst($student['status']) ?>
                </span>
            </p>
        </div>

        <!-- ENROLLMENTS -->
        <div class="card">
            <h3>Enrolled Courses</h3>

            <?php if (mysqli_num_rows($enrollments) > 0): ?>
                <ul class="clean-list">
                    <?php while ($e = mysqli_fetch_assoc($enrollments)): ?>
                        <li><?= htmlspecialchars($e['title']) ?></li>
                    <?php endwhile; ?>
                </ul>
            <?php else: ?>
                <p class="muted">No enrollments</p>
            <?php endif; ?>
        </div>

    </div>

    <!-- CERTIFICATES -->
    <div class="table-card" style="margin-top:30px;">
        <h3 style="margin-bottom:15px;">Certificates</h3>

        <?php if (mysqli_num_rows($certificates) > 0): ?>
            <table class="styled-table">
                <thead>
                    <tr>
                        <th>Course</th>
                        <th>Certificate Code</th>
                        <th>Issued</th>
                        <th>Expiry</th>
                        <th>Status</th>
                        <th style="width:240px;">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while ($c = mysqli_fetch_assoc($certificates)): ?>
                         <?php
                            $isExpired = strtotime($c['expires_at']) < time();
                            $statusText = $isExpired ? "Expired" : "Active";
                            $statusClass = $isExpired ? "cert-status badge bg-danger text-light" : "cert-status badge bg-success text-light";
                        ?>
                        <tr>
                            <td><?= htmlspecialchars($c['course_title']) ?></td>

                            <td>
                                <strong><?= $c['certificate_code'] ?></strong>
                            </td>

                            <td>
                                <?= date("F j, Y", strtotime($c['issued_at'])) ?>
                            </td>
                            <td><?= date("F j, Y", strtotime($c['expires_at'])) ?></td>
                            <td><span class="<?= $statusClass ?>"><?= $statusText ?></span></td>

                            <td class="actions">

                                <!-- DOWNLOAD CERTIFICATE USING CERTIFICATE ID -->
                                <a href="../student/certificate.php?id=<?= $c['id'] ?>&student_id=<?= $student_id ?>"
                                   class="btn btn-primary"
                                   target="_blank">
                                    <i class="fa fa-download"></i> Download
                                </a>

                                <!-- VERIFY CERTIFICATE -->
                                <a href="../verify.php?code=<?= $c['certificate_code'] ?>"
                                   class="btn btn-secondary"
                                   target="_blank">
                                    <i class="fa fa-check"></i> Verify
                                </a>

                            </td>
                        </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>

        <?php else: ?>
            <p class="muted">No certificates issued</p>
        <?php endif; ?>

    </div>

</div>

<?php include "../includes/footer.php"; ?>
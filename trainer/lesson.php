<?php
include "../includes/auth.php";
requireRole("trainer");
include "../config/database.php";
include "../includes/trainer_header.php";

$trainer_id = $_SESSION["user_id"];
$lesson_id  = isset($_GET["id"]) ? intval($_GET["id"]) : 0;

if ($lesson_id <= 0) {
    die("<h2 style='color:red;text-align:center;margin-top:40px;'>Invalid lesson.</h2>");
}

// Fetch lesson
$lesson_res = mysqli_query($conn, "
    SELECT lessons.*, courses.trainer_id 
    FROM lessons
    JOIN courses ON lessons.course_id = courses.id
    WHERE lessons.id = $lesson_id
    LIMIT 1
");

$lesson = mysqli_fetch_assoc($lesson_res);

if (!$lesson) {
    die("<h2 style='color:red;text-align:center;margin-top:40px;'>Lesson not found.</h2>");
}

// Security: Trainer owns this lesson
if ($lesson['trainer_id'] != $trainer_id) {
    die("<h2 style='color:red;text-align:center;margin-top:40px;'>Access denied — unauthorized lesson.</h2>");
}
?>

<style>
.lesson-container {
    width: 70%;
    margin: 40px auto;
}

.lesson-title {
    text-align: center;
    font-size: 32px;
    font-weight: bold;
    margin-bottom: 25px;
}

.lesson-horizontal-table {
    width: 100%;
    background: white;
    border-radius: 12px;
    border-collapse: collapse;
    box-shadow: 0 5px 25px rgba(0,0,0,0.05);
    overflow: hidden;
}

.lesson-horizontal-table th {
    background: #f1f3f5;
    padding: 18px;
    font-size: 17px;
    text-align: left;
    border-right: 1px solid #ddd;
    width: 25%;
    vertical-align: top;
}

.lesson-horizontal-table td {
    padding: 18px;
    font-size: 16px;
    border-bottom: 1px solid #eee;
    vertical-align: top;
}

.lesson-horizontal-table tr:last-child td {
    border-bottom: none;
}

.btn-download {
    display: inline-block;
    padding: 10px 18px;
    background: #0d6efd;
    color: white !important;
    border-radius: 6px;
    text-decoration: none;
    font-size: 15px;
}

.btn-download:hover {
    background: #0959c9;
}
</style>

<div class="lesson-container">

    <h2 class="lesson-title"><?= htmlspecialchars($lesson['title']); ?></h2>

    <a href="course.php?id=<?= $_GET['course_id'] ?>" class="btn btn-secondary mb-3">
    ← Back to Course
</a>

    <table class="lesson-horizontal-table">

        

        <?php if (!empty($lesson['file'])): ?>
        <tr>
            <th>Attached File</th>
            <td>
                <a href="../uploads/lessons/<?= $lesson['file']; ?>" 
                   target="_blank"
                   class="btn-download">
                   📄 View / Download File
                </a>
            </td>
        </tr>
        <?php endif; ?>

    </table>

</div>

<?php include "../includes/footer.php"; ?>

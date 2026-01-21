<?php
include "../includes/auth.php";
requireRole("trainer");
include "../config/database.php";

$trainer_id = $_SESSION["user_id"];
$course_id = isset($_GET["id"]) ? intval($_GET["id"]) : 0;

if ($course_id <= 0) {
    die("<div style='text-align:center;color:red;margin-top:40px;'>Invalid course.</div>");
}

// FETCH COURSE
$course_res = mysqli_query($conn, "SELECT * FROM courses WHERE id = $course_id LIMIT 1");
$course = mysqli_fetch_assoc($course_res);

if (!$course) {
    die("<div style='text-align:center;color:red;margin-top:40px;'>Course not found.</div>");
}

// ACCESS CHECK
if ($course['trainer_id'] != $trainer_id) {
    die("<div style='text-align:center;color:red;margin-top:40px;'>Access denied — you are not assigned to this course.</div>");
}

// FETCH LESSONS
$lessons = mysqli_query($conn, "SELECT * FROM lessons WHERE course_id = $course_id ORDER BY id ASC");
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Manage Course</title>
    <link rel="stylesheet" href="../assets/css/dashboard.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">

    <style>
        .page-title {
            font-size: 26px;
            font-weight: 700;
            margin-bottom: 25px;
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .course-box {
            background: #fff;
            padding: 35px;
            border-radius: 14px;
            box-shadow: 0 4px 16px rgba(0,0,0,0.08);
            margin-bottom: 35px;
            text-align: center;
        }

        .course-title {
            font-size: 28px;
            font-weight: 700;
            margin-bottom: 10px;
        }

        .add-btn {
            margin-top: 15px;
            padding: 10px 20px;
            background: #1a73e8;
            color: #fff;
            border-radius: 8px;
            font-weight: bold;
            border: none;
            cursor: pointer;
        }
        .add-btn i {
            margin-right: 6px;
        }

        .lessons-box {
            background: #fff;
            padding: 25px;
            border-radius: 14px;
            box-shadow: 0 4px 16px rgba(0,0,0,0.06);
        }

        .lessons-title {
            font-size: 22px;
            font-weight: 600;
            margin-bottom: 18px;
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .lesson-row {
            display: flex;
            justify-content: space-between;
            align-items: center;
            background: #f8f9fc;
            padding: 14px 18px;
            border-radius: 10px;
            margin-bottom: 10px;
        }

        .open-btn {
            background: #1a73e8;
            color: #fff;
            border-radius: 7px;
            padding: 7px 15px;
            font-size: 14px;
            border: none;
            cursor: pointer;
        }
        .open-btn i {
            margin-right: 5px;
        }
    </style>
</head>
<body>

<?php include "../includes/trainer_header.php"; ?>

<div class="page-container">

    <!-- PAGE TITLE -->
    <h2 class="page-title">
        <i class="bi bi-journal-text"></i> Manage Course
    </h2>

    <a href="dashboard.php" class="back-btn">
        <i class="fa fa-arrow-left"></i> Back to Main
    </a>

    <!-- COURSE BOX -->
    <div class="course-box">
        <div class="course-title"><?= htmlspecialchars($course['title']) ?></div>
        <p><?= nl2br(htmlspecialchars($course['description'])) ?></p>
        <p><strong>Price:</strong> $<?= number_format($course['price'], 2) ?></p>

        <a href="add_lesson.php?course_id=<?= $course_id ?>">
            <button class="add-btn">
                <i class="bi bi-plus-circle"></i> Add New Lesson
            </button>
        </a>
    </div>

    <!-- LESSON LIST -->
    <div class="lessons-box">
        <div class="lessons-title">
            <i class="bi bi-collection-play"></i> Course Lessons
        </div>

        <?php if (mysqli_num_rows($lessons) == 0): ?>
            <p style="color:#666;">No lessons added yet.</p>
        <?php else: ?>
            <?php while ($lesson = mysqli_fetch_assoc($lessons)): ?>
                <div class="lesson-row">
                    <div><?= htmlspecialchars($lesson['title']) ?></div>

                    <a href="lesson.php?id=<?= $lesson['id'] ?>">
                        <button class="open-btn">
                            <i class="bi bi-folder2-open"></i> Open
                        </button>
                    </a>
                </div>
            <?php endwhile; ?>
        <?php endif; ?>
    </div>

</div>

</body>
</html>

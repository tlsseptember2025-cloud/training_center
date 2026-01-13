<?php
include "includes/public_header.php";
?>

<div style="text-align:center; margin-top:60px;">
    <h1 style="font-size:42px;">Welcome to Training Center</h1>
    <p style="font-size:20px; color:#555; max-width:700px; margin:20px auto;">
        Learn new skills, track your progress, and earn verified certificates
        — all in one professional learning platform.
    </p>

    <div style="margin-top:40px;">
        <a href="register.php" style="
            background:#2c7be5;
            color:white;
            padding:14px 28px;
            text-decoration:none;
            font-size:18px;
            border-radius:6px;
            margin-right:10px;
        ">Get Started</a>

        <a href="login.php" style="
            background:#6c757d;
            color:white;
            padding:14px 28px;
            text-decoration:none;
            font-size:18px;
            border-radius:6px;
        ">Login</a>
    </div>
</div>

<hr style="margin:80px 0;">

<div style="display:flex; justify-content:space-between; gap:40px;">
    <div style="flex:1; background:white; padding:30px; border-radius:8px;">
        <h3>📚 Structured Courses</h3>
        <p>Learn through organized lessons created by professional trainers.</p>
    </div>

    <div style="flex:1; background:white; padding:30px; border-radius:8px;">
        <h3>📊 Track Progress</h3>
        <p>See your course progress and completed lessons in real time.</p>
    </div>

    <div style="flex:1; background:white; padding:30px; border-radius:8px;">
        <h3>🎓 Verified Certificates</h3>
        <p>Earn certificates with QR verification you can share anywhere.</p>
    </div>
</div>

<?php
include "includes/footer.php";
?>

<?php
include "includes/public_header.php";
?>

<!-- HERO SECTION -->
<div style="text-align:center; margin-top:60px; padding:0 20px;">
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
            display:inline-block;
        ">Get Started</a>

        <a href="login.php" style="
            background:#6c757d;
            color:white;
            padding:14px 28px;
            text-decoration:none;
            font-size:18px;
            border-radius:6px;
            display:inline-block;
        ">Login</a>
    </div>
</div>

<hr style="margin:80px 0;">

<!-- CERTIFICATE VERIFICATION -->
<div style="max-width:700px; margin:0 auto 80px; text-align:center; padding:0 20px;">

    <h2 style="font-size:32px;">Verify a Certificate</h2>

    <p style="font-size:18px; color:#555; margin:15px 0 30px;">
        Enter a certificate ID to verify its authenticity.
        This verification is public and does not require login.
    </p>

    <form action="verify.php" method="GET" target="_blank"
      style="display:flex; justify-content:center; gap:10px; flex-wrap:wrap;">


        <input type="text"
               name="code"
               placeholder="Enter Certificate ID"
               required
               style="
                   padding:14px;
                   font-size:16px;
                   width:280px;
                   border-radius:6px;
                   border:1px solid #ccc;
               ">

        <button type="submit"
                style="
                    background:#198754;
                    color:white;
                    padding:14px 28px;
                    font-size:16px;
                    border:none;
                    border-radius:6px;
                    cursor:pointer;
                ">
            Verify
        </button>

    </form>

</div>

<!-- FEATURES -->
<div style="display:flex; justify-content:space-between; gap:40px; padding:0 20px; flex-wrap:wrap;">

    <div style="flex:1; min-width:240px; background:white; padding:30px; border-radius:8px;">
        <h3>📚 Structured Courses</h3>
        <p>Learn through organized lessons created by professional trainers.</p>
    </div>

    <div style="flex:1; min-width:240px; background:white; padding:30px; border-radius:8px;">
        <h3>📊 Track Progress</h3>
        <p>See your course progress and completed lessons in real time.</p>
    </div>

    <div style="flex:1; min-width:240px; background:white; padding:30px; border-radius:8px;">
        <h3>🎓 Verified Certificates</h3>
        <p>Earn certificates with QR verification you can share anywhere.</p>
    </div>

</div>

<?php
include "includes/footer.php";
?>

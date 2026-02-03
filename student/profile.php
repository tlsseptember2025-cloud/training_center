<?php
include "../includes/auth.php";
requireRole("student");
include "../config/database.php";
include "../includes/student_header.php";

$student_id = $_SESSION["user_id"];

$user = mysqli_fetch_assoc(mysqli_query($conn, "SELECT * FROM users WHERE id=$student_id"));

$photoURL = $user["photo"] 
    ? "/training_center/uploads/profile_photos/" . $user["photo"]
    : "/training_center/uploads/profile_photos/default.png";
?>

<style>
.profile-card {
    max-width: 650px;
    margin: 40px auto;
    background: #fff;
    padding: 30px;
    border-radius: 12px;
    box-shadow: 0 4px 18px rgba(0,0,0,0.1);
    text-align: center;
}

.profile-img {
    width: 150px;
    height: 150px;
    border-radius: 50%;
    object-fit: cover;
    border: 4px solid #ddd;
}

.profile-card h2 {
    margin-top: 15px;
}

.profile-btn {
    margin-top: 25px;
    padding: 10px 20px;
    background: #0d6efd;
    color: white;
    border-radius: 6px;
    text-decoration: none;
    display: inline-block;
}
</style>

<div class="profile-card">

    <img src="<?= $photoURL ?>" class="profile-img">

    <h2><?= $user["name"] ?></h2>

    <p>
        <strong>Email:</strong> <?= $user["email"] ?>
        <a href="change_email.php" class="btn btn-primary">Change Email Here</a>
    </p>
    <p><strong>Phone:</strong> <?= $user["phone"] ?></p>
    <p><strong>Address:</strong> <?= $user["address"] ?></p>
    <p><strong>Bio:</strong> <?= $user["bio"] ?></p>

    <a href="edit_profile.php" class="profile-btn">Edit Profile</a>
</div>

<?php include "../includes/footer.php"; ?>

<?php
include "../includes/auth.php";
requireRole("trainer");
include "../config/database.php";
include "../includes/trainer_header.php";

$trainer_id = $_SESSION['user_id'];

$trainer = mysqli_fetch_assoc(mysqli_query($conn, "
    SELECT * FROM users WHERE id = $trainer_id
"));
?>

<div class="profile-container">

    <h2>My Profile</h2>

    <div class="profile-card">
        <img class="profile-photo" 
             src="<?= $trainer['photo'] ? '/training_center/uploads/profile_photos/'.$trainer['photo'] : '/training_center/assets/default-user.png' ?>" 
             alt="Profile Photo">

        <h3><?= htmlspecialchars($trainer['name']) ?></h3>

        <p><strong>Email:</strong> <?= $trainer['email'] ?></p>
        <p><strong>Phone:</strong> <?= $trainer['phone'] ?: '—' ?></p>
        <p><strong>Address:</strong> <?= $trainer['address'] ?: '—' ?></p>
        <p><strong>Bio:</strong><br><?= nl2br($trainer['bio']) ?: '—' ?></p>

        <a href="edit_profile.php" class="btn btn-primary">Edit Profile</a>
    </div>

</div>

<style>
.profile-container {
    max-width: 700px;
    margin: 30px auto;
}
.profile-card {
    background: #fff;
    padding: 30px;
    border-radius: 10px;
    box-shadow: 0 3px 10px rgba(0,0,0,0.1);
    text-align: center;
}
.profile-photo {
    width: 140px; height: 140px;
    border-radius: 50%;
    object-fit: cover;
    border: 4px solid #ddd;
    margin-bottom: 15px;
}
</style>

<?php include "../includes/footer.php"; ?>
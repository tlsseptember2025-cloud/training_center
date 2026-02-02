<?php
include "../includes/auth.php";
requireRole("trainer");
include "../config/database.php";
include "../includes/trainer_header.php";

$trainer_id = $_SESSION['user_id'];

$msg = "";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $name    = mysqli_real_escape_string($conn, $_POST['name']);
    $phone   = mysqli_real_escape_string($conn, $_POST['phone']);
    $address = mysqli_real_escape_string($conn, $_POST['address']);
    $bio     = mysqli_real_escape_string($conn, $_POST['bio']);

    $photo_file = "";

    if (!empty($_FILES['photo']['name'])) {

        $allowed = ['jpg','jpeg','png'];
        $ext = strtolower(pathinfo($_FILES['photo']['name'], PATHINFO_EXTENSION));

        if (in_array($ext, $allowed)) {

            $photo_file = time() . "_" . rand(1000,9999) . "." . $ext;
            $path = "../uploads/profile_photos/" . $photo_file;
            move_uploaded_file($_FILES['photo']['tmp_name'], $path);

        } else {
            $msg = "<div class='alert error'>Invalid image type.</div>";
        }
    }

    $sql = "
        UPDATE users SET 
            name='$name',
            phone='$phone',
            address='$address',
            bio='$bio'"
            . ($photo_file ? ", photo='$photo_file'" : "") .
        " WHERE id=$trainer_id";

    mysqli_query($conn, $sql);
    $msg = "<div class='alert success'>Profile updated successfully!</div>";
}

$user = mysqli_fetch_assoc(mysqli_query($conn, "SELECT * FROM users WHERE id=$trainer_id"));
?>

<style>
.edit-profile-card {
    max-width: 650px;
    margin: 40px auto;
    background: #fff;
    padding: 30px;
    border-radius: 12px;
    box-shadow: 0 4px 18px rgba(0,0,0,0.1);
    text-align: center;
}

.edit-profile-card h2 {
    margin-bottom: 25px;
    font-size: 26px;
    font-weight: 700;
}

.profile-img-preview {
    width: 120px;
    height: 120px;
    border-radius: 50%;
    border: 3px solid #ddd;
    object-fit: cover;
    margin-bottom: 15px;
}

.form-grid {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 15px 20px;
    margin-top: 20px;
}

.form-grid label {
    text-align: left;
    font-weight: 600;
}

.form-grid input,
.form-grid textarea {
    width: 100%;
    padding: 8px 10px;
    border: 1px solid #ccc;
    border-radius: 6px;
}

textarea {
    height: 80px;
}

.save-btn {
    margin-top: 25px;
    background: #0d6efd;
    color: white;
    border: none;
    padding: 10px 25px;
    font-size: 16px;
    border-radius: 6px;
    cursor: pointer;
}

.save-btn:hover {
    background: #0b5ed7;
}
</style>


<div class="edit-profile-card">

    <h2>Edit Profile</h2>

    <!-- Show photo if exists -->
    <img src="<?= $trainerPhotoURL ?>" class="profile-img-preview" />

    <form method="POST" enctype="multipart/form-data">

        <div class="form-grid">

            <div>
                <label>Name:</label>
                <input type="text" name="name" value="<?= $user['name'] ?>" required>
            </div>

            <div>
                <label>Phone:</label>
                <input type="text" name="phone" value="<?= $user['phone'] ?>">
            </div>

            <div>
                <label>Address:</label>
                <input type="text" name="address" value="<?= $user['address'] ?>">
            </div>

            <div>
                <label>Bio:</label>
                <textarea name="bio"><?= $user['bio'] ?></textarea>
            </div>

        </div>

        <div style="margin-top:20px; text-align:left;">
            <label><strong>Profile Photo:</strong></label><br>
            <input type="file" name="photo">
        </div>

        <button class="save-btn" type="submit">Save Changes</button>

    </form>
</div>
<?php include "../includes/footer.php"; ?>
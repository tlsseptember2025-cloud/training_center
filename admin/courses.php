<?php
include "../includes/admin_header.php";
include "../includes/auth.php";
requireRole('admin');
include "../config/database.php";

$result = mysqli_query($conn, "SELECT * FROM courses");
?>

<div class="admin-container">

    <h1 class="page-title">Courses</h1>

    <!-- BACK BUTTON -->
    <a href="dashboard.php" class="back-btn">
        <i class="fa fa-arrow-left"></i> Back to Dashboard
    </a>

    <div class="page-actions">
        <a href="add_course.php" class="btn btn-primary">
            <i class="fa fa-plus"></i> Add Course
        </a>
    </div>

    <div class="table-card">
        <table class="styled-table">
            <thead>
                <tr>
                    <th>Title</th>
                    <th>Description</th>
                    <th>Price</th>
                    <th>Actions</th>
                </tr>
            </thead>

            <tbody>
            <?php while ($course = mysqli_fetch_assoc($result)): ?>
                <tr>
                    <td><?= htmlspecialchars($course['title']) ?></td>
                    <td><?= htmlspecialchars($course['description']) ?></td>
                    <td><?= htmlspecialchars($course['price']) ?>$</td>

                    <td class="actions">
                        <a href="course_view.php?id=<?= $course['id'] ?>" title="View">
                            <i class="fa fa-eye"></i>
                        </a>

                        <a href="edit_course.php?id=<?= $course['id'] ?>" class="btn-edit">
                            Edit
                        </a>

                        <a href="#" class="delete-btn" data-id="<?= $course['id'] ?>" style="color:red;">Delete</a>
                    </td>

                </tr>
            <?php endwhile; ?>
            </tbody>
        </table>
    </div>

</div>

<!-- Beautiful Delete Confirmation Modal -->
<div id="deleteModal" class="custom-modal hidden">
    <div class="custom-modal-content">
        <h3>Confirm Delete</h3>
        <p>Are you sure you want to delete this course?</p>

        <div class="modal-actions">
            <button class="btn btn-danger" id="confirmDelete">Delete</button>
            <button class="btn btn-secondary" id="cancelDelete">Cancel</button>
        </div>
    </div>
</div>

<style>
.custom-modal {
    position: fixed;
    top: 0; left: 0;
    width: 100%; height: 100%;
    background: rgba(0,0,0,0.4);
    display: flex;
    justify-content: center;
    align-items: center;
    z-index: 9999;
}
.hidden { display: none; }

.custom-modal-content {
    background: #fff;
    padding: 25px;
    border-radius: 10px;
    width: 350px;
    text-align: center;
    box-shadow: 0 5px 25px rgba(0,0,0,0.15);
}

.modal-actions {
    display: flex;
    justify-content: space-between;
    margin-top: 20px;
}
</style>

<script>
let deleteId = null;

document.querySelectorAll(".delete-btn").forEach(btn => {
    btn.addEventListener("click", function(e) {
        e.preventDefault();
        deleteId = this.dataset.id;
        document.getElementById("deleteModal").classList.remove("hidden");
    });
});

document.getElementById("cancelDelete").onclick = function() {
    document.getElementById("deleteModal").classList.add("hidden");
    deleteId = null;
};

document.getElementById("confirmDelete").onclick = function() {
    if (deleteId) {
        window.location.href = "delete_course.php?id=" + deleteId;
    }
};
</script>


<?php include "../includes/footer.php"; ?>

<?php
require_once './include-header.php';
if (isset($_POST['update'])) {
    include_once '_db.php';
    $userId = $_SESSION['userId'];
    $oldPassword = $_POST['oldPassword'];
    $newPassword = $_POST['newPassword'];

    $row = $db->getSingleRecord("SELECT password FROM users where user_id=$userId");
    if (password_verify($oldPassword, $row['password'])) {
        $hashed_password = password_hash($newPassword, PASSWORD_DEFAULT);

        $query = $db->updateData("UPDATE users SET password='$hashed_password' WHERE user_id=$userId");

        if ($query) {
            header('location:change-password.php?status=success&message=' . urlencode('Password Changed Successfully'));
        } else {
            header('location:change-password.php?status=danger&message=' . urlencode('Database Problem'));
        }
    } else {
        header('location:change-password.php?status=danger&message=' . urlencode('Old Password Do not Match'));
    }
}

$pageName = breadCrumbs('Change Password');
?>
<div class="row">
    <div class="col-md-12">
        <div class="card">
            <legend><?= $pageName ?></legend>
            <div class="row">
                <div class="col-lg-6 col-lg-offset-2">
                    <?php require_once './include-message.php'; ?>
                    <div class="well bs-component">
                        <form class="form-horizontal" action="" method="post">
                            <div class="form-group">
                                <label class="col-lg-4 control-label" for="old-password">Old Password</label>
                                <div class="col-lg-8">
                                    <input class="form-control" name="oldPassword" id="old-password" type="password" placeholder="Enter Old Password" required>
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="col-lg-4 control-label" for="new-password">New Password</label>
                                <div class="col-lg-8">
                                    <input class="form-control" name="newPassword" id="new-password" type="password" placeholder="Enter New Password" required>
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="col-lg-4 control-label" for="confirm-password">Confirm Password</label>
                                <div class="col-lg-8">
                                    <input class="form-control" name="confirmPassword" id="confirm-password" type="password" placeholder="Enter Confirm Password" required>
                                </div>
                            </div>

                            <div class="form-group">
                                <div class="col-lg-8 col-lg-offset-4">
                                    <button class="btn btn-success" name="update" type="submit">Update</button>
                                    <button class="btn btn-danger" type="reset">Cancel</button>
                                </div>
                            </div>

                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>


<?php require_once './include-footer.php' ?>
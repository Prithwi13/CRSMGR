<?php
require_once './include-header.php';

if (isset($_POST['update'])) {
    include_once './_db.php';
    $userId = $_SESSION['userId'];
    $firstName = $_POST['firstName'];
    $lastName = $_POST['lastName'];
    $emailId = $_POST['emailId'];

    $check = $db->updateData("UPDATE users SET first_name='$firstName', last_name='$lastName', email_id='$emailId' where user_id=$userId");
    if ($check) {
        $_SESSION['firstName'] = $firstName;
        $_SESSION['lastName'] = $lastName;
        $_SESSION['email'] = $emailId;

        header('location:system-update-profile.php?status=success&message=' . urlencode('Profile Updated Successfully'));
    } else {
        header('location:system-update-profile.php?status=danger&message=' . urlencode('Database Problem'));
    }
}

$pageName = breadCrumbs('Update Profile');
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
                                <label class="col-lg-4 control-label" for="first-name">First Name</label>
                                <div class="col-lg-8">
                                    <input class="form-control" name="firstName" id="first-name" type="text" placeholder="Enter First Name" required value="<?= $_SESSION['firstName'] ?>">
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="col-lg-4 control-label" for="last-name">Last Name</label>
                                <div class="col-lg-8">
                                    <input class="form-control" name="lastName" id="last-name" type="text" placeholder="Enter Last Name" required value="<?= $_SESSION['lastName'] ?>">
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="col-lg-4 control-label" for="email-id">Email Id</label>
                                <div class="col-lg-8">
                                    <input class="form-control" name="emailId" id="email-id" type="text" placeholder="Enter Email Id" required value="<?= $_SESSION['emailId'] ?>">
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
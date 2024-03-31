<?php
require_once './include-header.php';
include_once './_db.php';
$LoggedIn = $_SESSION['userId'];
if ($_SESSION['type'] != ADMIN) {
    header('location:system-dashboard.php');
}

if (isset($_GET['edit-id'])) {
    $userEditId = $_GET['edit-id'];
    $userData = $db->getSingleRecord("SELECT type, first_name, last_name, email_id FROM users where user_id=$userEditId");
    $category = $userData['type'];
    $firstName = $userData['first_name'];
    $lastName = $userData['last_name'];
    $emailId = $userData['email_id'];
}

if (isset($_POST['create'])) {
    $category = $_POST['category'];
    $firstName = $_POST['firstName'];
    $lastName = $_POST['lastName'];
    $emailId = $_POST['emailId'];
    $password = password_hash('123456', PASSWORD_DEFAULT);
    $status = 1;
    // $password = substr(md5($emailId), 0, 8);
    $currentTime = getCurrentTime();

    $result = $db->getSingleRecord("SELECT email_id FROM users where email_id='$emailId'");
    if (count($result) === 0) {
        $sql = "INSERT INTO users(type, first_name, last_name, email_id, password, status, created_by, created_dt) VALUES ('$category','$firstName','$lastName','$emailId','$password', $status, '$LoggedIn', '$currentTime')";

        $check = $db->insertData($sql);
        if ($check > 0) {
            header('location:admin-add-users.php?status=success&message=' . urlencode('User created successfully'));
        } else {
            header('location:admin-add-users.php?status=danger&message=' . urlencode('Database Problem'));
        }
    } else {
        header('location:admin-add-users.php?status=danger&message=' . urlencode('User already exist'));
    }
} else if (isset($_POST['update'])) {
    $userEditId = $_POST['updateId'];
    $category = $_POST['category'];
    $firstName = $_POST['firstName'];
    $lastName = $_POST['lastName'];
    $emailId = $_POST['emailId'];
    $currentTime = getCurrentTime();
    $check = $db->updateData("UPDATE users SET type='$category', first_name='$firstName', last_name='$lastName', email_id='$emailId',modified_by='$LoggedIn', modified_dt='$currentTime' WHERE user_id='$userEditId'");

    if ($check) {
        header('location:admin-add-users.php?status=success&message=' . urlencode('User updated successfully'));
    } else {
        header('location:admin-add-users.php?status=danger&message=' . urlencode('Database Problem'));
    }
}

$pageName = breadCrumbs('Add Users');
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
                                <label class="col-lg-4 control-label" for="category">Role</label>
                                <div class="col-lg-8">
                                    <select class="form-control" id="category" name="category" required>
                                        <option value="">Select category</option>
                                        <?php
                                        $types  = implode(",", [ADMIN, STUDENT]);
                                        $roles = $db->getAllRecords("SELECT id, category FROM role WHERE NOT id in ($types ) ORDER BY category ASC");
                                        foreach ($roles as $key => $role) :
                                        ?>
                                            <option value="<?= $role['id'] ?>" <?= isset($category) ? getSelectOption($role['id'], $category) : '' ?>><?= $role['category'] ?></option>
                                        <?php

                                        endforeach;
                                        ?>
                                    </select>
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="col-lg-4 control-label" for="last-name">Last Name</label>
                                <div class="col-lg-8">
                                    <input class="form-control" name="lastName" id="last-name" type="text" placeholder="Enter Last Name" required value="<?= $lastName ?? '' ?>" />
                                </div>
                            </div>
                            <div class=" form-group">
                                <label class="col-lg-4 control-label" for="first-name">First Name</label>
                                <div class="col-lg-8">
                                    <input class="form-control" name="firstName" id="first-name" type="text" placeholder="Enter First Name" required value="<?= $firstName ?? ''  ?>" />
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="col-lg-4 control-label" for="email-id">Email Id</label>
                                <div class="col-lg-8">
                                    <input class="form-control" name="emailId" id="email-id" type="text" placeholder="Enter Email Id" required value="<?= $emailId ?? '' ?>" />
                                </div>
                            </div>

                            <div class="form-group">
                                <div class="col-lg-8 col-lg-offset-4">
                                    <?php if (isset($userEditId)) : ?>
                                        <input type="hidden" name="updateId" value="<?= $userEditId ?>">
                                        <button class="btn btn-success" name="update" type="submit">Update</button>
                                    <?php else : ?>
                                        <button class="btn btn-success" name="create" type="submit">Create</button>
                                    <?php endif; ?>

                                    <button class="btn btn-danger" type="reset">Cancel</button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
                <div class="col-lg-12">
                    <legend>All Users</legend>
                    <table class="table table-hover table-bordered" id="sampleTable">
                        <thead>
                            <tr>
                                <th>Sri No</th>
                                <th>Role</th>
                                <th>Last Name</th>
                                <th>First Name</th>
                                <th>Email Id</th>
                                <th>Created Date</th>
                                <th>Modified Date</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            $result = $db->getAllRecords("SELECT u.user_id,u.first_name, u.last_name, u.email_id, r.category, u.created_dt, u.modified_dt from users as u INNER JOIN role as r ON r.id=u.type WHERE r.category!='Admin' and r.category!='Student' ORDER BY u.first_name ASC");

                            if (count($result) > 0) :
                                foreach ($result as $key => $value) :
                            ?>
                                    <tr>
                                        <td><?= $key + 1 ?></td>
                                        <td><?= $value['category'] ?></td>
                                        <td><?= $value['last_name'] ?></td>
                                        <td><?= $value['first_name'] ?></td>
                                        <td><?= $value['email_id'] ?></td>
                                        <td><?= $value['created_dt'] ?></td>
                                        <td><?= $value['modified_dt'] ?? 'N/A'; ?></td>
                                        <td><a class="btn btn-success btn-sm" onclick="return confirm('Are you sure you want to edit <?= $value['email_id'] ?> ?')" href="admin-add-users.php?edit-id=<?= $value['user_id'] ?>">Edit</a></td>
                                    </tr>
                                <?php
                                endforeach;
                            else : ?>
                                <tr>
                                    <td colspan="8">No records found</td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
    <?php require_once './include-footer.php' ?>
<?php
require_once './include-header.php';
include_once './_db.php';
$LoggedIn = $_SESSION['userId'];
if ($_SESSION['type'] != STUDENT) {
    redirect("system-dashboard.php");
}

if (isset($_POST['selectGroup'])) {
    $courseId = base64_decode($_POST['courseId']);
    $groupId = $_POST['groupId'];

    $check = $db->getSingleRecord("SELECT *FROM group_leader WHERE student_id=$LoggedIn AND course_id=$courseId");
    if ($check) {
        redirect('student-course-group.php', 'status=danger&message=' . urlencode('Group already selected'));
    }

    $selectTotalCount = $db->getSingleRecord("SELECT total_count, groups_limit FROM student_group WHERE id=$groupId");
    if ((int)$selectTotalCount['total_count'] !== (int)$selectTotalCount['groups_limit']) {
        $groupSelection = ACTIVE;
        $leaderSelection = INACTIVE;
        $currentTime = getCurrentTime();
        $leaderCount = 0;
        $check = $db->insertData("INSERT INTO group_leader VALUES (null, $courseId, $groupId, $LoggedIn,$groupSelection, $leaderSelection, $leaderCount, '$currentTime')");
        if ($check) {
            $selectTotalCount = $selectTotalCount['total_count'] + 1;
            $db->updateData("UPDATE student_group SET total_count=$selectTotalCount WHERE id=$groupId");
            redirect('student-course-group.php', 'status=success&message=' . urlencode('Group selected successfully'));
        } else {
            redirect('student-course-group.php', 'status=danger&message=' . urlencode('Database Problem'));
        }
    } else {
        redirect('student-course-group.php', 'status=danger&message=' . urlencode('Group limit reached'));
    }
} elseif (isset($_POST['selectLeader'])) {
    $groupId = base64_decode($_POST['groupId']);
    $userId  = $_POST['userId'];
    $thePersonWhoIsVoting = $db->getSingleRecord("SELECT * FROM group_leader WHERE student_id=$LoggedIn AND group_id=$groupId");
    $groupTableId = $thePersonWhoIsVoting['id'];
    if ((int)$thePersonWhoIsVoting['leader_selection'] === 0) {
        $groupLeaderTable = $db->getSingleRecord("SELECT * FROM group_leader WHERE student_id=$userId AND group_id=$groupId");
        $id = $groupLeaderTable['id'];
        $leaderCount = $groupLeaderTable['leader_count'] + 1;
        $check2 = $db->updateData("UPDATE group_leader SET leader_selection=1 WHERE id=$groupTableId");
        $check1 = $db->updateData("UPDATE group_leader SET leader_count=$leaderCount WHERE id=$id");
        if ($check1 && $check2) {
            redirect('student-course-group.php', 'status=success&message=' . urlencode('Leader selected successfully'));
        } else {
            redirect('student-course-group.php', 'status=danger&message=' . urlencode('Database Problem'));
        }
    } else {
        redirect('student-course-group.php', 'status=danger&message=' . urlencode('You have already selected a leader for this group'));
    }
} else if (isset($_POST['uploadAssignment'])) {
    $courseId =  base64_decode($_POST['courseId']);
    $groupId = base64_decode($_POST['groupId']);


    $uploadFile = $_FILES['uploadFile'];
    $fileName = $uploadFile['name'];
    $fileTmpName = $uploadFile['tmp_name'];
    $fileSize = $uploadFile['size'];
    $fileError = $uploadFile['error'];
    $fileType = $uploadFile['type'];
    $fileExt = explode('.', $fileName);
    $fileActualExt = strtolower(end($fileExt));

    if ($fileError === 0) {
        $fileNameNew = time() . '.' . $fileActualExt;
        $fileDestination = './uploads/leader-assignment/' . $fileNameNew;
        if (move_uploaded_file($fileTmpName, $fileDestination)) {
            $currentTime = getCurrentTime();
            $status = ACTIVE;
            $check = $db->insertData("INSERT INTO group_assignment values(null, $courseId, $groupId, '$fileNameNew', $LoggedIn, '$currentTime')");
            if ($check) {
                redirect('student-course-group.php', "status=success&message=" . urlencode('Assignment uploaded successfully'));
            } else {
                redirect('student-course-group.php', "status=danger&message=" . urlencode('Database Problem'));
            }
        } else {
            redirect('student-course-group.php', "status=danger&message=" . urlencode('File not uploaded'));
        }
    } else {
        redirect('student-course-group.php', "status=danger&message=" . urlencode('Something went wrong'));
    }
}

$pageName = breadCrumbs('Course Group', '<i class="fa fa-users" aria-hidden="true"></i>');
$status = ACTIVE;
$getActiveCourse = $db->getSingleRecord("SELECT c.*, ct.term_name FROM course as c INNER JOIN course_term as ct ON ct.id=c.term  WHERE c.status=$status ORDER BY c.created_dt DESC");
if (count($getActiveCourse) === 0) :
?>
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                There are no active course
            </div>
        </div>
    </div>
    <?php else :
    $courseId = $getActiveCourse['course_id'];
    $checkGroupTableExist = $db->getSingleRecord("SELECT * FROM student_group WHERE course_id=$courseId");
    $selectGroupLeaderTable = $db->getSingleRecord("SELECT * FROM group_leader WHERE student_id=$LoggedIn AND course_id=$courseId");
    if (count($checkGroupTableExist) == 0) : ?>
        <div class="row">
            <div class="col-md-12">
                <div class="card">
                    There are no groups for this course
                </div>
            </div>
        </div>
    <?php
    // this code is use to select group
    elseif (count($selectGroupLeaderTable) == 0) :
    ?>
        <div class="row">
            <div class="col-md-12">
                <div class="card">
                    <?php require_once './include-message.php'; ?>
                    <legend>All Groups</legend>
                    <table class="table table-hover table-bordered" id="sampleTable">
                        <thead>
                            <tr>
                                <th>Sri No</th>
                                <th>Group Name</th>
                                <th>Group Limit</th>
                                <th>Total Count</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            $result = $db->getAllRecords("SELECT * FROM student_group WHERE course_id=$courseId");
                            if (count($result) > 0) :
                                foreach ($result as $key => $value) :
                            ?>
                                    <tr>
                                        <td><?= $key + 1 ?></td>
                                        <td><?= $value['group_name'] ?></td>
                                        <td><?= $value['groups_limit'] ?></td>
                                        <td><?= $value['total_count'] ?></td>
                                    </tr>
                                <?php
                                endforeach;
                            else : ?>
                                <tr>
                                    <td colspan="4">No records found</td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                    <legend>Select You Group</legend>
                    <div class="row">
                        <div class="col-lg-6 col-lg-offset-2">
                            <?php
                            $activeCourse = $getActiveCourse['course_id']
                            ?>
                            <div class="well bs-component">
                                <?php $groupTable = $db->getSingleRecord("SELECT * FROM group_leader WHERE student_id=$LoggedIn AND course_id=$activeCourse"); ?>
                                <form class="form-horizontal" onsubmit="return confirm('Are you sure you want to select this group ? Once you select any group you cannot modify it ?')" action="<?= $_SERVER['PHP_SELF'] ?>" method="post" enctype="multipart/form-data">
                                    <input type="hidden" name="courseId" value="<?= base64_encode($activeCourse) ?>">
                                    <div class="form-group">
                                        <label class="col-lg-4 control-label" for="select-group">Select Group</label>
                                        <div class="col-lg-8">
                                            <select class="form-control" id="select-group" name="groupId" required>
                                                <option value="">Select Group</option>
                                                <?php
                                                $result = $db->getAllRecords("SELECT * FROM student_group WHERE course_id=$activeCourse ORDER BY created_dt ASC");
                                                foreach ($result as $key => $value) :
                                                ?>
                                                    <option value="<?= $value['id'] ?>"><?= $value['group_name'] ?></option>
                                                <?php endforeach ?>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <div class="col-lg-8 col-lg-offset-4">
                                            <button class="btn btn-success btn-sm" name="selectGroup" type="submit">Select</button>
                                            <button class="btn btn-danger btn-sm" type="reset">Cancel</button>
                                        </div>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    <?php
    // End of code is use to select group

    // this code is used to select group leader
    elseif ($selectGroupLeaderTable['group_selection'] == ACTIVE and $selectGroupLeaderTable['leader_selection'] == INACTIVE) :
        $groupId = $selectGroupLeaderTable['group_id'];
        $selectGroup = $db->getSingleRecord("SELECT * FROM student_group WHERE id=$groupId");
    ?>
        <div class="row">
            <div class="col-md-12">
                <div class="card">
                    <legend>Your Group</legend>
                    <?php require_once './include-message.php' ?>
                    <table class="table table-hover table-bordered" id="sampleTable">
                        <thead>
                            <tr>
                                <th>Group Name</th>
                                <th>Number of Members/Capacity</th>
                                <th>Project Id</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td><?= $selectGroup['group_name'] ?></td>
                                <td><?= $selectGroup['total_count'] . ' / ' . $selectGroup['groups_limit'] ?></td>
                                <td><?= $selectGroup['id'] ?></td>
                            </tr>
                        </tbody>
                    </table>
                    <?php
                    $groupId = $selectGroupLeaderTable['group_id'];
                    $selectGroup = $db->getSingleRecord("SELECT * FROM student_group WHERE id=$groupId");
                    $groupsLimit = $selectGroup['groups_limit'];
                    $selectGroupLeaderWholeTable = $db->getSingleRecord("SELECT COUNT(id) as count FROM group_leader WHERE course_id=$courseId AND group_id=$groupId");
                    if ((int)$selectGroupLeaderWholeTable['count'] != (int)$groupsLimit) : ?>
                        <div class="alert alert-info">
                            Now please wait for other members to fill this group, and then we will proceed with the selection of a group leader
                        </div>
                    <?php else :
                    ?>
                        <div class="well bs-component">
                            <legend>Select Your group leader</legend>
                            <form class="form-horizontal" onsubmit="return confirm('Are you sure you want to select this person ? Once you select any person you cannot modify it ?')" action="<?= $_SERVER['PHP_SELF'] ?>" method="post" enctype="multipart/form-data">
                                <div class="form-group">
                                    <label class="col-lg-4 control-label" for="select-group-Leader">Select Group Leader</label>
                                    <div class="col-lg-4">
                                        <input type="hidden" name="groupId" value="<?= base64_encode($groupId) ?>">
                                        <select class="form-control" id="select-group-Leader" name="userId" required>
                                            <option value="">Select Group Leader</option>
                                            <?php
                                            $studentType = STUDENT;
                                            $result = $db->getAllRecords("SELECT * FROM users as u INNER JOIN group_leader as gl ON gl.student_id=u.user_id WHERE u.type=$studentType and gl.group_id=$groupId ORDER BY u.first_name ASC");

                                            foreach ($result as $key => $value) :
                                                if ($LoggedIn !== $value['user_id']) :
                                            ?>
                                                    <option value="<?= $value['user_id'] ?>"><?= $value['first_name'] . ' ' . $value['last_name'] ?> (<?= $value['email_id'] ?>)</option>
                                            <?php
                                                endif;
                                            endforeach;
                                            ?>
                                        </select>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <div class="col-lg-8 col-lg-offset-4">
                                        <button class="btn btn-success btn-sm" name="selectLeader" type="submit">Select</button>
                                        <button class="btn btn-danger btn-sm" type="reset">Cancel</button>
                                    </div>
                                </div>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    <?php
    // End of  code is used to select group leader

    // this code is used to selected leader
    elseif ($selectGroupLeaderTable['leader_selection'] == ACTIVE) :
        $groupId = $selectGroupLeaderTable['group_id'];
        $selectGroup = $db->getSingleRecord("SELECT * FROM student_group WHERE id=$groupId");
    ?>
        <div class="row">
            <div class="col-md-12">
                <div class="card">
                    <legend>Your Group</legend>
                    <?php require_once './include-message.php' ?>
                    <table class="table table-hover table-bordered" id="sampleTable">
                        <thead>
                            <tr>
                                <th>Group Name</th>
                                <th>Number of Members/Capacity</th>
                                <th>Project Id</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td><?= $selectGroup['group_name'] ?></td>
                                <td><?= $selectGroup['total_count'] . ' / ' . $selectGroup['groups_limit'] ?></td>
                                <td><?= $selectGroup['id'] ?></td>
                            </tr>
                        </tbody>
                    </table>
                    <?php
                    $courseId = $getActiveCourse['course_id'];
                    $groupId = $selectGroupLeaderTable['group_id'];
                    $selectGroup = $db->getSingleRecord("SELECT * FROM student_group WHERE id=$groupId");
                    $totalCount = $selectGroup['total_count'];
                    $groupsLimit = $selectGroup['groups_limit'];

                    $selectGroupLeaderWholeTable = $db->getSingleRecord("SELECT COUNT(id) as count FROM group_leader WHERE course_id=$courseId AND group_id=$groupId and leader_selection=0");
                    if ((int)$selectGroupLeaderWholeTable['count'] === (int)$totalCount) : ?>
                        <div class="alert alert-info">
                            Now please wait for other members to fill this group, and then we will proceed with the selection of a group leader.
                        </div>
                    <?php else :
                        $selectLeader = $db->getSingleRecord("SELECT u.user_id,u.first_name,u.last_name,u.email_id from group_leader as g INNER JOIN users as u ON u.user_id=g.student_id WHERE leader_count = (SELECT MAX(leader_count) as max from group_leader) and group_id=$groupId and course_id=$courseId");
                    ?>
                        <legend>Group Members</legend>
                        <table class="table table-hover table-bordered" id="sampleTable">
                            <thead>
                                <tr>
                                    <td>Sr No</td>
                                    <td>You Members Name</td>
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                $result = $db->getAllRecords("SELECT u.user_id, u.first_name, u.last_name, u.email_id from users as u WHERE u.user_id IN(select student_id from group_leader WHERE course_id=$courseId and group_id=$groupId) ORDER by u.first_name ASC");

                                if (count($result) > 0) :
                                    foreach ($result as $key => $value) :
                                ?>
                                        <tr>
                                            <td><?= $key + 1 ?></td>
                                            <td><?= $value['first_name'] ?> <?= $value['last_name'] ?> (<?= $value['email_id'] ?>)</td>
                                        </tr>
                                    <?php
                                    endforeach;
                                else : ?>
                                    <tr>
                                        <td colspan="3">No records found</td>
                                    </tr>
                                <?php endif; ?>
                            </tbody>
                        </table>
                        <?php
                        $selectGroupLeaderWholeTable = $db->getSingleRecord("SELECT COUNT(id) as count FROM group_leader WHERE course_id=$courseId AND group_id=$groupId and leader_selection=1");
                        if ((int)$selectGroupLeaderWholeTable['count'] === (int)$totalCount) : ?>
                            <legend>Your Group Leader Details</legend>
                            <table class="table table-hover table-bordered" id="sampleTable">
                                <thead>
                                    <tr>
                                        <th>First Name</th>
                                        <th>Last Name</th>
                                        <th>Email Id</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr>
                                        <td><?= $selectLeader['first_name'] ?></td>
                                        <td><?= $selectLeader['last_name'] ?></td>
                                        <td><?= $selectLeader['email_id'] ?></td>
                                    </tr>
                                </tbody>
                            </table>
                        <?php
                        endif;
                        if ($selectLeader['user_id'] === $LoggedIn && (int)$selectGroupLeaderWholeTable['count'] === (int)$totalCount) : ?>
                            <legend>Upload your Assignment</legend>
                            <div class="row">
                                <div class="col-lg-6 col-lg-offset-2">
                                    <div class="well bs-component">
                                        <form class="form-horizontal" action="<?= $_SERVER['PHP_SELF'] ?>" method="post" enctype="multipart/form-data">
                                            <input type="hidden" name="courseId" value="<?= base64_encode($courseId) ?>">
                                            <input type="hidden" name="groupId" value="<?= base64_encode($groupId) ?>">
                                            <div class="form-group">
                                                <label class="col-lg-4 control-label" for="select-group">Upload Assignment</label>
                                                <div class="col-lg-8">
                                                    <input type="file" class="form-control" name="uploadFile" id="uploadFile" required accept=".pdf">
                                                </div>
                                            </div>
                                            <div class="form-group">
                                                <div class="col-lg-8 col-lg-offset-4">
                                                    <button class="btn btn-success" name="uploadAssignment" type="submit">upload</button>
                                                    <button class="btn btn-danger" type="reset">Cancel</button>
                                                </div>
                                            </div>
                                        </form>
                                    </div>

                                </div>
                            </div>
                            <?php
                            $result = $db->getAllRecords("SELECT * FROM group_assignment WHERE course_id=$courseId and group_id=$groupId and student_id=$LoggedIn ORDER BY created_dt DESC");

                            if (count($result) > 0) :
                            ?>
                                <legend>Uploaded Assignments</legend>
                                <table class="table table-hover table-bordered" id="sampleTable">
                                    <thead>
                                        <tr>
                                            <th>Sri No</th>
                                            <th>File Name</th>
                                            <th>Created Date</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php
                                        $result = $db->getAllRecords("SELECT * FROM group_assignment WHERE course_id=$courseId and group_id=$groupId and student_id=$LoggedIn ORDER BY created_dt DESC");
                                        foreach ($result as $key => $value) :
                                        ?>
                                            <tr>
                                                <td><?= $key + 1 ?></td>
                                                <td><a href="./uploads/leader-assignment/<?= $value['upload_file'] ?>" target="_blank"><i class="fa fa-file" aria-hidden="true"></i> <?= $value['upload_file'] ?></a></td>
                                                <td><?= $value['created_dt'] ?></td>
                                            </tr>
                                        <?php
                                        endforeach;
                                        ?>
                                    </tbody>
                                </table>
                            <?php endif; ?>
                        <?php endif; ?>
                    <?php endif; ?>
                </div>
            </div>
        </div>
<?php
    endif;
endif;
require_once './include-footer.php';
?>
<?php
require_once './include-header.php';
include_once './_db.php';

if ($_SESSION['type'] != ADMIN) {
    redirect('system-dashboard.php');
}
if (isset($_POST['update'])) {
    $userEditId = base64_decode($_POST['updateId']);
    $term = $_POST['term'];
    $courseName = htmlentities($_POST['courseName']);
    $startDate = $_POST['startDate'];
    $endDate = $_POST['endDate'];
    $currentTime = getCurrentTime();
    $check = $db->updateData("UPDATE course SET term = '$term', course_name = '$courseName', start_date = '$startDate', end_date = '$endDate', modified_dt = '$currentTime' WHERE course_id = $userEditId");

    if ($check) {
        redirect('admin-add-course.php?status=success&message=' . urlencode('Course updated successfully'));
    } else {
        redirect('admin-add-course.php?status=danger&message=' . urlencode('Database Problem'));
    }
} elseif (isset($_GET['edit-id'])) {
    $courseId = base64_decode($_GET['edit-id']);
    $courseData = $db->getSingleRecord("SELECT term, course_name, start_date, end_date FROM course where course_id=$courseId");
    $term = $courseData['term'];
    $courseName = $courseData['course_name'];
    $startDate = $courseData['start_date'];
    $endDate = $courseData['end_date'];
} elseif (isset($_GET['active-course-id'])) {
    $courseId = base64_decode($_GET['active-course-id']);
    $currentTime = getCurrentTime();
    $activeStatus = ACTIVE;
    $inactive = INACTIVE;;
    $db->updateData("UPDATE course SET status=$inactive");
    $check = $db->updateData("UPDATE course SET status=$activeStatus, modified_dt = '$currentTime' WHERE course_id=$courseId");
    if ($check) {
        redirect('admin-add-course.php', 'status=success&message=' . urlencode('Course status updated successfully'));
    } else {
        redirect('admin-add-course.php', 'status=danger&message=' . urlencode('Database Problem'));
    }
} elseif (isset($_POST['create'])) {
    $term = $_POST['term'];
    $courseName = htmlentities($_POST['courseName']);
    $startDate = $_POST['startDate'];
    $endDate = $_POST['endDate'];
    $status = INACTIVE;
    $currentTime = getCurrentTime();
    $result = $db->getSingleRecord("SELECT course_name FROM course where course_name='$courseName' and start_date='$startDate' and end_date='$endDate'");

    if (count($result) === 0) {
        $check = $db->insertData("INSERT INTO course VALUES (null,'$term','$courseName',$status,'$startDate','$endDate','$currentTime',null)");
        if ($check) {
            redirect('admin-add-course.php', 'status=success&message=' . urlencode('Course created successfully & You have to active it first.'));
        } else {
            redirect('admin-add-course.php', 'status=danger&message=' . urlencode('Database Problem'));
        }
    } else {
        redirect('admin-add-course.php', 'status=danger&message=' . urlencode('Course already exist'));
    }
}
$pageName = breadCrumbs('Add Course', '<i class="fa fa-book" aria-hidden="true"></i>');
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
                                <label class="col-lg-4 control-label" for="term">Term</label>
                                <div class="col-lg-8">
                                    <select class="form-control" id="term" name="term" required>
                                        <option value="">Select Term</option>
                                        <?php
                                        $result = $db->getAllRecords("SELECT * FROM course_term ORDER BY id ASC");
                                        foreach ($result as $key => $value) :
                                        ?>
                                            <option value="<?= $value['id'] ?>" <?= getSelectOption($value['id'], $term ?? '') ?>><?= $value['term_name'] ?></option>

                                        <?php endforeach ?>
                                    </select>
                                </div>
                            </div>
                            <div class=" form-group">
                                <label class="col-lg-4 control-label" for="course-name">Course Name</label>
                                <div class="col-lg-8">
                                    <input class="form-control" name="courseName" id="course-name" type="text" placeholder="Enter Course Name" required value="<?= $courseName ?? ''  ?>" />
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="col-lg-4 control-label" for="start-date">Start Date</label>
                                <div class="col-lg-8">
                                    <input class="form-control demoDate" id="start-date" name="startDate" type="text" placeholder="Select Start Date" required value="<?= $startDate ?? ''  ?>" />
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="col-lg-4 control-label" for="end-date">End Date</label>
                                <div class="col-lg-8">
                                    <input class="form-control demoDate" id="end-date" name="endDate" type="text" placeholder="Select End Date" required value="<?= $endDate ?? ''  ?>" />
                                </div>
                            </div>
                            <div class="form-group">
                                <div class="col-lg-8 col-lg-offset-4">
                                    <?php if (isset($courseId)) : ?>
                                        <input type="hidden" name="updateId" value="<?= base64_encode($courseId); ?>">
                                        <button class="btn btn-success" name="update" type="submit">Update</button>
                                    <?php else : ?>
                                        <button class="btn btn-success" name="create" type="submit">Create</button>
                                        <button class="btn btn-danger" type="reset">Cancel</button>
                                    <?php endif; ?>

                                </div>
                            </div>
                        </form>
                    </div>
                </div>
                <div class="col-lg-12">
                    <legend>All Courses</legend>
                    <table class="table table-hover table-bordered" id="sampleTable">
                        <thead>
                            <tr>
                                <th>Sri No</th>
                                <th>Term</th>
                                <th>Course Name</th>
                                <th>Start Date</th>
                                <th>End Date</th>
                                <th>Status</th>
                                <th>Created Date</th>
                                <th>Modified Date</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            $result = $db->getAllRecords("SELECT c.*, ct.term_name FROM course as c INNER JOIN course_term as ct ON ct.id=c.term ORDER BY c.created_dt DESC");
                            if (count($result) > 0) :
                                foreach ($result as $key => $value) :
                            ?>
                                    <tr>
                                        <td><?= $key + 1 ?></td>
                                        <td><?= $value['term_name'] ?></td>
                                        <td><?= $value['course_name'] ?></td>
                                        <td><?= $value['start_date'] ?></td>
                                        <td><?= $value['end_date'] ?></td>
                                        <td><?= $value['status'] ? 'Active' : 'Inactive'; ?></td>
                                        <td><?= $value['created_dt'] ?></td>
                                        <td><?= $value['modified_dt'] ?? 'N/A'; ?></td>
                                        <td>
                                            <a class="btn btn-success btn-sm" onclick="return confirm('Are you sure you want to edit <?= $value['course_name'] ?> course ?')" href="admin-add-course.php?edit-id=<?= base64_encode($value['course_id']) ?>">Edit</a>
                                            <a class="btn btn-info btn-sm" onclick="return confirm('Are you sure you want to <?= $value['status'] ?  'inactive' : 'active' ?> <?= $value['course_name'] ?> course ?')" href='admin-add-course.php?active-course-id=<?= base64_encode($value['course_id']); ?>'><?= $value['status'] ? 'Inactive' : 'Active' ?></a>
                                        </td>
                                    </tr>
                                <?php
                                endforeach;
                            else : ?>
                                <tr>
                                    <td colspan="9">No records found</td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>


    <?php require_once './include-footer.php' ?>
    <script type="text/javascript" src="js/plugins/bootstrap-datepicker.min.js"></script>
    <script type="text/javascript" src="js/plugins/select2.min.js"></script>
    <script type="text/javascript" src="js/plugins/bootstrap-datepicker.min.js"></script>
    <script type="text/javascript">
        $(' .demoDate').datepicker({
            format: "yyyy-mm-dd",
            autoclose: true,
            todayHighlight: true
        });
        $('#demoSelect').select2();
    </script>
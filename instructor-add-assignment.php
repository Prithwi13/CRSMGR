<?php
require_once './include-header.php';
include_once './_db.php';
$LoggedIn = $_SESSION['userId'];
if ($_SESSION['type'] != INSTRUCTOR) {
    header('location:system-dashboard.php');
}

if (isset($_POST['add'])) {
    $title = htmlentities($_POST['title']);
    $courseId = $_POST['courseId'];
    $weight = $_POST['weight'];
    $maxMarks = $_POST['maxMarks'];
    $dueDate = $_POST['dueDate'];
    $workType = $_POST['workType'];
    $peerReview = $_POST['peerReview'];

    $uploadedFile = $_FILES['uploadedFile'];
    $fileName = $uploadedFile['name'];
    $fileTmpName = $uploadedFile['tmp_name'];
    $fileSize = $uploadedFile['size'];
    $fileError = $uploadedFile['error'];
    $fileType = $uploadedFile['type'];
    $fileExt = explode('.', $fileName);
    $fileActualExt = strtolower(end($fileExt));

    if ($fileError === 0) {
        $fileNameNew = $title . '.' . $fileActualExt;
        $fileDestination = './uploads/assignments/' . $fileNameNew;
        if (move_uploaded_file($fileTmpName, $fileDestination)) {
            $currentTime = getCurrentTime();
            $status = ACTIVE;
            $check = $db->insertData("INSERT INTO assignment values(null, $courseId, '$title', $weight, $maxMarks,'$dueDate','$fileNameNew', '$workType', '$peerReview', $status, $LoggedIn, '$currentTime', null, null)");
            if ($check) {
                redirect('instructor-add-assignment.php', "status=success&message=" . urlencode('Assignment added successfully'));
            } else {
                redirect('instructor-add-assignment.php', "status=danger&message=" . urlencode('Database Problem'));
            }
        } else {
            redirect('instructor-add-assignment.php', "status=danger&message=" . urlencode('File not uploaded'));
        }
    } else {
        redirect('instructor-add-assignment.php', "status=danger&message=" . urlencode('Something went wrong'));
    }
} else if (isset($_GET['delete-id'])) {
    $id = base64_decode($_GET['delete-id']);
    $getFile = $db->getSingleRecord("SELECT upload_file FROM assignment WHERE id=$id");
    $path    = './uploads/assignments/';
    $files = scandir($path);
    $files = array_diff(scandir($path), array('.', '..'));
    if (in_array($getFile['upload_file'], $files)) {
        $check = $db->deleteData("DELETE FROM assignment WHERE id=$id");
        if ($check) {
            if (unlink($path . '/' . $getFile['upload_file'])) {
                redirect('instructor-add-assignment.php', "status=success&message=" . urlencode('Assignment deleted successfully'));
            }
        } else {
            redirect('instructor-add-assignment.php', "status=danger&message=" . urlencode('Database Problem'));
        }
    }
}

$pageName = breadCrumbs('Add Assignment Notes', '<i class="fa fa-file-text-o" aria-hidden="true"></i>');
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
<?php else : ?>
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <legend><?= $pageName ?></legend>
                <div class="row">
                    <div class="col-lg-6 col-lg-offset-2">
                        <?php require_once './include-message.php'; ?>
                        <div class="well bs-component">
                            <form class="form-horizontal" action="<?= $_SERVER['PHP_SELF'] ?>" method="post" enctype="multipart/form-data">
                                <input type="hidden" name="courseId" value="<?= $getActiveCourse['course_id'] ?>">
                                <div class="form-group">
                                    <label class="col-lg-4 control-label" for="title">Title</label>
                                    <div class="col-lg-8">
                                        <input class="form-control" name="title" id="title" type="text" placeholder="Enter Title" required>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label class="col-lg-4 control-label" for="weight">Weight</label>
                                    <div class="col-lg-8">
                                        <input class="form-control" name="weight" id="weight" type="number" placeholder="Enter Weight" required min="1">
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label class="col-lg-4 control-label" for="maxMarks">Max Marks</label>
                                    <div class="col-lg-8">
                                        <input class="form-control" name="maxMarks" id="max-marks" type="number" placeholder="Enter Max Marks" required min="1">
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label class="col-lg-4 control-label" for="due-date">Due Date</label>
                                    <div class="col-lg-8">
                                        <input class="form-control demoDate" id="due-date" name="dueDate" type="text" placeholder="Select Due Date" required />
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label class="col-lg-4 control-label" for="uploaded-file"> Uploaded file</label>
                                    <div class="col-lg-8">
                                        <input type="file" class="form-control" name="uploadedFile" id="uploaded-file" required accept=".pdf" />
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label class="col-lg-4 control-label" for="work-type">Work Type</label>
                                    <div class="col-lg-8">
                                        <select class="form-control" id="work-type" name="workType" required>
                                            <option value="">Select Work Type</option>
                                            <option value="Individual">Individual</option>
                                            <option value="Group">Group</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label class="col-lg-4 control-label" for="peer-review">Peer Review</label>
                                    <div class="col-lg-8">
                                        <select class="form-control" id="peer-review" name="peerReview" required>
                                            <option value="">Select Peer Review</option>
                                            <option value="Not Required">Not Required</option>
                                            <option value="Required">Required</option>
                                        </select>
                                    </div>
                                </div>

                                <div class="form-group">
                                    <div class="col-lg-8 col-lg-offset-4">
                                        <button class="btn btn-success btn-sm" name="add" type="submit">Add</button>
                                        <button class="btn btn-danger btn-sm" type="reset">Cancel</button>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>
                    <?php
                    $courseId = $getActiveCourse['course_id'];
                    $result = $db->getAllRecords("SELECT * FROM  assignment WHERE course_id=$courseId ORDER BY created_dt DESC");

                    if (count($result) > 0) :
                    ?>
                        <div class="col-lg-12">
                            <legend>All Assignments</legend>
                            <table class="table table-hover table-bordered" id="sampleTable">
                                <thead>
                                    <tr>
                                        <th>Sri No</th>
                                        <th>Title</th>
                                        <th>Weight</th>
                                        <th>Max Marks</th>
                                        <th>Due Date</th>
                                        <th>Upload File</th>
                                        <th>Work type</th>
                                        <th>Peer Review</th>
                                        <th>Created Date</th>
                                        <th>Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php
                                    foreach ($result as $key => $value) :
                                    ?>
                                        <tr>
                                            <td><?= $key + 1 ?></td>
                                            <td><?= $value['title'] ?></td>
                                            <td><?= $value['weight'] ?></td>
                                            <td><?= $value['max_marks'] ?></td>
                                            <td><?= $value['due_date'] ?></td>
                                            <td><a href="./uploads/assignments/<?= $value['upload_file'] ?>" target="_blank"><i class="fa fa-file" aria-hidden="true"></i> <?= $value['upload_file'] ?></a></td>
                                            <td><?= $value['work_type'] ?></td>
                                            <td><?= $value['peer_review'] ?></td>
                                            <td><?= $value['created_dt'] ?></td>
                                            <td><a class="btn btn-danger btn-sm" onclick="return confirm('Are you sure you want to delete <?= $value['title'] ?> ?')" href="instructor-add-assignment.php?delete-id=<?= base64_encode($value['id']) ?>">Delete</a></td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
<?php
endif;
require_once './include-footer.php';
?>
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
<?php
require_once './include-header.php';
include_once './_db.php';
$LoggedIn = $_SESSION['userId'];
if ($_SESSION['type'] != INSTRUCTOR) {
    header('location:system-dashboard.php');
}

if (isset($_POST['add'])) {
    $title = $_POST['title'];
    $courseId = $_POST['courseId'];

    $courseFile = $_FILES['courseFile'];
    $fileName = $courseFile['name'];
    $fileTmpName = $courseFile['tmp_name'];
    $fileSize = $courseFile['size'];
    $fileError = $courseFile['error'];
    $fileType = $courseFile['type'];
    $fileExt = explode('.', $fileName);
    $fileActualExt = strtolower(end($fileExt));

    if ($fileError === 0) {
        $fileNameNew = $title . '.' . $fileActualExt;
        $fileDestination = './uploads/lecture-notes/' . $fileNameNew;
        if (move_uploaded_file($fileTmpName, $fileDestination)) {
            $currentTime = getCurrentTime();
            $status = ACTIVE;
            $check = $db->insertData("INSERT INTO lectures_notes values(null, '$title', '$fileNameNew',$courseId ,$status, $LoggedIn, '$currentTime')");
            if ($check) {
                redirect('instructor-add-lecture-notes.php', "status=success&message=" . urlencode('Lecture notes added successfully'));
            } else {
                redirect('instructor-add-lecture-notes.php', "status=danger&message=" . urlencode('Database Problem'));
            }
        } else {
            redirect('instructor-add-lecture-notes.php', "status=danger&message=" . urlencode('File not uploaded'));
        }
    } else {
        redirect('instructor-add-lecture-notes.php', "status=danger&message=" . urlencode('Something went wrong'));
    }
} else if (isset($_GET['delete-id'])) {
    $id = base64_decode($_GET['delete-id']);
    $getFile = $db->getSingleRecord("SELECT lecture_file FROM lectures_notes WHERE id=$id");
    $path    = './uploads/lecture-notes/';
    $files = scandir($path);
    $files = array_diff(scandir($path), array('.', '..'));

    if (in_array($getFile['lecture_file'], $files)) {
        $check = $db->deleteData("DELETE FROM lectures_notes WHERE id=$id");
        if ($check) {
            unlink($path . '/' . $getFile['lecture_file']);
            redirect('instructor-add-lecture-notes.php', "status=success&message=" . urlencode('Lecture notes deleted successfully'));
        } else {
            redirect('instructor-add-lecture-notes.php', "status=danger&message=" . urlencode('Database Problem'));
        }
    }
}

$pageName = breadCrumbs('Add Lecture Notes', '<i class="fa fa-file-text-o" aria-hidden="true"></i>');
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
                                    <label class="col-lg-4 control-label" for="course-file">Course file</label>
                                    <div class="col-lg-8">
                                        <input type="file" class="form-control" name="courseFile" id="course-file" required accept=".pdf" />
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
                    $result = $db->getAllRecords("SELECT * FROM lectures_notes WHERE course_id=$courseId ORDER BY created_dt DESC");

                    if (count($result) > 0) :
                    ?>
                        <div class="col-lg-12">
                            <legend>All Lecture Notes</legend>
                            <table class="table table-hover table-bordered" id="sampleTable">
                                <thead>
                                    <tr>
                                        <th>Sri No</th>
                                        <th>Title</th>
                                        <th>Created Date</th>
                                        <th>File Name</th>
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
                                            <td><a href="./uploads/lecture-notes/<?= $value['lecture_file'] ?>" target="_blank"><i class="fa fa-file" aria-hidden="true"></i> <?= $value['lecture_file'] ?></a></td>
                                            <td><?= $value['created_dt'] ?></td>
                                            <td><a class="btn btn-danger btn-sm" onclick="return confirm('Are you sure you want to delete <?= $value['title'] ?> ?')" href="instructor-add-lecture-notes.php?delete-id=<?= base64_encode($value['id']) ?>">Delete</a></td>
                                        </tr>
                                    <?php
                                    endforeach;
                                    ?>
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
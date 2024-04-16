<?php
require_once './include-header.php';
require_once './_db.php';

if ($_SESSION['type'] != INSTRUCTOR) {
    redirect('system-dashboard.php');
}

if (isset($_POST['create'])) {
    if (!$_POST['studentPerGroup']) {
        redirect('instructor-add-student-groups.php', 'status=danger&message=' . urlencode('Please select valid groups limit'));
    }
    $courseId = $_POST['courseId'];
    $count = $db->getSingleRecord("SELECT COUNT(id) as count FROM student_group WHERE course_id=$courseId");
    if ($count['count'] > 0) {
        redirect('instructor-add-student-groups.php', 'status=danger&message=' . urlencode('Student groups already created for this course'));
    } else {
        $studentPerGroup = $_POST['studentPerGroup'];
        $queryString = '';
        $loggedIn = $_SESSION['userId'];
        $currentTime = getCurrentTime();
        $status = ACTIVE;
        $groups_limit = $_POST['groups_limit'];
        $totalCount = 0;

        for ($i = 1; $i <= $studentPerGroup; $i++) {
            $groupName = str_replace(' ', '_', $_POST['courseName']) . '_Group_' . $i;
            $queryString .= "INSERT INTO student_group VALUES (null,'$groupName', $courseId, $groups_limit, $totalCount,$status, $loggedIn, '$currentTime');";
        }
        $check = $db->insertMultipleData("$queryString");
        if ($check) {
            redirect('instructor-add-student-groups.php', 'status=success&message=' . urlencode('Student groups created successfully'));
        } else {
            redirect('instructor-add-student-groups.php', 'status=danger&message=' . urlencode('Database Problem'));
        }
    }
} else if (isset($_GET['deleteQuery'])) {
    $courseId = $_GET['courseId'];
    $check = $db->deleteData("DELETE FROM student_group WHERE course_id=$courseId");
    if ($check) {
        redirect('instructor-add-student-groups.php', 'status=success&message=' . urlencode('Student groups deleted successfully'));
    } else {
        redirect('instructor-add-student-groups.php', 'status=danger&message=' . urlencode('Database Problem'));
    }
}

$pageName = breadCrumbs('Create Student Groups', '<i class="fa fa-users" aria-hidden="true"></i>');
$activeStatus = ACTIVE;
$getData = $db->getSingleRecord("SELECT c.*, ct.term_name FROM course as c INNER JOIN course_term as ct ON ct.id=c.term  WHERE c.status=$activeStatus ORDER BY c.created_dt DESC");

if (count($getData) === 0) :
?>
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                There are no active course
            </div>
        </div>
    </div>
    <?php else :
    $courseId = $getData['course_id'];
    $studentType = STUDENT;
    $studentCount = $db->getSingleRecord("SELECT COUNT(user_id) as count FROM users as u INNER JOIN student_enroll_course  as sec ON sec.student_id=u.student_id WHERE u.type=$studentType AND sec.course_id=$courseId");
    if ($studentCount['count'] > 0) :
    ?>
        <div class="row">
            <div class="col-md-12">
                <div class="card">
                    <legend><?= $pageName ?></legend>
                    <div class="row">
                        <div class="col-lg-6 col-lg-offset-2">
                            <?php require_once './include-message.php'; ?>
                            <div class="well bs-component">
                                <table class="table table-bordered">
                                    <tr>
                                        <td><strong>Term</strong></td>
                                        <td><?= $getData['term_name'] ?></td>
                                    </tr>
                                    <tr>
                                        <td><strong>Course Name</strong></td>
                                        <td><?= $getData['course_name'] ?></td>
                                    </tr>
                                    <tr>
                                        <td><strong>Start Date</strong></td>
                                        <td><?= $getData['start_date'] ?></td>
                                    </tr>
                                    <tr>
                                        <td><strong>End Date</strong></td>
                                        <td><?= $getData['end_date'] ?></td>
                                    </tr>
                                    <tr>
                                        <td><strong>Total Students</strong></td>
                                        <td id="total-student"> <?= $studentCount['count'] ?></td>
                                    </tr>
                                </table>
                                <form onsubmit="return confirm('Are you sure you want to create student groups ?')" class="form-horizontal" action="<?= $_SERVER['PHP_SELF']; ?>" method="post">
                                    <input type="hidden" name="courseId" value="<?= $getData['course_id']; ?>">
                                    <input type="hidden" id="student-per-group" name="studentPerGroup" value="">
                                    <input type="hidden" id="course-name" name="courseName" value="<?= $getData['course_name']; ?>">
                                    <div class=" form-group">
                                        <label class="col-lg-3 control-label" for="groups_limit">Groups</label>
                                        <div class="col-lg-8">
                                            <select class="form-control" id="groups_limit" name="groups_limit" required aria-placeholder="Select groups_limit">
                                                <option value="">Select Groups</option>
                                                <?php for ($i = 1; $i <= 25; $i++) : ?>
                                                    <option value="<?= $i; ?>"><?= $i ?></option>
                                                <?php endfor; ?>
                                            </select>
                                        </div>
                                    </div>
                                    <div class=" form-group">
                                        <label class="col-lg-3 control-label" for="groups_limit">Student in Per Group</label>
                                        <div class="col-lg-8">
                                            <div class="form-control" id="show-student-per-group">N/A</div>
                                        </div>
                                    </div>

                                    <div class="form-group" id="submit-buttons">
                                        <div class="col-lg-8 col-lg-offset-2">
                                            <button class="btn btn-success btn-sm" name="create" type="submit">Create</button>
                                            <button class="btn btn-danger btn-sm" type="reset">Cancel</button>
                                        </div>
                                    </div>

                                </form>
                            </div>
                        </div>
                        <?php
                        $result = $db->getAllRecords("SELECT * FROM student_group WHERE course_id=$courseId ORDER BY created_dt ASC");
                        if (count($result) > 0) :
                        ?>
                            <div class="col-lg-12">
                                <a href="instructor-add-student-groups.php?courseId=<?= $courseId; ?>&deleteQuery=yes" class="btn btn-danger pull-right btn-sm" onclick="return confirm('Are you sure you want to delete all groups ?')">Delete All Group</a>
                                <legend>All Groups</legend>
                                <table class="table table-hover table-bordered" id="sampleTable">
                                    <thead>
                                        <tr>
                                            <th>Sri No</th>

                                            <th>Group Name</th>
                                            <th>Group Limit</th>
                                            <th>Start Date</th>
                                            <th>Status</th>

                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php

                                        foreach ($result as $key => $value) :
                                        ?>
                                            <tr>
                                                <td><?= $key + 1 ?></td>
                                                <td><?= $value['group_name'] ?></td>
                                                <td><?= $value['groups_limit'] ?></td>
                                                <td><?= $value['created_dt'] ?></td>
                                                <td><?= getActiveOrInactive($value['status']); ?></td>

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
    <?php else : ?>
        <div class="row">
            <div class="col-md-12">
                <div class="card">
                    There are no students enrolled for this course
                </div>
            </div>
        </div><?php
            endif;
        endif;
        require_once './include-footer.php' ?>
<script type="text/javascript" src="js/plugins/bootstrap-datepicker.min.js"></script>
<script type="text/javascript" src="js/plugins/select2.min.js"></script>
<script type="text/javascript" src="js/plugins/bootstrap-datepicker.min.js"></script>
<script type="text/javascript">
    $('#groups_limit').select2();
    $('#groups_limit').on('change', function(e) {
        if ($(this)?.val()) {
            var totalStudents = Number($('#total-student').text());
            var groups_limit = Number($(this)?.val());
            var perGroup = totalStudents / groups_limit;
            perGroup = Math.round(perGroup);
            if (groups_limit <= totalStudents) {
                $('#student-per-group').val(perGroup);
                perGroup = `${perGroup} (${totalStudents} / ${groups_limit})`;
                $('#show-student-per-group').text(perGroup);
            } else {
                $('#show-student-per-group').text('N/A');
                $('#groups_limit')?.val(1)?.select2()
                alert('Please select valid groups limit');
                $('#student-per-group').val('');
            }
        } else {
            $('#show-student-per-group').text('N/A');
            $('#student-per-group').val('');
        }
    });
</script>
<?php
require_once './include-header.php';
include_once './_db.php';
$LoggedIn = $_SESSION['userId'];
if ($_SESSION['type'] != INSTRUCTOR) {
    header('location:system-dashboard.php');
}
$pageName = breadCrumbs('Current Course Students', '<i class="fa fa-address-card" aria-hidden="true"></i>');
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
                <a href="./instructor-upload-csv.php" class="btn btn-primary pull-right btn-sm">Add Students</a>
                <legend>Current Course</legend>
                <div class="row">
                    <div class="col-lg-12">
                        <div class="well bs-component">
                            <table class="table table-bordered">
                                <tr>
                                    <td><strong>Term</strong></td>
                                    <td><?= $getActiveCourse['term_name'] ?></td>
                                </tr>
                                <tr>
                                    <td><strong>Course Name</strong></td>
                                    <td><?= $getActiveCourse['course_name'] ?></td>
                                </tr>
                                <tr>
                                    <td><strong>Start Date</strong></td>
                                    <td><?= $getActiveCourse['start_date'] ?></td>
                                </tr>
                                <tr>
                                    <td><strong>End Date</strong></td>
                                    <td><?= $getActiveCourse['end_date'] ?></td>
                                </tr>
                            </table>
                            <?php
                            $courseId = $getActiveCourse['course_id'];
                            $type = STUDENT;
                            $allStudents = $db->getAllRecords("SELECT * FROM users as u INNER JOIN student_enroll_course as sec ON sec.student_id=u.student_id WHERE sec.course_id=$courseId and u.type=$type ORDER BY u.first_name ASC");

                            if (count($allStudents) > 0) :

                            ?>
                                <legend>Students</legend>
                                <table class="table table-striped table-hover table-bordered">
                                    <thead>
                                        <tr>
                                            <th>Sri</th>
                                            <th>Student Id</th>
                                            <th>Last Name</th>
                                            <th>First Name</th>
                                            <th>Email Id</th>
                                            <th>Status</th>
                                            <th>Created Date</th>
                                            <th>Modified Date</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php foreach ($allStudents as $key => $value) : ?>
                                            <tr>
                                                <td><?= $key + 1 ?></td>
                                                <td><?= $value['student_id'] ?></td>
                                                <td><?= $value['last_name'] ?></td>
                                                <td><?= $value['first_name'] ?></td>
                                                <td><?= $value['email_id'] ?></td>
                                                <td><?= getActiveOrInactive($value['status']) ?></td>
                                                <td><?= $value['created_dt'] ?></td>
                                                <td><?= $value['modified_dt'] ?? 'N/A'; ?></td>
                                            </tr>
                                        <?php
                                        endforeach;
                                        ?>
                                    </tbody>
                                </table>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>


<?php

endif;
require_once './include-footer.php';
?>
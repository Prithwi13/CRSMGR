<?php
require_once('./include-header.php');
include_once './_db.php';
$pageName = breadCrumbs('Dashboard', '<i class="fa fa-dashboard"></i>');
?>

<?php if ((int)$_SESSION['type'] == ADMIN) :
  $admin = ADMIN;
  $student = STUDENT;
  $totalUsers = $db->getSingleRecord("SELECT COUNT(*) as count FROM users where type !=$admin and type !=$student");
  $totalCourse = $db->getSingleRecord("SELECT COUNT(*) as count FROM course");
?>
  <div class="row">
    <div class="col-md-12">
      <div class="card">
        <h3 class="card-title"><?= $pageName ?></h3>

        <div class="row">
          <a href="admin-add-users.php">
            <div class="col-md-3">
              <div class="widget-small primary"><i class="icon fa fa-users fa-3x"></i>
                <div class="info">
                  <h4>Total Users</h4>
                  <p><b><?= $totalUsers['count'] ?></b></p>
                </div>
              </div>
            </div>
          </a>
          <a href="admin-add-course.php">
            <div class="col-md-3">
              <div class="widget-small info"><i class="icon fa fa-book fa-3x"></i>
                <div class="info">
                  <h4>Total Courses</h4>
                  <p><b><?= $totalCourse['count'] ?></b></p>
                </div>
              </div>
            </div>
          </a>
        </div>
      </div>
    </div>
  </div>
<?php elseif ((int) $_SESSION['type'] == INSTRUCTOR) :
  $activeStatus = ACTIVE;
  $type = STUDENT;

  $getActiveCourse = $db->getSingleRecord("SELECT c.*, ct.term_name FROM course as c INNER JOIN course_term as ct ON ct.id=c.term  WHERE c.status=$activeStatus ORDER BY c.created_dt DESC");
  $courseId = $getActiveCourse['course_id'];

  $totalStudent = $db->getSingleRecord("SELECT COUNT(*) as count FROM users as u INNER JOIN student_enroll_course as sec ON sec.student_id=u.student_id WHERE sec.course_id=$courseId and u.type=$type ORDER BY u.first_name ASC");
  $totalAssignment = $db->getSingleRecord("SELECT COUNT(*) as count FROM assignment WHERE course_id=$courseId");
  $totalLectureNotes = $db->getSingleRecord("SELECT COUNT(*) as count FROM lectures_notes WHERE course_id=$courseId");
  $totalGroups = $db->getSingleRecord("SELECT COUNT(*) as count FROM student_group WHERE course_id=$courseId");
?>
  <div class="row">
    <div class="col-md-12">
      <div class="card">
        <h3 class="card-title"><?= $pageName ?></h3>

        <div class="row">
          <a href="instructor-current-course-students.php">
            <div class="col-md-3">
              <div class="widget-small primary"><i class="icon fa fa-users fa-3x"></i>
                <div class="info">
                  <h5>Total Students</h5>
                  <p><b><?= $totalStudent['count'] ?></b></p>
                </div>
              </div>
            </div>
          </a>
          <a href="instructor-add-assignment.php">
            <div class="col-md-3">
              <div class="widget-small info"><i class="icon fa fa-file fa-3x"></i>
                <div class="info">
                  <h5>Total Assignment</h5>
                  <p><b><?= $totalAssignment['count'] ?></b></p>
                </div>
              </div>
            </div>
          </a>
          <a href="instructor-add-lecture-notes.php">
            <div class="col-md-3">
              <div class="widget-small warning"><i class="icon fa fa-file-text-o fa-3x"></i>
                <div class=" info">
                  <h5>Total Lecture Notes</h5>
                  <p><b><?= $totalLectureNotes['count'] ?></b></p>
                </div>
              </div>
            </div>
          </a>
          <a href="instructor-add-student-groups.php">
            <div class="col-md-3">
              <div class="widget-small info"><i class="icon fa fa-users fa-3x"></i>
                <div class="info">
                  <h5>Total Groups</h5>
                  <p><b><?= $totalGroups['count'] ?></b></p>
                </div>
              </div>
            </div>
          </a>

        </div>
      </div>
    </div>
  </div>
<?php elseif ((int) $_SESSION['type'] == STUDENT) :
  $activeStatus = ACTIVE;
  $getActiveCourse = $db->getSingleRecord("SELECT c.*, ct.term_name FROM course as c INNER JOIN course_term as ct ON ct.id=c.term  WHERE c.status=$activeStatus ORDER BY c.created_dt DESC");
  $courseId = $getActiveCourse['course_id'];
?>
  <div class="row">
    <div class="col-md-12">
      <div class="card">
        <h3 class="card-title"><?= $pageName ?></h3>

        <div class="row">
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
                </tr>
              </thead>
              <tbody>
                <?php
                $courseId = $getActiveCourse['course_id'];
                $result = $db->getAllRecords("SELECT * FROM  assignment WHERE course_id=$courseId ORDER BY created_dt DESC");

                if (count($result) > 0) :
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

            <legend>All Lecture Notes</legend>
            <table class="table table-hover table-bordered" id="sampleTable">
              <thead>
                <tr>
                  <th>Sri No</th>
                  <th>Title</th>
                  <th>File</th>
                  <th>Created Date</th>
                </tr>
              </thead>
              <tbody>
                <?php
                $courseId = $getActiveCourse['course_id'];
                $result = $db->getAllRecords("SELECT * FROM lectures_notes WHERE course_id=$courseId ORDER BY created_dt DESC");

                if (count($result) > 0) :
                  foreach ($result as $key => $value) :
                ?>
                    <tr>
                      <td><?= $key + 1 ?></td>
                      <td><?= $value['title'] ?></td>
                      <td><a href="./uploads/lecture-notes/<?= $value['lecture_file'] ?>" target="_blank"><i class="fa fa-file" aria-hidden="true"></i> <?= $value['lecture_file'] ?></a></td>
                      <td><?= $value['created_dt'] ?></td>
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
  </div>

  <?php
elseif ((int) $_SESSION['type'] == TA_Marker) :
  $status = ACTIVE;
  $result = $db->getAllRecords("SELECT sg.group_name,u.first_name, u.last_name,g.upload_file, g.created_dt FROM group_assignment as g INNER JOIN users as u ON u.user_id=g.student_id INNER JOIN student_group as sg ON sg.id=g.group_id INNER JOIN course as c ON c.course_id=g.course_id WHERE c.status=$status ORDER BY g.created_dt ASC");
  if (count($result) > 0) :
  ?>
    <div class="row">
      <div class="col-md-12">
        <div class="card">
          <h3 class="card-title"><?= $pageName ?></h3>
          <table class="table table-hover table-bordered" id="sampleTable">
            <thead>
              <tr>
                <th>Sri No</th>
                <th>leader Name</th>
                <th>Group Name</th>
                <th>File Name</th>
                <th>Created Date</th>
              </tr>
            </thead>
            <tbody>
              <?php
              foreach ($result as $key => $value) :
              ?>
                <tr>
                  <td><?= $key + 1 ?></td>
                  <td><?= $value['last_name'] ?> <?= $value['first_name'] ?></td>
                  <td><?= $value['group_name'] ?></td>
                  <td><a href="./uploads/leader-assignment/<?= $value['upload_file'] ?>" target="_blank"><i class="fa fa-file" aria-hidden="true"></i> <?= $value['upload_file'] ?></a></td>
                  <td><?= $value['created_dt'] ?></td>
                </tr>
              <?php
              endforeach;
              ?>
            </tbody>
          </table>
        </div>
      </div>
    </div>
  <?php
  endif;
elseif ((int) $_SESSION['type'] == TA_Lab_Tutor) :
  $active = ACTIVE;
  $courseId = $db->getSingleRecord("SELECT * FROM course WHERE status=$active");
  $courseId = $courseId['course_id'];
  $type = STUDENT;
  $allStudents = $db->getAllRecords("SELECT * FROM users as u INNER JOIN student_enroll_course as sec ON sec.student_id=u.student_id WHERE sec.course_id=$courseId and u.type=$type ORDER BY u.first_name ASC");
  if (count($allStudents) > 0) :
  ?>
    <div class="row">
      <div class="col-md-12">
        <div class="card">
          <h3 class="card-title"><?= $pageName ?></h3>
          <legend>All Students</legend>
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
              </tr>
            </thead>
            <tbody>
              <?php
              $result = $db->getAllRecords("SELECT * FROM  assignment WHERE course_id=$courseId ORDER BY created_dt DESC");

              if (count($result) > 0) :
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
          <legend>All Lecture Notes</legend>
          <table class="table table-hover table-bordered" id="sampleTable">
            <thead>
              <tr>
                <th>Sri No</th>
                <th>Title</th>
                <th>File</th>
                <th>Created Date</th>
              </tr>
            </thead>
            <tbody>
              <?php
              $result = $db->getAllRecords("SELECT * FROM lectures_notes WHERE course_id=$courseId ORDER BY created_dt DESC");

              if (count($result) > 0) :
                foreach ($result as $key => $value) :
              ?>
                  <tr>
                    <td><?= $key + 1 ?></td>
                    <td><?= $value['title'] ?></td>
                    <td><a href="./uploads/lecture-notes/<?= $value['lecture_file'] ?>" target="_blank"><i class="fa fa-file" aria-hidden="true"></i> <?= $value['lecture_file'] ?></a></td>
                    <td><?= $value['created_dt'] ?></td>
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
<?php endif;
endif;
require_once('./include-footer.php');
?>
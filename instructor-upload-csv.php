<?php
require_once './include-header.php';
include_once './_db.php';
$getData = $db->getSingleRecord("SELECT c.*, ct.term_name FROM course as c INNER JOIN course_term as ct ON ct.id=c.term  WHERE c.status=1 ORDER BY c.created_dt DESC");

if ($_SESSION['type'] != INSTRUCTOR) {
    header('location:system-dashboard.php');
    exit;
}

if (isset($_GET['type'])) {
    if ($_GET['type'] == 'delete') {
        $db->deleteData("TRUNCATE TABLE temp_users");
        -header('location:instructor-upload-csv.php?status=success&message=' . urlencode('All data deleted successfully'));
        exit;
    } else if ($_GET['type'] == 'submit') {
        $getTempData = $db->getAllRecords("SELECT * FROM temp_users ORDER BY user_id ASC");
        $insertDataString = '';
        $sessionDataStringOfCourse = [];
        $emailIdForInQuery = [];

        foreach ($getTempData as $key => $value) {
            $emailIdForInQuery[] = "'" . $value['email_id'] . "'";
            $firstName = $value['first_name'];
            $lastName = $value['last_name'];
            $emailId = $value['email_id'];
            $studentId = $value['student_id'];
            $courseId = $value['course_id'];
            $passwordHashed = password_hash('123456', PASSWORD_DEFAULT);
            $type = STUDENT;
            $LoggedIn = $_SESSION['userId'];
            $currentTime = getCurrentTime();
            $status = 1;

            $sessionDataStringOfCourse[] = [
                'studentId' => $studentId,
                'courseId' => $courseId,
            ];

            $insertDataString .= "INSERT into users(type, student_id, first_name, last_name, email_id, password, status, created_by, created_dt) values('$type', $studentId,'$firstName', '$lastName', '$emailId', '$passwordHashed',$status, '$LoggedIn', '$currentTime');";
        }
        $_SESSION['csv'] = $sessionDataStringOfCourse;
        $emailIdForInQuery = implode(",", $emailIdForInQuery);
        $checkExistence = $db->getSingleRecord("SELECT * FROM users where email_id IN ($emailIdForInQuery)");
        if (count($checkExistence)) {
            header('location:instructor-upload-csv.php?status=danger&message=' . urlencode("$emailId already exist in system") . "&stay=true");
            exit;
        }

        $checkingInsertion = $db->insertMultipleData($insertDataString);

        if ($checkingInsertion) {
            header('location:instructor-upload-csv.php?status=success&message=' . urlencode('Data submitted into system successfully') . '&courseQuery=true');
            exit;
        } else {
            header('location:instructor-upload-csv.php?status=danger&message=' . urlencode('Database Problem'));
            exit;
        }
    }
} else if (isset($_GET['courseQuery'])) {

    $insertDataOfCourse = '';
    $status = 1;
    $LoggedIn = $_SESSION['userId'];
    $currentTime = getCurrentTime();
    $insertDateStringOfCourse = '';

    foreach ($_SESSION['csv'] as $key => $value) {
        $studentId = $value['studentId'];
        $courseId = $value['courseId'];
        $insertDateStringOfCourse .= "INSERT INTO  student_enroll_course values(null, $studentId, $courseId, $status, '$LoggedIn' ,'$currentTime');";
    }
    $checkingInsertion = $db->insertMultipleData($insertDateStringOfCourse);
    if ($checkingInsertion) {
        header('location:instructor-upload-csv.php?status=success&message=' . urlencode('Data submitted into system successfully') . '&deleteQuery=true');
        exit;
    } else {
        header('location:instructor-upload-csv.php?status=danger&message=' . urlencode('Database Problem'));
        exit;
    }
} else if (isset($_GET['deleteQuery'])) {
    $db->deleteData("TRUNCATE TABLE temp_users");
}

if (isset($_POST["upload"])) {
    $courseId = $_POST['courseId'];
    $checkTempTable = $db->getAllRecords("SELECT * FROM temp_users");
    if (count($checkTempTable) > 0) {
        $db->deleteData("TRUNCATE TABLE temp_users");
    }
    if (isset($_FILES["csvFile"]) && $_FILES["csvFile"]["error"] == 0) {
        $allowed_ext = array("csv");
        $file_ext = pathinfo($_FILES["csvFile"]["name"], PATHINFO_EXTENSION);

        if (in_array($file_ext, $allowed_ext)) {
            $file = $_FILES["csvFile"]["tmp_name"];
            $handle = fopen($file, "r");
            if ($handle !== FALSE) {
                $i = 0;
                $sqlString = '';
                while (($data = fgetcsv($handle, 1000, ",")) !== FALSE) {
                    if ($i == 0) {
                        $i++;
                        continue;
                    }
                    $studentId =  $data[0] ? htmlentities($data[0]) : 'N/A';
                    $lastName =  $data[0] ? htmlentities($data[1]) : 'N/A';
                    $firstName =  $data[0] ? htmlentities($data[2]) : 'N/A';
                    $emailId =  $data[0] ? htmlentities($data[3]) : 'N/A';

                    $sqlString .= "INSERT into temp_users values(null ,'$firstName', '$lastName', '$emailId','$studentId' ,'$courseId');";
                }
                $check  = $db->insertMultipleData($sqlString);

                if ($check) {
                    header('location:instructor-upload-csv.php?status=success&message=' . urlencode('CSV uploaded successfully'));
                    exit;
                } else {
                    header('location:instructor-upload-csv.php?status=danger&message=' . urlencode('Database Problem'));
                    exit;
                }
                fclose($handle);
            } else {
                header('location:instructor-upload-csv.php?status=danger&message=' . urlencode('Failed to open the file'));
                exit;
            }
        } else {
            header('location:instructor-upload-csv.php?status=danger&message=' . urlencode('Sorry, only CSV files are allowed'));
            exit;
        }
    } else {
        header('location:instructor-upload-csv.php?status=danger&message=' . urlencode('Please select a file to upload.'));
        exit;
    }
}

$pageName = breadCrumbs('Upload CSV');

if (count($getData) === 0) :

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
                <a href="./users.csv" class="btn btn-primary pull-right" download="">Download Sample</a>
                <legend><?= $pageName ?></legend>

                <div class="row">
                    <div class="col-lg-6 col-lg-offset-2">
                        <?php require_once './include-message.php'; ?>
                        <div class="well bs-component">
                            <form class="form-horizontal" action="<?= $_SERVER['PHP_SELF'] ?>" method="post" enctype="multipart/form-data">
                                <input type="hidden" value="<?= $getData['course_id'] ?>" name="courseId">

                                <div class=" form-group">
                                    <label class="col-lg-4 control-label" for="course-name">Course Details</label>
                                    <div class="col-lg-8">

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
                                        </table>

                                    </div>
                                </div>

                                <div class="form-group">
                                    <label class="control-label col-md-4">CSV File</label>
                                    <div class="col-md-8">
                                        <input class="form-control" type="file" name="csvFile" accept=".csv" required>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <div class="col-lg-8 col-lg-offset-4">
                                        <button class="btn btn-success" name="upload" type="submit">Upload</button>
                                        <button class="btn btn-danger" type="reset">Cancel</button>
                                    </div>
                                </div>
                            </form>
                        </div>

                    </div>
                    <div class="col-lg-12">
                        <div class="pull-right">
                            <a href="./instructor-upload-csv.php?type=submit" class="btn btn-success" onclick="return confirm('Are you sure you want to submit users ?')">Submit</a>
                            <a href="./instructor-upload-csv.php?type=delete" class="btn btn-danger" onclick="return confirm('Are you sure you want to delete uploaded data ?')">Delete All</a>
                        </div>
                        <legend>Uploaded Users</legend>
                        <table class="table table-hover table-bordered" id="sampleTable">
                            <thead>
                                <tr>
                                    <th>Sri No</th>
                                    <th>Student Id</th>
                                    <th>Last Name</th>
                                    <th>First Name</th>
                                    <th>Email Id</th>

                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                $result = $db->getAllRecords("SELECT * FROM temp_users ORDER BY user_id ASC");

                                if (count($result) > 0) :
                                    foreach ($result as $key => $value) :
                                ?>
                                        <tr>
                                            <td><?= $key + 1 ?></td>
                                            <td><?= $value['student_id'] ?></td>
                                            <td><?= $value['last_name'] ?></td>
                                            <td><?= $value['first_name'] ?></td>
                                            <td><?= $value['email_id'] ?></td>
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



    <?php endif ?>
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
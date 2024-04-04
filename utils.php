<?php
// Header issue fixed
ob_start();

// all User types
define('ADMIN', 1);
define('MARKER', 2);
define("LABTUTOR", 3);
define('INSTRUCTOR', 4);
define("INSTRUCTOR_LABTUTOR", 5);
define("STUDENT", 6);
define('ACTIVE', 1);
define('INACTIVE', 0);

// PagesName
define('DASHBOARD', 'system-dashboard');
define('ADD_USERS', 'admin-add-users');
define('ADD_COURSE', 'admin-add-course');
define('INSTRUCTOR_UPLOAD_CSV', 'instructor-upload-csv');
define('INSTRUCTOR_CURRENT_COURSE_STUDENTS', 'instructor-current-course-students');
define('INSTRUCTOR_ADD_STUDENT_GROUPS', 'instructor-add-student-groups');
define('SYSTEM_MESSAGE', 'system-message');
define('SYSTEM_FAQ', 'system-faq');

function getCurrentTime(): string
{
    $now = new DateTime();
    $now->setTimezone(new DateTimezone('asia/calcutta'));
    $get_time = $now->format('Y-m-d H:i:s');
    return $get_time;
}

function getPostValues(): void
{
    echo "<pre>";
    print_r($_POST);
    exit;
}
function getLinkActiveClass(string $fileName = DASHBOARD)
{
    $url = basename($_SERVER['REQUEST_URI'], '?' . $_SERVER['QUERY_STRING']);
    $addressBarFileName =  pathinfo($url, PATHINFO_FILENAME); //file name without extension
    return $fileName == $addressBarFileName ?  'class="active"' : '';
}

function getSelectOption(string $optionValue = '', string $databaseValue = ''): string
{
    return $optionValue == $databaseValue ? 'selected' : '';
}

function getActiveOrInactive(string $dataBaseValue = ''): string
{
    return $dataBaseValue ? 'Active' : 'Inactive';
}

function redirect(string $fileName = '', string $message = ''): void
{
    $fileName  = $message ? $fileName . '?' . $message : $fileName;
    header("location: $fileName");
    exit;
}

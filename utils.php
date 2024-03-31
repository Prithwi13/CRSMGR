<?php
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

function getHttpAddressBarValues(string $type = 'baseUrl'): string
{
    $baseUrl = '//' . $_SERVER['HTTP_HOST'] . dirname($_SERVER['PHP_SELF']);
    $url = basename($_SERVER['REQUEST_URI'], '?' . $_SERVER['QUERY_STRING']);
    $fileName = pathinfo($url, PATHINFO_FILENAME); //file name without extension

    if ($type == 'baseUrl') {
        return $baseUrl;
    } else if ($type == 'fileName') {
        return $fileName;
    } else {
        die('Sending wrong values in getHttpAddressBarValues function');
    }
}

function getLinkActiveClass(string $fileName = DASHBOARD)
{
    return $fileName == getHttpAddressBarValues('fileName') ?  'class="active"' : '';
}

function getSelectOption(string $optionValue = '', string $databaseValue = ''): string
{
    return $optionValue == $databaseValue ? 'selected' : '';
}

function getActiveOrInactive(string $dataBaseValue = ''): string
{
    return $dataBaseValue ? 'Active' : 'Inactive';
}

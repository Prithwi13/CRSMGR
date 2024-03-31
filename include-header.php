<?php
require_once './session-logged-in.php';
require_once './utils.php';

function breadCrumbs($pageName = 'Dashboard')
{
    $currentName = $pageName != 'Dashboard' ? '<li><a href="#">' . $pageName . '</a></li>' : '';
    echo '<div class="page-title">
    <div>
        <h1><i class="fa fa-dashboard"></i> ' . $pageName . '</h1>
    </div>
    <div>
        <ul class="breadcrumb">
            <li><i class="fa fa-home fa-lg"></i></li>
            <li><a href="index.php">Dashboard</a></li>
            ' . $currentName . '
        </ul>
    </div>
</div>';
    return $pageName;
}


?>

<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8">
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <!-- CSS-->
    <link rel="stylesheet" type="text/css" href="css/main.css">
    <!-- Font-icon css-->
    <link rel="stylesheet" type="text/css" href="https://maxcdn.bootstrapcdn.com/font-awesome/4.7.0/css/font-awesome.min.css">
    <title>Course Management System</title>
    <!-- HTML5 Shim and Respond.js IE8 support of HTML5 elements and media queries-->
    <!--if lt IE 9
    script(src='https://oss.maxcdn.com/libs/html5shiv/3.7.0/html5shiv.js')
    script(src='https://oss.maxcdn.com/libs/respond.js/1.4.2/respond.min.js')
    -->
</head>

<body class="sidebar-mini fixed">
    <div class="wrapper">
        <!-- Navbar-->
        <header class="main-header hidden-print"><a class="logo" href="index.html">Course Management</a>
            <nav class="navbar navbar-static-top">
                <!-- Sidebar toggle button--><a class="sidebar-toggle" href="#" data-toggle="offcanvas"></a>
                <!-- Navbar Right Menu-->
                <div class="navbar-custom-menu">
                    <ul class="top-nav">
                        <!-- User Menu-->
                        <li class="dropdown"><a class="dropdown-toggle" href="#" data-toggle="dropdown" role="button" aria-haspopup="true" aria-expanded="false"><i class="fa fa-user fa-lg"></i></a>
                            <ul class="dropdown-menu settings-menu">
                                <li><a href="system-update-profile.php"><i class="fa fa-user fa-lg"></i>Update Profile</a></li>
                                <li><a href="system-change-password.php"><i class="fa fa-cog fa-lg"></i> Change Password</a></li>
                                <li><a href="system-logout.php"><i class="fa fa-sign-out fa-lg"></i> Logout</a></li>
                            </ul>
                        </li>
                    </ul>
                </div>
            </nav>
        </header>
        <!-- Side-Nav-->
        <aside class="main-sidebar hidden-print">
            <section class="sidebar">
                <div class="user-panel">
                    <div class="pull-left image">
                        <img class="img-circle" src="<?= getHttpAddressBarValues('baseUrl'); ?>/images/user.png" alt="User Image">
                    </div>
                    <div class="pull-left info">
                        <p><?= $_SESSION['firstName']; ?></p>
                        <p class="designation"><?= $_SESSION['emailId']; ?></p>
                    </div>
                </div>
                <!-- Sidebar Menu-->
                <?php $currentPageName = getHttpAddressBarValues('fileName'); ?>
                <ul class="sidebar-menu">
                    <li <?= getLinkActiveClass(DASHBOARD) ?>><a href="system-dashboard.php"><i class="fa fa-dashboard"></i><span>Dashboard</span></a></li>


                    <?php if ($_SESSION['type'] == ADMIN) : ?>
                        <!-- Admin Pages -->
                        <li <?= getLinkActiveClass(ADD_USERS) ?>><a href="admin-add-users.php"><i class="fa fa-user-o" aria-hidden="true"></i><span>Add Users</span></a></li>
                        <li <?= getLinkActiveClass(ADD_COURSE) ?>><a href="admin-add-course.php"><i class="fa fa-book" aria-hidden="true"></i><span>Add Course</span></a></li>
                        <!-- End of Admin Pages -->
                    <?php endif; ?>

                    <?php if ($_SESSION['type'] == INSTRUCTOR) : ?>
                        <!-- Instructor Pages -->
                        <li <?= getLinkActiveClass(INSTRUCTOR_UPLOAD_CSV); ?>><a href="instructor-upload-csv.php"><i class="fa fa-file-text-o" aria-hidden="true"></i><span>Upload CSV</span></a></li>
                        <li <?= getLinkActiveClass(INSTRUCTOR_CURRENT_COURSE_STUDENTS) ?>><a href="instructor-current-course-students.php"><i class="fa fa-address-card" aria-hidden="true"></i><span></span>All Students</a></li>
                        <!-- End of Instructor Pages -->
                    <?php endif; ?>
                    <li><a href="charts.html"><i class="fa fa-pie-chart"></i><span>Link</span></a></li>
                    <li><a href="charts.html"><i class="fa fa-pie-chart"></i><span>Link</span></a></li>
                    <li><a href="charts.html"><i class="fa fa-pie-chart"></i><span>Link</span></a></li>
                    <li><a href="charts.html"><i class="fa fa-pie-chart"></i><span>Link</span></a></li>
                </ul>
            </section>
        </aside>
        <div class="content-wrapper">
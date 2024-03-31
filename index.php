<?php
require_once './session-non-logged-in.php';
if (isset($_POST['signIn'])) {
  require_once './_db.php';
  $emailId = $_POST['email'];
  $password = $_POST['password'];

  $row = $db->getSingleRecord("SELECT * FROM users where email_id='$emailId'");

  if (count($row) == 0) {
    header('location:index.php?status=danger&message=' . urlencode('Invalid User'));
    exit;
  }

  if (!password_verify($password, $row['password'])) {
    header('location:index.php?status=danger&message=' . urlencode('Invalid Email and Password'));
    exit;
  }

  $_SESSION = array(
    'userId' => $row['user_id'],
    'type' => $row['type'],
    'firstName' => $row['first_name'],
    'lastName' => $row['last_name'],
    'emailId' => $row['email_id']
  );
  header('location:system-dashboard.php');
  exit;
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

<body>
  <section class="material-half-bg">
    <div class="cover"></div>
  </section>
  <section class="login-content">
    <div class="logo">
      <h1>Course Management System</h1>
    </div>
    <div class="login-box">

      <form class="login-form" action="<?= $_SERVER['PHP_SELF'] ?>" method="post">
        <h3 class="login-head"><i class="fa fa-lg fa-fw fa-user"></i>SIGN IN</h3>
        <?php require_once './include-message.php'; ?>
        <div class="form-group">
          <label class="control-label" for="email">Email Id</label>
          <input class="form-control" id="email" type="text" placeholder="Enter Email" name="email" required autofocus>
        </div>
        <div class="form-group">
          <label class="control-label" for="password">Password</label>
          <input class="form-control" id="password" name="password" type="password" placeholder="Enter Password" required>
        </div>
        <div class="form-group btn-container">
          <button class="btn btn-primary btn-block" type="submit" name="signIn"><i class="fa fa-sign-in fa-lg fa-fw"></i>SIGN IN</button>
        </div>
      </form>
    </div>
  </section>
</body>
<script src="js/jquery-2.1.4.min.js"></script>
<script src="js/bootstrap.min.js"></script>
<script src="js/plugins/pace.min.js"></script>
<script src="js/main.js"></script>

</html>
<?php
require_once('./include-header.php');
include_once './_db.php';

$adminType = ADMIN;
$userId = $_SESSION['userId'];
$allUsers = $db->getAllRecords("SELECT * from users WHERE type !=$adminType AND user_id !=$userId");

if (isset($_POST['send'])) {
  $receiverId = $_POST['receiver'];
  $message = $_POST['message'];
  $room = $db->getSingleRecord("SELECT * from chat_rooms WHERE (sender_id=$receiverId AND receiver_id=$userId) OR (sender_id=$userId AND receiver_id=$receiverId)");
  if (empty($room)) {
    $roomId = $db->insertData("INSERT INTO chat_rooms (sender_id, receiver_id) VALUES ('$userId','$receiverId' )");
  } else {
    $roomId = $room['id'];
  }
  $check = $db->insertData("INSERT INTO messages (room_id, sender_id, receiver_id, `message`) VALUES ('$roomId','$userId','$receiverId','$message' )");

  if ($check) {
    header('location:system-message.php?status=success&message=' . urlencode('Message sent successfully'));
    exit;
  } else {
    header('location:system-message.php?status=danger&message=' . urlencode('Database Problem'));
    exit;
  }
}

$pageName = breadCrumbs('Send Message');
?>
<div class="row">
  <div class="col-md-12">
    <div class="card">
      <div class="row">
        <div class="col-lg-6 col-lg-offset-2">
          <div class="well bs-component">
            <form class="form-horizontal" method="post" action="<?= $_SERVER['PHP_SELF']; ?>">

              <legend><?= $pageName ?></legend>
              <div class="form-group">
                <label class="col-lg-2 control-label" for="receiver">User</label>
                <div class="col-lg-10">
                  <select class="form-control" id="receiver" name="receiver" required aria-placeholder="Select User">
                    <option value="">Select User</option>
                    <?php foreach ($allUsers as $user) : ?>
                      <option value="<?= $user['user_id']; ?>"><?= $user['first_name'] ?></option>
                    <?php endforeach; ?>
                  </select>
                </div>
              </div>
              <div class="form-group">
                <label class="col-lg-2 control-label" for="message">Message</label>
                <div class="col-lg-10">
                  <textarea class="form-control" id="message" name="message" rows="5" placeholder="Enter your message..." required></textarea>
                </div>
              </div>

              <div class="form-group">
                <div class="col-lg-8 col-lg-offset-2">
                  <button class="btn btn-success" name="send" type="submit">Send</button>
                  <button class="btn btn-danger" type="reset">Cancel</button>
                </div>
              </div>
            </form>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>
<?php require_once './include-footer.php' ?>
<script type="text/javascript" src="js/plugins/bootstrap-datepicker.min.js"></script>
<script type="text/javascript" src="js/plugins/select2.min.js"></script>
<script type="text/javascript" src="js/plugins/bootstrap-datepicker.min.js"></script>
<script type="text/javascript">
  $('#receiver').select2();
</script>
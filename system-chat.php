<?php
require_once('./include-header.php');
include_once './_db.php';
$pageName = breadCrumbs('Chat');
$roomId = base64_decode($_GET['roomId']);
$userId = $_SESSION['userId'];
$room = $db->getSingleRecord("SELECT * from `chat_rooms` WHERE id = $roomId");

if ($room['sender_id'] == $userId) {
  $receiverId = $room['receiver_id'];
} else {
  $receiverId = $room['sender_id'];
}
$receiverDetails = $db->getSingleRecord("SELECT * from `users` WHERE user_id = $receiverId");
$db->updateData("UPDATE messages SET is_read = 1 WHERE room_id = $roomId AND receiver_id = $userId");

$allMessages = $db->getAllRecords("SELECT m.sender_id,
m.receiver_id,
m.message,
m.created_at,
u.first_name AS sender_first_name,
u.last_name AS sender_last_name
FROM messages AS m
JOIN users AS u ON m.sender_id = u.user_id
WHERE room_id = $roomId
ORDER BY m.created_at;");

?>
<div class="row">
  <div class="col-md-12">
    <div class="card">
      <h3 class="card-title">Chat with<?= $receiverDetails['first_name'] ?> (<?= $receiverDetails['email_id'] ?> )</h3>
      <div class="messanger">
        <?php require_once './include-message.php'; ?>
        <div class="messages">
          <?php
          foreach ($allMessages as $message) :
          ?>
            <div class="message <?= $_SESSION['userId'] == $message['sender_id'] ? 'me' : null ?>">
              <p class="info">
                <strong>
                <?= $_SESSION['userId'] == $message['sender_id'] ? $message['sender_first_name'] : $receiverDetails['first_name'] ?>
                </strong>
                ( <small><?= $message['created_at'] ?></small> )
                <br />
                <?= $message['message'] ?> <br />
              </p>
            </div>
          <?php endforeach; ?>
        </div>
        <form action="system-message-ajax.php" method="post">
          <div class="sender">
            <textarea name="message" placeholder="Type your message..." required class="form-control" rows="1" style="margin-bottom: 20px;"></textarea>
            <input type="hidden" name="roomId" value="<?= $roomId ?>">
            <button class="btn btn-primary" type="submit" style="height: 40px;"><i class="fa fa-paper-plane"></i></button>
          </div>
        </form>
      </div>
    </div>
  </div>
</div>
<?php require './include-footer.php' ?>
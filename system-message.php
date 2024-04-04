<?php
require_once('./include-header.php');
include_once './_db.php';

$userId  = $_SESSION['userId'];
$totalMessages = $db->getSingleRecord("SELECT COUNT(*) AS unread_count FROM messages WHERE receiver_id = '$userId' && is_read = 0");

$allMessages = [];
$myChatRooms = $db->getAllRecords("SELECT * from `chat_rooms` WHERE (sender_id = $userId OR receiver_id = $userId)");
$roomIds = implode(",", array_column($myChatRooms, 'id'));

foreach ($myChatRooms as $roomData) {
  $chatRoomId = $roomData['id'];
  $chatDetails = $db->getSingleRecord("SELECT m.room_id, m.sender_id, u.first_name AS sender_first_name, u.last_name AS sender_last_name, m.receiver_id, ua.first_name AS receiver_first_name, ua.last_name AS receiver_last_name, m.message, m.is_read, m.created_at FROM `messages` as m LEFT JOIN users AS u ON m.sender_id = u.user_id LEFT JOIN users AS ua ON m.receiver_id = ua.user_id WHERE room_id = $chatRoomId ORDER BY created_at DESC LIMIT 1");
  $allMessages[] = $chatDetails;
}
$pageName = breadCrumbs('Messages', '<i class="fa fa-comments" aria-hidden="true"></i>');
?>
<div class="row">
  <div class="col-md-3">
    <div class="card p-0">
      <h4 class="card-title folder-head">Menu</h4>
      <div class="card-body">
        <ul class="nav nav-pills nav-stacked mail-nav">
          <li><a href="system-send-msg.php">
              <i class="fa fa-paper-plane"></i> New Chat</a>
          </li>
          <li><a href="#">
              <i class="fa fa-inbox fa-fw"></i> Inbox<span class="label label-primary pull-right"><?= $totalMessages['unread_count'] ?></span></a>
          </li>
        </ul>
      </div>
    </div>
  </div>
  <div class="col-md-9">
    <div class="card">
      <?php require_once './include-message.php'; ?>
      <div class="mailbox-controls">
        You Inbox
      </div>
      <div class="table-responsive mailbox-messages" style="cursor: pointer;">
        <?php if (count($allMessages)) : ?>
          <table class="table table-hover">
            <tbody>
              <?php
              foreach ($allMessages as $message) { ?>
                <tr onclick="location.href='<?php echo htmlspecialchars('system-chat.php?roomId=' . urlencode(base64_encode($message['room_id']))); ?>'">
                  <td>
                    <a href="system-chat.php?roomId=<?= urlencode(base64_encode($message['room_id'])); ?>">
                      <?php
                      if ($message['sender_id'] == $userId) {
                        echo $message['receiver_first_name'] . ' ' . $message['receiver_last_name'];
                      } else {
                        echo $message['sender_first_name'] . ' ' . $message['sender_last_name'];
                      }
                      ?>
                    </a>
                  </td>
                  <td class="mail-subject">
                    <?php
                    if (!$message['is_read'] && $message['sender_id'] != $userId) { ?>
                      <b><?php echo $message['message']; ?></b>
                    <?php } else {
                      echo $message['message'];
                    } ?>
                  </td>
                  <td><?php echo $message['created_at'] ?></td>
                </tr>
              <?php
              } ?>
            </tbody>
          </table>
        <?php else : ?>
          No conversation exits
        <?php endif ?>
      </div>
    </div>
  </div>
</div>
<?php require_once './include-footer.php'; ?>
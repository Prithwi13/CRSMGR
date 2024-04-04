<?php
require_once('./include-header.php');
include_once './_db.php';

$userId = $_SESSION['userId'];
if (isset($_POST['message']) && isset($_POST['roomId'])) {
  $roomId = $_POST['roomId'];
  $room = $db->getSingleRecord("SELECT * from `chat_rooms` WHERE id = $roomId");
  if ($room['sender_id'] == $userId) {
    $receiverId = $room['receiver_id'];
  } else {
    $receiverId = $room['sender_id'];
  }
  $message = $_POST['message'];
  $db->insertData("INSERT INTO messages (room_id ,sender_id, receiver_id, `message`) VALUES ('$roomId','$userId','$receiverId','$message' )");
  $encodedRoomId = base64_encode($roomId);
  $url = "system-chat.php?roomId=" . urlencode($encodedRoomId);
  header("Location: $url");
  exit;
}

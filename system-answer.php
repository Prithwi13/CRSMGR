<?php
require_once('./include-header.php');
include_once './_db.php';
$userId = $_SESSION['userId'];
if (isset($_POST['updateAnswer'])) {
  $editedMessage = htmlentities($_POST['editedMessage']);
  $editAnswerId = $_POST['answerId'];
  $currentTime = getCurrentTime();
  $check = $db->updateData("UPDATE answers SET answer='$editedMessage', modified_by='$currentTime' WHERE id = $editAnswerId");
  $qId = base64_encode($_POST['questionId']);
  if ($check) {
    redirect("system-answer.php", "status=success&message=" . urlencode('Answer updated successfully') . "&questionId=$qId");
  } else {
    redirect("system-answer.php", "status=danger&message=" . urlencode('Database Problem') . "&questionId=$qId");
  }
} else if (isset($_POST['submit'])) {
  $answer = htmlentities($_POST['answer']);
  $questionId = ($_POST['questionId']);
  $currentTime = getCurrentTime();
  $check = $db->insertData("INSERT INTO answers VALUES (null, '$answer','$userId', $questionId , '$currentTime', null)");
  $qId = base64_encode($questionId);
  if ($check) {
    redirect("system-answer.php", "questionId=$qId&status=success&message=" . urlencode('Answer added successfully'));
  } else {
    redirect("system-answer.php", "questionId=$qId&status=danger&message=" . urlencode('Database Problem'));
  }
}

$questionId = isset($_GET['questionId']) ?  base64_decode($_GET['questionId']) : 0;
if (!$questionId) {
  redirect('system-dashboard.php');
}
$questionDetails = $db->getSingleRecord("SELECT * from `questions` WHERE id=$questionId");
if (count($questionDetails) == 0) {
  redirect('system-dashboard.php?status=danger&message=' . urlencode('Question not found'));
}

$allAnswers = $db->getAllRecords("SELECT * from `answers` as a LEFT JOIN users as u ON a.user_id = u.user_id WHERE question_id = $questionId ORDER BY created_at DESC");
$myAnswer = $db->getSingleRecord("SELECT * from `answers` WHERE question_id = $questionId && user_id = $userId");

$pageName = breadCrumbs('Answers', '<i class="fa fa-stack-exchange" aria-hidden="true"></i>');
?>
<div class="row">
  <div class="col-md-12">
    <div class="card">
      <h3 class="card-title"><i class="fa fa-newspaper-o" aria-hidden="true"></i> <?= $questionDetails['question'] ?></h3>

      <?php if ($userId == $questionDetails['user_id']) : ?>
        <div class="alert alert-info">
          You cannot answer this question because You are the one asking it
        </div>
      <?php endif ?>
      <?php require_once './include-message.php'; ?>
      <?php if (count($allAnswers)) : ?>
        <table class="table table-hover">
          <tbody>
            <?php foreach ($allAnswers as $answer) : ?>
              <tr>
                <td><?= $answer['answer'] ?></td>
                <td><?= $answer['first_name'] ?></td>
                <td><?= $answer['created_at'] ?></td>
                <td><?php if (!empty($myAnswer)) : ?>
                    <a class="btn btn-success btn-sm" onclick="return confirm('Are you sure you want to edit this answer ?')" href="<?= $_SERVER['PHP_SELF'] . "?questionId=" . base64_encode($questionId) . "&answerId=" . base64_encode($answer['id']) . "&action=edit"; ?>">Edit</a>
                  <?php endif; ?>
                </td>
              </tr>
            <?php endforeach; ?>
          </tbody>
        </table>
      <?php else : ?>
        <p>No Answers found </p>
      <?php endif;
      if ($userId != $questionDetails['user_id'] && empty($myAnswer)) :
      ?>
        <br /><br />
        <form class="form-horizontal" action="<?= $_SERVER['PHP_SELF']; ?>?questionId=<?= base64_encode($questionId); ?>" method="post">
          <input type="hidden" name="questionId" value="<?= $questionId ?>">
          <div class="form-group">
            <label class="col-lg-1 control-label" for="answer">Answer</label>
            <div class="col-lg-10">
              <textarea class="form-control" id="answer" name="answer" rows="5" placeholder="Enter your answer..." required></textarea>
            </div>
          </div>
          <div class="form-group">
            <div class="col-lg-8 col-lg-offset-1">
              <button class="btn btn-success btn-sm" name="submit" type="submit">Submit</button>
              <button class="btn btn-danger btn-sm" type="reset">Cancel</button>
            </div>
          </div>
        </form>
      <?php endif;
      if (isset($_GET['action']) && !empty($myAnswer) && $_GET['action'] === 'edit') {
      ?>
        <form class="form-horizontal" action="<?= $_SERVER['PHP_SELF']; ?>?questionId=<?= base64_encode($questionId); ?>" method="post">
          <input type="hidden" name="answerId" value="<?= $myAnswer['id'] ?>">
          <input type="hidden" name="questionId" value="<?= $questionId ?>">
          <div class="form-group">
            <label class="col-lg-1 control-label" for="editedMessage">Answer</label>
            <div class="col-lg-10">
              <textarea class="form-control" id="editedMessage" name="editedMessage" rows="5" placeholder="Enter your answer..." required><?= $myAnswer['answer']; ?></textarea>
            </div>

          </div>
          <div class="form-group">
            <div class="col-lg-8 col-lg-offset-1">
              <button class="btn btn-success btn-sm" name="updateAnswer" type="submit">Update</button>
            </div>
          </div>
        </form>
      <?php
      }
      ?>
    </div>
  </div>
</div>
<?php
require_once('./include-footer.php');
?>
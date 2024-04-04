<?php
require_once('./include-header.php');
include_once './_db.php';
if (isset($_POST['create'])) {
    $userId = $_SESSION['userId'];
    $question = $_POST['question'];
    $currentTime = getCurrentTime();
    $check = $db->insertData("INSERT INTO questions VALUES (null,'$question','$userId', '$currentTime')");
    if ($check) {
        redirect('system-faq.php', 'status=success&message=' . urlencode('Question added successfully'));
    } else {
        redirect('system-faq.php', 'status=danger&message=' . urlencode('Database Problem'));
    }
}

$pageName = breadCrumbs('FAQ', '<i class="fa fa-list-alt" aria-hidden="true"></i>');

$allQuestions = $db->getAllRecords("SELECT * from `questions` ORDER BY created_at DESC");
?>
<div class="row">
    <div class="col-md-12">
        <div class="card">
            <button type="button" class="btn btn-primary btn-sm pull-right" data-toggle="modal" data-target="#fqs-model">
                Ask Question
            </button>
            <h3 class="card-title"><?= $pageName ?></h3>
            <?php require_once './include-message.php'; ?>
            <p><strong>Your Questions</strong></p>
            <?php if (count($allQuestions)) : ?>
                <table class="table table-hover">
                    <tbody>
                        <?php
                        $c = 0;
                        foreach ($allQuestions as $question) : ?>
                            <tr style="cursor:pointer" title="<?= $question['question'] ?>" onclick="location.href='system-answer.php?questionId=<?= base64_encode($question['id']); ?>'">
                                <td><?= ++$c ?></td>
                                <td><?= $question['question'] ?></td>
                                <td><?= $question['created_at'] ?></td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            <?php else : ?>
                No questions found
            <?php endif ?>
        </div>
    </div>
</div>
<!-- Modal -->
<div class="modal fade" id="fqs-model" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="btn btn-default btn-sm pull-right" data-dismiss="modal">Close</button>
                <h5 class="modal-title" id="exampleModalLabel">Are you want to ask any question ?</h5>
            </div>
            <form class="form-horizontal" action="<?= $_SERVER['PHP_SELF']; ?>" method="post">
                <div class="modal-body">
                    <div class="form-group">
                        <label class="control-label" style="margin-left: 15px" for="message">What is your Question ?</label><br /><br />
                        <div class="col-lg-12">
                            <textarea class="form-control" id="question" name="question" rows="5" placeholder="Enter your question..." required></textarea>
                        </div>
                    </div>

                </div>
                <div class="modal-footer">
                    <div class="pull-left">
                        <button type="submit" class="btn btn-success btn-sm" name="create">Create</button>
                        <button type="reset" class="btn btn-danger btn-sm">Cancel</button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>
<!-- End of Model -->
<?php
require_once('./include-footer.php');
?>
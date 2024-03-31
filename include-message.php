<?php
if (isset($_GET['status'])) :
    $color = $_GET['status'];
    $message = $_GET['message'];
    $stay = !isset($_GET['stay']) ? true : false;
?>
    <div class="alert alert-<?= $color; ?> show-alert">
        <?= $message; ?>
    </div>
    <?php if ($stay) : ?>
        <script>
            window.setTimeout(function() {
                $(".show-alert").fadeTo(500, 0).slideUp(500, function() {
                    $(this).remove();
                });
            }, 5000);
        </script>
<?php
    endif;
endif; ?>
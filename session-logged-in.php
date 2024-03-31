<?php
session_start();
if (!isset($_SESSION['userId'])) {
    header('location:index.php?status=danger&message=' . urlencode('You Must login first'));
}

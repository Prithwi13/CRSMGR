<?php
session_start();
if (isset($_SESSION['userId'])) {
    header('location:system-dashboard.php');
}

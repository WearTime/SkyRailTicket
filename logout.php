<?php
session_start();

if (isset($_SESSION["logged_in"])) {
    session_destroy();
    header("Location: login");
    exit();
} else {
    session_destroy();
    header("Location: home");
    exit();
}
?>
<?php
$localhost = 'localhost';
$user = 'root';
$pass = '';
$db = 'tiketskyrail';

$conn = mysqli_connect($localhost, $user, $pass, $db);
if (!$conn) {
    die("error: " . mysqli_connect_error());
}


?>
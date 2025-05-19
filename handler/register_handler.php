<?php
session_start();
include '../config/connection.php';
header('Content-Type: application/json');

$response = ["success" => false, "email_error" => "", "password_error" => "", "confirm_passwd_error" => "", "username_error" => ""];

if (!isset($_POST['csrf_token']) || $_POST['csrf_token'] !== $_SESSION['csrf_token']) {
    $response["email_error"] = "Verifikasi keamanan gagal. Silakan coba lagi.";
    echo json_encode($response);
    exit;
}


$username = trim($_POST['username']);
$email = trim($_POST['email']);
$password = $_POST['password'];
$confirm_password = $_POST['confirm_password'];

if (empty($email)) {
    $response["email_error"] = "Email tidak boleh kosong!";
}
if (empty($password)) {
    $response["password_error"] = "Password tidak boleh kosong!";
}
if (empty($confirm_password)) {
    $response["confirm_passwd_error"] = "Confirm Password tidak boleh kosong!";
}
if (empty($username)) {
    $response["username_error"] = "Username tidak boleh kosong!";
}
if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    $response["email_error"] = "Format email tidak valid.";
}
if ($password !== $confirm_password) {
    $response["confirm_passwd_error"] = "Password dan konfirmasi password tidak cocok.";
}
if (strlen($password) < 8) {
    $response["password_error"] = "Password harus minimal 8 karakter.";
}

if (empty($response["email_error"]) && empty($response["password_error"]) && empty($response['confirm_passwd_error']) && empty($response['username_error'])) {
    $check_sql = "SELECT id FROM users WHERE username = ?";
    $check_stmt = $conn->prepare($check_sql);
    $check_stmt->bind_param("s", $username);
    $check_stmt->execute();
    $check_stmt->store_result();

    if ($check_stmt->num_rows > 0) {
        $response["username_error"] = "Username sudah digunakan. Silakan pilih username lain.";
    } else {
        $check_email_sql = "SELECT id FROM users WHERE email = ?";
        $check_email_stmt = $conn->prepare($check_email_sql);
        $check_email_stmt->bind_param("s", $email);
        $check_email_stmt->execute();
        $check_email_stmt->store_result();

        if ($check_email_stmt->num_rows > 0) {
            $response["email_error"] = "Email sudah terdaftar. Silakan gunakan email lain.";
        } else {
            $hashed_password = password_hash($password, PASSWORD_DEFAULT);
            $role = 'Member';
            $sql = "INSERT INTO users (username, email, password, role) VALUES (?, ?, ?, ?)";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("ssss", $username, $email, $hashed_password, $role);

            if ($stmt->execute()) {
                $_SESSION['register_success'] = true;
                $response['success'] = true;
                $response["redirect"] = "login";

            } else {
                $response["username_error"] = "Terjadi Kesalahan: " . $stmt->error;
            }
        }
        $check_email_stmt->close();
    }
    $check_stmt->close();
}

echo json_encode($response);
exit;
?>
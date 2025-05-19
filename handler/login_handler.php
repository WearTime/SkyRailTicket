<?php
session_start();
include '../config/connection.php';
header('Content-Type: application/json');

$response = ["success" => false, "email_error" => "", "password_error" => ""];

if (!isset($_POST['csrf_token']) || $_POST['csrf_token'] !== $_SESSION['csrf_token']) {
    $response["email_error"] = "Verifikasi keamanan gagal. Silakan coba lagi.";
    echo json_encode($response);
    exit;
}

$email = trim($_POST['email'] ?? '');
$password = $_POST['password'] ?? '';

if (empty($email)) {
    $response["email_error"] = "Email harus diisi.";
}
if (empty($password)) {
    $response["password_error"] = "Password harus diisi.";
}

if (empty($response["email_error"]) && empty($response["password_error"])) {
    $sql = "SELECT id, username, password, role FROM users WHERE email = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $stmt->store_result();

    if ($stmt->num_rows > 0) {
        $stmt->bind_result($id_user, $username, $hashed_password, $role);
        $stmt->fetch();

        if (password_verify($password, $hashed_password)) {
            $_SESSION['id'] = $id_user;
            $_SESSION['username'] = $username;
            $_SESSION['email'] = $email;
            $_SESSION['role'] = $role;
            $_SESSION['logged_in'] = true;

            $response["success"] = true;
            $response["redirect"] = $role === "Admin" ? "admin" : "skyrailticket";
        } else {
            $response["password_error"] = "Email Atau Password tidak valid!";
            $response["email_error"] = "Email Atau Password tidak valid!";
        }
    } else {
        $response["email_error"] = "Email Atau Password tidak valid!";
        $response["password_error"] = "Email Atau Password tidak valid!";

    }
    $stmt->close();
}

echo json_encode($response);
exit;

<?php
session_start();
if (!isset($_SESSION['id']) && !isset($_SESSION['logged_in'])) {
    header("Location: /skyrailticket");
    exit;
}

if ($_SESSION['role'] != "Admin") {
    header("Location: /skyrailticket");
    exit;
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard</title>
    <link rel="apple-touch-icon" sizes="180x180" href="../assets/image/apple-touch-icon.png">
    <link rel="icon" type="image/png" sizes="32x32" href="../assets/image/favicon-32x32.png">
    <link rel="icon" type="image/png" sizes="16x16" href="../assets/image/favicon-16x16.png">
    <link rel="manifest" href="../assets/image/site.webmanifest">
</head>

<body>
    <?php include "../layouts/admin/navbar.php" ?>
    <main>
        <?php include "../layouts/admin/sidebar.php" ?>
        <h1>Dashboard</h1>
    </main>
</body>

</html>
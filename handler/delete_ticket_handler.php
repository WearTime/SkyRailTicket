<?php

require_once '../config/connection.php';

// Check if form is submitted
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id = $_POST['delete-id'];

    $checkStmt = $conn->prepare("SELECT * FROM ticket WHERE id = ?");
    $checkStmt->bind_param("i", $id);
    $checkStmt->execute();
    $checkResult = $checkStmt->get_result();

    if ($checkResult->num_rows === 0) {
        // Ticket not found
        $_SESSION['error'] = "Ticket tidak ditemukan.";
        header("Location: ../admin/ticket");
        exit;
    }
    $checkStmt->close();

    $stmt = $conn->prepare("DELETE ");

    $stmt->bind_param(
        "sssssssssisssi",
        $namaTicket,
        $deskripsi,
        $tipeTicket,
        $kelasTicket,
        $hostTicket,
        $harga,
        $tempatBerangkat,
        $destinasi,
        $tanggal,
        $stok,
        $penumpangMax,
        $luarNegeri,
        $imagePath,
        $id
    );

    if ($stmt->execute()) {
        $_SESSION['success'] = "Ticket berhasil diperbarui.";
    } else {
        $_SESSION['error'] = "Gagal memperbarui ticket: " . $conn->error;
    }

    $stmt->close();

    // Redirect back to ticket page
    header("Location: ../admin/ticket.php");
    exit;
}
<?php
require_once '../config/connection.php';

// Check if form is submitted
if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $response = [
        'success' => false,
        'message' => ''
    ];

    try {
        // Get form data
        $id = $_POST['id'];
        $namaTicket = trim($_POST['namaTicket']);
        $deskripsi = trim($_POST['deskripsi']);
        $tipeTicket = trim($_POST['tipeTicket']);
        $kelasTicket = trim($_POST['kelasTicket']);
        $hostTicket = $_POST['hostTicket'];
        $harga = trim(str_replace('.', '', $_POST['harga']));
        $destinasi = trim($_POST['destinasi']);
        $tempatBerangkat = trim($_POST['tempatBerangkat']);
        $tanggal = $_POST['tanggal'];
        $stok = $_POST['stok'];
        $penumpangMax = trim($_POST['penumpangMax']);
        $luarNegeri = isset($_POST['luarNegeri']) ? 1 : 0;

        $checkStmt = $conn->prepare("SELECT imageTujuan FROM ticket WHERE id = ?");
        $checkStmt->bind_param("i", $id);
        $checkStmt->execute();
        $checkResult = $checkStmt->get_result();

        if ($checkResult->num_rows === 0) {
            throw new Exception('Ticket tidak ditemukan.');

        }

        $hostCheckQuery = "SELECT id FROM host WHERE id = ?";
        $hostStmt = $conn->prepare($hostCheckQuery);
        $hostStmt->bind_param("i", $hostTicket);
        $hostStmt->execute();
        $hostResult = $hostStmt->get_result();

        if ($hostResult->num_rows == 0) {
            throw new Exception('Host yang dipilih tidak valid.');
        }
        $hostStmt->close();

        $ticketData = $checkResult->fetch_assoc();
        $currentImage = $ticketData['imageTujuan'];
        $checkStmt->close();

        // Handle image upload if provided
        $imagePath = $currentImage; // Default to current image

        if (isset($_FILES['imageTujuan']) && $_FILES['imageTujuan']['size'] > 0) {
            $targetDir = "../uploads/tickets/";
            $fileName = basename($_FILES["imageTujuan"]["name"]);
            $imageFileType = strtolower(pathinfo($fileName, PATHINFO_EXTENSION));

            // Generate unique filename
            $uniqueFileName = uniqid() . '.' . $imageFileType;
            $targetFile = $targetDir . $uniqueFileName;

            // Check if image file is a actual image
            $check = getimagesize($_FILES["imageTujuan"]["tmp_name"]);
            if ($check === false) {
                throw new Exception('File yang diupload bukan gambar.');
            }

            // Check file size (limit to 5MB)
            if ($_FILES['imageTujuan']['size'] > 5000000) {
                throw new Exception('Ukuran file terlalu besar. Maksimal 5MB.');
            }

            // Allow certain file formats
            if (!in_array($imageFileType, ['jpg', 'jpeg', 'png', 'gif'])) {
                throw new Exception('Hanya format JPG, JPEG, PNG & GIF yang diizinkan.');
            }

            // Upload file
            if (move_uploaded_file($_FILES["imageTujuan"]["tmp_name"], $targetFile)) {
                $imagePath = $uniqueFileName;

                // Delete old image if exists and not default
                if (!empty($currentImage) && file_exists($targetDir . $currentImage)) {
                    unlink($targetDir . $currentImage);
                }
            } else {
                throw new Exception('Gagal mengupload gambar.');
            }
        }

        // Update ticket in database
        $stmt = $conn->prepare("UPDATE ticket SET 
                                namaTicket = ?, 
                                deskripsi = ?, 
                                tipeTicket = ?, 
                                kelasTicket = ?, 
                                hostTicket = ?, 
                                harga = ?, 
                                tempatBerangkat = ?, 
                                destinasi = ?, 
                                tanggal = ?, 
                                stok = ?, 
                                penumpangMax = ?, 
                                luarNegeri = ?, 
                                imageTujuan = ? 
                                WHERE id = ?");

        $stmt->bind_param(
            "ssssissssisssi",
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
            $response['success'] = true;
            $response['message'] = 'Ticket berhasil ditambahkan!';
        } else {
            throw new Exception('Gagal menambahkan ticket: ' . $stmt->error);
        }

        $stmt->close();
        //code...
    } catch (Exception $e) {
        $response['message'] = $e->getMessage();
    }

    // Close connection
    $conn->close();

    // Return JSON response
    header('Content-Type: application/json');
    echo json_encode($response);
    exit;
}
header("Location: ../admin/ticket.php");
exit;
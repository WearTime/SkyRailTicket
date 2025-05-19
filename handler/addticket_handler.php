<?php
// Include database connection
require_once '../config/connection.php';

// Check if the request method is POST
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Initialize response array
    $response = [
        'success' => false,
        'message' => ''
    ];

    try {
        // Get form data
        $namaTicket = trim($_POST['namaTicket']);
        $deskripsi = trim($_POST['deskripsi']);
        $luarNegeri = isset($_POST['luarNegeri']) ? 1 : 0;
        $tipeTicket = trim($_POST['tipeTicket']);
        $kelasTicket = trim($_POST['kelasTicket']);
        $hostTicket = (int) $_POST['hostTicket']; // Now expecting an ID, not a name
        $harga = trim(str_replace('.', '', $_POST['harga'])); // Remove thousand separators
        $destinasi = trim($_POST['destinasi']);
        $tempatBerangkat = trim($_POST['tempatBerangkat']);
        $tanggal = $_POST['tanggal'];
        $stok = (int) $_POST['stok'];
        $penumpangMax = trim($_POST['penumpangMax']);

        // Validate host ID exists
        $hostCheckQuery = "SELECT id FROM host WHERE id = ?";
        $hostStmt = $conn->prepare($hostCheckQuery);
        $hostStmt->bind_param("i", $hostTicket);
        $hostStmt->execute();
        $hostResult = $hostStmt->get_result();

        if ($hostResult->num_rows == 0) {
            throw new Exception('Host yang dipilih tidak valid.');
        }
        $hostStmt->close();

        // Handle file upload
        $uploadDir = '../uploads/tickets/';
        $imageTujuan = '';

        // Create directory if it doesn't exist
        if (!file_exists($uploadDir)) {
            mkdir($uploadDir, 0777, true);
        }

        if (isset($_FILES['imageTujuan']) && $_FILES['imageTujuan']['error'] === UPLOAD_ERR_OK) {
            $tempFile = $_FILES['imageTujuan']['tmp_name'];
            $imageFileType = strtolower(pathinfo($_FILES['imageTujuan']['name'], PATHINFO_EXTENSION));

            // Generate unique filename
            $imageTujuan = uniqid() . '_' . time() . '.' . $imageFileType;
            $targetFile = $uploadDir . $imageTujuan;

            // Check if image file is a actual image
            $check = getimagesize($tempFile);
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
            if (!move_uploaded_file($tempFile, $targetFile)) {
                throw new Exception('Gagal mengupload gambar.');
            }
        } else {
            throw new Exception('Gambar tujuan diperlukan.');
        }

        // Prepare SQL statement
        $sql = "INSERT INTO ticket 
                (namaTicket, deskripsi, luarNegeri, tipeTicket, kelasTicket, hostTicket, 
                harga, destinasi, tempatBerangkat, tanggal, stok, penumpangMax, imageTujuan) 
                VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";

        $stmt = $conn->prepare($sql);

        // Bind parameters
        $stmt->bind_param(
            "ssississssiss",
            $namaTicket,
            $deskripsi,
            $luarNegeri,
            $tipeTicket,
            $kelasTicket,
            $hostTicket, // Now an integer ID
            $harga,
            $destinasi,
            $tempatBerangkat,
            $tanggal,
            $stok,
            $penumpangMax,
            $imageTujuan
        );

        // Execute statement
        if ($stmt->execute()) {
            $response['success'] = true;
            $response['message'] = 'Ticket berhasil ditambahkan!';
        } else {
            throw new Exception('Gagal menambahkan ticket: ' . $stmt->error);
        }

        // Close statement
        $stmt->close();

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

// If not POST request, redirect to the ticket page
header('Location: ../admin/ticket.php');
exit;
?>
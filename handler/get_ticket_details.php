<?php
// Include database connection
require_once '../config/connection.php';

// Check if ID is provided
if (!isset($_GET['id']) || empty($_GET['id'])) {
    echo json_encode([
        'status' => 'error',
        'message' => 'ID ticket tidak ditemukan'
    ]);
    exit;
}

$ticketId = (int) $_GET['id'];

// Fetch ticket details including host information
$sql = "SELECT t.*, h.nameHost, h.tipeHost 
        FROM ticket t
        JOIN host h ON t.hostTicket = h.id
        WHERE t.id = ?";

$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $ticketId);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    $ticketData = $result->fetch_assoc();

    echo json_encode([
        'status' => 'success',
        'data' => $ticketData
    ]);
} else {
    echo json_encode([
        'status' => 'error',
        'message' => 'Ticket tidak ditemukan'
    ]);
}

$stmt->close();
$conn->close();
?>
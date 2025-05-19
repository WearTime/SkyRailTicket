<?php
require_once '../config/connection.php';
session_start();

if (!isset($_SESSION['id']) || !isset($_SESSION['logged_in'])) {
    header("Location: /skyrailticket");
    exit;
}

if ($_SESSION['role'] !== "Admin") {
    header("Location: /skyrailticket");
    exit;
}

// Tambah Booking
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['customerName'])) {
    $namaCustomer = $_POST['customerName'];
    $jumlahTiket = $_POST['ticketCount'] ?? 0;
    $tanggalBooking = $_POST['bookingDate'];
    $statusPembayaran = $_POST['paymentStatus'];

    if ($namaCustomer !== '' && $jumlahTiket > 0 && $tanggalBooking !== '' && $statusPembayaran !== '') {
        $stmtUser = $conn->prepare("SELECT id FROM users WHERE username = ?");
        $stmtUser->bind_param("s", $namaCustomer);
        $stmtUser->execute();
        $stmtUser->bind_result($userId);
        $stmtUser->fetch();
        $stmtUser->close();

        $stmtBayar = $conn->prepare("SELECT id FROM pembayaran WHERE metode_pembayaran = ?");
        $stmtBayar->bind_param("s", $statusPembayaran);
        $stmtBayar->execute();
        $stmtBayar->bind_result($pembayaranId);
        $stmtBayar->fetch();
        $stmtBayar->close();

        $ticketId = 1;

        if ($userId && $pembayaranId) {
            $stmt = $conn->prepare("INSERT INTO booking (userId, pembayaranId, ticketId, tanggalBooking) VALUES (?, ?, ?, ?)");
            $stmt->bind_param("iiis", $userId, $pembayaranId, $ticketId, $tanggalBooking);
            $stmt->execute();
            $stmt->close();

            echo "<script>alert('Booking berhasil ditambahkan!'); window.location.href = 'booking.php';</script>";
            exit;
        }
    }
}

// Edit Booking
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['submitEdit'])) {
    $id = $_POST['editId'];
    $namaCustomer = $_POST['editCustomerName'];
    $jumlahTiket = $_POST['editTicketCount'] ?? 0;
    $tanggalBooking = $_POST['editBookingDate'];
    $statusPembayaran = $_POST['editPaymentStatus'];

    $stmtUser = $conn->prepare("SELECT id FROM users WHERE username = ?");
    $stmtUser->bind_param("s", $namaCustomer);
    $stmtUser->execute();
    $stmtUser->bind_result($userId);
    $stmtUser->fetch();
    $stmtUser->close();

    $stmtBayar = $conn->prepare("SELECT id FROM pembayaran WHERE metode_pembayaran = ?");
    $stmtBayar->bind_param("s", $statusPembayaran);
    $stmtBayar->execute();
    $stmtBayar->bind_result($pembayaranId);
    $stmtBayar->fetch();
    $stmtBayar->close();

    if ($userId && $pembayaranId) {
        $stmt = $conn->prepare("UPDATE booking SET userId = ?, pembayaranId = ?, ticketId = 1, tanggalBooking = ? WHERE id = ?");
        $stmt->bind_param("iisi", $userId, $pembayaranId, $tanggalBooking, $id);
        $stmt->execute();
        $stmt->close();

        echo "<script>alert('Booking berhasil diupdate!'); window.location.href = 'booking.php';</script>";
        exit;
    }
}

// Hapus Booking
if (isset($_GET['delete'])) {
    $deleteId = $_GET['delete'];
    $stmt = $conn->prepare("DELETE FROM booking WHERE id = ?");
    $stmt->bind_param("i", $deleteId);
    $stmt->execute();
    $stmt->close();

    echo "<script>alert('Booking berhasil dihapus!'); window.location.href = 'booking.php';</script>";
    exit;
}

// Ambil data booking
$query = "
    SELECT 
        b.id,
        u.username AS namaUser,
        p.metode_pembayaran AS metodePembayaran,
        t.namaTicket,
        b.tanggalBooking
    FROM booking b
    JOIN users u ON b.userId = u.id
    JOIN pembayaran p ON b.pembayaranId = p.id
    JOIN ticket t ON b.ticketId = t.id
    ORDER BY b.tanggalBooking DESC
";
$result = $conn->query($query);
$bookings = [];
if ($result && $result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $bookings[] = $row;
    }
}

$userQuery = $conn->query("SELECT username FROM users");
$users = $userQuery->fetch_all(MYSQLI_ASSOC);

$bayarQuery = $conn->query("SELECT metode_pembayaran FROM pembayaran");
$pembayaranList = $bayarQuery->fetch_all(MYSQLI_ASSOC);
?>


<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Manajemen Booking</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="../assets/css/booking.css">
    <script src="../assets/js/booking.js"></script>
    <link rel="apple-touch-icon" sizes="180x180" href="../assets/image/apple-touch-icon.png">
    <link rel="icon" type="image/png" sizes="32x32" href="../assets/image/favicon-32x32.png">
    <link rel="icon" type="image/png" sizes="16x16" href="../assets/image/favicon-16x16.png">
    <link rel="manifest" href="../assets/image/site.webmanifest">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
</head>

<body>
    <?php include "../layouts/admin/navbar.php" ?>
    <main>
        <?php include "../layouts/admin/sidebar.php" ?>
        <div class="content">
            <div class="header-content">
                <h2>Booking</h2>
                <button class="add-booking" onclick="openModal()">+ Add Booking</button>
            </div>

            <div class="table-container">
                <table>
                    <thead>
                        <tr>
                            <th>ID Booking</th>
                            <th>Nama Customer</th>
                            <th>Metode Pembayaran</th>
                            <th>Nama Tiket</th>
                            <th>Tanggal Booking</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($bookings as $i => $booking): ?>
                            <tr>
                                <td><?= $i + 1 ?></td>
                                <td><?= htmlspecialchars($booking['namaUser']) ?></td>
                                <td><?= htmlspecialchars($booking['metodePembayaran']) ?></td>
                                <td><?= htmlspecialchars($booking['namaTicket']) ?></td>
                                <td><?= htmlspecialchars($booking['tanggalBooking']) ?></td>
                                <td class="action-buttons">
                                    <button class="detail-btn" data-id="<?= $booking['id'] ?>">Detail</button>
                                    <button class="edit-btn" data-id="<?= $booking['id'] ?>">Edit</button>
                                    <button class="delete-btn" data-id="<?= $booking['id'] ?>">Hapus</button>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Modal Add Booking -->
        <div class="modal" id="modalBooking">
            <div class="modal-content">
                <div class="modal-header">
                    <h2>Tambah Booking</h2>
                    <button class="close" onclick="closeModal()">&times;</button>
                </div>
                <form method="POST">
                    <label for="customerName">Nama Customer</label>
                    <select name="customerName" required>
                        <option value="">Pilih Customer</option>
                        <?php foreach ($users as $user): ?>
                            <option value="<?= htmlspecialchars($user['username']) ?>">
                                <?= htmlspecialchars($user['username']) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>

                    <label for="ticketCount">Jumlah Tiket</label>
                    <input type="number" name="ticketCount" min="1" required />

                    <label for="bookingDate">Tanggal Booking</label>
                    <input type="date" name="bookingDate" required />

                    <label for="paymentStatus">Status Pembayaran</label>
                    <select name="paymentStatus" required>
                        <option value="">Pilih Metode</option>
                        <?php foreach ($pembayaranList as $pembayaran): ?>
                            <option value="<?= htmlspecialchars($pembayaran['metode_pembayaran']) ?>">
                                <?= htmlspecialchars($pembayaran['metode_pembayaran']) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>

                    <div class="modal-footer btn-container">
                        <button type="button" class="close-modal" onclick="closeModal()">Batal</button>
                        <button type="submit" class="save-booking">Simpan</button>
                    </div>
                </form>
            </div>
        </div>

        <!-- Modal Edit Booking -->
        <div class="modal" id="modalEdit">
            <div class="modal-content">
                <div class="modal-header">
                    <h2>Edit Booking</h2>
                    <button class="close-edit">&times;</button>
                </div>
                <form method="POST" action="booking.php">
                    <input type="hidden" name="editId" id="editId">
                    <label for="editCustomerName">Nama Customer</label>
                    <input type="text" id="editCustomerName" name="editCustomerName" required>

                    <label for="editTicketCount">Jumlah Tiket</label>
                    <input type="number" id="editTicketCount" name="editTicketCount" min="1" required>

                    <label for="editBookingDate">Tanggal Booking</label>
                    <input type="date" id="editBookingDate" name="editBookingDate" required>

                    <label for="editPaymentStatus">Status Pembayaran</label>
                    <select id="editPaymentStatus" name="editPaymentStatus" required>
                        <option value="lunas">Lunas</option>
                        <option value="belum">Belum Lunas</option>
                    </select>

                    <div class="modal-footer btn-container">
                        <button type="button" class="close-edit">Batal</button>
                        <button type="submit" name="submitEdit" class="save-booking">Simpan Perubahan</button>
                    </div>
                </form>
            </div>
        </div>

    </main>

    <script>
        function openModal() {
            document.getElementById("modalBooking").style.display = "flex";
        }

        function closeModal() {
            document.getElementById("modalBooking").style.display = "none";
        }
    </script>

    <script>
        document.addEventListener("DOMContentLoaded", function () {
            // Buka modal edit dengan isi data
            document.querySelectorAll(".edit-btn").forEach(button => {
                button.addEventListener("click", function () {
                    const row = this.closest("tr");
                    const id = this.dataset.id;
                    const nama = row.querySelector(".nama-customer").textContent;
                    const jumlah = row.querySelector(".jumlah-tiket").textContent;
                    const tanggal = row.querySelector(".tanggal-booking").textContent;
                    const pembayaran = row.querySelector(".status-pembayaran").textContent;

                    document.getElementById("editId").value = id;
                    document.getElementById("editCustomerName").value = nama;
                    document.getElementById("editTicketCount").value = jumlah;
                    document.getElementById("editBookingDate").value = tanggal;
                    document.getElementById("editPaymentStatus").value = pembayaran;

                    document.getElementById("modalEdit").style.display = "block";
                });
            });

            // Tombol hapus
            document.querySelectorAll(".delete-btn").forEach(button => {
                button.addEventListener("click", function () {
                    const bookingId = this.dataset.id;
                    Swal.fire({
                        title: "Yakin ingin hapus?",
                        icon: "warning",
                        showCancelButton: true,
                        confirmButtonText: "Ya, hapus!",
                        cancelButtonText: "Batal"
                    }).then(result => {
                        if (result.isConfirmed) {
                            window.location.href = `booking.php?delete=${bookingId}`;
                        }
                    });
                });
            });

            // Tutup modal edit
            document.querySelectorAll(".close-edit").forEach(btn => {
                btn.addEventListener("click", () => {
                    document.getElementById("modalEdit").style.display = "none";
                });
            });
        });
    </script>


</body>

</html>
<?php
require_once '../config/connection.php';
session_start();
$hostQuery = "SELECT id, nameHost, tipeHost FROM host ORDER BY nameHost ASC";
$hostResult = $conn->query($hostQuery);

$hosts = [];
if ($hostResult && $hostResult->num_rows > 0) {
    while ($hostRow = $hostResult->fetch_assoc()) {
        $hosts[] = $hostRow;
    }
}
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
    <link rel="stylesheet" href="../assets/css/ticket.css">
    <script src="../assets/js/ticketModal.js"></script>
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
        <div class="main-content">
            <div class="image-header">
                <p><a href="/admin">Admin</a> / Ticket</p>
                <h1>Ticket Section</h1>
            </div>
            <div class="content">
                <div class="header-content">
                    <div class="search">
                        <input type="search" name="search" id="search" placeholder="Search...">
                        <svg viewBox="0 0 24 24" height="24px" class="icon" fill="none"
                            xmlns="http://www.w3.org/2000/svg">
                            <g id="SVGRepo_bgCarrier" stroke-width="0"></g>
                            <g id="SVGRepo_tracerCarrier" stroke-linecap="round" stroke-linejoin="round"></g>
                            <g id="SVGRepo_iconCarrier">
                                <path
                                    d="M11 6C13.7614 6 16 8.23858 16 11M16.6588 16.6549L21 21M19 11C19 15.4183 15.4183 19 11 19C6.58172 19 3 15.4183 3 11C3 6.58172 6.58172 3 11 3C15.4183 3 19 6.58172 19 11Z"
                                    stroke="#000000" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                </path>
                            </g>
                        </svg>
                    </div>
                    <button class="add-ticket" id="openModalBtn">New Ticket <svg height="24px" viewBox="0 0 24 24"
                            fill="none" xmlns="http://www.w3.org/2000/svg" stroke="#ffffff">
                            <g id="SVGRepo_bgCarrier" stroke-width="0"></g>
                            <g id="SVGRepo_tracerCarrier" stroke-linecap="round" stroke-linejoin="round"></g>
                            <g id="SVGRepo_iconCarrier">
                                <path fill-rule="evenodd" clip-rule="evenodd"
                                    d="M11.25 12.75V18H12.75V12.75H18V11.25H12.75V6H11.25V11.25H6V12.75H11.25Z"
                                    fill="#ffffff"></path>
                            </g>
                        </svg></button>
                </div>
                <?php
                // Query to get all tickets with host names
                $ticketQuery = "SELECT t.id, t.namaTicket, t.deskripsi, t.tipeTicket, t.kelasTicket, 
                h.nameHost, t.harga, t.tempatBerangkat, t.destinasi
                FROM ticket t
                JOIN host h ON t.hostTicket = h.id
                ORDER BY t.id DESC";
                $ticketResult = $conn->query($ticketQuery);
                ?>

                <div class="table-content">
                    <table>
                        <thead>
                            <tr>
                                <th>Name</th>
                                <th>Deskripsi</th>
                                <th>Tipe</th>
                                <th>Kelas</th>
                                <th>Host</th>
                                <th>Harga</th>
                                <th>Awal</th>
                                <th>Destinasi</th>
                                <th></th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if ($ticketResult && $ticketResult->num_rows > 0): ?>
                                <?php while ($row = $ticketResult->fetch_assoc()): ?>
                                    <tr>
                                        <td><?php echo htmlspecialchars($row['namaTicket']); ?></td>
                                        <td class="deskripsi"><?php echo htmlspecialchars($row['deskripsi']); ?></td>
                                        <td><?php echo htmlspecialchars($row['tipeTicket']); ?></td>
                                        <td><?php echo htmlspecialchars($row['kelasTicket']); ?></td>
                                        <td><?php echo htmlspecialchars($row['nameHost']); ?></td>
                                        <td><?php echo number_format((float) $row['harga'], 0, ',', '.'); ?></td>
                                        <td><?php echo htmlspecialchars($row['tempatBerangkat']); ?></td>
                                        <td><?php echo htmlspecialchars($row['destinasi']); ?></td>
                                        <td>
                                            <button class="detail-btn" data-id="<?php echo $row['id']; ?>">
                                                <svg fill="#fff" height="20px" viewBox="0 0 24 24"
                                                    xmlns="http://www.w3.org/2000/svg">
                                                    <g id="SVGRepo_bgCarrier" stroke-width="0"></g>
                                                    <g id="SVGRepo_tracerCarrier" stroke-linecap="round"
                                                        stroke-linejoin="round"></g>
                                                    <g id="SVGRepo_iconCarrier">
                                                        <path
                                                            d="M20 3H4c-1.103 0-2 .897-2 2v14c0 1.103.897 2 2 2h16c1.103 0 2-.897 2-2V5c0-1.103-.897-2-2-2zM4 19V5h16l.002 14H4z">
                                                        </path>
                                                        <path d="M6 7h12v2H6zm0 4h12v2H6zm0 4h6v2H6z"></path>
                                                    </g>
                                                </svg>
                                            </button>
                                            <button class="delete-btn" data-id="<?php echo $row['id']; ?>">
                                                <svg height="20px" viewBox="0 0 24 24" fill="none"
                                                    xmlns="http://www.w3.org/2000/svg">
                                                    <g id="SVGRepo_bgCarrier" stroke-width="0"></g>
                                                    <g id="SVGRepo_tracerCarrier" stroke-linecap="round"
                                                        stroke-linejoin="round"></g>
                                                    <g id="SVGRepo_iconCarrier">
                                                        <path
                                                            d="M10 12L14 16M14 12L10 16M4 6H20M16 6L15.7294 5.18807C15.4671 4.40125 15.3359 4.00784 15.0927 3.71698C14.8779 3.46013 14.6021 3.26132 14.2905 3.13878C13.9376 3 13.523 3 12.6936 3H11.3064C10.477 3 10.0624 3 9.70951 3.13878C9.39792 3.26132 9.12208 3.46013 8.90729 3.71698C8.66405 4.00784 8.53292 4.40125 8.27064 5.18807L8 6M18 6V16.2C18 17.8802 18 18.7202 17.673 19.362C17.3854 19.9265 16.9265 20.3854 16.362 20.673C15.7202 21 14.8802 21 13.2 21H10.8C9.11984 21 8.27976 21 7.63803 20.673C7.07354 20.3854 6.6146 19.9265 6.32698 19.362C6 18.7202 6 17.8802 6 16.2V6"
                                                            stroke="#fff" stroke-width="2" stroke-linecap="round"
                                                            stroke-linejoin="round"></path>
                                                    </g>
                                                </svg>
                                            </button>
                                            <button class="edit-btn" data-id="<?php echo $row['id']; ?>">
                                                <svg height="20px" viewBox="0 0 16 16" fill="#fff"
                                                    xmlns="http://www.w3.org/2000/svg">
                                                    <g id="SVGRepo_bgCarrier" stroke-width="0"></g>
                                                    <g id="SVGRepo_tracerCarrier" stroke-linecap="round"
                                                        stroke-linejoin="round"></g>
                                                    <g id="SVGRepo_iconCarrier">
                                                        <path d="M8.29289 3.70711L1 11V15H5L12.2929 7.70711L8.29289 3.70711Z"
                                                            fill="#fff"></path>
                                                        <path
                                                            d="M9.70711 2.29289L13.7071 6.29289L15.1716 4.82843C15.702 4.29799 16 3.57857 16 2.82843C16 1.26633 14.7337 0 13.1716 0C12.4214 0 11.702 0.297995 11.1716 0.828428L9.70711 2.29289Z"
                                                            fill="#fff"></path>
                                                    </g>
                                                </svg>
                                            </button>
                                        </td>
                                    </tr>
                                <?php endwhile; ?>
                            <?php else: ?>
                                <tr>
                                    <td colspan="9" style="text-align: center;">Tidak ada data ticket</td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </main>

    <!-- Modal Add Ticket -->
    <div id="addTicketModal" class="modal">
        <div class="modal-content">
            <div class="modal-header">
                <h2>Add New Ticket</h2>
                <span class="close">&times;</span>
            </div>
            <form id="addTicketForm" action="../handler/addticket_handler.php" method="POST"
                enctype="multipart/form-data">
                <div class="form-row">
                    <div class="form-col">
                        <div class="form-group">
                            <label for="namaTicket">Nama Ticket</label>
                            <input type="text" class="form-control" id="namaTicket" name="namaTicket" required>
                        </div>
                    </div>
                    <div class="form-col">
                        <div class="form-group">
                            <label for="tipeTicket">Tipe Ticket</label>
                            <select class="form-control" id="tipeTicket" name="tipeTicket" required>
                                <option value="">Pilih Tipe</option>
                                <option value="Pesawat">Pesawat</option>
                                <option value="Kereta">Kereta</option>
                                <option value="Bus">Bus</option>
                                <option value="Kapal">Kapal</option>
                            </select>
                        </div>
                    </div>
                </div>

                <div class="form-row">
                    <div class="form-col">
                        <div class="form-group">
                            <label for="kelasTicket">Kelas Ticket</label>
                            <select class="form-control" id="kelasTicket" name="kelasTicket" required>
                                <option value="">Pilih Kelas</option>
                                <option value="Ekonomi">Ekonomi</option>
                                <option value="Bisnis">Bisnis</option>
                                <option value="Eksekutif">Eksekutif</option>
                                <option value="First Class">First Class</option>
                            </select>
                        </div>
                    </div>
                    <div class="form-col">
                        <div class="form-group">
                            <label for="hostTicket">Host/Provider</label>
                            <select class="form-control" id="hostTicket" name="hostTicket" required>
                                <option value="">Pilih Host</option>
                                <?php foreach ($hosts as $host): ?>
                                    <option value="<?php echo $host['id']; ?>" data-type="<?php echo $host['tipeHost']; ?>">
                                        <?php echo htmlspecialchars($host['nameHost']); ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                    </div>
                </div>

                <div class="form-group">
                    <label for="deskripsi">Deskripsi</label>
                    <textarea class="form-control" id="deskripsi" name="deskripsi" rows="3" required
                        style="resize: vertical;"></textarea>
                </div>

                <div class="form-row">
                    <div class="form-col">
                        <div class="form-group">
                            <label for="luarNegeri">Tujuan</label>
                            <div class="checkbox-group">
                                <input type="checkbox" id="luarNegeri" name="luarNegeri" value="1">
                                <label for="luarNegeri" style="display: inline;">Luar Negeri</label>
                            </div>
                        </div>
                    </div>
                    <div class="form-col">
                        <div class="form-group">
                            <label for="harga">Harga (Rp)</label>
                            <input type="text" class="form-control" id="harga" name="harga" required>
                        </div>
                    </div>
                </div>

                <div class="form-row">
                    <div class="form-col">
                        <div class="form-group">
                            <label for="tempatBerangkat">Tempat Berangkat</label>
                            <select class="form-control" id="tempatBerangkat" name="tempatBerangkat" required>
                                <option value="">Pilih Tempat Berangkat</option>
                            </select>
                        </div>
                    </div>
                    <div class="form-col">
                        <div class="form-group">
                            <label for="destinasi">Destinasi</label>
                            <select class="form-control" id="destinasi" name="destinasi" required>
                                <option value="">Pilih Destinasi</option>
                            </select>
                        </div>
                    </div>
                </div>

                <div class="form-row">
                    <div class="form-col">
                        <div class="form-group">
                            <label for="tanggal">Tanggal Berangkat</label>
                            <input type="datetime-local" class="form-control" id="tanggal" name="tanggal" required>
                        </div>
                    </div>
                    <div class="form-col">
                        <div class="form-group">
                            <label for="stok">Stok Ticket</label>
                            <input type="number" class="form-control" id="stok" name="stok" min="1" required>
                        </div>
                    </div>
                </div>

                <div class="form-row">
                    <div class="form-col">
                        <div class="form-group">
                            <label for="penumpangMax">Maksimal Penumpang</label>
                            <input type="text" class="form-control" id="penumpangMax" name="penumpangMax" required>
                        </div>
                    </div>
                    <div class="form-col">
                        <div class="form-group">
                            <label for="imageTujuan">Gambar Tujuan</label>
                            <div class="file-input-container">
                                <label for="imageTujuan" class="file-input-label">Pilih File</label>
                                <input type="file" class="file-input" id="imageTujuan" name="imageTujuan"
                                    accept="image/*" required>
                                <span class="file-name" id="fileName">No file chosen</span>
                            </div>
                            <div class="image-preview-container" style="margin-top: 10px; display: none;">
                                <img id="imagePreview" src="#" alt="Preview"
                                    style="max-width: 100%; max-height: 200px; border-radius: 5px;">
                            </div>
                        </div>
                    </div>
                </div>

                <div class="btn-container">
                    <button type="submit" class="submit-btn">Simpan Ticket</button>
                </div>
            </form>
        </div>
    </div>
    <!-- Detail Ticket Modal -->
    <div id="detailTicketModal" class="modal">
        <div class="modal-content">
            <div class="modal-header">
                <h2>Detail Ticket</h2>
                <span class="close">&times;</span>
            </div>
            <div class="ticket-details">
                <div class="detail-row">
                    <div class="detail-group">
                        <label>Nama Ticket</label>
                        <p id="detail-namaTicket"></p>
                    </div>
                </div>

                <div class="detail-row">
                    <div class="detail-group">
                        <label>Deskripsi</label>
                        <p id="detail-deskripsi" class="detail-deskripsi"></p>
                    </div>
                </div>

                <div class="detail-row">
                    <div class="detail-group">
                        <label>Tipe Ticket</label>
                        <p id="detail-tipeTicket"></p>
                    </div>
                    <div class="detail-group">
                        <label>Kelas Ticket</label>
                        <p id="detail-kelasTicket"></p>
                    </div>
                </div>

                <div class="detail-row">
                    <div class="detail-group">
                        <label>Host/Provider</label>
                        <p id="detail-hostTicket"></p>
                    </div>
                    <div class="detail-group">
                        <label>Harga (Rp)</label>
                        <p id="detail-harga"></p>
                    </div>
                </div>

                <div class="detail-row">
                    <div class="detail-group">
                        <label>Tempat Berangkat</label>
                        <p id="detail-tempatBerangkat"></p>
                    </div>
                    <div class="detail-group">
                        <label>Destinasi</label>
                        <p id="detail-destinasi"></p>
                    </div>
                </div>

                <div class="detail-row">
                    <div class="detail-group">
                        <label>Tanggal Berangkat</label>
                        <p id="detail-tanggal"></p>
                    </div>
                    <div class="detail-group">
                        <label>Stok Ticket</label>
                        <p id="detail-stok"></p>
                    </div>
                </div>

                <div class="detail-row">
                    <div class="detail-group">
                        <label>Maksimal Penumpang</label>
                        <p id="detail-penumpangMax"></p>
                    </div>
                </div>

                <div class="detail-row" id="detail-image-container" style="display: none;">
                    <div class="detail-group">
                        <label>Gambar Tujuan</label>
                        <div class="image-container">
                            <img id="detail-image" src="#" alt="Gambar Tujuan">
                        </div>
                    </div>
                </div>

                <div class="btn-container">
                    <button class="close-btn">Tutup</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Edit Ticket Modal -->
    <div id="editTicketModal" class="modal">
        <div class="modal-content">
            <div class="modal-header">
                <h2>Edit Ticket</h2>
                <span class="close">&times;</span>
            </div>
            <form id="editTicketForm" action="../handler/edit_ticket_handler.php" method="POST"
                enctype="multipart/form-data">
                <input type="hidden" id="edit-id" name="id">

                <div class="form-row">
                    <div class="form-col">
                        <div class="form-group">
                            <label for="edit-namaTicket">Nama Ticket</label>
                            <input type="text" class="form-control" id="edit-namaTicket" name="namaTicket" required>
                        </div>
                    </div>
                    <div class="form-col">
                        <div class="form-group">
                            <label for="edit-tipeTicket">Tipe Ticket</label>
                            <select class="form-control" id="edit-tipeTicket" name="tipeTicket" required>
                                <option value="">Pilih Tipe</option>
                                <option value="Pesawat">Pesawat</option>
                                <option value="Kereta">Kereta</option>
                                <option value="Bus">Bus</option>
                                <option value="Kapal">Kapal</option>
                            </select>
                        </div>
                    </div>
                </div>

                <div class="form-row">
                    <div class="form-col">
                        <div class="form-group">
                            <label for="edit-kelasTicket">Kelas Ticket</label>
                            <select class="form-control" id="edit-kelasTicket" name="kelasTicket" required>
                                <option value="">Pilih Kelas</option>
                                <option value="Ekonomi">Ekonomi</option>
                                <option value="Bisnis">Bisnis</option>
                                <option value="Eksekutif">Eksekutif</option>
                                <option value="First Class">First Class</option>
                            </select>
                        </div>
                    </div>
                    <div class="form-col">
                        <div class="form-group">
                            <label for="edit-hostTicket">Host/Provider</label>
                            <select class="form-control" id="edit-hostTicket" name="hostTicket" required>
                                <option value="">Pilih Host</option>
                                <?php foreach ($hosts as $host): ?>
                                    <option value="<?php echo $host['id']; ?>" data-type="<?php echo $host['tipeHost']; ?>">
                                        <?php echo htmlspecialchars($host['nameHost']); ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                    </div>
                </div>

                <div class="form-group">
                    <label for="edit-deskripsi">Deskripsi</label>
                    <textarea class="form-control" id="edit-deskripsi" name="deskripsi" rows="3" required
                        style="resize: vertical;"></textarea>
                </div>

                <div class="form-row">
                    <div class="form-col">
                        <div class="form-group">
                            <label for="edit-luarNegeri">Tujuan</label>
                            <div class="checkbox-group">
                                <input type="checkbox" id="edit-luarNegeri" name="luarNegeri" value="1">
                                <label for="edit-luarNegeri" style="display: inline;">Luar Negeri</label>
                            </div>
                        </div>
                    </div>
                    <div class="form-col">
                        <div class="form-group">
                            <label for="edit-harga">Harga (Rp)</label>
                            <input type="text" class="form-control" id="edit-harga" name="harga" required>
                        </div>
                    </div>
                </div>

                <div class="form-row">
                    <div class="form-col">
                        <div class="form-group">
                            <label for="edit-tempatBerangkat">Tempat Berangkat</label>
                            <select class="form-control" id="edit-tempatBerangkat" name="tempatBerangkat" required>
                                <option value="">Pilih Tempat Berangkat</option>
                            </select>
                        </div>
                    </div>
                    <div class="form-col">
                        <div class="form-group">
                            <label for="edit-destinasi">Destinasi</label>
                            <select class="form-control" id="edit-destinasi" name="destinasi" required>
                                <option value="">Pilih Destinasi</option>
                            </select>
                        </div>
                    </div>
                </div>

                <div class="form-row">
                    <div class="form-col">
                        <div class="form-group">
                            <label for="edit-tanggal">Tanggal Berangkat</label>
                            <input type="datetime-local" class="form-control" id="edit-tanggal" name="tanggal" required>
                        </div>
                    </div>
                    <div class="form-col">
                        <div class="form-group">
                            <label for="edit-stok">Stok Ticket</label>
                            <input type="number" class="form-control" id="edit-stok" name="stok" min="1" required>
                        </div>
                    </div>
                </div>

                <div class="form-row">
                    <div class="form-col">
                        <div class="form-group">
                            <label for="edit-penumpangMax">Maksimal Penumpang</label>
                            <input type="text" class="form-control" id="edit-penumpangMax" name="penumpangMax" required>
                        </div>
                    </div>
                    <div class="form-col">
                        <div class="form-group">
                            <label for="edit-imageTujuan">Gambar Tujuan</label>
                            <div id="current-image-container" style="margin-bottom: 10px; display: none;">
                                <p>Gambar saat ini:</p>
                                <img id="current-image" src="#" alt="Current Image"
                                    style="max-width: 100%; max-height: 150px; border-radius: 5px;">
                            </div>
                            <div class="file-input-container">
                                <label for="edit-imageTujuan" class="file-input-label">Ganti Gambar</label>
                                <input type="file" class="file-input" id="edit-imageTujuan" name="imageTujuan"
                                    accept="image/*">
                                <span class="file-name" id="edit-fileName">No file chosen</span>
                            </div>
                            <div class="image-preview-container" id="edit-imagePreviewContainer"
                                style="margin-top: 10px; display: none;">
                                <img id="edit-imagePreview" src="#" alt="Preview"
                                    style="max-width: 100%; max-height: 150px; border-radius: 5px;">
                            </div>
                        </div>
                    </div>
                </div>

                <div class="btn-container">
                    <button type="submit" class="submit-btn">Update Ticket</button>
                </div>
            </form>
        </div>
    </div>

    <!-- Delete Confirmation Modal -->
    <div id="deleteTicketModal" class="modal">
        <div class="modal-content delete-modal-content">
            <div class="modal-header">
                <h2>Hapus Ticket</h2>
                <span class="close">&times;</span>
            </div>
            <div class="delete-confirmation">
                <p>Anda yakin ingin menghapus ticket "<span id="delete-ticketName"></span>"?</p>
                <p class="warning">Perhatian: Tindakan ini tidak dapat dibatalkan!</p>

                <form id="deleteTicketForm" action="../handler/delete_ticket_handler.php" method="POST">
                    <input type="hidden" id="delete-id" name="id">
                    <div class="btn-container">
                        <button type="button" class="cancel-btn close">Batal</button>
                        <button type="submit" class="delete-confirm-btn">Hapus</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script src="../assets/js/ticketModal.js">
</body >

</html >
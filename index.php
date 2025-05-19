<?php
include "./config/connection.php";
session_start();

// Fetch tickets from the database - default to Pesawat type
$ticketType = isset($_GET['type']) ? $_GET['type'] : 'Pesawat';
$query = "SELECT * FROM ticket WHERE tipeTicket = ? ORDER BY tanggal DESC LIMIT 10";
$stmt = $conn->prepare($query);
$stmt->bind_param("s", $ticketType);
$stmt->execute();
$result = $stmt->get_result();
$tickets = [];
while ($row = $result->fetch_assoc()) {
    $tickets[] = $row;
}
$stmt->close();

// Function to format price with IDR and commas
function formatPrice($price)
{
    return "IDR " . number_format((float) $price, 0, ',', '.');
}

// Function to format date
function formatDate($date)
{
    $dateObj = new DateTime($date);
    return $dateObj->format('d M y');
}

// Function to get host information
function getHostInfo($conn, $hostId)
{
    $query = "SELECT * FROM host WHERE id = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("i", $hostId);
    $stmt->execute();
    $result = $stmt->get_result();
    $host = $result->fetch_assoc();
    $stmt->close();
    return $host;
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SkyRail</title>
    <link rel="stylesheet" href="assets/css/style.css">
    <link rel="apple-touch-icon" sizes="180x180" href="assets/image/apple-touch-icon.png">
    <link rel="icon" type="image/png" sizes="32x32" href="assets/image/favicon-32x32.png">
    <link rel="icon" type="image/png" sizes="16x16" href="assets/image/favicon-16x16.png">
    <link rel="manifest" href="assets/image/site.webmanifest">
    <style>
        .smaller-text {
            font-size: 0.85em;
        }

        .text-card h3 {
            overflow: hidden;
            text-overflow: ellipsis;
            white-space: nowrap;
            max-width: 100px;
        }

        .text-card h3.smaller-text {
            max-width: 110px;
        }

        .no-tickets {
            width: 100%;
            text-align: center;
            padding: 30px 0;
        }

        .ticket-card {
            cursor: pointer;
            transition: transform 0.2s ease, box-shadow 0.2s ease;
        }

        .ticket-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.1);
        }
    </style>
</head>

<body>
    <div class="header">
        <div class="navbar">
            <h1>SKYRAIL</h1>
            <nav class="nav-container">
                <ul class="nav-row">
                    <li><a href="about">About Us</a></li>
                    <li class="nav-ticket">
                        <div class="ticket-title" id="btn-ticket">
                            Ticket
                            <svg xmlns="http://www.w3.org/2000/svg" height="24px" viewBox="0 -960 960 960" width="24px"
                                fill="#dbd5d5">
                                <path d="M480-528 296-344l-56-56 240-240 240 240-56 56-184-184Z" />
                            </svg>
                        </div>
                        <div class="ticket-dropdown">
                            <ul>
                                <li><a href="?type=Pesawat">Pesawat</a></li>
                                <li><a href="?type=Kapal">Kapal</a></li>
                                <li><a href="?type=Kereta">Kereta</a></li>
                                <li><a href="?type=Bus Travel">Bus Travel</a></li>
                            </ul>
                        </div>
                    </li>
                    <li><a href="contact">Contact Us</a></li>
                </ul>
            </nav>
            <?php if (isset($_SESSION["logged_in"]) && $_SESSION["logged_in"] == true): ?>
                <div class="profile">
                    <div class="profile-item" id="profile">
                        <img src="./assets/image/default-avatar.png" alt="" class="avatar">
                        <h1><?= htmlspecialchars($_SESSION['username']) ?></h1>
                    </div>
                    <div class="profile-dropdown">
                        <ul class="dropdown">
                            <li><a href="logout">Logout</a></li>
                        </ul>
                    </div>
                </div>
            <?php else: ?>
                <a href="login" class="login-btn">Sign In</a>
            <?php endif; ?>
        </div>
        <div class="title-container">
            <div class="title">
                <h1>Hai
                    <?php echo (isset($_SESSION['logged_in']) && $_SESSION['logged_in'] === true) ? htmlspecialchars($_SESSION['username']) : "Kamu"; ?>,
                    <span>mau pesan Ticket apa?</span>
                </h1>
                <p>Pesan saja menggunakan - skyrail.com</p>
            </div>
            <div class="btn-category">
                <div class="btn-category-item">
                    <div class="icon">
                        <svg xmlns="http://www.w3.org/2000/svg" height="27px" viewBox="0 -960 960 960" width="24px"
                            fill="#dbd5d5">
                            <path
                                d="M280-80v-100l120-84v-144L80-280v-120l320-224v-176q0-33 23.5-56.5T480-880q33 0 56.5 23.5T560-800v176l320 224v120L560-408v144l120 84v100l-200-60-200 60Z" />
                        </svg>
                    </div>
                    <h3>Pesawat</h3>
                </div>
                <div class="btn-category-item">
                    <div class="icon">
                        <svg xmlns="http://www.w3.org/2000/svg" height="27px" viewBox="0 -960 960 960" width="24px"
                            fill="#dbd5d5">
                            <path
                                d="M160-340v-380q0-53 27.5-84.5t72.5-48q45-16.5 102.5-22T480-880q66 0 124.5 5.5t102 22q43.5 16.5 68.5 48t25 84.5v380q0 59-40.5 99.5T660-200l60 60v20h-80l-80-80H400l-80 80h-80v-20l60-60q-59 0-99.5-40.5T160-340Zm320-460q-106 0-155 12.5T258-760h448q-15-17-64.5-28.5T480-800ZM240-560h200v-120H240v120Zm420 80H240h480-60Zm-140-80h200v-120H520v120ZM340-320q26 0 43-17t17-43q0-26-17-43t-43-17q-26 0-43 17t-17 43q0 26 17 43t43 17Zm280 0q26 0 43-17t17-43q0-26-17-43t-43-17q-26 0-43 17t-17 43q0 26 17 43t43 17Zm-320 40h360q26 0 43-17t17-43v-140H240v140q0 26 17 43t43 17Zm180-480h226-448 222Z" />
                        </svg>
                    </div>
                    <h3>Kereta</h3>
                </div>
                <div class="btn-category-item">
                    <div class="icon">
                        <svg xmlns="http://www.w3.org/2000/svg" height="27px" viewBox="0 -960 960 960" width="24px"
                            fill="#dbd5d5">
                            <path
                                d="M240-200v40q0 17-11.5 28.5T200-120h-40q-17 0-28.5-11.5T120-160v-320l84-240q6-18 21.5-29t34.5-11h440q19 0 34.5 11t21.5 29l84 240v320q0 17-11.5 28.5T800-120h-40q-17 0-28.5-11.5T720-160v-40H240Zm-8-360h496l-42-120H274l-42 120Zm-32 80v200-200Zm100 160q25 0 42.5-17.5T360-380q0-25-17.5-42.5T300-440q-25 0-42.5 17.5T240-380q0 25 17.5 42.5T300-320Zm360 0q25 0 42.5-17.5T720-380q0-25-17.5-42.5T660-440q-25 0-42.5 17.5T600-380q0 25 17.5 42.5T660-320Zm-460 40h560v-200H200v200Z" />
                        </svg>
                    </div>
                    <h3>Bus Travel</h3>
                </div>
                <div class="btn-category-item">
                    <div class="icon">
                        <svg xmlns="http://www.w3.org/2000/svg" height="27px" viewBox="0 -960 960 960" width="24px"
                            fill="#dbd5d5">
                            <path
                                d="M152-80h-32v-80h32q48 0 91.5-10.5T341-204q38 19 66.5 31.5T480-160q44 0 72.5-12.5T619-204q53 23 97.5 33.5T809-160h31v80h-31q-49 0-95.5-9T622-116q-40 19-73 27t-69 8q-36 0-68.5-8T339-116q-45 18-91.5 27T152-80Zm328-160q-60 0-105-40l-45-40q-27 27-60.5 46T198-247l-85-273q-5-17 3-31t25-19l59-16v-134q0-33 23.5-56.5T280-800h100v-80h200v80h100q33 0 56.5 23.5T760-720v134l59 16q17 5 25 19t3 31l-85 273q-38-8-71.5-27T630-320l-45 40q-45 40-105 40Zm2-80q31 0 55-20.5t44-43.5l46-53 41 42q11 11 22.5 20.5T713-355l46-149-279-73-278 73 46 149q11-10 22.5-19.5T293-395l41-42 46 53q20 24 45 44t57 20ZM280-607l200-53 200 53v-113H280v113Zm201 158Z" />
                        </svg>
                    </div>
                    <h3>Kapal</h3>
                </div>
            </div>
        </div>
    </div>
    <main class="main">
        <div class="ticketDaerah">
            <div class="ticket-header">
                <h1>Cek ticket yang ada di skyrail</h1>
                <ul class="ticket-list">
                    <li class="<?= $ticketType == 'Pesawat' ? 'active' : '' ?>"
                        onclick="window.location.href='?type=Pesawat'">Pesawat</li>
                    <li class="<?= $ticketType == 'Kereta' ? 'active' : '' ?>"
                        onclick="window.location.href='?type=Kereta'">Kereta</li>
                    <li class="<?= $ticketType == 'Bus Travel' ? 'active' : '' ?>"
                        onclick="window.location.href='?type=Bus Travel'">Bus Travel</li>
                    <li class="<?= $ticketType == 'Kapal' ? 'active' : '' ?>"
                        onclick="window.location.href='?type=Kapal'">Kapal</li>
                </ul>
            </div>
            <div class="ticket-main">
                <svg class="arrow-left" id="arrow-left" xmlns="http://www.w3.org/2000/svg" height="24px"
                    viewBox="0 -960 960 960" width="24px" fill="#000000">
                    <path d="m313-440 224 224-57 56-320-320 320-320 57 56-224 224h487v80H313Z" />
                </svg>
                <div class="ticket-section">
                    <?php if (count($tickets) > 0): ?>
                        <?php foreach ($tickets as $ticket): ?>
                            <div class="ticket-card" onclick="window.location.href='search.php?id=<?= $ticket['id'] ?>'">
                                <img src="./uploads/tickets/<?= htmlspecialchars($ticket['imageTujuan']) ?>"
                                    alt="<?= htmlspecialchars($ticket['destinasi']) ?>">
                                <div class="main-card">
                                    <div class="text-card">
                                        <h3 class="<?= strlen($ticket['tempatBerangkat']) > 5 ? 'smaller-text' : '' ?>">
                                            <?= htmlspecialchars($ticket['tempatBerangkat']) ?></h3>
                                        <svg xmlns="http://www.w3.org/2000/svg" height="24px" viewBox="0 -960 960 960"
                                            width="24px" fill="#000">
                                            <path d="M647-440H160v-80h487L423-744l57-56 320 320-320 320-57-56 224-224Z" />
                                        </svg>
                                        <h3 class="<?= strlen($ticket['destinasi']) > 5 ? 'smaller-text' : '' ?>">
                                            <?= htmlspecialchars($ticket['destinasi']) ?></h3>
                                    </div>
                                    <div class="info-card">
                                        <div class="detail">
                                            <?php
                                            $host = getHostInfo($conn, $ticket['hostTicket']);
                                            $logoPath = isset($host['logo']) ? $host['logo'] : 'AirAsia_New_Logo.svg.webp';
                                            $hostName = isset($host['namaHost']) ? $host['namaHost'] : 'Air Asia';
                                            ?>
                                            <img src="./uploads/hosts/<?= htmlspecialchars($logoPath) ?>"
                                                alt="<?= htmlspecialchars($hostName) ?>">
                                            <h4><?= htmlspecialchars($hostName) ?> •
                                                <?= htmlspecialchars($ticket['kelasTicket']) ?></h4>
                                        </div>
                                        <div class="harga">
                                            <h2><?= formatPrice($ticket['harga']) ?></h2>
                                            <div class="tanggal">
                                                <svg xmlns="http://www.w3.org/2000/svg" height="19px" viewBox="0 -960 960 960"
                                                    width="24px" fill="#dbd5d5">
                                                    <path
                                                        d="M200-80q-33 0-56.5-23.5T120-160v-560q0-33 23.5-56.5T200-800h40v-80h80v80h320v-80h80v80h40q33 0 56.5 23.5T840-720v560q0 33-23.5 56.5T760-80H200Zm0-80h560v-400H200v400Zm0-480h560v-80H200v80Zm0 0v-80 80Z" />
                                                </svg>
                                                <h3><?= formatDate($ticket['tanggal']) ?></h3>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <div class="no-tickets">
                            <h3>Tidak ada ticket <?= htmlspecialchars($ticketType) ?> tersedia saat ini</h3>
                        </div>
                    <?php endif; ?>
                </div>
                <svg class="arrow-right" id="arrow-right" xmlns="http://www.w3.org/2000/svg" height="24px"
                    viewBox="0 -960 960 960" width="24px" fill="#000000">
                    <path d="M647-440H160v-80h487L423-744l57-56 320 320-320 320-57-56 224-224Z" />
                </svg>
            </div>
        </div>
    </main>
</body>

<script src="assets/js/scripts.js"></script>

</html>
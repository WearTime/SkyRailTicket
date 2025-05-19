<?php
include "./config/connection.php";
session_start();

// Fungsi untuk mengambil semua tiket berdasarkan kriteria pencarian
function searchTickets($conn, $origin = '', $destination = '', $date = '', $class = '', $tripType = 'one-way')
{
    $query = "SELECT t.*, h.namaHost, h.logo FROM ticket t 
              LEFT JOIN host h ON t.hostTicket = h.id 
              WHERE 1=1";
    $params = [];
    $types = "";

    // Filter berdasarkan tempat berangkat
    if (!empty($origin)) {
        $query .= " AND (t.tempatBerangkat LIKE ? OR t.tempatBerangkat = ?)";
        $params[] = "%" . $origin . "%";
        $params[] = $origin;
        $types .= "ss";
    }

    // Filter berdasarkan destinasi
    if (!empty($destination)) {
        $query .= " AND (t.destinasi LIKE ? OR t.destinasi = ?)";
        $params[] = "%" . $destination . "%";
        $params[] = $destination;
        $types .= "ss";
    }

    // Filter berdasarkan tanggal
    if (!empty($date)) {
        $query .= " AND DATE(t.tanggal) = ?";
        $params[] = $date;
        $types .= "s";
    }

    // Filter berdasarkan kelas
    if (!empty($class) && $class !== 'all') {
        $query .= " AND t.kelasTicket = ?";
        $params[] = $class;
        $types .= "s";
    }

    // Urutkan berdasarkan harga
    $query .= " ORDER BY t.harga ASC";

    $stmt = $conn->prepare($query);

    if (!empty($params)) {
        $stmt->bind_param($types, ...$params);
    }

    $stmt->execute();
    $result = $stmt->get_result();
    $tickets = [];

    while ($row = $result->fetch_assoc()) {
        $tickets[] = $row;
    }

    $stmt->close();
    return $tickets;
}

$ticketId = null; // Tambahkan default value

if (!isset($_GET['id'])) {
    $origin = isset($_GET['origin']) ? $_GET['origin'] : '';
    $destination = isset($_GET['destination']) ? $_GET['destination'] : '';
    $searchDate = isset($_GET['depart_date']) ? $_GET['depart_date'] : date('Y-m-d');
    $class = isset($_GET['class']) ? $_GET['class'] : '';
    $tripType = isset($_GET['trip_type']) ? $_GET['trip_type'] : 'one-way';
    $passengers = isset($_GET['passengers']) ? (int) $_GET['passengers'] : 1;

    // Ambil hasil pencarian
    $searchResults = searchTickets($conn, $origin, $destination, $searchDate, $class, $tripType);

    // Set default ticket untuk keperluan display
    $ticket = [
        'tanggal' => $searchDate,
        'kelasTicket' => $class ?: 'ekonomi',
        'tempatBerangkat' => $origin,
        'destinasi' => $destination,
        'hostTicket' => 1 // default host
    ];
} else {
    // Jika ada ID ticket (detail ticket), ambil ticket spesifik
    $ticketId = $_GET['id'];
    $query = "SELECT * FROM ticket WHERE id = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("i", $ticketId);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows === 0) {
        header("Location: index.php");
        exit;
    }

    $ticket = $result->fetch_assoc();
    $stmt->close();

    // Set default values untuk pencarian
    $origin = $ticket['tempatBerangkat'];
    $destination = $ticket['destinasi'];
    $searchDate = $ticket['tanggal'];
    $class = $ticket['kelasTicket'];
    $tripType = 'one-way';
    $passengers = 1;

    // Untuk detail ticket, simpan dalam array
    $searchResults = [$ticket];
}


// Get host details
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

$host = getHostInfo($conn, $ticket['hostTicket']);
$hostName = isset($host['namaHost']) ? $host['namaHost'] : 'Air Asia';
$logoPath = isset($host['logo']) ? $host['logo'] : 'AirAsia_New_Logo.svg.webp';

$price = isset($ticket['hargaTicket']) ? $ticket['hargaTicket'] : (isset($ticket['harga']) ? $ticket['harga'] : 0);

// Check if we have separate time columns
function formatTicketTime($ticket)
{
    $hasTimeColumns = isset($ticket['jamBerangkat']) && isset($ticket['jamTiba']);

    if ($hasTimeColumns) {
        $departureTime = date('H:i', strtotime($ticket['jamBerangkat']));
        $arrivalTime = date('H:i', strtotime($ticket['jamTiba']));
        $departureDateTime = new DateTime($ticket['tanggal'] . ' ' . $ticket['jamBerangkat']);
        $arrivalDateTime = new DateTime($ticket['tanggal'] . ' ' . $ticket['jamTiba']);
    } else {
        $departureTime = date('H:i', strtotime($ticket['tanggal']));
        $departureDateTime = new DateTime($ticket['tanggal']);
        $arrivalDateTime = new DateTime($ticket['tanggal']);
        $arrivalDateTime->modify('+2 hours');
        $arrivalTime = $arrivalDateTime->format('H:i');
    }

    if ($arrivalDateTime < $departureDateTime) {
        $arrivalDateTime->modify('+1 day');
    }

    $interval = $departureDateTime->diff($arrivalDateTime);
    $duration = $interval->format('%hj %im');

    return [
        'departureTime' => $departureTime,
        'arrivalTime' => $arrivalTime,
        'duration' => $duration
    ];
}
// Price information
$originalPrice = $price * 1.02;
// Format price
function formatPrice($price)
{
    return "IDR " . number_format((float) $price, 0, ',', '.');
}

// Format date
function formatDate($date)
{
    $dateObj = new DateTime($date);
    return $dateObj->format('d M Y');
}

// Set default values for display
$departDate = $ticket['tanggal'];
$departDateDisplay = date('d M Y', strtotime($departDate));

// Calculate a return date (2 days after departure) for round trips
$returnDate = date('Y-m-d', strtotime($departDate . ' + 2 days'));
$returnDateDisplay = date('d M Y', strtotime($returnDate));

// Get ticket class
$ticketClass = $ticket['kelasTicket'];

// Default values for search form
$origin = isset($_GET['origin']) ? $_GET['origin'] : $ticket['tempatBerangkat'];
$destination = isset($_GET['destination']) ? $_GET['destination'] : $ticket['destinasi'];
$tripType = isset($_GET['trip_type']) ? $_GET['trip_type'] : 'one-way';
$passengers = isset($_GET['passengers']) ? $_GET['passengers'] : 1;
$class = isset($_GET['class']) ? $_GET['class'] : $ticketClass;
$searchDate = isset($_GET['depart_date']) ? $_GET['depart_date'] : $departDate;

// Indonesian month names
$months_id = [
    1 => 'Januari',
    2 => 'Februari',
    3 => 'Maret',
    4 => 'April',
    5 => 'Mei',
    6 => 'Juni',
    7 => 'Juli',
    8 => 'Agustus',
    9 => 'September',
    10 => 'Oktober',
    11 => 'November',
    12 => 'Desember'
];

$currentMonth = isset($_GET['month']) ? $_GET['month'] : date('n');
$currentYear = isset($_GET['year']) ? $_GET['year'] : date('Y');
$currentMonthName = $months_id[$currentMonth];

function generateCalendarDays($month, $year)
{
    $daysInMonth = cal_days_in_month(CAL_GREGORIAN, $month, $year);
    $firstDay = date('w', mktime(0, 0, 0, $month, 1, $year));
    $days = [];

    for ($i = 0; $i < $firstDay; $i++) {
        $days[] = '';
    }

    for ($day = 1; $day <= $daysInMonth; $day++) {
        $days[] = $day;
    }

    return $days;
}

$calendarDays = generateCalendarDays($currentMonth, $currentYear);

function getRandomPrice()
{
    $prices = ['1,1jt', '1,2jt', '1,3jt', '1,4jt'];
    return $prices[array_rand($prices)];
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Search Tickets - SkyRail</title>
    <link rel="stylesheet" href="assets/css/search.css">
    <link rel="apple-touch-icon" sizes="180x180" href="assets/image/apple-touch-icon.png">
    <link rel="icon" type="image/png" sizes="32x32" href="assets/image/favicon-32x32.png">
    <link rel="icon" type="image/png" sizes="16x16" href="assets/image/favicon-16x16.png">
    <link rel="manifest" href="assets/image/site.webmanifest">
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
                                fill="black">
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
    </div>

    <!-- <div class="search-container">
        <form class="search-form" method="GET" action="">
            <input type="hidden" name="id" value="<?= $ticketId ?>">

            <div class="search-form-item origin">
                <label for="origin">Dari</label>
                <input type="text" id="origin" name="origin" value="<?= htmlspecialchars($origin) ?>"
                    placeholder="Pilih kota asal">
            </div>

            <button type="button" class="swap-button" id="swap-button" onclick="swapDestinations()">
                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <path
                        d="M21 12C21 16.9706 16.9706 21 12 21C9.69494 21 7.59227 20.1334 6 18.7083L3 16M3 12C3 7.02944 7.02944 3 12 3C14.3051 3 16.4077 3.86656 18 5.29168L21 8M3 21V16M3 16H8M21 3V8M21 8H16"
                        stroke="#000000" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                </svg>
            </button>

            <div class="search-form-item destination">
                <label for="destination">Ke</label>
                <input type="text" id="destination" name="destination" value="<?= htmlspecialchars($destination) ?>"
                    placeholder="Pilih kota tujuan">
            </div>

            <div class="search-form-item date">
                <label for="date-display">Tanggal</label>
                <div class="dropdown-display" id="date-display" onclick="toggleDateDropdown()">
                    <span id="date-text">
                        <?php
                        $formatDate = date('D, d M y', strtotime($searchDate));
                        echo $tripType === 'round-trip' ? $formatDate . ' (Pulang-Pergi)' : $formatDate . ' (Sekali Jalan)';
                        ?>
                    </span>
                </div>

                <div class="date-dropdown" id="date-dropdown">
                    <div class="trip-type-tabs">
                        <div class="trip-type-tab <?= $tripType === 'one-way' ? 'active' : '' ?>"
                            onclick="setTripType('one-way')">
                            Sekali Jalan
                        </div>
                        <div class="trip-type-tab <?= $tripType === 'round-trip' ? 'active' : '' ?>"
                            onclick="setTripType('round-trip')">
                            Pulang-Pergi
                        </div>
                    </div>

                    <div class="date-pickers">
                        <div class="date-picker">
                            <div class="date-picker-header">
                                <h3 class="month-title"><?= $currentMonthName . ' ' . $currentYear ?></h3>
                                <div class="calendar-navigation">
                                    <button type="button" class="cal-nav-btn" onclick="previousMonth()">‹</button>
                                    <button type="button" class="cal-nav-btn" onclick="nextMonth()">›</button>
                                </div>
                            </div>
                            <div class="weekdays">
                                <div>Min</div>
                                <div>Sen</div>
                                <div>Sel</div>
                                <div>Rab</div>
                                <div>Kam</div>
                                <div>Jum</div>
                                <div>Sab</div>
                            </div>
                            <div class="days" id="departure-days">
                                <?php foreach ($calendarDays as $day): ?>
                                    <div class="day <?= $day && date('Y-m-d') === date('Y-m-' . sprintf('%02d', $day)) ? 'today' : '' ?> <?= $day && date('Y-m-d', strtotime($searchDate)) === date($currentYear . '-' . sprintf('%02d', $currentMonth) . '-' . sprintf('%02d', $day)) ? 'selected' : '' ?>"
                                        <?= $day ? 'onclick="selectDate(\'' . $currentYear . '-' . sprintf('%02d', $currentMonth) . '-' . sprintf('%02d', $day) . '\', \'departure\')"' : '' ?>>
                                        <?php if ($day): ?>
                                            <?= $day ?>
                                            <span class="price"><?= getRandomPrice() ?></span>
                                        <?php endif; ?>
                                    </div>
                                <?php endforeach; ?>
                            </div>
                        </div>

                        <div class="date-picker" id="return-picker"
                            style="<?= $tripType === 'one-way' ? 'display: none;' : '' ?>">
                            <div class="date-picker-header">
                                <h3 class="month-title"><?= $currentMonthName . ' ' . $currentYear ?></h3>
                                <div class="calendar-navigation">
                                    <button type="button" class="cal-nav-btn" onclick="previousMonthReturn()">‹</button>
                                    <button type="button" class="cal-nav-btn" onclick="nextMonthReturn()">›</button>
                                </div>
                            </div>
                            <div class="weekdays">
                                <div>Min</div>
                                <div>Sen</div>
                                <div>Sel</div>
                                <div>Rab</div>
                                <div>Kam</div>
                                <div>Jum</div>
                                <div>Sab</div>
                            </div>
                            <div class="days" id="return-days">
                                <?php foreach ($calendarDays as $day): ?>
                                    <div class="day" <?= $day ? 'onclick="selectDate(\'' . $currentYear . '-' . sprintf('%02d', $currentMonth) . '-' . sprintf('%02d', $day) . '\', \'return\')"' : '' ?>>
                                        <?php if ($day): ?>
                                            <?= $day ?>
                                            <span class="price"><?= getRandomPrice() ?></span>
                                        <?php endif; ?>
                                    </div>
                                <?php endforeach; ?>
                            </div>
                        </div>
                    </div>

                    <div class="date-select-footer">
                        <button type="button" class="apply-dates-btn" onclick="applyDates()">Terapkan</button>
                    </div>
                </div>
            </div>

            <div class="search-form-item passengers">
                <label for="passengers-display">Penumpang & Kelas</label>
                <div class="dropdown-display" id="passengers-display" onclick="togglePassengerDropdown()">
                    <span id="passenger-text"><?= $passengers ?> Penumpang, <?= ucfirst($class) ?></span>
                </div>

                <div class="passenger-dropdown" id="passenger-dropdown">
                    <div class="passenger-type">
                        <div class="passenger-info">
                            <span class="passenger-label">Dewasa</span>
                            <span class="passenger-age">12 tahun ke atas</span>
                        </div>
                        <div class="counter">
                            <button type="button" class="counter-btn" onclick="decrementPassenger('adults')"
                                id="adults-decrement">-</button>
                            <span class="passenger-count" id="adults-count"><?= $passengers ?></span>
                            <button type="button" class="counter-btn" onclick="incrementPassenger('adults')">+</button>
                        </div>
                    </div>

                    <div class="passenger-type">
                        <div class="passenger-info">
                            <span class="passenger-label">Anak</span>
                            <span class="passenger-age">2-11 tahun</span>
                        </div>
                        <div class="counter">
                            <button type="button" class="counter-btn" onclick="decrementPassenger('children')"
                                id="children-decrement">-</button>
                            <span class="passenger-count" id="children-count">0</span>
                            <button type="button" class="counter-btn"
                                onclick="incrementPassenger('children')">+</button>
                        </div>
                    </div>

                    <div class="passenger-type">
                        <div class="passenger-info">
                            <span class="passenger-label">Bayi</span>
                            <span class="passenger-age">Di bawah 2 tahun</span>
                        </div>
                        <div class="counter">
                            <button type="button" class="counter-btn" onclick="decrementPassenger('infants')"
                                id="infants-decrement">-</button>
                            <span class="passenger-count" id="infants-count">0</span>
                            <button type="button" class="counter-btn" onclick="incrementPassenger('infants')">+</button>
                        </div>
                    </div>

                    <div class="cabin-class">
                        <div class="cabin-class-label">Kelas Kabin</div>
                        <div class="cabin-options">
                            <div class="cabin-option <?= $class === 'ekonomi' ? 'selected' : '' ?>"
                                onclick="selectCabinClass('ekonomi')">
                                Ekonomi
                            </div>
                            <div class="cabin-option <?= $class === 'bisnis' ? 'selected' : '' ?>"
                                onclick="selectCabinClass('bisnis')">
                                Bisnis
                            </div>
                            <div class="cabin-option <?= $class === 'first' ? 'selected' : '' ?>"
                                onclick="selectCabinClass('first')">
                                First Class
                            </div>
                            <div class="cabin-option <?= $class === 'premium' ? 'selected' : '' ?>"
                                onclick="selectCabinClass('premium')">
                                Premium
                            </div>
                        </div>
                    </div>

                    <div class="passenger-footer">
                        <button type="button" class="apply-passengers-btn" onclick="applyPassengers()">Terapkan</button>
                    </div>
                </div>
            </div>

            <button type="submit" class="search-button">Cari</button>

            <input type="hidden" name="trip_type" id="trip-type-input" value="<?= $tripType ?>">
            <input type="hidden" name="depart_date" id="depart-date-input" value="<?= $searchDate ?>">
            <input type="hidden" name="return_date" id="return-date-input" value="">
            <input type="hidden" name="passengers" id="passengers-input" value="<?= $passengers ?>">
            <input type="hidden" name="children" id="children-input" value="0">
            <input type="hidden" name="infants" id="infants-input" value="0">
            <input type="hidden" name="class" id="class-input" value="<?= $class ?>">
        </form>

    </div> -->
    <!-- BAGIAN HASIL PENCARIAN - Ganti bagian ticket-card dengan kode ini -->
    <div class="search-results">
        <?php if (empty($searchResults)): ?>
            <div class="no-results">
                <div class="no-results-icon">
                    <svg width="64" height="64" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <path
                            d="M21 16V8a2 2 0 0 0-1-1.73l-7-4a2 2 0 0 0-2 0l-7 4A2 2 0 0 0 3 8v8a2 2 0 0 0 1 1.73l7 4a2 2 0 0 0 2 0l7-4A2 2 0 0 0 21 16z"
                            stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                        <circle cx="12" cy="12" r="4" stroke="currentColor" stroke-width="2" />
                    </svg>
                </div>
                <h3>Tidak ada tiket yang ditemukan</h3>
                <p>Coba ubah kriteria pencarian Anda atau pilih tanggal lain.</p>
            </div>
        <?php else: ?>
            <!-- <div class="results-header">
                <h2>Hasil Pencarian Tiket</h2>
                <span class="results-count"><?= count($searchResults) ?> tiket ditemukan</span>
            </div> -->

            <div class="ticket-list">
                <?php foreach ($searchResults as $index => $ticketData): ?>
                    <?php
                    // Get host info
                    $hostInfo = getHostInfo($conn, $ticketData['hostTicket']);
                    $hostName = isset($hostInfo['namaHost']) ? $hostInfo['namaHost'] : 'Air Asia';
                    $logoPath = isset($hostInfo['logo']) ? $hostInfo['logo'] : 'AirAsia_New_Logo.svg.webp';

                    // Get price
                    $price = isset($ticketData['harga']) ? $ticketData['harga'] : 0;
                    $originalPrice = $price * 1.02;

                    // Get time info
                    $timeInfo = formatTicketTime($ticketData);
                    ?>

                    <div class="ticket-card" data-ticket-id="<?= $ticketData['id'] ?>">
                        <div class="ticket-header">
                            <div class="airline-info">
                                <img src="./uploads/hosts/<?= $logoPath ?>" alt="<?= $hostName ?>" class="airline-logo">
                                <span class="airline-name"><?= $hostName ?></span>
                                <span class="flight-class"><?= ucfirst($ticketData['kelasTicket']) ?></span>
                                <span class="luggage-icon">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24"
                                        fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                                        stroke-linejoin="round">
                                        <rect x="9" y="2" width="6" height="20" rx="2"></rect>
                                        <path d="M4 9h16"></path>
                                        <path d="M4 15h16"></path>
                                    </svg>
                                </span>
                            </div>
                        </div>

                        <div class="ticket-content">
                            <div class="flight-times">
                                <div class="departure">
                                    <div class="time"><?= $timeInfo['departureTime'] ?></div>
                                    <div class="airport"><?= $ticketData['tempatBerangkat'] ?></div>
                                    <div class="date"><?= date('d M Y', strtotime($ticketData['tanggal'])) ?></div>
                                </div>

                                <div class="flight-info">
                                    <div class="duration"><?= $timeInfo['duration'] ?></div>
                                    <div class="line">⎯⎯⎯⎯⎯⎯</div>
                                    <div class="flight-type">Langsung</div>
                                </div>

                                <div class="arrival">
                                    <div class="time"><?= $timeInfo['arrivalTime'] ?></div>
                                    <div class="airport"><?= $ticketData['destinasi'] ?></div>
                                    <div class="date"><?= date('d M Y', strtotime($ticketData['tanggal'])) ?></div>
                                </div>
                            </div>

                            <div class="price-info">
                                <div class="price-container">
                                    <?php if ($originalPrice > $price): ?>
                                        <div class="original-price"><?= formatPrice($originalPrice) ?></div>
                                    <?php endif; ?>
                                    <div class="current-price"><?= formatPrice($price) ?>/pax</div>
                                    <?php if (isset($ticketData['stok']) && $ticketData['stok'] <= 5): ?>
                                        <div class="stock-warning">Tersisa <?= $ticketData['stok'] ?> kursi</div>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>

                        <div class="ticket-footer">
                            <div class="ticket-actions">
                                <button class="detail-button" onclick="viewTicketDetail(<?= $ticketData['id'] ?>)">
                                    Lihat Detail
                                </button>
                                <a href="booking.php?id=<?= $ticketData['id'] ?>" class="select-button">
                                    Pilih
                                </a>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>

            <!-- Pagination jika diperlukan -->
            <?php if (count($searchResults) > 10): ?>
                <div class="pagination">
                    <button class="pagination-btn prev" disabled>Sebelumnya</button>
                    <span class="pagination-info">1 dari 1</span>
                    <button class="pagination-btn next" disabled>Selanjutnya</button>
                </div>
            <?php endif; ?>
        <?php endif; ?>
    </div>
    <script>
        // Global variables
        let currentTripType = '<?= $tripType ?>';
        let selectedDepartureDate = '<?= $searchDate ?>';
        let selectedReturnDate = '';
        let passengerCounts = {
            adults: <?= $passengers ?>,
            children: 0,
            infants: 0
        };
        let selectedCabinClass = '<?= $class ?>';
        let departureMonth = <?= $currentMonth ?>;
        let departureYear = <?= $currentYear ?>;
        let returnMonth = <?= $currentMonth ?>;
        let returnYear = <?= $currentYear ?>;
        const today = new Date();
        today.setHours(0, 0, 0, 0); // Reset time part for accurate comparisons

        // Toggle date dropdown
        function toggleDateDropdown() {
            const dropdown = document.getElementById('date-dropdown');
            dropdown.classList.toggle('active');

            // Close passenger dropdown if open
            document.getElementById('passenger-dropdown').classList.remove('active');
        }

        // Toggle passenger dropdown
        function togglePassengerDropdown() {
            const dropdown = document.getElementById('passenger-dropdown');
            dropdown.classList.toggle('active');

            // Close date dropdown if open
            document.getElementById('date-dropdown').classList.remove('active');
        }

        // Set trip type
        function setTripType(type) {
            currentTripType = type;
            document.getElementById('trip-type-input').value = type;
            const dateDropdown = document.getElementById('date-dropdown');

            // Update tab appearance
            document.querySelectorAll('.trip-type-tab').forEach(tab => {
                tab.classList.remove('active');
            });
            event.target.classList.add('active');

            // Show/hide return picker
            const returnPicker = document.getElementById('return-picker');
            if (type === 'round-trip') {
                returnPicker.style.display = 'block';
                dateDropdown.style.width = "700px";

                // If no return date is set yet, set a default one (2 days after departure)
                if (!selectedReturnDate) {
                    const departDate = new Date(selectedDepartureDate);
                    const returnDate = new Date(departDate);
                    returnDate.setDate(departDate.getDate() + 2);

                    // Format date as YYYY-MM-DD
                    const returnYear = returnDate.getFullYear();
                    const returnMonth = String(returnDate.getMonth() + 1).padStart(2, '0');
                    const returnDay = String(returnDate.getDate()).padStart(2, '0');
                    selectedReturnDate = `${returnYear}-${returnMonth}-${returnDay}`;

                    document.getElementById('return-date-input').value = selectedReturnDate;

                    // Update the return calendar to make sure it's on the right month
                    returnMonth = returnDate.getMonth() + 1;
                    returnYear = returnDate.getFullYear();
                    updateCalendar('return', returnMonth, returnYear);
                }
            } else {
                returnPicker.style.display = 'none';
                dateDropdown.style.width = "400px";
                // Don't clear selectedReturnDate, just hide the picker
            }

            updateDateDisplay();
        }

        // Select date
        function selectDate(date, type) {
            const selectedDate = new Date(date);

            // Ensure selected date is not before today
            if (selectedDate < today) {
                return; // Don't allow dates before today
            }

            if (type === 'departure') {
                selectedDepartureDate = date;
                document.getElementById('depart-date-input').value = date;

                // Update selected appearance for departure
                document.querySelectorAll('#departure-days .day').forEach(day => {
                    day.classList.remove('selected');
                });
                event.target.classList.add('selected');

                // If return date is before new departure date, clear it
                if (selectedReturnDate) {
                    const returnDate = new Date(selectedReturnDate);
                    if (returnDate < selectedDate) {
                        selectedReturnDate = '';
                        document.getElementById('return-date-input').value = '';
                        document.querySelectorAll('#return-days .day').forEach(day => {
                            day.classList.remove('selected');
                        });

                        // Update return calendar to reflect the new departure date constraint
                        updateCalendar('return', returnMonth, returnYear);
                    }
                } else {
                    // Even without a selected return date, update the return calendar
                    // to properly disable dates before the new departure date
                    updateCalendar('return', returnMonth, returnYear);
                }
            } else if (type === 'return') {
                // Make sure return date is not before departure date
                const departureDate = new Date(selectedDepartureDate);
                if (selectedDate < departureDate) {
                    return; // Don't allow return dates before departure
                }

                selectedReturnDate = date;
                document.getElementById('return-date-input').value = date;

                // Update selected appearance for return
                document.querySelectorAll('#return-days .day').forEach(day => {
                    day.classList.remove('selected');
                });
                event.target.classList.add('selected');
            }

            updateDateDisplay();
        }

        // Update date display text
        function updateDateDisplay() {
            const departDate = new Date(selectedDepartureDate);
            const days = ['Min', 'Sen', 'Sel', 'Rab', 'Kam', 'Jum', 'Sab'];
            const months = ['Jan', 'Feb', 'Mar', 'Apr', 'Mei', 'Jun', 'Jul', 'Ags', 'Sep', 'Okt', 'Nov', 'Des'];

            const departStr = days[departDate.getDay()] + ', ' +
                departDate.getDate() + ' ' +
                months[departDate.getMonth()] + ' ' +
                departDate.getFullYear().toString().substr(2);

            let displayText = '';

            if (currentTripType === 'round-trip' && selectedReturnDate) {
                // For round trip with return date selected, show both dates
                const returnDate = new Date(selectedReturnDate);
                const returnStr = days[returnDate.getDay()] + ', ' +
                    returnDate.getDate() + ' ' +
                    months[returnDate.getMonth()] + ' ' +
                    returnDate.getFullYear().toString().substr(2);

                displayText = departStr + ' - ' + returnStr + ' (Pulang-Pergi)';
            } else if (currentTripType === 'round-trip') {
                // For round trip without return date
                displayText = departStr + ' (Pulang-Pergi)';
            } else {
                // For one way
                displayText = departStr + ' (Sekali Jalan)';
            }

            document.getElementById('date-text').textContent = displayText;
        }

        // Apply dates and close dropdown
        function applyDates() {
            // Make sure a return date is set for round trips
            if (currentTripType === 'round-trip' && !selectedReturnDate) {
                // Auto-select a return date 2 days after departure
                const departDate = new Date(selectedDepartureDate);
                const returnDate = new Date(departDate);
                returnDate.setDate(departDate.getDate() + 2);

                // Format date as YYYY-MM-DD
                const returnYear = returnDate.getFullYear();
                const returnMonth = String(returnDate.getMonth() + 1).padStart(2, '0');
                const returnDay = String(returnDate.getDate()).padStart(2, '0');
                selectedReturnDate = `${returnYear}-${returnMonth}-${returnDay}`;

                document.getElementById('return-date-input').value = selectedReturnDate;
                updateDateDisplay();
            }

            document.getElementById('date-dropdown').classList.remove('active');
        }

        // Calendar navigation for departure calendar
        function previousMonth() {
            let newMonth = departureMonth - 1;
            let newYear = departureYear;

            if (newMonth < 1) {
                newMonth = 12;
                newYear--;
            }

            // Check if the new date would be in the past
            const lastDayOfMonth = new Date(newYear, newMonth, 0).getDate();
            const lastDayDate = new Date(newYear, newMonth - 1, lastDayOfMonth);

            // If the entire month is in the past, don't allow navigation
            if (lastDayDate < today) {
                return;
            }

            departureMonth = newMonth;
            departureYear = newYear;
            updateCalendar('departure', departureMonth, departureYear);
        }

        function nextMonth() {
            departureMonth++;
            if (departureMonth > 12) {
                departureMonth = 1;
                departureYear++;
            }
            updateCalendar('departure', departureMonth, departureYear);
        }

        // Calendar navigation for return calendar
        function previousMonthReturn() {
            let newMonth = returnMonth - 1;
            let newYear = returnYear;

            if (newMonth < 1) {
                newMonth = 12;
                newYear--;
            }

            // Check if the new month would be before the current month and year
            const newMonthDate = new Date(newYear, newMonth - 1, 1);
            const todayMonthStart = new Date(today.getFullYear(), today.getMonth(), 1);

            // Don't allow navigation before the current month
            if (newMonthDate < todayMonthStart) {
                return;
            }

            returnMonth = newMonth;
            returnYear = newYear;
            updateCalendar('return', returnMonth, returnYear);
        }

        function nextMonthReturn() {
            returnMonth++;
            if (returnMonth > 12) {
                returnMonth = 1;
                returnYear++;
            }
            updateCalendar('return', returnMonth, returnYear);
        }

        // Function to update calendar based on month/year
        function updateCalendar(calendarType, month, year) {
            // Get month name
            const months_id = [
                'Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni',
                'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'
            ];

            // Update month title
            const calendarId = calendarType === 'departure' ? 'departure-days' : 'return-days';
            const calendarContainer = document.getElementById(calendarId).parentNode;
            calendarContainer.querySelector('.month-title').textContent = months_id[month - 1] + ' ' + year;

            // Generate calendar days
            const daysInMonth = new Date(year, month, 0).getDate();
            const firstDay = new Date(year, month - 1, 1).getDay();
            let html = '';

            // Add empty cells for days before month starts
            for (let i = 0; i < firstDay; i++) {
                html += '<div class="day"></div>';
            }

            // Add days of the month
            for (let day = 1; day <= daysInMonth; day++) {
                const date = year + '-' + String(month).padStart(2, '0') + '-' + String(day).padStart(2, '0');
                const dateObj = new Date(date);

                // Check if this date is today
                const isToday = dateObj.toDateString() === today.toDateString();

                // Check if date is selected
                const isSelected = calendarType === 'departure' ?
                    date === selectedDepartureDate : date === selectedReturnDate;

                // Check if date is in the past
                const isPast = dateObj < today;

                // For return calendar, also check if date is before departure date
                let isBeforeDeparture = false;
                if (calendarType === 'return' && selectedDepartureDate) {
                    const departureDate = new Date(selectedDepartureDate);
                    isBeforeDeparture = dateObj < departureDate;
                }

                // A date is disabled if it's in the past or (for return calendar) before departure date
                const isDisabled = isPast || isBeforeDeparture;

                // Create day element with appropriate classes
                html += `<div class="day ${isToday ? 'today' : ''} ${isSelected ? 'selected' : ''} ${isDisabled ? 'past-date' : ''}"
             ${!isDisabled ? `onclick="selectDate('${date}', '${calendarType}')"` : ''}>
             ${day}
             ${!isDisabled ? '<span class="price">' + getRandomPrice() + '</span>' : ''}
         </div>`;
            }

            // Update calendar days
            document.getElementById(calendarId).innerHTML = html;

            // Add CSS for past dates if not already added
            if (!document.getElementById('calendar-custom-styles')) {
                const styleEl = document.createElement('style');
                styleEl.id = 'calendar-custom-styles';
                styleEl.textContent = `
    .day.past-date {
        opacity: 0.5;
        color: #999;
        cursor: default;
    }
    .day.past-date span.price {
        display: none;
    }
`;
                document.head.appendChild(styleEl);
            }
        }

        // Function to get random price (just for display)
        function getRandomPrice() {
            const prices = ['1,1jt', '1,2jt', '1,3jt', '1,4jt'];
            return prices[Math.floor(Math.random() * prices.length)];
        }

        // Passenger counter functions
        function incrementPassenger(type) {
            passengerCounts[type]++;
            document.getElementById(type + '-count').textContent = passengerCounts[type];
            updatePassengerDisplay();
            updateCounterButtons();
        }

        function decrementPassenger(type) {
            if (passengerCounts[type] > 0) {
                if (type === 'adults' && passengerCounts[type] === 1) return; // At least 1 adult required
                passengerCounts[type]--;
                document.getElementById(type + '-count').textContent = passengerCounts[type];
                updatePassengerDisplay();
                updateCounterButtons();
            }
        }

        // Update counter button states
        function updateCounterButtons() {
            document.getElementById('adults-decrement').disabled = passengerCounts.adults <= 1;
            document.getElementById('children-decrement').disabled = passengerCounts.children <= 0;
            document.getElementById('infants-decrement').disabled = passengerCounts.infants <= 0;
        }

        // Select cabin class
        function selectCabinClass(className) {
            selectedCabinClass = className;
            document.getElementById('class-input').value = className;

            // Update selected appearance
            document.querySelectorAll('.cabin-option').forEach(option => {
                option.classList.remove('selected');
            });
            event.target.classList.add('selected');

            updatePassengerDisplay();
        }

        // Update passenger display text
        function updatePassengerDisplay() {
            const totalPassengers = passengerCounts.adults + passengerCounts.children + passengerCounts.infants;
            const classNames = {
                'ekonomi': 'Ekonomi',
                'bisnis': 'Bisnis',
                'first': 'First Class',
                'premium': 'Premium'
            };

            document.getElementById('passenger-text').textContent =
                totalPassengers + ' Penumpang, ' + classNames[selectedCabinClass];

            // Update hidden inputs
            document.getElementById('passengers-input').value = passengerCounts.adults;
            document.getElementById('children-input').value = passengerCounts.children;
            document.getElementById('infants-input').value = passengerCounts.infants;
        }

        // Apply passengers and close dropdown
        function applyPassengers() {
            document.getElementById('passenger-dropdown').classList.remove('active');
        }

        // Swap destinations
        function swapDestinations() {
            const origin = document.getElementById('origin');
            const destination = document.getElementById('destination');
            const temp = origin.value;
            origin.value = destination.value;
            destination.value = temp;
        }

        // Close dropdowns when clicking outside
        document.addEventListener('click', function (event) {
            const dateDropdown = document.getElementById('date-dropdown');
            const passengerDropdown = document.getElementById('passenger-dropdown');
            const dateDisplay = document.getElementById('date-display');
            const passengerDisplay = document.getElementById('passengers-display');

            if (!dateDisplay.contains(event.target) && !dateDropdown.contains(event.target)) {
                dateDropdown.classList.remove('active');
            }

            if (!passengerDisplay.contains(event.target) && !passengerDropdown.contains(event.target)) {
                passengerDropdown.classList.remove('active');
            }
        });

        // Initialize on document load
        document.addEventListener('DOMContentLoaded', function () {
            // Initialize counter button states
            updateCounterButtons();

            // Initialize calendars with current month/year
            updateCalendar('departure', departureMonth, departureYear);
            if (currentTripType === 'round-trip') {
                updateCalendar('return', returnMonth, returnYear);
            }
        });
    </script>

    <script>
        // Add this script to your existing search.php file
        const INDONESIA_PROVINCES_API =
            "https://www.emsifa.com/api-wilayah-indonesia/api/provinces.json";
        const INDONESIA_REGENCIES_API =
            "https://www.emsifa.com/api-wilayah-indonesia/api/regencies";
        const WORLD_COUNTRIES_API = "https://restcountries.com/v3.1/all";
        // Global variables for location data
        let indonesianProvinces = [];
        let indonesianRegencies = {};
        let worldCountries = [];
        let locationDropdownActive = null;

        // Initialize locations on document load
        document.addEventListener('DOMContentLoaded', function () {
            // Initialize location selectors
            initLocationSelectors();
        });

        // Function to initialize location selectors
        async function initLocationSelectors() {
            try {
                // Create and add location dropdowns to the DOM
                setupLocationDropdowns();

                // Fetch location data
                await Promise.all([
                    fetchIndonesianProvinces(),
                    fetchWorldCountries()
                ]);

                // Set up event listeners
                setupLocationEventListeners();

                // Pre-select values from URL parameters (will come from database)
                const origin = document.getElementById('origin').value;
                const destination = document.getElementById('destination').value;

                if (origin) {
                    document.getElementById('origin-display').textContent = origin;
                }

                if (destination) {
                    document.getElementById('destination-display').textContent = destination;
                }
            } catch (error) {
                console.error('Error initializing location selectors:', error);
            }
        }

        // Set up location dropdown structures
        function setupLocationDropdowns() {
            // Create origin dropdown
            const originContainer = document.querySelector('.search-form-item.origin');
            originContainer.innerHTML = `
        <label for="origin-display">Dari</label>
        <div class="dropdown-display" id="origin-display" onclick="toggleLocationDropdown('origin')">
            ${document.getElementById('origin').value || 'Pilih kota asal'}
        </div>
        <input type="hidden" id="origin" name="origin" value="${document.getElementById('origin').value}">
        <div class="location-dropdown" id="origin-dropdown">
            <div class="location-search">
                <input type="text" placeholder="Cari kota atau negara" id="origin-search" oninput="filterLocations('origin')">
            </div>
            <div class="location-tabs">
                <div class="location-tab active" onclick="switchLocationTab('origin', 'domestic')">Domestik</div>
                <div class="location-tab" onclick="switchLocationTab('origin', 'international')">Internasional</div>
            </div>
            <div class="location-lists">
                <div class="location-list domestic active" id="origin-domestic-list">
                    <div class="loading">Memuat...</div>
                </div>
                <div class="location-list international" id="origin-international-list">
                    <div class="loading">Memuat...</div>
                </div>
            </div>
        </div>
    `;

            // Create destination dropdown
            const destinationContainer = document.querySelector('.search-form-item.destination');
            destinationContainer.innerHTML = `
        <label for="destination-display">Ke</label>
        <div class="dropdown-display" id="destination-display" onclick="toggleLocationDropdown('destination')">
            ${document.getElementById('destination').value || 'Pilih kota tujuan'}
        </div>
        <input type="hidden" id="destination" name="destination" value="${document.getElementById('destination').value}">
        <div class="location-dropdown" id="destination-dropdown">
            <div class="location-search">
                <input type="text" placeholder="Cari kota atau negara" id="destination-search" oninput="filterLocations('destination')">
            </div>
            <div class="location-tabs">
                <div class="location-tab active" onclick="switchLocationTab('destination', 'domestic')">Domestik</div>
                <div class="location-tab" onclick="switchLocationTab('destination', 'international')">Internasional</div>
            </div>
            <div class="location-lists">
                <div class="location-list domestic active" id="destination-domestic-list">
                    <div class="loading">Memuat...</div>
                </div>
                <div class="location-list international" id="destination-international-list">
                    <div class="loading">Memuat...</div>
                </div>
            </div>
        </div>
    `;

            // Add styles
            addLocationStyles();
        }

        // Add CSS styles for location selectors
        function addLocationStyles() {
            const styleEl = document.createElement('style');
            styleEl.id = 'location-selector-styles';
            styleEl.innerHTML = `
        .location-dropdown {
            position: absolute;
            z-index: 1000;
            background: white;
            border-radius: 8px;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
            width: 360px;
            max-height: 450px;
            display: none;
            overflow: hidden;
            top: 100%;
            margin-top: 8px;
        }
        
        .location-dropdown.active {
            display: block;
        }
        
        .location-search {
            padding: 12px;
            border-bottom: 1px solid #eee;
        }
        
        .location-search input {
            width: 100%;
            padding: 10px;
            border: 1px solid #ddd;
            border-radius: 4px;
            font-size: 14px;
        }
        
        .location-tabs {
            display: flex;
            border-bottom: 1px solid #eee;
        }
        
        .location-tab {
            flex: 1;
            text-align: center;
            padding: 12px;
            cursor: pointer;
            font-weight: 500;
            color: #666;
        }
        
        .location-tab.active {
            color: #1a73e8;
            border-bottom: 2px solid #1a73e8;
        }
        
        .location-lists {
            position: relative;
            height: 300px;
        }
        
        .location-list {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            overflow-y: auto;
            display: none;
            padding: 8px 0;
        }
        
        .location-list.active {
            display: block;
        }
        
        .location-category {
            padding: 8px 16px;
            font-weight: bold;
            background-color: #f5f5f5;
            position: sticky;
            top: 0;
            z-index: 1;
        }
        
        .location-item {
            padding: 10px 16px;
            cursor: pointer;
            display: flex;
            align-items: center;
        }
        
        .location-item:hover {
            background-color: #f1f8ff;
        }
        
        .location-item-icon {
            margin-right: 12px;
            width: 20px;
            text-align: center;
            color: #666;
        }
        
        .location-item-name {
            flex: 1;
        }
        
        .location-item-meta {
            font-size: 12px;
            color: #666;
        }
        
        .loading {
            padding: 16px;
            text-align: center;
            color: #666;
        }
        
        .no-results {
            padding: 16px;
            text-align: center;
            color: #666;
        }
    `;
            document.head.appendChild(styleEl);
        }

        // Fetch Indonesian provinces
        async function fetchIndonesianProvinces() {
            try {
                const response = await fetch(INDONESIA_PROVINCES_API);
                if (!response.ok) throw new Error('Failed to fetch Indonesian provinces');

                indonesianProvinces = await response.json();

                // Fetch regencies for each province
                await Promise.all(indonesianProvinces.map(province =>
                    fetchIndonesianRegencies(province.id)
                ));

                // Populate domestic location lists
                populateDomesticLocations('origin');
                populateDomesticLocations('destination');

            } catch (error) {
                console.error('Error fetching Indonesian provinces:', error);
                document.getElementById('origin-domestic-list').innerHTML = '<div class="no-results">Gagal memuat data provinsi</div>';
                document.getElementById('destination-domestic-list').innerHTML = '<div class="no-results">Gagal memuat data provinsi</div>';
            }
        }

        // Fetch Indonesian regencies by province ID
        async function fetchIndonesianRegencies(provinceId) {
            try {
                const response = await fetch(`${INDONESIA_REGENCIES_API}/${provinceId}.json`);
                if (!response.ok) throw new Error(`Failed to fetch regencies for province ${provinceId}`);

                const regencies = await response.json();
                indonesianRegencies[provinceId] = regencies;

            } catch (error) {
                console.error(`Error fetching regencies for province ${provinceId}:`, error);
                indonesianRegencies[provinceId] = [];
            }
        }

        // Fetch world countries
        async function fetchWorldCountries() {
            try {
                const response = await fetch(WORLD_COUNTRIES_API);
                if (!response.ok) throw new Error('Failed to fetch world countries');

                const countries = await response.json();

                // Process and filter countries
                worldCountries = countries
                    .filter(country => country.name && country.name.common)
                    .map(country => ({
                        code: country.cca2,
                        name: country.name.common,
                        capital: country.capital && country.capital.length > 0 ? country.capital[0] : null,
                        region: country.region || 'Other'
                    }))
                    .sort((a, b) => a.name.localeCompare(b.name));

                // Remove Indonesia (already covered in domestic)
                worldCountries = worldCountries.filter(country => country.code !== 'ID');

                // Populate international location lists
                populateInternationalLocations('origin');
                populateInternationalLocations('destination');

            } catch (error) {
                console.error('Error fetching world countries:', error);
                document.getElementById('origin-international-list').innerHTML = '<div class="no-results">Gagal memuat data negara</div>';
                document.getElementById('destination-international-list').innerHTML = '<div class="no-results">Gagal memuat data negara</div>';
            }
        }

        // Populate domestic locations (Indonesian cities)
        function populateDomesticLocations(type) {
            const listElement = document.getElementById(`${type}-domestic-list`);

            if (!indonesianProvinces.length || Object.keys(indonesianRegencies).length === 0) {
                listElement.innerHTML = '<div class="loading">Memuat...</div>';
                return;
            }

            let html = '';
            const sortedProvinces = [...indonesianProvinces].sort((a, b) => a.name.localeCompare(b.name));

            sortedProvinces.forEach(province => {
                if (!indonesianRegencies[province.id] || indonesianRegencies[province.id].length === 0) return;

                html += `<div class="location-category">${province.name}</div>`;
                const sortedRegencies = [...indonesianRegencies[province.id]].sort((a, b) => a.name.localeCompare(b.name));

                sortedRegencies.forEach(regency => {
                    // Hapus kata "Kota" dan "Kabupaten" dari nama regency
                    let cleanedName = regency.name.replace(/^(Kota|Kabupaten)\s+/i, '').trim();

                    html += `
            <div class="location-item" onclick="selectLocation('${type}', '${cleanedName}', 'domestic', '${province.name}')">
                <div class="location-item-icon">
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <path d="M12 2C8.13 2 5 5.13 5 9C5 14.25 12 22 12 22C12 22 19 14.25 19 9C19 5.13 15.87 2 12 2ZM12 11.5C10.62 11.5 9.5 10.38 9.5 9C9.5 7.62 10.62 6.5 12 6.5C13.38 6.5 14.5 7.62 14.5 9C14.5 10.38 13.38 11.5 12 11.5Z" fill="currentColor"/>
                    </svg>
                </div>
                <div class="location-item-name">${cleanedName}</div>
                <div class="location-item-meta">${province.name}</div>
            </div>
            `;
                });
            });

            listElement.innerHTML = html || '<div class="no-results">Tidak ada kota yang ditemukan</div>';
        }

        // Populate international locations (countries)
        function populateInternationalLocations(type) {
            const listElement = document.getElementById(`${type}-international-list`);

            if (!worldCountries.length) {
                listElement.innerHTML = '<div class="loading">Memuat...</div>';
                return;
            }

            let html = '';

            // Group countries by region
            const regionMap = worldCountries.reduce((acc, country) => {
                if (!acc[country.region]) {
                    acc[country.region] = [];
                }
                acc[country.region].push(country);
                return acc;
            }, {});

            // Sort regions
            const sortedRegions = Object.keys(regionMap).sort();

            sortedRegions.forEach(region => {
                html += `<div class="location-category">${region}</div>`;

                regionMap[region].forEach(country => {
                    html += `
                <div class="location-item" onclick="selectLocation('${type}', '${country.name}', 'international', '${region}')">
                    <div class="location-item-icon">
                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <path d="M12 2C6.48 2 2 6.48 2 12C2 17.52 6.48 22 12 22C17.52 22 22 17.52 22 12C22 6.48 17.52 2 12 2ZM12 20C7.59 20 4 16.41 4 12C4 7.59 7.59 4 12 4C16.41 4 20 7.59 20 12C20 16.41 16.41 20 12 20Z" fill="currentColor"/>
                            <path d="M12 2C6.48 2 2 6.48 2 12C2 17.52 6.48 22 12 22C17.52 22 22 17.52 22 12C22 6.48 17.52 2 12 2ZM2.5 12C2.5 7.86 5.06 4.31 8.66 3.01L9.5 5.5H13L13.5 8L15 8.5L14.5 10.5L13 11L13.5 13L11 15L8.5 14.5L7.5 16.5L6 16.25V17.5L4 18C3.06 16.23 2.5 14.16 2.5 12ZM12 21.5C8.71 21.5 5.81 19.8 4.26 17.15L5.5 16.5V15.5L7 15L8 13L11 13.5L12.5 12L12 10L14 9.5L15 7L14 5.5L15.5 4.5L16.5 7H18L19 8.5L21 9C20.97 14.16 17.04 18.5 12 18.5V21.5Z" fill="currentColor"/>
                        </svg>
                    </div>
                    <div class="location-item-name">${country.name}</div>
                    <div class="location-item-meta">${country.capital || region}</div>
                </div>
            `;
                });
            });

            listElement.innerHTML = html || '<div class="no-results">Tidak ada negara yang ditemukan</div>';
        }

        // Toggle location dropdown
        function toggleLocationDropdown(type) {
            const dropdown = document.getElementById(`${type}-dropdown`);
            const otherType = type === 'origin' ? 'destination' : 'origin';
            const otherDropdown = document.getElementById(`${otherType}-dropdown`);

            // Close other dropdowns
            otherDropdown.classList.remove('active');
            document.getElementById('date-dropdown').classList.remove('active');
            document.getElementById('passenger-dropdown').classList.remove('active');

            // Toggle this dropdown
            dropdown.classList.toggle('active');

            // Set active dropdown
            locationDropdownActive = dropdown.classList.contains('active') ? type : null;

            // Focus search input if opening
            if (dropdown.classList.contains('active')) {
                setTimeout(() => {
                    document.getElementById(`${type}-search`).focus();
                }, 100);
            }
        }

        // Switch between domestic and international tabs
        function switchLocationTab(type, tabType) {
            // Update tab appearance
            const tabs = document.querySelectorAll(`#${type}-dropdown .location-tab`);
            tabs.forEach(tab => tab.classList.remove('active'));

            const activeTab = Array.from(tabs).find(tab => tab.textContent.toLowerCase().includes(tabType === 'domestic' ? 'domestik' : 'internasional'));
            if (activeTab) activeTab.classList.add('active');

            // Show/hide corresponding lists
            document.getElementById(`${type}-domestic-list`).classList.toggle('active', tabType === 'domestic');
            document.getElementById(`${type}-international-list`).classList.toggle('active', tabType === 'international');
        }

        // Filter locations based on search input
        function filterLocations(type) {
            const searchInput = document.getElementById(`${type}-search`);
            const searchText = searchInput.value.toLowerCase().trim();

            // Filter domestic locations
            filterDomesticLocations(type, searchText);

            // Filter international locations
            filterInternationalLocations(type, searchText);
        }

        // Filter domestic locations
        function filterDomesticLocations(type, searchText) {
            if (!indonesianProvinces.length || Object.keys(indonesianRegencies).length === 0) return;

            const listElement = document.getElementById(`${type}-domestic-list`);

            if (!searchText) {
                populateDomesticLocations(type);
                return;
            }

            let html = '';
            let resultsFound = false;
            const sortedProvinces = [...indonesianProvinces].sort((a, b) => a.name.localeCompare(b.name));

            sortedProvinces.forEach(province => {
                if (!indonesianRegencies[province.id] || indonesianRegencies[province.id].length === 0) return;

                // Filter regencies dengan nama yang sudah dibersihkan
                const matchingRegencies = indonesianRegencies[province.id].filter(regency => {
                    let cleanedName = regency.name.replace(/^(Kota|Kabupaten)\s+/i, '').trim();
                    return cleanedName.toLowerCase().includes(searchText) ||
                        province.name.toLowerCase().includes(searchText);
                });

                if (matchingRegencies.length === 0) return;

                resultsFound = true;
                html += `<div class="location-category">${province.name}</div>`;
                const sortedRegencies = [...matchingRegencies].sort((a, b) => a.name.localeCompare(b.name));

                sortedRegencies.forEach(regency => {
                    // Hapus kata "Kota" dan "Kabupaten" dari nama regency
                    let cleanedName = regency.name.replace(/^(Kota|Kabupaten)\s+/i, '').trim();

                    html += `
            <div class="location-item" onclick="selectLocation('${type}', '${cleanedName}', 'domestic', '${province.name}')">
                <div class="location-item-icon">
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <path d="M12 2C8.13 2 5 5.13 5 9C5 14.25 12 22 12 22C12 22 19 14.25 19 9C19 5.13 15.87 2 12 2ZM12 11.5C10.62 11.5 9.5 10.38 9.5 9C9.5 7.62 10.62 6.5 12 6.5C13.38 6.5 14.5 7.62 14.5 9C14.5 10.38 13.38 11.5 12 11.5Z" fill="currentColor"/>
                    </svg>
                </div>
                <div class="location-item-name">${cleanedName}</div>
                <div class="location-item-meta">${province.name}</div>
            </div>
            `;
                });
            });

            listElement.innerHTML = html || '<div class="no-results">Tidak ada kota yang ditemukan</div>';
        }

        // Filter international locations
        function filterInternationalLocations(type, searchText) {
            if (!worldCountries.length) return;

            const listElement = document.getElementById(`${type}-international-list`);

            if (!searchText) {
                // If no search text, show all
                populateInternationalLocations(type);
                return;
            }

            // Filter countries that match search
            const matchingCountries = worldCountries.filter(country =>
                country.name.toLowerCase().includes(searchText) ||
                (country.capital && country.capital.toLowerCase().includes(searchText)) ||
                country.region.toLowerCase().includes(searchText)
            );

            if (matchingCountries.length === 0) {
                listElement.innerHTML = '<div class="no-results">Tidak ada negara yang ditemukan</div>';
                return;
            }

            // Group matching countries by region
            const regionMap = matchingCountries.reduce((acc, country) => {
                if (!acc[country.region]) {
                    acc[country.region] = [];
                }
                acc[country.region].push(country);
                return acc;
            }, {});

            // Sort regions
            const sortedRegions = Object.keys(regionMap).sort();

            let html = '';

            sortedRegions.forEach(region => {
                html += `<div class="location-category">${region}</div>`;

                regionMap[region].forEach(country => {
                    html += `
                <div class="location-item" onclick="selectLocation('${type}', '${country.name}', 'international', '${region}')">
                    <div class="location-item-icon">
                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <path d="M12 2C6.48 2 2 6.48 2 12C2 17.52 6.48 22 12 22C17.52 22 22 17.52 22 12C22 6.48 17.52 2 12 2ZM2.5 12C2.5 7.86 5.06 4.31 8.66 3.01L9.5 5.5H13L13.5 8L15 8.5L14.5 10.5L13 11L13.5 13L11 15L8.5 14.5L7.5 16.5L6 16.25V17.5L4 18C3.06 16.23 2.5 14.16 2.5 12ZM12 21.5C8.71 21.5 5.81 19.8 4.26 17.15L5.5 16.5V15.5L7 15L8 13L11 13.5L12.5 12L12 10L14 9.5L15 7L14 5.5L15.5 4.5L16.5 7H18L19 8.5L21 9C20.97 14.16 17.04 18.5 12 18.5V21.5Z" fill="currentColor"/>
                        </svg>
                    </div>
                    <div class="location-item-name">${country.name}</div>
                    <div class="location-item-meta">${country.capital || region}</div>
                </div>
            `;
                });
            });

            listElement.innerHTML = html;
        }

        // Select a location
        function selectLocation(type, locationName, locationType, regionName) {
            // Update hidden input and display
            document.getElementById(type).value = locationName;
            document.getElementById(`${type}-display`).textContent = locationName;

            // Close dropdown
            document.getElementById(`${type}-dropdown`).classList.remove('active');
            locationDropdownActive = null;
        }

        // Set up event listeners for outside clicks
        function setupLocationEventListeners() {
            document.addEventListener('click', function (event) {
                if (locationDropdownActive) {
                    const dropdown = document.getElementById(`${locationDropdownActive}-dropdown`);
                    const display = document.getElementById(`${locationDropdownActive}-display`);

                    if (!dropdown.contains(event.target) && !display.contains(event.target)) {
                        dropdown.classList.remove('active');
                        locationDropdownActive = null;
                    }
                }
            });
        }
    </script>
</body>

</html>
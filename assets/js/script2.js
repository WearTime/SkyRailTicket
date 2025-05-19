let ticketBtn = document.getElementById("btn-ticket");
let ticketDropdown = document.querySelector(".ticket-dropdown ul");
let profileBtn = document.getElementById("profile");
let profileDropdown = document.querySelector(".profile-dropdown ul");
let isDropdownOpen = false;
document.addEventListener("DOMContentLoaded", function () {
  // Tab switching functionality
  const tabs = document.querySelectorAll(".date-tab");
  tabs.forEach((tab) => {
    tab.addEventListener("click", function () {
      tabs.forEach((t) => t.classList.remove("active"));
      this.classList.add("active");

      // Update the date input based on tab
      const dateInput = document.getElementById("depart-date");
      if (this.textContent === "Sekali jalan") {
        dateInput.value = "Rab, 21 Mei 25 (Sekali Jalan)";
      } else {
        dateInput.value = "Rab, 21 Mei 25 - Jum, 23 Mei 25";
      }
    });
  });

  // Calendar day selection
  const days = document.querySelectorAll(".day");
  days.forEach((day) => {
    if (day.textContent.trim() !== "") {
      day.addEventListener("click", function () {
        days.forEach((d) => d.classList.remove("selected"));
        this.classList.add("selected");

        // Update the date input
        const dateInput = document.getElementById("depart-date");
        const activeTab = document.querySelector(".date-tab.active");
        const dayNum = this.textContent.trim().split("\n")[0];

        if (activeTab.textContent === "Sekali jalan") {
          dateInput.value = `Rab, ${dayNum} Mei 25 (Sekali Jalan)`;
        } else {
          // For simplicity, just add 2 days for return
          const returnDay = parseInt(dayNum) + 2;
          dateInput.value = `Rab, ${dayNum} Mei 25 - Jum, ${returnDay} Mei 25`;
        }
      });
    }
  });

  // Passenger count functionality
  const decrementBtn = document.querySelector(".decrement");
  const incrementBtn = document.querySelector(".increment");
  const countDisplay = document.querySelector(".count");

  decrementBtn.addEventListener("click", function () {
    let count = parseInt(countDisplay.textContent);
    if (count > 1) {
      countDisplay.textContent = count - 1;
    }
  });

  incrementBtn.addEventListener("click", function () {
    let count = parseInt(countDisplay.textContent);
    if (count < 9) {
      countDisplay.textContent = count + 1;
    }
  });

  // Month navigation
  const prevMonth = document.querySelector(".prev-month");
  const nextMonth = document.querySelector(".next-month");
  const calendarTitle = document.querySelector(".calendar-title");
  const months = [
    "Januari",
    "Februari",
    "Maret",
    "April",
    "Mei",
    "Juni",
    "Juli",
    "Agustus",
    "September",
    "Oktober",
    "November",
    "Desember",
  ];
  let currentMonthIndex = 4; // May

  prevMonth.addEventListener("click", function () {
    if (currentMonthIndex > 0) {
      currentMonthIndex--;
      calendarTitle.textContent = `${months[currentMonthIndex]} 2025`;
    }
  });

  nextMonth.addEventListener("click", function () {
    if (currentMonthIndex < 11) {
      currentMonthIndex++;
      calendarTitle.textContent = `${months[currentMonthIndex]} 2025`;
    }
  });

  // Swap origin and destination
  const swapButton = document.querySelector(".swap-button");
  const originInput = document.getElementById("origin");
  const destinationInput = document.getElementById("destination");

  swapButton.addEventListener("click", function () {
    const tempValue = originInput.value;
    originInput.value = destinationInput.value;
    destinationInput.value = tempValue;
  });
});
// Ticket dropdown functionality
ticketBtn.addEventListener("click", () => {
  isDropdownOpen = !isDropdownOpen;

  if (isDropdownOpen) {
    // Change SVG to down arrow
    ticketBtn.innerHTML = `
            Ticket
            <svg xmlns="http://www.w3.org/2000/svg" height="24px" viewBox="0 -960 960 960" width="24px" fill="#dbd5d5">
                <path d="M480-344 240-584l56-56 184 184 184-184 56 56-240 240Z"/>
            </svg>
        `;

    // Show dropdown
    ticketDropdown.style.top = "auto";
    ticketDropdown.style.opacity = "1";
    ticketDropdown.style.visibility = "visible";
  } else {
    // Change SVG back to up arrow
    ticketBtn.innerHTML = `
            Ticket
            <svg xmlns="http://www.w3.org/2000/svg" height="24px" viewBox="0 -960 960 960" width="24px" fill="#dbd5d5">
                <path d="M480-528 296-344l-56-56 240-240 240 240-56 56-184-184Z" />
            </svg>
        `;

    // Hide dropdown
    ticketDropdown.style.top = "-130px";
    ticketDropdown.style.opacity = "0";
    ticketDropdown.style.visibility = "hidden";
  }
});

// Profile dropdown functionality
let isProfileopen = false;

if (profileBtn) {
  profileBtn.addEventListener("click", () => {
    isProfileopen = !isProfileopen;

    if (isProfileopen) {
      profileDropdown.style.top = "auto";
      profileDropdown.style.opacity = "1";
      profileDropdown.style.visibility = "visible";
    } else {
      profileDropdown.style.top = "-130px";
      profileDropdown.style.opacity = "0";
      profileDropdown.style.visibility = "hidden";
    }
  });
}

// TAMBAHKAN JAVASCRIPT INI KE BAGIAN BAWAH FILE, SETELAH SCRIPT YANG ADA

// Function untuk melihat detail tiket
function viewTicketDetail(ticketId) {
  const currentUrl = new URL(window.location);
  currentUrl.searchParams.set("id", ticketId);
  window.location.href = currentUrl.toString();
}

// Function untuk filter hasil pencarian secara real-time (opsional)
function filterResults() {
  const cards = document.querySelectorAll(".ticket-card");
  const priceFilter = document.getElementById("price-filter");
  const classFilter = document.getElementById("class-filter");

  if (!priceFilter || !classFilter) return;

  const maxPrice = parseFloat(priceFilter.value) || Infinity;
  const selectedClass = classFilter.value;

  cards.forEach((card) => {
    const priceText = card.querySelector(".current-price").textContent;
    const price = parseFloat(priceText.replace(/[^\d]/g, ""));
    const classText = card
      .querySelector(".flight-class")
      .textContent.toLowerCase();

    const priceMatch = price <= maxPrice;
    const classMatch =
      selectedClass === "all" || classText.includes(selectedClass);

    if (priceMatch && classMatch) {
      card.style.display = "block";
    } else {
      card.style.display = "none";
    }
  });

  updateResultsCount();
}

// Function untuk update jumlah hasil
function updateResultsCount() {
  const visibleCards = document.querySelectorAll(
    '.ticket-card[style="display: block"], .ticket-card:not([style*="display: none"])'
  );
  const countElement = document.querySelector(".results-count");

  if (countElement) {
    countElement.textContent = `${visibleCards.length} tiket ditemukan`;
  }
}

// Function untuk sort hasil
function sortResults(sortBy) {
  const ticketList = document.querySelector(".ticket-list");
  const cards = Array.from(document.querySelectorAll(".ticket-card"));

  cards.sort((a, b) => {
    switch (sortBy) {
      case "price-low":
        const priceA = parseFloat(
          a.querySelector(".current-price").textContent.replace(/[^\d]/g, "")
        );
        const priceB = parseFloat(
          b.querySelector(".current-price").textContent.replace(/[^\d]/g, "")
        );
        return priceA - priceB;

      case "price-high":
        const priceA2 = parseFloat(
          a.querySelector(".current-price").textContent.replace(/[^\d]/g, "")
        );
        const priceB2 = parseFloat(
          b.querySelector(".current-price").textContent.replace(/[^\d]/g, "")
        );
        return priceB2 - priceA2;

      case "departure":
        const timeA = a.querySelector(".departure .time").textContent;
        const timeB = b.querySelector(".departure .time").textContent;
        return timeA.localeCompare(timeB);

      default:
        return 0;
    }
  });

  // Clear and re-append sorted cards
  ticketList.innerHTML = "";
  cards.forEach((card) => ticketList.appendChild(card));
}

// Event listeners untuk form pencarian yang sudah ada
document.addEventListener("DOMContentLoaded", function () {
  // Update existing event listeners
  updateCounterButtons();
  updateCalendar("departure", departureMonth, departureYear);
  if (currentTripType === "round-trip") {
    updateCalendar("return", returnMonth, returnYear);
  }

  // Add sort dropdown if it exists
  const sortDropdown = document.getElementById("sort-dropdown");
  if (sortDropdown) {
    sortDropdown.addEventListener("change", function () {
      sortResults(this.value);
    });
  }

  // Add filter event listeners if they exist
  const priceFilter = document.getElementById("price-filter");
  const classFilter = document.getElementById("class-filter");

  if (priceFilter) {
    priceFilter.addEventListener("input", filterResults);
  }

  if (classFilter) {
    classFilter.addEventListener("change", filterResults);
  }
});

// Function untuk load more results (jika menggunakan pagination)
function loadMoreResults(page) {
  // Implementation untuk load more results via AJAX
  const formData = new FormData(document.querySelector(".search-form"));
  formData.append("page", page);
  formData.append("ajax", "1");

  fetch(window.location.pathname, {
    method: "POST",
    body: formData,
  })
    .then((response) => response.json())
    .then((data) => {
      if (data.success) {
        const ticketList = document.querySelector(".ticket-list");
        ticketList.insertAdjacentHTML("beforeend", data.html);
        updateResultsCount();
      }
    })
    .catch((error) => {
      console.error("Error loading more results:", error);
    });
}

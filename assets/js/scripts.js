let ticketBtn = document.getElementById("btn-ticket");
let ticketDropdown = document.querySelector(".ticket-dropdown ul");
let profileBtn = document.getElementById("profile");
let profileDropdown = document.querySelector(".profile-dropdown ul");
let arrowRight = document.getElementById("arrow-right");
let ticketSection = document.querySelector(".ticket-section");
let ticketCards = document.querySelectorAll(".ticket-card");
let arrowLeft = document.getElementById("arrow-left");
let isDropdownOpen = false;

// Create arrow left element
// let arrowLeft = document.createElement("svg");
// arrowLeft.className = "arrow-left";
// arrowLeft.id = "arrow-left";
// arrowLeft.setAttribute("xmlns", "http://www.w3.org/2000/svg");
// arrowLeft.setAttribute("height", "24px");
// arrowLeft.setAttribute("viewBox", "0 -960 960 960");
// arrowLeft.setAttribute("width", "24px");
// arrowLeft.setAttribute("fill", "#000000");
// arrowLeft.innerHTML =
//   '<path d="m313-440 224 224-57 56-320-320 320-320 57 56-224 224h487v80H313Z"/>';

// Add CSS style for arrow-left and improve ticket-section
// const style = document.createElement("style");
// style.textContent = `
//   .main .ticketDaerah .ticket-main .arrow-left {
//     left: 50px;
//     background-color: white;
//     padding: 5px;
//     border-radius: 6px;
//     position: absolute;
//     box-shadow: 0px 0px 32px -8px rgba(0, 0, 0, 0.75);
//     -webkit-box-shadow: 0px 0px 32px -8px rgba(0, 0, 0, 0.75);
//     -moz-box-shadow: 0px 0px 32px -8px rgba(0, 0, 0, 0.75);
//     cursor: pointer;
//     opacity: 0;
//     visibility: hidden;
//     z-index: 10;
//   }

//   .main .ticketDaerah .ticket-main .ticket-section {
//     display: flex;
//     padding-bottom: 40px;
//     gap: 30px;
//     transition: all 0.5s ease;
//     padding-left: 18px;
//     margin-left: 27px;
//     flex-wrap: nowrap;  /* Ensure cards stay in a single row */
//   }
// `;
// document.head.appendChild(style);

// Add arrow left to ticket-main
let ticketMain = document.querySelector(".ticket-main");
ticketMain.insertBefore(arrowLeft, ticketMain.firstChild);

// Variables to track current card position and scrolling
let currentCardIndex = 0;
const totalCards = ticketCards.length;
let visibleCards = 0;
let maxIndex = 0;

// Calculate how many cards are visible at current viewport width
function calculateVisibleCards() {
  // Approximate the number of visible cards based on container width
  const containerWidth = ticketMain.offsetWidth - 100; // Subtract arrow space
  const cardWidth = 282 + 30; // card width + gap
  visibleCards = Math.floor(containerWidth / cardWidth);

  // Calculate maximum index we can scroll to
  maxIndex = Math.max(0, totalCards - visibleCards);
}

// Initialize on load
window.addEventListener("load", () => {
  calculateVisibleCards();
  console.log("Loaded Success");

  // Hide arrow left initially
  arrowLeft.style.opacity = "0";
  arrowLeft.style.visibility = "hidden";

  // Show arrow right only if there are more cards than can be displayed
  if (totalCards <= visibleCards) {
    arrowRight.style.opacity = "0";
    arrowRight.style.visibility = "hidden";
  }
});

// Update on resize
window.addEventListener("resize", () => {
  calculateVisibleCards();

  // Adjust current position if needed after resize
  if (currentCardIndex > maxIndex) {
    currentCardIndex = maxIndex;
    scrollCards();
  }

  // Update arrow visibility
  updateArrowVisibility();
});

// Function to scroll cards
function scrollCards() {
  const scrollAmount = currentCardIndex * (137 + 50); // card width + gap
  ticketCards.forEach((i) => {
    i.style.transform = `translateX(-${scrollAmount}px)`;
  });
}

// Function to update arrow visibility
function updateArrowVisibility() {
  // Show/hide left arrow
  if (currentCardIndex <= 0) {
    arrowLeft.style.opacity = "0";
    arrowLeft.style.visibility = "hidden";
  } else {
    arrowLeft.style.opacity = "1";
    arrowLeft.style.visibility = "visible";
  }

  // Show/hide right arrow
  if (currentCardIndex >= maxIndex) {
    arrowRight.style.opacity = "0";
    arrowRight.style.visibility = "hidden";
  } else {
    arrowRight.style.opacity = "1";
    arrowRight.style.visibility = "visible";
  }
}

// Arrow right click handler
arrowRight.addEventListener("click", () => {
  if (currentCardIndex < maxIndex) {
    currentCardIndex++;
    scrollCards();
    updateArrowVisibility();
  }
});

// Arrow left click handler
arrowLeft.addEventListener("click", () => {
  if (currentCardIndex > 0) {
    currentCardIndex--;
    scrollCards();
    updateArrowVisibility();
  }
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

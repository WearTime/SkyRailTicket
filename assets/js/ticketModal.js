// Modal functionality
document.addEventListener("DOMContentLoaded", function () {
  console.log("Loaded");

  // All the existing variables
  const addModal = document.getElementById("addTicketModal");
  const detailModal = document.getElementById("detailTicketModal");
  const editModal = document.getElementById("editTicketModal");
  const deleteModal = document.getElementById("deleteTicketModal");
  const openModalBtn = document.getElementById("openModalBtn");
  const closeBtn = document.querySelectorAll(".close");
  const closeBottomBtn = document.querySelectorAll(".close-btn");
  const fileInput = document.getElementById("imageTujuan");
  const fileName = document.getElementById("fileName");
  const imagePreview = document.getElementById("imagePreview");
  const imagePreviewContainer = document.querySelector(
    ".image-preview-container"
  );
  const AddTicketform = document.getElementById("addTicketForm");
  const EditTicketform = document.getElementById("editTicketForm");
  const hargaInput = document.getElementById("harga");

  const tipeTicketSelect = document.getElementById("tipeTicket");
  const editTipeTicketSelect = document.getElementById("edit-tipeTicket");

  const hostTicketSelect = document.getElementById("hostTicket");
  const editHostTicketSelect = document.getElementById("edit-hostTicket");

  const addTanggal = document.getElementById("tanggal");

  // NEW: Variables for location selectors
  const luarNegeriCheckbox = document.getElementById("luarNegeri");
  const editLuarNegeriCheckbox = document.getElementById("edit-luarNegeri");
  const tempatBerangkatSelect = document.getElementById("tempatBerangkat");
  const destinasiSelect = document.getElementById("destinasi");
  const editTempatBerangkatSelect = document.getElementById(
    "edit-tempatBerangkat"
  );
  const editDestinasiSelect = document.getElementById("edit-destinasi");

  // Updated API endpoints
  const INDONESIA_PROVINCES_API =
    "https://www.emsifa.com/api-wilayah-indonesia/api/provinces.json";
  const INDONESIA_REGENCIES_API =
    "https://www.emsifa.com/api-wilayah-indonesia/api/regencies";
  const WORLD_COUNTRIES_API = "https://restcountries.com/v3.1/all";

  // Data storage
  let indonesiaCities = [];
  let indonesiaProvinces = [];
  let worldCountries = [];

  // Set minimum datetime
  const now = new Date();
  const year = now.getFullYear();
  const month = String(now.getMonth() + 1).padStart(2, "0");
  const day = String(now.getDate()).padStart(2, "0");
  const hours = String(now.getHours()).padStart(2, "0");
  const minutes = String(now.getMinutes()).padStart(2, "0");
  const minDatetime = `${year}-${month}-${day}T${hours}:${minutes}`;
  addTanggal.min = minDatetime;

  // Store original hosts for filtering
  let originalHosts = [];
  if (hostTicketSelect) {
    originalHosts = Array.from(hostTicketSelect.options).map((option) => {
      return {
        value: option.value,
        text: option.text,
        type: option.getAttribute("data-type"),
      };
    });
  }

  // Truncate long descriptions
  document.querySelectorAll(".deskripsi").forEach((el) => {
    const text = el.textContent.split(" ");
    if (text && text.length > 5) {
      const shortText = text.slice(0, 5).join(" ").trim() + "...";
      el.textContent = shortText;
    }
  });

  // UPDATED: Fetch Indonesia cities from emsifa API
  async function fetchIndonesiaCities() {
    try {
      // Using emsifa API for Indonesian cities/regencies
      const provincesResponse = await fetch(INDONESIA_PROVINCES_API);
      const provinces = await provincesResponse.json();

      if (!provinces || !Array.isArray(provinces)) {
        throw new Error("Invalid provinces API response");
      }

      indonesiaProvinces = provinces;

      const allRegencies = [];

      const regencyPromises = provinces.map(async (province) => {
        try {
          const regencyResponse = await fetch(
            `${INDONESIA_REGENCIES_API}/${province.id}.json`
          );
          const regencies = await regencyResponse.json();

          if (regencies && Array.isArray(regencies)) {
            return regencies.map((regency) => ({
              ...regency,
              provinceName: province.name,
            }));
          }
          return [];
        } catch (error) {
          console.warn(
            `Failed to fetch regencies for province ${province.name}:`,
            error
          );
          return [];
        }
      });

      const regencyResults = await Promise.all(regencyPromises);

      regencyResults.forEach((regencies) => {
        allRegencies.push(...regencies);
      });
      if (allRegencies.length > 0) {
        // Filter out "KOTA" and "KABUPATEN" prefixes and sort alphabetically
        indonesiaCities = allRegencies
          .map((regency) => ({
            id: regency.id,
            name: regency.name.replace(/^(KOTA|KABUPATEN)\s+/, ""),
            provinceName: regency.provinceName,
            type: "indonesia",
          }))
          .sort((a, b) => a.name.localeCompare(b.name));
      } else {
        throw new Error("No regencies found");
      }

      console.log(
        `Successfully loaded ${indonesiaCities.length} Indonesian cities from ${indonesiaProvinces.length} provinces`
      );
    } catch (error) {
      console.error("Error fetching Indonesia cities from emsifa API:", error);

      // Enhanced fallback to comprehensive list of major Indonesian cities
      indonesiaCities = [
        {
          id: 1,
          name: "Jakarta",
          provinceName: "DKI Jakarta",
          type: "indonesia",
        },
        {
          id: 2,
          name: "Surabaya",
          provinceName: "Jawa Timur",
          type: "indonesia",
        },
        {
          id: 3,
          name: "Bandung",
          provinceName: "Jawa Barat",
          type: "indonesia",
        },
        {
          id: 4,
          name: "Medan",
          provinceName: "Sumatera Utara",
          type: "indonesia",
        },
        {
          id: 5,
          name: "Semarang",
          provinceName: "Jawa Tengah",
          type: "indonesia",
        },
        {
          id: 6,
          name: "Makassar",
          provinceName: "Sulawesi Selatan",
          type: "indonesia",
        },
        {
          id: 7,
          name: "Palembang",
          provinceName: "Sumatera Selatan",
          type: "indonesia",
        },
        { id: 8, name: "Tangerang", provinceName: "Banten", type: "indonesia" },
        { id: 9, name: "Depok", provinceName: "Jawa Barat", type: "indonesia" },
        {
          id: 10,
          name: "Bekasi",
          provinceName: "Jawa Barat",
          type: "indonesia",
        },
        {
          id: 11,
          name: "Yogyakarta",
          provinceName: "DI Yogyakarta",
          type: "indonesia",
        },
        {
          id: 12,
          name: "Padang",
          provinceName: "Sumatera Barat",
          type: "indonesia",
        },
        {
          id: 13,
          name: "Malang",
          provinceName: "Jawa Timur",
          type: "indonesia",
        },
        {
          id: 14,
          name: "Samarinda",
          provinceName: "Kalimantan Timur",
          type: "indonesia",
        },
        { id: 15, name: "Denpasar", provinceName: "Bali", type: "indonesia" },
        {
          id: 16,
          name: "Banjarmasin",
          provinceName: "Kalimantan Selatan",
          type: "indonesia",
        },
        { id: 17, name: "Pekanbaru", provinceName: "Riau", type: "indonesia" },
        {
          id: 18,
          name: "Batam",
          provinceName: "Kepulauan Riau",
          type: "indonesia",
        },
        {
          id: 19,
          name: "Bandar Lampung",
          provinceName: "Lampung",
          type: "indonesia",
        },
        {
          id: 20,
          name: "Bogor",
          provinceName: "Jawa Barat",
          type: "indonesia",
        },
        { id: 21, name: "Jambi", provinceName: "Jambi", type: "indonesia" },
        {
          id: 22,
          name: "Cimahi",
          provinceName: "Jawa Barat",
          type: "indonesia",
        },
        {
          id: 23,
          name: "Surakarta",
          provinceName: "Jawa Tengah",
          type: "indonesia",
        },
        {
          id: 24,
          name: "Balikpapan",
          provinceName: "Kalimantan Timur",
          type: "indonesia",
        },
        {
          id: 25,
          name: "Manado",
          provinceName: "Sulawesi Utara",
          type: "indonesia",
        },
        {
          id: 26,
          name: "Kupang",
          provinceName: "Nusa Tenggara Timur",
          type: "indonesia",
        },
        {
          id: 27,
          name: "Pontianak",
          provinceName: "Kalimantan Barat",
          type: "indonesia",
        },
        { id: 28, name: "Jayapura", provinceName: "Papua", type: "indonesia" },
        { id: 29, name: "Ambon", provinceName: "Maluku", type: "indonesia" },
        {
          id: 30,
          name: "Mataram",
          provinceName: "Nusa Tenggara Barat",
          type: "indonesia",
        },
        {
          id: 31,
          name: "Bengkulu",
          provinceName: "Bengkulu",
          type: "indonesia",
        },
        { id: 32, name: "Banda Aceh", provinceName: "Aceh", type: "indonesia" },
        {
          id: 33,
          name: "Gorontalo",
          provinceName: "Gorontalo",
          type: "indonesia",
        },
        {
          id: 34,
          name: "Kendari",
          provinceName: "Sulawesi Tenggara",
          type: "indonesia",
        },
        {
          id: 35,
          name: "Mamuju",
          provinceName: "Sulawesi Barat",
          type: "indonesia",
        },
        {
          id: 36,
          name: "Sorong",
          provinceName: "Papua Barat",
          type: "indonesia",
        },
        {
          id: 37,
          name: "Ternate",
          provinceName: "Maluku Utara",
          type: "indonesia",
        },
        {
          id: 38,
          name: "Pangkal Pinang",
          provinceName: "Kepulauan Bangka Belitung",
          type: "indonesia",
        },
        {
          id: 39,
          name: "Tanjung Pinang",
          provinceName: "Kepulauan Riau",
          type: "indonesia",
        },
        {
          id: 40,
          name: "South Tangerang",
          provinceName: "Banten",
          type: "indonesia",
        },
      ].sort((a, b) => a.name.localeCompare(b.name));

      console.log("Using fallback city data");
    }
  }

  // UPDATED: Fetch world countries from API (unchanged)
  async function fetchWorldCountries() {
    try {
      const response = await fetch(WORLD_COUNTRIES_API);
      const data = await response.json();

      worldCountries = data
        .filter((country) => country.name.common !== "Indonesia")
        .map((country) => ({
          id: country.cca3,
          name: country.name.common,
          type: "international",
        }))
        .sort((a, b) => a.name.localeCompare(b.name));
    } catch (error) {
      console.error("Error fetching world countries:", error);
      // Fallback data if API fails
      worldCountries = [
        { id: "SGP", name: "Singapore", type: "international" },
        { id: "MYS", name: "Malaysia", type: "international" },
        { id: "THA", name: "Thailand", type: "international" },
        { id: "JPN", name: "Japan", type: "international" },
        { id: "KOR", name: "South Korea", type: "international" },
        { id: "USA", name: "United States", type: "international" },
        { id: "GBR", name: "United Kingdom", type: "international" },
        { id: "FRA", name: "France", type: "international" },
        { id: "DEU", name: "Germany", type: "international" },
        { id: "AUS", name: "Australia", type: "international" },
        { id: "CHN", name: "China", type: "international" },
        { id: "IND", name: "India", type: "international" },
        { id: "BRA", name: "Brazil", type: "international" },
        { id: "CAN", name: "Canada", type: "international" },
        { id: "RUS", name: "Russia", type: "international" },
      ];
    }
  }

  // NEW: Populate departure location select
  function populateTempatBerangkat(selectElement, isInternational = false) {
    selectElement.innerHTML =
      '<option value="">Pilih Tempat Berangkat</option>';

    if (isInternational) {
      const citiesByProvince = indonesiaCities.reduce((acc, city) => {
        const province = city.provinceName || "Lainnya";
        if (!acc[province]) {
          acc[province] = [];
        }
        acc[province].push(city);
        return acc;
      }, {});

      Object.keys(citiesByProvince)
        .sort()
        .forEach((provinceName) => {
          const indonesiaGroup = document.createElement("optgroup");
          indonesiaGroup.label = `Indonesia - ${provinceName}`;
          citiesByProvince[provinceName].forEach((city) => {
            const option = document.createElement("option");
            option.value = city.name;
            option.textContent = city.name;
            option.setAttribute("data-type", "indonesia");
            option.setAttribute("data-province", city.provinceName);
            indonesiaGroup.appendChild(option);
          });
          selectElement.appendChild(indonesiaGroup);
        });

      const internationalGroup = document.createElement("optgroup");
      internationalGroup.label = "Luar Negeri";
      worldCountries.forEach((country) => {
        const option = document.createElement("option");
        option.value = country.name;
        option.textContent = country.name;
        option.setAttribute("data-type", "international");
        internationalGroup.appendChild(option);
      });
      selectElement.appendChild(internationalGroup);
    } else {
      // const indonesiaGroup = document.createElement("optgroup");
      // indonesiaGroup.label = "Indonesia";
      // indonesiaCities.forEach((city) => {
      //   const option = document.createElement("option");
      //   option.value = city.name;
      //   option.textContent = city.name;
      //   option.setAttribute("data-type", "indonesia");
      //   option.setAttribute("data-province", city.provinceName);
      //   indonesiaGroup.appendChild(option);
      // });
      // selectElement.appendChild(indonesiaGroup);
      const citiesByProvince = indonesiaCities.reduce((acc, city) => {
        const province = city.provinceName || "Lainnya";
        if (!acc[province]) {
          acc[province] = [];
        }
        acc[province].push(city);
        return acc;
      }, {});

      Object.keys(citiesByProvince)
        .sort()
        .forEach((provinceName) => {
          const indonesiaGroup = document.createElement("optgroup");
          indonesiaGroup.label = `Indonesia - ${provinceName}`;
          citiesByProvince[provinceName].forEach((city) => {
            const option = document.createElement("option");
            option.value = city.name;
            option.textContent = city.name;
            option.setAttribute("data-type", "indonesia");
            option.setAttribute("data-province", city.provinceName);
            indonesiaGroup.appendChild(option);
          });
          selectElement.appendChild(indonesiaGroup);
        });
    }
  }

  // NEW: Populate destination select
  function populateDestinasi(selectElement, isInternational = false) {
    selectElement.innerHTML = '<option value="">Pilih Destinasi</option>';

    if (isInternational) {
      const internationalGroup = document.createElement("optgroup");
      internationalGroup.label = "Luar Negeri";
      worldCountries.forEach((country) => {
        const option = document.createElement("option");
        option.value = country.name;
        option.textContent = country.name;
        option.setAttribute("data-type", "international");
        internationalGroup.appendChild(option);
      });
      selectElement.appendChild(internationalGroup);
    } else {
      // const indonesiaGroup = document.createElement("optgroup");
      // indonesiaGroup.label = "Indonesia";
      // indonesiaCities.forEach((city) => {
      //   const option = document.createElement("option");
      //   option.value = city.name;
      //   option.textContent = city.name;
      //   option.setAttribute("data-type", "indonesia");
      //   option.setAttribute("data-province", city.provinceName || "");
      //   indonesiaGroup.appendChild(option);
      // });
      // selectElement.appendChild(indonesiaGroup);
      const citiesByProvince = indonesiaCities.reduce((acc, city) => {
        const province = city.provinceName || "Lainnya";
        if (!acc[province]) {
          acc[province] = [];
        }
        acc[province].push(city);
        return acc;
      }, {});

      Object.keys(citiesByProvince)
        .sort()
        .forEach((provinceName) => {
          const indonesiaGroup = document.createElement("optgroup");
          indonesiaGroup.label = `Indonesia - ${provinceName}`;
          citiesByProvince[provinceName].forEach((city) => {
            const option = document.createElement("option");
            option.value = city.name;
            option.textContent = city.name;
            option.setAttribute("data-type", "indonesia");
            option.setAttribute("data-province", city.provinceName);
            indonesiaGroup.appendChild(option);
          });
          selectElement.appendChild(indonesiaGroup);
        });
    }
  }

  // UPDATED: Initialize location data
  async function initializeLocationData() {
    // Show loading indicator (optional)
    const tempatBerangkatSelect = document.getElementById("tempatBerangkat");
    const destinasiSelect = document.getElementById("destinasi");

    if (tempatBerangkatSelect) {
      tempatBerangkatSelect.innerHTML =
        '<option value="">Loading kota Indonesia...</option>';
      tempatBerangkatSelect.disabled = true;
    }

    if (destinasiSelect) {
      destinasiSelect.innerHTML = '<option value="">Loading...</option>';
      destinasiSelect.disabled = true;
    }

    try {
      // Fetch data from APIs
      await Promise.all([fetchIndonesiaCities(), fetchWorldCountries()]);

      // Initialize both forms
      if (tempatBerangkatSelect && destinasiSelect) {
        populateTempatBerangkat(tempatBerangkatSelect, false);
        populateDestinasi(destinasiSelect, false);
        tempatBerangkatSelect.disabled = false;
        destinasiSelect.disabled = false;
      }

      // Initialize edit forms if they exist
      const editTempatBerangkatSelect = document.getElementById(
        "edit-tempatBerangkat"
      );
      const editDestinasiSelect = document.getElementById("edit-destinasi");

      if (editTempatBerangkatSelect && editDestinasiSelect) {
        populateTempatBerangkat(editTempatBerangkatSelect, false);
        populateDestinasi(editDestinasiSelect, false);
      }

      console.log("Location data successfully initialized");
    } catch (error) {
      console.error("Error initializing location data:", error);

      // Re-enable selects even if there was an error
      if (tempatBerangkatSelect) tempatBerangkatSelect.disabled = false;
      if (destinasiSelect) destinasiSelect.disabled = false;
    }
  }

  // NEW: Event listener for "Luar Negeri" checkbox in add form
  if (luarNegeriCheckbox) {
    luarNegeriCheckbox.addEventListener("change", function () {
      const isInternational = this.checked;
      populateTempatBerangkat(tempatBerangkatSelect, isInternational);
      populateDestinasi(destinasiSelect, isInternational);

      // Reset selected values
      tempatBerangkatSelect.value = "";
      destinasiSelect.value = "";
    });
  }

  // NEW: Event listener for "Luar Negeri" checkbox in edit form
  if (editLuarNegeriCheckbox) {
    editLuarNegeriCheckbox.addEventListener("change", function () {
      const isInternational = this.checked;
      populateTempatBerangkat(editTempatBerangkatSelect, isInternational);
      populateDestinasi(editDestinasiSelect, isInternational);

      // Reset selected values
      editTempatBerangkatSelect.value = "";
      editDestinasiSelect.value = "";
    });
  }

  // Initialize location data on page load
  initializeLocationData();

  // Function to filter hosts by ticket type
  function filterHostsByType(ticketType, hostSelect) {
    if (!ticketType || ticketType === "") {
      hostSelect.innerHTML = "";
      hostSelect.appendChild(new Option("Pilih Host", ""));

      originalHosts.forEach((host) => {
        if (host.value !== "") {
          const option = new Option(host.text, host.value);
          option.setAttribute("data-type", host.type);
          hostSelect.appendChild(option);
        }
      });
      return;
    }

    hostSelect.innerHTML = "";
    hostSelect.appendChild(new Option("Pilih Host", ""));

    originalHosts.forEach((host) => {
      if (host.value !== "" && host.type === ticketType) {
        const option = new Option(host.text, host.value);
        option.setAttribute("data-type", host.type);
        hostSelect.appendChild(option);
      }
    });

    if (hostSelect.options.length === 1) {
      hostSelect.appendChild(new Option("Tidak ada host untuk tipe ini", ""));
      hostSelect.disabled = true;
    } else {
      hostSelect.disabled = false;
    }
  }

  // Event listeners for ticket type changes
  if (tipeTicketSelect) {
    tipeTicketSelect.addEventListener("change", function () {
      filterHostsByType(this.value, hostTicketSelect);
    });
  }

  if (editTipeTicketSelect) {
    editTipeTicketSelect.addEventListener("change", function () {
      filterHostsByType(this.value, editHostTicketSelect);
    });
  }

  // Open modal
  openModalBtn.addEventListener("click", function () {
    addModal.style.display = "block";
    document.body.style.overflow = "hidden";
  });

  // Close modal with x button
  closeBtn.forEach((e) => {
    e.addEventListener("click", function () {
      addModal.style.display = "none";
      deleteModal.style.display = "none";
      detailModal.style.display = "none";
      editModal.style.display = "none";
      document.body.style.overflow = "auto";
    });
  });

  closeBottomBtn.forEach((e) => {
    e.addEventListener("click", function () {
      addModal.style.display = "none";
      deleteModal.style.display = "none";
      detailModal.style.display = "none";
      document.body.style.overflow = "auto";
    });
  });

  // Close modal when clicking outside
  window.addEventListener("click", function (event) {
    if (event.target === addModal) {
      addModal.style.display = "none";
      document.body.style.overflow = "auto";
    }
    if (event.target === detailModal) {
      detailModal.style.display = "none";
    }
    if (event.target === editModal) {
      editModal.style.display = "none";
    }
    if (event.target === deleteModal) {
      deleteModal.style.display = "none";
    }
  });

  // Display file name and preview image when file is selected
  if (fileInput) {
    fileInput.addEventListener("change", function () {
      if (this.files.length > 0) {
        const file = this.files[0];
        fileName.textContent = file.name;

        if (file.type.match("image.*")) {
          const reader = new FileReader();
          reader.onload = function (e) {
            imagePreview.src = e.target.result;
            imagePreviewContainer.style.display = "block";
          };
          reader.readAsDataURL(file);
        }
      } else {
        fileName.textContent = "No file chosen";
        imagePreviewContainer.style.display = "none";
      }
    });
  }

  // Format currency input
  if (hargaInput) {
    hargaInput.addEventListener("input", function () {
      let value = this.value.replace(/\D/g, "");
      if (value) {
        value = parseInt(value, 10).toLocaleString("id-ID");
      }
      this.value = value;
    });
  }

  // Form submission
  if (AddTicketform) {
    AddTicketform.addEventListener("submit", function (event) {
      event.preventDefault();

      const requiredFields = AddTicketform.querySelectorAll("[required]");
      let isValid = true;

      requiredFields.forEach((field) => {
        if (!field.value.trim()) {
          isValid = false;
          field.classList.add("error");
        } else {
          field.classList.remove("error");
        }
      });

      if (!isValid) {
        alert("Mohon lengkapi semua field yang diperlukan.");
        return;
      }

      const formData = new FormData(this);
      formData.set(
        "luarNegeri",
        document.getElementById("luarNegeri").checked ? "1" : "0"
      );
      const rawHarga = document
        .getElementById("harga")
        .value.replace(/\./g, "");
      formData.set("harga", rawHarga);

      fetch("../handler/addticket_handler.php", {
        method: "POST",
        body: formData,
      })
        .then((response) => {
          if (!response.ok) {
            throw new Error("Network response was not ok");
          }
          return response.json();
        })
        .then((data) => {
          if (data.success) {
            alert("Ticket berhasil ditambahkan!");
            addModal.style.display = "none";
            AddTicketform.reset();
            fileName.textContent = "No file chosen";
            imagePreviewContainer.style.display = "none";
            editModal.style.display = "none";
            Swal.fire({
              title: "Success!",
              html: "Ticket berhasil diupload!",
              timer: 1000,
              timerProgressBar: true,
              didOpen: () => {
                const timer = Swal.getPopup().querySelector("b");
                timerInterval = setInterval(() => {
                  timer.textContent = `${Swal.getTimerLeft()}`;
                }, 100);
              },
              willClose: () => {
                clearInterval(timerInterval);
              },
            }).then((result) => {
              /* Read more about handling dismissals below */
              if (result.dismiss === Swal.DismissReason.timer) {
                location.reload();
              }
            });
          } else {
            alert(
              "Error: " +
                (data.message || "Terjadi kesalahan saat menambahkan ticket.")
            );
          }
        })
        .catch((error) => {
          console.error("Error:", error);
          alert("Terjadi kesalahan saat menghubungi server.");
        });
    });
  }

  // Edit image handling
  const editImageTujuan = document.getElementById("edit-imageTujuan");
  const editFileName = document.getElementById("edit-fileName");
  const editImagePreview = document.getElementById("edit-imagePreview");
  const editPreviewContainer = document.getElementById(
    "edit-imagePreviewContainer"
  );

  if (editImageTujuan) {
    editImageTujuan.addEventListener("change", function () {
      if (this.files && this.files[0]) {
        editFileName.textContent = this.files[0].name;

        const reader = new FileReader();
        reader.onload = function (e) {
          editImagePreview.src = e.target.result;
          editPreviewContainer.style.display = "block";
          document.getElementById("current-image-container").style.display =
            "none";
        };
        reader.readAsDataURL(this.files[0]);
      } else {
        editFileName.textContent = "No file chosen";
        editPreviewContainer.style.display = "none";
      }
    });
  }

  // Detail button click handlers
  document.querySelectorAll(".detail-btn").forEach((btn) => {
    btn.addEventListener("click", function () {
      const ticketId = this.getAttribute("data-id");
      fetchTicketDetails(ticketId);
    });
  });

  // Edit button click handlers
  document.querySelectorAll(".edit-btn").forEach((btn) => {
    btn.addEventListener("click", function () {
      const ticketId = this.getAttribute("data-id");
      fetchTicketForEdit(ticketId);
    });
  });

  // Delete button click handlers
  document.querySelectorAll(".delete-btn").forEach((btn) => {
    btn.addEventListener("click", function () {
      const ticketId = this.getAttribute("data-id");
      const ticketName =
        this.closest("tr").querySelector("td:first-child").textContent;

      document.getElementById("delete-id").value = ticketId;
      document.getElementById("delete-ticketName").textContent = ticketName;
      deleteModal.style.display = "block";
    });
  });

  // Function to fetch ticket details
  function fetchTicketDetails(ticketId) {
    fetch(`../handler/get_ticket_details.php?id=${ticketId}`)
      .then((response) => response.json())
      .then((data) => {
        if (data.status === "success") {
          const ticket = data.data;

          document.getElementById("detail-namaTicket").textContent =
            ticket.namaTicket;
          document.getElementById("detail-deskripsi").textContent =
            ticket.deskripsi;
          document.getElementById("detail-tipeTicket").textContent =
            ticket.tipeTicket;
          document.getElementById("detail-kelasTicket").textContent =
            ticket.kelasTicket;
          document.getElementById("detail-hostTicket").textContent =
            ticket.nameHost;
          document.getElementById("detail-harga").textContent = formatRupiah(
            ticket.harga
          );
          document.getElementById("detail-tempatBerangkat").textContent =
            ticket.tempatBerangkat;
          document.getElementById("detail-destinasi").textContent =
            ticket.destinasi;
          document.getElementById("detail-tanggal").textContent = formatDate(
            ticket.tanggal
          );
          document.getElementById("detail-stok").textContent = ticket.stok;
          document.getElementById("detail-penumpangMax").textContent =
            ticket.penumpangMax;

          if (ticket.imageTujuan) {
            document.getElementById(
              "detail-image"
            ).src = `../uploads/tickets/${ticket.imageTujuan}`;
            document.getElementById("detail-image-container").style.display =
              "block";
          } else {
            document.getElementById("detail-image-container").style.display =
              "none";
          }

          detailModal.style.display = "block";
        } else {
          alert("Gagal mengambil data ticket: " + data.message);
        }
      })
      .catch((error) => {
        console.error("Error:", error);
        alert("Terjadi kesalahan saat mengambil data ticket");
      });
  }

  // Function to fetch ticket data for editing
  function fetchTicketForEdit(ticketId) {
    fetch(`../handler/get_ticket_details.php?id=${ticketId}`)
      .then((response) => response.json())
      .then((data) => {
        if (data.status === "success") {
          const ticket = data.data;
          const isLuarNegeri = ticket.luarNegeri == "1";
          populateTempatBerangkat(editTempatBerangkatSelect, isLuarNegeri);
          populateDestinasi(editDestinasiSelect, isLuarNegeri);
          document.getElementById("edit-id").value = ticket.id;
          document.getElementById("edit-namaTicket").value = ticket.namaTicket;
          document.getElementById("edit-deskripsi").value = ticket.deskripsi;
          document.getElementById("edit-tipeTicket").value = ticket.tipeTicket;

          // Set the ticket type, then filter hosts
          filterHostsByType(ticket.tipeTicket, editHostTicketSelect);

          document.getElementById("edit-kelasTicket").value =
            ticket.kelasTicket;
          document.getElementById("edit-hostTicket").value = ticket.hostTicket;
          document.getElementById("edit-harga").value = ticket.harga;

          // NEW: Handle location selectors for edit
          document.getElementById("edit-luarNegeri").checked = isLuarNegeri;

          // Populate location selectors based on luarNegeri status

          // Set the values after populating options
          setTimeout(() => {
            document.getElementById("edit-tempatBerangkat").value =
              ticket.tempatBerangkat;
            document.getElementById("edit-destinasi").value = ticket.destinasi;
            alert(ticket.destinasi);
          }, 300);

          // Format date for datetime-local input
          const dateObj = new Date(ticket.tanggal);
          const formattedDate = dateObj.toISOString().slice(0, 16);
          document.getElementById("edit-tanggal").value = formattedDate;

          document.getElementById("edit-stok").value = ticket.stok;
          document.getElementById("edit-penumpangMax").value =
            ticket.penumpangMax;

          // Handle image display
          if (ticket.imageTujuan) {
            document.getElementById(
              "current-image"
            ).src = `../uploads/tickets/${ticket.imageTujuan}`;
            document.getElementById("current-image-container").style.display =
              "block";
          } else {
            document.getElementById("current-image-container").style.display =
              "none";
          }

          // Reset file input
          document.getElementById("edit-fileName").textContent =
            "No file chosen";
          document.getElementById("edit-imagePreviewContainer").style.display =
            "none";

          editModal.style.display = "block";
        } else {
          alert("Gagal mengambil data ticket: " + data.message);
        }
      })
      .catch((error) => {
        console.error("Error:", error);
        alert("Terjadi kesalahan saat mengambil data ticket");
      });
  }

  // Format rupiah function
  function formatRupiah(angka) {
    return "Rp " + Number(angka).toLocaleString("id-ID");
  }

  // Format date function
  function formatDate(dateString) {
    const options = {
      weekday: "long",
      year: "numeric",
      month: "long",
      day: "numeric",
      hour: "2-digit",
      minute: "2-digit",
    };
    return new Date(dateString).toLocaleDateString("id-ID", options);
  }

  if (EditTicketform) {
    EditTicketform.addEventListener("submit", function (event) {
      event.preventDefault();

      const requiredFields = EditTicketform.querySelectorAll("[required]");
      let isValid = true;

      requiredFields.forEach((field) => {
        if (!field.value.trim()) {
          isValid = false;
          field.classList.add("error");
        } else {
          field.classList.remove("error");
        }
      });

      if (!isValid) {
        alert("Mohon lengkapi semua field yang diperlukan.");
        return;
      }

      const formData = new FormData(this);
      formData.set(
        "luarNegeri",
        document.getElementById("luarNegeri").checked ? "1" : "0"
      );
      const rawHarga = document
        .getElementById("edit-harga")
        .value.replace(/\./g, "");
      formData.set("harga", rawHarga);

      fetch("../handler/edit_ticket_handler.php", {
        method: "POST",
        body: formData,
      })
        .then((response) => {
          if (!response.ok) {
            throw new Error("Network response was not ok");
          }
          return response.json();
        })
        .then((data) => {
          if (data.success) {
            addModal.style.display = "none";
            EditTicketform.reset();
            fileName.textContent = "No file chosen";
            imagePreviewContainer.style.display = "none";
            editModal.style.display = "none";
            let timerInterval;
            Swal.fire({
              title: "Success!",
              html: "Ticket berhasil diedit!",
              timer: 1000,
              timerProgressBar: true,
              didOpen: () => {
                const timer = Swal.getPopup().querySelector("b");
                timerInterval = setInterval(() => {
                  timer.textContent = `${Swal.getTimerLeft()}`;
                }, 100);
              },
              willClose: () => {
                clearInterval(timerInterval);
              },
            }).then((result) => {
              /* Read more about handling dismissals below */
              if (result.dismiss === Swal.DismissReason.timer) {
                location.reload();
              }
            });
          } else {
            alert(
              "Error: " +
                (data.message || "Terjadi kesalahan saat menambahkan ticket.")
            );
          }
        })
        .catch((error) => {
          console.error("Error:", error);
          alert("Terjadi kesalahan saat menghubungi server.");
        });
    });
  }
});

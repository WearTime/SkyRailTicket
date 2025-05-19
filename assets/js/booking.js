document.addEventListener("DOMContentLoaded", function () {
  const modal = document.getElementById("modalBooking");
  const closeModalButtons = document.querySelectorAll(".close, .close-modal");
  const openModalButton = document.querySelector(".add-booking");
  const form = document.getElementById("formBooking");

  // Buka modal
  openModalButton.addEventListener("click", () => {
    modal.style.display = "flex";
  });

  // Tutup modal
  closeModalButtons.forEach((btn) => {
    btn.addEventListener("click", () => {
      modal.style.display = "none";
      form.reset();
    });
  });

  // Handle submit form
  form.addEventListener("submit", function (e) {
    e.preventDefault();

    const formData = new FormData(form);

    fetch("proses_tambah_booking.php", {
      method: "POST",
      body: formData,
    })
      .then((res) => res.json())
      .then((data) => {
        if (data.success) {
          Swal.fire("Berhasil!", data.message, "success").then(() => {
            location.reload();
          });
        } else {
          Swal.fire("Gagal!", data.message, "error");
        }
      })
      .catch((err) => {
        console.error("Error:", err);
        Swal.fire("Error", "Terjadi kesalahan saat mengirim data.", "error");
      });
  });
});

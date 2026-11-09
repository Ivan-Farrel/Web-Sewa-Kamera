const rupiah = value => "Rp " + Number(value).toLocaleString("id-ID");

function addDaysToDateString(dateStr, daysToAdd) {
    if (!dateStr) return "";
    const parts = dateStr.split("-");
    if (parts.length !== 3) return dateStr;
    const year = parseInt(parts[0], 10);
    const month = parseInt(parts[1], 10) - 1;
    const day = parseInt(parts[2], 10);

    const d = new Date(year, month, day);
    d.setDate(d.getDate() + Number(daysToAdd));

    const resYear = d.getFullYear();
    const resMonth = String(d.getMonth() + 1).padStart(2, "0");
    const resDay = String(d.getDate()).padStart(2, "0");
    return `${resYear}-${resMonth}-${resDay}`;
}

const startDate = document.getElementById("startDate");
const endDate = document.getElementById("endDate");
const bookingLink = document.getElementById("bookingLink");
const displayedPrice = document.getElementById("displayedPrice");
const dateHint = document.getElementById("dateHint");

let selectedDuration = "24h";
let selectedAddDays = 1;
let selectedDays = 1;

function refreshDateAndLink() {
    if (startDate && startDate.value && endDate) {
        endDate.value = addDaysToDateString(startDate.value, selectedAddDays);
    }
    updateBookingLink();
}

function updateBookingLink() {
    if (!bookingLink) return;
    try {
        const url = new URL(bookingLink.href, window.location.origin);
        url.searchParams.set("duration", selectedDuration);
        url.searchParams.set("days", String(selectedDays));
        if (startDate && startDate.value) {
            url.searchParams.set("start", startDate.value);
        }
        bookingLink.href = url.pathname + url.search;
    } catch (e) {
        // fallback
    }
}

document.querySelectorAll(".duration-options button").forEach(button => {
    button.addEventListener("click", () => {
        document.querySelectorAll(".duration-options button").forEach(item => item.classList.remove("active"));
        button.classList.add("active");

        selectedDuration = button.dataset.duration || "24h";
        selectedAddDays = Number(button.dataset.addDays ?? 1);
        selectedDays = (selectedDuration === "2d") ? 2 : 1;
        const price = Number(button.dataset.price) || 0;
        const label = button.dataset.label || button.textContent.trim();

        if (displayedPrice && price > 0) {
            displayedPrice.innerHTML = `${rupiah(price)}<small id="displayedDuration">/ ${label.toLowerCase()}</small>`;
        }

        if (dateHint) {
            if (selectedDuration === "8h") {
                dateHint.textContent = "📅 Pengembalian di hari yang sama (maksimal 8 jam).";
            } else if (selectedDuration === "12h") {
                dateHint.textContent = "📅 Pengembalian di hari yang sama (maksimal 12 jam).";
            } else if (selectedDuration === "24h") {
                dateHint.textContent = "📅 Pengembalian 1 hari setelah sewa (durasi 24 jam).";
            } else if (selectedDuration === "2d") {
                dateHint.textContent = "📅 Pengembalian 2 hari setelah sewa (durasi 48 jam).";
            }
        }

        refreshDateAndLink();
    });
});

if (startDate) {
    startDate.addEventListener("change", () => {
        refreshDateAndLink();
    });
}

// Initial calculation on page load
if (startDate && endDate) {
    const activeBtn = document.querySelector(".duration-options button.active");
    if (activeBtn) {
        selectedAddDays = Number(activeBtn.dataset.addDays ?? 1);
        selectedDuration = activeBtn.dataset.duration || "24h";
        selectedDays = (selectedDuration === "2d") ? 2 : 1;
    }
    refreshDateAndLink();
}

// Booking form & guarantee preview handler
const bookingForm = document.getElementById("bookingForm");
if (bookingForm) {
    const dateInput = document.getElementById("bookingStartDate");
    dateInput?.addEventListener("change", () => {
        dateInput.setCustomValidity(dateInput.value && dateInput.value < dateInput.min ? "Tanggal mulai harus hari ini atau setelahnya." : "");
    });
}

// Preview file input image
function setupImagePreview(inputId, previewId) {
    const input = document.getElementById(inputId);
    const preview = document.getElementById(previewId);
    if (input && preview) {
        input.addEventListener("change", function () {
            const file = this.files && this.files[0];
            if (file) {
                const reader = new FileReader();
                reader.onload = function (e) {
                    preview.src = e.target.result;
                    preview.style.display = "block";
                };
                reader.readAsDataURL(file);
            }
        });
    }
}

setupImagePreview("ktpInput", "ktpPreview");
setupImagePreview("guaranteeInput", "guaranteePreview");

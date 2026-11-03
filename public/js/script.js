const rupiah = value => "Rp " + Number(value).toLocaleString("id-ID");

const startDate = document.getElementById("startDate");
const endDate = document.getElementById("endDate");
const bookingLink = document.getElementById("bookingLink");
let selectedDays = 1;

function updateBookingLink() {
    if (!bookingLink) return;
    const url = new URL(bookingLink.href);
    url.searchParams.set("days", String(selectedDays));
    if (startDate?.value) url.searchParams.set("start", startDate.value);
    bookingLink.href = url.toString();
}

document.querySelectorAll(".duration-options button").forEach(button => {
    button.addEventListener("click", () => {
        document.querySelectorAll(".duration-options button").forEach(item => item.classList.remove("active"));
        button.classList.add("active");
        selectedDays = Number(button.dataset.days) || 1;
        if (startDate?.value && endDate) {
            const date = new Date(`${startDate.value}T00:00:00`);
            date.setDate(date.getDate() + selectedDays - 1);
            endDate.value = date.toISOString().slice(0, 10);
        }
        updateBookingLink();
    });
});

if (startDate && endDate) {
    startDate.addEventListener("change", () => {
        endDate.min = startDate.value;
        const date = new Date(`${startDate.value}T00:00:00`);
        date.setDate(date.getDate() + selectedDays - 1);
        endDate.value = date.toISOString().slice(0, 10);
        updateBookingLink();
    });
}

const bookingForm = document.getElementById("bookingForm");
if (bookingForm) {
    const dateInput = document.getElementById("bookingStartDate");
    const durationInput = document.getElementById("bookingDuration");
    const days = Math.max(1, Number(durationInput?.value || 3));
    const subtotal = days * 250000;
    const dayLabel = document.getElementById("bookingDays");
    const subtotalLabel = document.getElementById("bookingSubtotal");
    const totalLabel = document.getElementById("bookingTotal");
    if (dayLabel) dayLabel.textContent = `${days} hari`;
    if (subtotalLabel) subtotalLabel.textContent = rupiah(subtotal);
    if (totalLabel) totalLabel.textContent = rupiah(subtotal + 5000);
    dateInput?.addEventListener("change", () => {
        dateInput.setCustomValidity(dateInput.value && dateInput.value < dateInput.min ? "Tanggal mulai harus hari ini atau setelahnya." : "");
    });
}

document.querySelectorAll("#ktp").forEach(input => {
    input.addEventListener("input", () => {
        input.value = input.value.replace(/\D/g, "").slice(0, 16);
    });
});

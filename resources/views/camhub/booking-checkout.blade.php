@extends('layouts.camhub')

@section('title', 'Booking & Checkout | CamHub')

@section('content')
<div class="page-heading">
    <h1>Booking & Checkout</h1>
    <p class="muted">Pastikan detail pesanan sudah benar.</p>
</div>

<form action="{{ route('camhub.booking.store') }}" method="POST"
      class="checkout-layout">
    @csrf

    <section class="card">
        <h2>Informasi Penyewa</h2>

        <label>Nama lengkap</label>
        <input type="text" name="nama" required>

        <label>Nomor HP</label>
        <input type="text" name="nomor_hp" required>

        <label>Alamat pengiriman</label>
        <input type="text" name="alamat" required>

        <label>Pilih tanggal sewa</label>
        <div class="calendar-picker" id="booking-calendar"
             data-start="{{ request('tanggal_mulai') }}" data-end="{{ request('tanggal_kembali') }}">
            <div class="calendar-selection">
                <span id="calendar-start-label">Pilih tanggal mulai</span><span>→</span><span id="calendar-end-label">Pilih tanggal kembali</span>
            </div>
            <div class="calendar-heading">
                <button type="button" id="calendar-prev" aria-label="Bulan sebelumnya">‹</button>
                <strong id="calendar-month"></strong>
                <button type="button" id="calendar-next" aria-label="Bulan berikutnya">›</button>
            </div>
            <div class="calendar-grid calendar-weekdays" aria-hidden="true">
                <span>Min</span><span>Sen</span><span>Sel</span><span>Rab</span><span>Kam</span><span>Jum</span><span>Sab</span>
            </div>
            <div class="calendar-grid" id="calendar-days" role="group" aria-label="Pilih tanggal"></div>
            <input type="hidden" name="tanggal_mulai" id="tanggal_mulai" value="{{ request('tanggal_mulai') }}">
            <input type="hidden" name="tanggal_kembali" id="tanggal_kembali" value="{{ request('tanggal_kembali') }}">
        </div>

        <h3>Metode Pembayaran</h3>

        <label class="radio-label">
            <input type="radio" name="metode_pembayaran"
                   value="transfer" checked>
            Transfer Bank
        </label>

        <label class="radio-label">
            <input type="radio" name="metode_pembayaran"
                   value="ewallet">
            E-Wallet
        </label>
    </section>

    <aside class="card">
        <h2>Ringkasan Pesanan</h2>
        <p>Sony A7 III</p>

        @php
            $durasi = max(1, (int) request('durasi', 3));
            $subtotal = 250000 * $durasi;
            $admin = 5000;
        @endphp

        <div class="summary-row">
            <span>Durasi sewa</span>
            <span>{{ $durasi }} hari</span>
        </div>

        <div class="summary-row">
            <span>Subtotal</span>
            <span>Rp {{ number_format($subtotal, 0, ',', '.') }}</span>
        </div>

        <div class="summary-row">
            <span>Biaya admin</span>
            <span>Rp {{ number_format($admin, 0, ',', '.') }}</span>
        </div>

        <hr>

        <h2 class="price">
            Rp {{ number_format($subtotal + $admin, 0, ',', '.') }}
        </h2>

        <button type="submit" class="btn btn-block">
            Lanjutkan Pembayaran
        </button>
    </aside>
</form>
<script>
(() => {
    const root = document.getElementById('booking-calendar');
    const startInput = document.getElementById('tanggal_mulai');
    const endInput = document.getElementById('tanggal_kembali');
    const days = document.getElementById('calendar-days');
    const monthLabel = document.getElementById('calendar-month');
    const startLabel = document.getElementById('calendar-start-label');
    const endLabel = document.getElementById('calendar-end-label');
    const today = new Date(); today.setHours(0, 0, 0, 0);
    let shown = startInput.value ? new Date(`${startInput.value}T00:00:00`) : new Date(today.getFullYear(), today.getMonth(), 1);
    let choosingEnd = Boolean(startInput.value && !endInput.value);
    const iso = (date) => `${date.getFullYear()}-${String(date.getMonth()+1).padStart(2,'0')}-${String(date.getDate()).padStart(2,'0')}`;
    const display = (value) => value ? new Date(`${value}T00:00:00`).toLocaleDateString('id-ID', { day: 'numeric', month: 'short', year: 'numeric' }) : '';
    function render() {
        monthLabel.textContent = shown.toLocaleDateString('id-ID', { month: 'long', year: 'numeric' });
        startLabel.textContent = display(startInput.value) || 'Pilih tanggal mulai';
        endLabel.textContent = display(endInput.value) || 'Pilih tanggal kembali';
        days.replaceChildren();
        const first = new Date(shown.getFullYear(), shown.getMonth(), 1);
        const offset = (first.getDay() + 6) % 7;
        const count = new Date(shown.getFullYear(), shown.getMonth()+1, 0).getDate();
        for (let i = 0; i < offset; i++) { const blank = document.createElement('span'); blank.className = 'calendar-empty'; days.append(blank); }
        for (let day = 1; day <= count; day++) {
            const date = new Date(shown.getFullYear(), shown.getMonth(), day); const value = iso(date);
            const button = document.createElement('button'); button.type = 'button'; button.textContent = day;
            button.disabled = date < today || (choosingEnd && startInput.value && value < startInput.value);
            if (value === startInput.value || value === endInput.value) button.classList.add('is-selected');
            if (startInput.value && endInput.value && value > startInput.value && value < endInput.value) button.classList.add('is-in-range');
            button.setAttribute('aria-label', date.toLocaleDateString('id-ID', { dateStyle: 'full' }));
            button.addEventListener('click', () => {
                if (!choosingEnd || !startInput.value || endInput.value) { startInput.value = value; endInput.value = ''; choosingEnd = true; }
                else { endInput.value = value; choosingEnd = false; }
                render();
            });
            days.append(button);
        }
    }
    document.getElementById('calendar-prev').addEventListener('click', () => { shown = new Date(shown.getFullYear(), shown.getMonth()-1, 1); render(); });
    document.getElementById('calendar-next').addEventListener('click', () => { shown = new Date(shown.getFullYear(), shown.getMonth()+1, 1); render(); });
    root.closest('form').addEventListener('submit', (event) => { if (!startInput.value || !endInput.value) { event.preventDefault(); root.scrollIntoView({ behavior: 'smooth', block: 'center' }); } });
    render();
})();
</script>
@endsection

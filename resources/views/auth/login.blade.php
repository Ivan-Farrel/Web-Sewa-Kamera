@extends('layouts.app')
@section('title', 'Masuk atau Daftar - CamHub')

@section('content')
<main class="page auth-page" style="background-color: #f8fafc; min-height: 100vh; padding: 3rem 1rem;">
    <div style="max-width: 500px; margin: 0 auto; background: #ffffff; padding: 2.25rem; border-radius: 16px; border: 1px solid #e2e8f0; box-shadow: 0 4px 12px rgba(15, 23, 42, 0.05);">

        {{-- Alert Error / Success --}}
        @if ($errors->any())
            <div style="background: #fef2f2; border: 1px solid #fecaca; color: #dc2626; padding: 0.85rem 1rem; border-radius: 10px; margin-bottom: 1.5rem; font-size: 0.875rem;">
                <ul style="margin: 0; padding-left: 1.25rem;">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        @if (session('success'))
            <div style="background: #f0fdf4; border: 1px solid #bbf7d0; color: #16a34a; padding: 0.85rem 1rem; border-radius: 10px; margin-bottom: 1.5rem; font-size: 0.875rem;">
                {{ session('success') }}
            </div>
        @endif

        {{-- ==================== FORM LOGIN ==================== --}}
        <div>
            <h1 style="font-size: 1.65rem; font-weight: 800; color: #0f172a; margin-bottom: 0.35rem;">Masuk ke Akun CamHub</h1>
            <p style="color: #64748b; font-size: 0.9rem; margin-bottom: 1.5rem;">
                Masuk menggunakan email dan password akunmu.
            </p>

            <form action="{{ url('/login') }}" method="POST" style="display: flex; flex-direction: column; gap: 1.1rem;">
                @csrf
                <div>
                    <label style="display: block; font-size: 0.85rem; font-weight: 600; color: #1e293b; margin-bottom: 0.4rem;">Email</label>
                    <input type="email" name="email" value="{{ old('email') }}" placeholder="nama@email.com" required
                        style="width: 100%; padding: 0.7rem 0.9rem; border: 1px solid #cbd5e1; border-radius: 8px; font-size: 0.9rem; outline: none;">
                </div>

                <div>
                    <label style="display: block; font-size: 0.85rem; font-weight: 600; color: #1e293b; margin-bottom: 0.4rem;">Password</label>
                    <input type="password" name="password" placeholder="Masukkan password" required
                        style="width: 100%; padding: 0.7rem 0.9rem; border: 1px solid #cbd5e1; border-radius: 8px; font-size: 0.9rem; outline: none;">
                </div>

                <div style="display: flex; justify-content: space-between; align-items: center; font-size: 0.85rem;">
                    <label style="display: flex; align-items: center; gap: 0.5rem; color: #475569; cursor: pointer;">
                        <input type="checkbox" name="remember" style="width: 15px; height: 15px; cursor: pointer;">
                        <span>Ingat saya</span>
                    </label>
                    <a href="#" style="color: #2563eb; font-weight: 600; text-decoration: none;">Lupa password?</a>
                </div>

                <button type="submit" style="width: 100%; background: #2563eb; color: #ffffff; padding: 0.75rem; border: none; border-radius: 8px; font-weight: 700; font-size: 0.95rem; cursor: pointer;">
                    Masuk ke Akun
                </button>
            </form>

            <div style="text-align: center; margin: 1.5rem 0; font-size: 0.85rem; color: #64748b;">
                Belum punya akun? <a href="#register-form" style="color: #2563eb; font-weight: 700; text-decoration: none;">Daftar Akun Baru</a>
            </div>
        </div>

        <hr style="margin: 2rem 0; border: none; border-top: 1px dashed #cbd5e1;">

        {{-- ==================== FORM REGISTER ==================== --}}
        <div id="register-form">
            <h2 style="font-size: 1.4rem; font-weight: 800; color: #0f172a; margin-bottom: 0.35rem;">Daftar Akun Baru</h2>
            <p style="color: #64748b; font-size: 0.875rem; margin-bottom: 1.25rem;">
                Penyewa bisa menyewa langsung tanpa akun (pendaftaran bersifat opsional), sedangkan Mitra wajib mendaftar untuk mengelola etalase kamera & Rekber.
            </p>

            <form action="{{ url('/register') }}" method="POST" style="display: flex; flex-direction: column; gap: 1.1rem;">
                @csrf
                <div>
                    <label style="display: block; font-size: 0.85rem; font-weight: 600; color: #1e293b; margin-bottom: 0.4rem;">Nama Lengkap</label>
                    <input type="text" name="name" value="{{ old('name') }}" placeholder="Nama sesuai identitas" required
                        style="width: 100%; padding: 0.7rem 0.9rem; border: 1px solid #cbd5e1; border-radius: 8px; font-size: 0.9rem; outline: none;">
                </div>

                <div>
                    <label style="display: block; font-size: 0.85rem; font-weight: 600; color: #1e293b; margin-bottom: 0.4rem;">Email</label>
                    <input type="email" name="email" value="{{ old('email') }}" placeholder="email@contoh.com" required
                        style="width: 100%; padding: 0.7rem 0.9rem; border: 1px solid #cbd5e1; border-radius: 8px; font-size: 0.9rem; outline: none;">
                </div>

                <div>
                    <label style="display: block; font-size: 0.85rem; font-weight: 600; color: #1e293b; margin-bottom: 0.4rem;">No. HP / WhatsApp</label>
                    <input type="text" name="phone" value="{{ old('phone') }}" placeholder="081234567890" required
                        style="width: 100%; padding: 0.7rem 0.9rem; border: 1px solid #cbd5e1; border-radius: 8px; font-size: 0.9rem; outline: none;">
                </div>

                <div>
                    <label style="display: block; font-size: 0.85rem; font-weight: 600; color: #1e293b; margin-bottom: 0.4rem;">Daftar Sebagai</label>
                    <select id="role-select" name="role" required onchange="updateRoleHint(this.value)"
                        style="width: 100%; padding: 0.7rem 0.9rem; border: 1px solid #cbd5e1; border-radius: 8px; font-size: 0.9rem; background: #ffffff; color: #1e293b; outline: none;">
                        <option value="customer" {{ old('role') == 'customer' ? 'selected' : '' }}>Penyewa / Pelanggan (Opsional)</option>
                        <option value="vendor" {{ old('role') == 'vendor' ? 'selected' : '' }}>Mitra (Titip Sewa / Jual Kamera)</option>
                    </select>
                    <small id="role-hint" style="display: block; margin-top: 0.4rem; color: #64748b; font-size: 0.78rem; line-height: 1.4;">
                        💡 <b>Opsional untuk Penyewa:</b> Setelah daftar akan langsung diarahkan ke Beranda Utama untuk menyewa atau membeli kamera.
                    </small>
                </div>

                <div>
                    <label style="display: block; font-size: 0.85rem; font-weight: 600; color: #1e293b; margin-bottom: 0.4rem;">Password (minimal 8 karakter)</label>
                    <input type="password" name="password" placeholder="Minimal 8 karakter" required
                        style="width: 100%; padding: 0.7rem 0.9rem; border: 1px solid #cbd5e1; border-radius: 8px; font-size: 0.9rem; outline: none;">
                </div>

                <div>
                    <label style="display: block; font-size: 0.85rem; font-weight: 600; color: #1e293b; margin-bottom: 0.4rem;">Ulangi Password</label>
                    <input type="password" name="password_confirmation" placeholder="Ketik ulang password" required
                        style="width: 100%; padding: 0.7rem 0.9rem; border: 1px solid #cbd5e1; border-radius: 8px; font-size: 0.9rem; outline: none;">
                </div>

                <button type="submit" style="width: 100%; background: #0f172a; color: #ffffff; padding: 0.75rem; border: none; border-radius: 8px; font-weight: 700; font-size: 0.95rem; cursor: pointer; margin-top: 0.25rem;">
                    Daftar Akun Sekarang
                </button>
            </form>
        </div>

    </div>
</main>

<script>
function updateRoleHint(role) {
    const hint = document.getElementById('role-hint');
    if (role === 'vendor') {
        hint.innerHTML = '🏪 <b>Khusus Mitra (C2C & C2B):</b> Langsung diarahkan ke <b>Dashboard Mitra</b> untuk upload kamera/lensa, atur harga sewa/jual, konfirmasi pesanan, dan pencairan Rekber.';
        hint.style.color = '#2563eb';
    } else {
        hint.innerHTML = '💡 <b>Opsional untuk Penyewa:</b> Setelah daftar akan langsung diarahkan ke Beranda Utama untuk menyewa atau membeli kamera.';
        hint.style.color = '#64748b';
    }
}
</script>
@endsection
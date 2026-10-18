@extends('layouts.camhub')

@section('title', 'Verifikasi Identitas KYC | CamHub')

@section('content')
<section class="card kyc-card">
    <h1>Verifikasi Identitas KYC</h1>
    <p class="muted">Lengkapi data sesuai identitas resmi Anda.</p>

    <div class="steps">
        <span class="step-active">1. Data Diri</span>
        <span>2. Upload Dokumen</span>
        <span>3. Verifikasi</span>
    </div>

    <form action="{{ route('camhub.kyc.store') }}" method="POST" enctype="multipart/form-data">
        @csrf

        <label>Nama lengkap</label>
        <input type="text" name="nama_lengkap" required>

        <label>Nomor KTP</label>
        <input type="text" name="nik" maxlength="16"
               inputmode="numeric" required>

        <label>Tanggal lahir</label>
        <input type="date" name="tanggal_lahir" required>

        <label>Jenis kelamin</label>
        <select name="jenis_kelamin" required>
            <option value="">Pilih jenis kelamin</option>
            <option value="laki-laki">Laki-laki</option>
            <option value="perempuan">Perempuan</option>
        </select>

        <label for="dokumen_ktp">Foto atau scan KTP</label>
        <input id="dokumen_ktp" type="file" name="dokumen_ktp"
               accept="image/jpeg,image/png,application/pdf" required>
        <small class="field-hint">Format JPG, PNG, atau PDF. Maksimal 5 MB.</small>
        @if ($errors->any())
            <div class="form-errors" role="alert">{{ $errors->first() }}</div>
        @endif

        <button class="btn btn-block" type="submit">
            Lanjut ke Upload Dokumen
        </button>
    </form>
</section>
@endsection

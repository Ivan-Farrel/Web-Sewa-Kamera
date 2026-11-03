
@extends('layouts.app')

@section('title', 'Verifikasi Identitas KYC - CamHub')

@section('content')
<div class="page">
    <div class="panel">
        <div class="kyc-layout">
            <aside>
                <div class="kyc-steps">
                    <span class="active">● &nbsp; Data Diri</span>
                    <span>2 &nbsp; Upload Dokumen</span>
                    <span>3 &nbsp; Verifikasi</span>
                </div>

                <div class="panel" style="margin-top:35px">
                    <strong>✓ Data Anda aman</strong>
                    <p>
                        Kami menggunakan enkripsi untuk menjaga
                        keamanan informasi pribadi Anda.
                    </p>
                </div>
            </aside>

            <section>
                <h1>Verifikasi Identitas KYC</h1>
                <p>Lengkapi data diri sesuai identitas resmi Anda.</p>

                @if ($submission)
                    <div class="notice">Dokumen Anda sedang ditinjau. Pengajuan terakhir dikirim {{ $submission->created_at->format('d M Y') }}.</div>
                @else
                <form id="kycForm" method="POST" action="{{ route('kyc.store') }}" enctype="multipart/form-data">
                    @csrf
                    @if ($errors->any()) <div class="notice" role="alert">{{ $errors->first() }}</div> @endif
                    <label>Nama Lengkap</label>
                    <input type="text" name="nama_lengkap" value="{{ old('nama_lengkap', auth()->user()->name) }}" required>

                    <label>No. KTP</label>
                    <input type="text" id="ktp"
                           name="nik" inputmode="numeric" maxlength="16"
                           placeholder="Masukkan nomor KTP" required>

                    <label>Tanggal Lahir</label>
                    <input type="date" name="tanggal_lahir" required>

                    <label>Jenis Kelamin</label>
                    <select name="jenis_kelamin" required>
                        <option value="">Pilih jenis kelamin</option>
                        <option value="Laki-laki">Laki-laki</option>
                        <option value="Perempuan">Perempuan</option>
                    </select>

                    <label for="ktp-document">Foto atau PDF KTP (maksimal 5 MB)</label>
                    <input id="ktp-document" type="file" name="dokumen_ktp" accept=".jpg,.jpeg,.png,.pdf" required>

                    <button class="btn" type="submit"
                            style="width:100%;margin-top:20px">
                        Lanjut ke Upload Dokumen
                    </button>
                </form>
                @endif
            </section>
        </div>
    </div>
</div>
@endsection

<?php

namespace App\Http\Controllers;

use App\Models\KycSubmission;
use Illuminate\Http\Request;

class KycController extends Controller
{
    public function show()
    {
        return view('camhub.verifikasi-kyc');
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'nama_lengkap' => ['required', 'string', 'max:255'],
            'nik' => ['required', 'digits:16'],
            'tanggal_lahir' => ['required', 'date', 'before:today'],
            'jenis_kelamin' => ['required', 'in:laki-laki,perempuan'],
            'dokumen_ktp' => ['required', 'file', 'mimes:jpg,jpeg,png,pdf', 'max:5120'],
        ]);

        $path = $request->file('dokumen_ktp')->store('kyc', 'local');
        unset($data['dokumen_ktp']);

        KycSubmission::create($data + [
            'user_id' => $request->user()->id,
            'dokumen_path' => $path,
        ]);

        return redirect()->route('camhub.kyc')->with('success', 'Data dan dokumen KTP berhasil dikirim untuk verifikasi.');
    }
}

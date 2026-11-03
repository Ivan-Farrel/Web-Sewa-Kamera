<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class KycSubmission extends Model
{
    protected $fillable = ['user_id', 'nama_lengkap', 'nik', 'tanggal_lahir', 'jenis_kelamin', 'dokumen_path'];

    protected $hidden = ['nik', 'dokumen_path'];
}

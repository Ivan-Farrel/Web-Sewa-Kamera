<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class KycSubmission extends Model
{
    protected $fillable = ['user_id', 'nama_lengkap', 'nik', 'tanggal_lahir', 'jenis_kelamin', 'dokumen_path', 'status'];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    protected $hidden = ['nik', 'dokumen_path'];
}

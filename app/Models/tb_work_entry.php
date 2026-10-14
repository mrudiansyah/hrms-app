<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class tb_work_entry extends Model
{
    protected $table = "tb_work_entry";
    protected $fillable = ['id_employee', 'tipe_kontrak', 'periode', 'hari_kerja', 'aktual_kerja', 'ot_weekend', 'cuti', 'aktual_shift', 'import_status', 'ijin', 'sakit', 'mangkir', 'absen'];

}


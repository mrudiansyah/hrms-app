<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class tb_ksk_detail_performance extends Model
{
    protected $table="tb_ksk_detail_performance";
    protected $fillable=['periode','id_ksk_detail','warning_letter','sick','permit','alpa','late','minutes','performance','skill_matriks'];
}

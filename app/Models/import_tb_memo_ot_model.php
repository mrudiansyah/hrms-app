<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class import_tb_memo_ot_model extends Model
{
    protected $table="tb_memo_ot";
    protected $fillable=['id_memo','part_no','part_name','lines','jph_gsph','process','date_ot','start_ot','finish_ot','plan_qty','reason_ot','remark'];
}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class tb_roleuser extends Model
{
    protected $table="role_users";
    public $timestamps = false;
    protected $fillable=['user_id','role_id'];
}

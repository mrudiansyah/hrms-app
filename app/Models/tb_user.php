<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class tb_user extends Model
{
    protected $table="users";
    protected $fillable=['name','email','email_verified_at','password','remember_token'];
}

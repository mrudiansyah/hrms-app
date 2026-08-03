<?php

namespace App\Http\Controllers;
use Illuminate\Http\Request;

use Illuminate\Support\Facades\DB;
use DateTime;
use Session;
use Auth;

class log_controller extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth','verified']);
    }
    function index(){
      $email=Auth::user()->email;
      $tb_log=DB::table('log_activities')->where('subject',$email)->orderby('created_at','desc')->take(1)->get();
      foreach($tb_log as $dt){
        $id_logactivity=$dt->id;
      }
      $table_name=session('table_name','');
      $id_table=session('id_table','');
      $activity=session('activity','');
      $fields=session('fields','');
      $before=session('before','');
      $after=session('after','');

      $simpan=DB::table('log_access')->insert([
        'id_logactivity'=>$id_logactivity,
        'table_name'=>$table_name,
        'id_table'=>$id_table,
        'activity'=>$activity,
        'fields'=>$fields,
        'before'=>$before,
        'after'=>$after,
      ]);
    }
}

<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use DateTime;
use Auth;
use Illuminate\Support\Facades\Storage;

class download_controller extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth','verified']);
    }
    function index($file_name){
        return Storage::download('public/'.$file_name);
    }
    function lihat($file_name){
        //return $file_name;
        if (!Storage::exists('public/'.$file_name)) {
            abort(404);
        }
        
        return Storage::response('public/'.$file_name);
    }

}

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
        $file_name = urldecode($file_name);
        if (Storage::disk('public')->exists($file_name)) {
            return Storage::disk('public')->download($file_name);
        }
        if (Storage::disk('local')->exists('public/' . $file_name)) {
            return Storage::disk('local')->download('public/' . $file_name);
        }
        if (Storage::disk('local')->exists($file_name)) {
            return Storage::disk('local')->download($file_name);
        }
        $path = storage_path('app/public/' . $file_name);
        if (file_exists($path)) {
            return response()->download($path);
        }
        $pathLocal = storage_path('app/' . $file_name);
        if (file_exists($pathLocal)) {
            return response()->download($pathLocal);
        }
        abort(404, 'File not found');
    }
    function lihat($file_name){
        $file_name = urldecode($file_name);
        if (Storage::disk('public')->exists($file_name)) {
            return Storage::disk('public')->response($file_name);
        }
        if (Storage::disk('local')->exists('public/' . $file_name)) {
            return Storage::disk('local')->response('public/' . $file_name);
        }
        if (Storage::disk('local')->exists($file_name)) {
            return Storage::disk('local')->response($file_name);
        }
        $path = storage_path('app/public/' . $file_name);
        if (file_exists($path)) {
            return response()->file($path);
        }
        $pathLocal = storage_path('app/' . $file_name);
        if (file_exists($pathLocal)) {
            return response()->file($pathLocal);
        }
        abort(404, 'File not found');
    }

}

<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class MasterApController extends Controller
{
    public function __construct() { $this->middleware('auth'); }

    public function index() {
        $menu = 'master_ap';
        $data = DB::table('tb_area_ap')->get();
        return view('master_ap.index', compact('menu', 'data'));
    }

    public function create() {
        $menu = 'master_ap';
        return view('master_ap.create', compact('menu'));
    }

    public function store(Request $request) {
        $request->validate(['access_point' => 'required|numeric', 'area' => 'required']);
        DB::table('tb_area_ap')->insert([
            'access_point' => $request->access_point,
            'area' => $request->area
        ]);
        return redirect('/master_ap')->with('success', 'Data Saved Successfully');
    }

    public function edit($id) {
        $menu = 'master_ap';
        $data = DB::table('tb_area_ap')->where('id', $id)->first();
        if(!$data) return redirect('/master_ap')->with('error', 'Data Not Found');
        return view('master_ap.edit', compact('menu', 'data'));
    }

    public function update(Request $request, $id) {
        $request->validate(['access_point' => 'required|numeric', 'area' => 'required']);
        DB::table('tb_area_ap')->where('id', $id)->update([
            'access_point' => $request->access_point,
            'area' => $request->area
        ]);
        return redirect('/master_ap')->with('success', 'Data Updated Successfully');
    }

    public function destroy($id) {
        $data = DB::table('tb_area_master')->where('ap', $id)->first();
        if($data) return redirect('/master_ap')->with('error', 'Data Already Used');
        DB::table('tb_area_ap')->where('id', $id)->delete();
        return redirect('/master_ap')->with('success', 'Data Deleted Successfully');
    }
}

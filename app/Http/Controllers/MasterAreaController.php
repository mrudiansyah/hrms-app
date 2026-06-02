<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class MasterAreaController extends Controller
{
    public function __construct() { $this->middleware('auth'); }

    public function index() {
        $menu = 'master_area';
        $data = DB::table('tb_area_master')->get();
        return view('master_area.index', compact('menu', 'data'));
    }

    public function create() {
        $menu = 'master_area';
        return view('master_area.create', compact('menu'));
    }

    public function store(Request $request) {
        $request->validate(['plant' => 'required', 'area' => 'required', 'ap' => 'required', 'is_active' => 'required']);
        DB::table('tb_area_master')->insert([
            'plant' => $request->plant,
            'area' => $request->area,
            'ap' => $request->ap,
            'is_active' => $request->is_active
        ]);
        return redirect('/master_area')->with('success', 'Data Saved Successfully');
    }

    public function edit($id) {
        $menu = 'master_area';
        $data = DB::table('tb_area_master')->where('id', $id)->first();
        if(!$data) return redirect('/master_area')->with('error', 'Data Not Found');
        return view('master_area.edit', compact('menu', 'data'));
    }

    public function update(Request $request, $id) {
        $request->validate(['plant' => 'required', 'area' => 'required', 'ap' => 'required', 'is_active' => 'required']);
        DB::table('tb_area_master')->where('id', $id)->update([
            'plant' => $request->plant,
            'area' => $request->area,
            'ap' => $request->ap,
            'is_active' => $request->is_active
        ]);
        return redirect('/master_area')->with('success', 'Data Updated Successfully');
    }

    public function destroy($id) {
        $data = DB::table('tb_employee_area')->where('id_area', $id)->first();
        if($data) return redirect('/master_area')->with('error', 'Data Already Used');
        DB::table('tb_area_master')->where('id', $id)->delete();
        return redirect('/master_area')->with('success', 'Data Deleted Successfully');
    }
}

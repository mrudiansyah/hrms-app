<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Auth;

class WorkingAreaController extends Controller
{
    public function __construct() { $this->middleware('auth'); }

    public function index() {
        $menu = 'working_area';
        $data = DB::table('tb_employee_area')
            ->leftJoin('tb_employees', 'tb_employee_area.id_employee', '=', 'tb_employees.id')
            ->leftJoin('tb_area_master', 'tb_employee_area.id_area', '=', 'tb_area_master.id')
            ->select('tb_employee_area.*', 'tb_employees.employee_name', 'tb_area_master.area as area_name')
            ->get();
        return view('working_area.index', compact('menu', 'data'));
    }

    public function create() {
        $menu = 'working_area';
        $employees = DB::table('tb_employees')
            ->select('id', 'employee_name')
            ->whereNotIn('id', function($query) {
                $query->select('id_employee')->from('tb_employee_area');
            })
            ->where('status', 1)
            ->where('delete', 0)
            ->orderBy('employee_name')
            ->get();
        $areas = DB::table('tb_area_master')->select('id', 'area')->orderBy('area')->get();
        return view('working_area.create', compact('menu', 'employees', 'areas'));
    }

    public function store(Request $request) {
        $request->validate(['id_employee' => 'required|numeric', 'id_area' => 'required|numeric']);
        DB::table('tb_employee_area')->insert([
            'id_employee' => $request->id_employee,
            'id_area' => $request->id_area,
            'updated_at' => date('Y-m-d H:i:s'),
            'updated_by' => Auth::user()->name
        ]);
        return redirect('/working_area')->with('success', 'Data Saved Successfully');
    }

    public function edit($id) {
        $menu = 'working_area';
        $data = DB::table('tb_employee_area')->where('id', $id)->first();
        if(!$data) return redirect('/working_area')->with('error', 'Data Not Found');
        
        $employees = DB::table('tb_employees')->select('id', 'employee_name')->orderBy('employee_name')->get();
        $areas = DB::table('tb_area_master')->select('id', 'area')->orderBy('area')->get();
        
        return view('working_area.edit', compact('menu', 'data', 'employees', 'areas'));
    }

    public function update(Request $request, $id) {
        $request->validate(['id_employee' => 'required|numeric', 'id_area' => 'required|numeric']);
        DB::table('tb_employee_area')->where('id', $id)->update([
            'id_employee' => $request->id_employee,
            'id_area' => $request->id_area,
            'updated_at' => date('Y-m-d H:i:s'),
            'updated_by' => Auth::user()->name
        ]);
        return redirect('/working_area')->with('success', 'Data Updated Successfully');
    }

    public function destroy($id) {
        DB::table('tb_employee_area')->where('id', $id)->delete();
        return redirect('/working_area')->with('success', 'Data Deleted Successfully');
    }
}

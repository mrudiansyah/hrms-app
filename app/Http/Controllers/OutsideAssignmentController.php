<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Auth;

class OutsideAssignmentController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index()
    {
        $menu = 'outside';
        $driver = DB::getDriverName();
        $concatExpression = $driver === 'sqlsrv'
            ? "STRING_AGG(tb_work_contract.nama_karyawan, ', ') as employee_names"
            : "GROUP_CONCAT(tb_work_contract.nama_karyawan SEPARATOR ', ') as employee_names";

        $data = DB::table('tb_tugasluar')
            ->select('tb_tugasluar.*', 'sub.qty', 'sub.employee_names')
            ->leftJoin(DB::raw('(
                SELECT id_tugasluar, COUNT(tb_tugasluar_detail.id) as qty, 
                ' . $concatExpression . ' 
                FROM tb_tugasluar_detail 
                LEFT JOIN tb_work_contract ON tb_tugasluar_detail.id_employee = tb_work_contract.id_employee 
                GROUP BY id_tugasluar
            ) as sub'), 'tb_tugasluar.id', '=', 'sub.id_tugasluar')
            ->orderBy('tb_tugasluar.tanggal', 'desc')
            ->orderBy('tb_tugasluar.id', 'desc')
            ->get();
        return view('outside.index', compact('menu', 'data'));
    }

    public function create()
    {
        $menu = 'outside';
        $employees = DB::table('tb_work_contract')
            ->where('isactive', 1)
            ->orderBy('nama_karyawan')
            ->get();
        return view('outside.create', compact('menu', 'employees'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'book_id' => 'required',
            'tanggal' => 'required|date',
            'nopol' => 'required',
            'employees' => 'required|array'
        ]);

        DB::beginTransaction();
        try {
            $id_tugasluar = DB::table('tb_tugasluar')->insertGetId([
                'book_id' => $request->book_id,
                'tanggal' => $request->tanggal,
                'nopol' => $request->nopol
            ]);

            $details = [];
            foreach ($request->employees as $emp_id) {
                $details[] = [
                    'id_tugasluar' => $id_tugasluar,
                    'tanggal' => $request->tanggal,
                    'id_employee' => $emp_id,
                    'created_at' => now(),
                    'checked_by' => Auth::user()->name
                ];
            }
            DB::table('tb_tugasluar_detail')->insert($details);

            DB::commit();
            return redirect('/outside')->with('success', 'Outside Assignment created successfully.');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->with('error', 'Error: ' . $e->getMessage())->withInput();
        }
    }

    public function edit($id)
    {
        $menu = 'outside';
        $header = DB::table('tb_tugasluar')->where('id', $id)->first();
        if (!$header) return redirect('/outside')->with('error', 'Data not found.');

        $details = DB::table('tb_tugasluar_detail')->where('id_tugasluar', $id)->pluck('id_employee')->toArray();
        $employees = DB::table('tb_work_contract')
            ->where('isactive', 1)
            ->orderBy('nama_karyawan')
            ->get();

        return view('outside.edit', compact('menu', 'header', 'details', 'employees'));
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'book_id' => 'required',
            'tanggal' => 'required|date',
            'nopol' => 'required',
            'employees' => 'required|array'
        ]);

        DB::beginTransaction();
        try {
            DB::table('tb_tugasluar')->where('id', $id)->update([
                'book_id' => $request->book_id,
                'tanggal' => $request->tanggal,
                'nopol' => $request->nopol
            ]);

            DB::table('tb_tugasluar_detail')->where('id_tugasluar', $id)->delete();

            $details = [];
            foreach ($request->employees as $emp_id) {
                $details[] = [
                    'id_tugasluar' => $id,
                    'tanggal' => $request->tanggal,
                    'id_employee' => $emp_id,
                    'outside_status' => 1,
                    'created_at' => now(),
                    'checked_by' => Auth::user()->name
                ];
            }
            DB::table('tb_tugasluar_detail')->insert($details);

            DB::commit();
            return redirect('/outside')->with('success', 'Outside Assignment updated successfully.');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->with('error', 'Error: ' . $e->getMessage())->withInput();
        }
    }

    public function destroy($id)
    {
        DB::beginTransaction();
        try {
            DB::table('tb_tugasluar')->where('id', $id)->delete();
            DB::table('tb_tugasluar_detail')->where('id_tugasluar', $id)->delete();
            DB::commit();
            return redirect('/outside')->with('success', 'Outside Assignment deleted successfully.');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect('/outside')->with('error', 'Error: ' . $e->getMessage());
        }
    }
}

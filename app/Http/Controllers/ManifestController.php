<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use Auth;

class ManifestController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index(Request $request)
    {
        $pending = DB::table('tb_employees')
        ->select('id', 'employee_name')
        ->whereNotIn('id', function($query) {
            $query->select('id_employee')->from('tb_employee_area');
        })
        ->where('status', 1)
        ->where('delete', 0)
        ->orderBy('employee_name')
        ->count();
        if($pending>0){
            //return redirect('/working_area')->with('error', 'Employee Need Setup Area');
        }
        $now=date('Y-m-d H:i:s');
        $limit= date('Y-m-d H:i:s',strtotime('-4 hours',strtotime($now)));
        $menu = 'manifest';
        $tgl = $request->input('tgl', date('Y-m-d'));
        $shift = $request->input('shift');
        $dept_code = $request->input('dept_code');
        $ap = $request->input('ap');
        $search_name = $request->input('search_name');

        $shifts = DB::table('tb_manifest')->select('shift')->whereNotNull('shift')->distinct()->orderBy('shift')->pluck('shift');
        $departments = DB::table('tb_manifest')->select('dept_code')->whereNotNull('dept_code')->distinct()->orderBy('dept_code')->pluck('dept_code');
        $aps = DB::table('tb_manifest')->select('ap')->whereNotNull('ap')->distinct()->orderBy('ap')->pluck('ap');
        
        $query = DB::table('tb_manifest')
            ->leftJoin('tb_manifest_outside', function($join) {
                $join->on('tb_manifest.id_employee', '=', 'tb_manifest_outside.id_employee')
                     ->on('tb_manifest.tanggal', '=', 'tb_manifest_outside.tanggal');
            })
            ->select('tb_manifest.*', 'tb_manifest_outside.outside_status', 'tb_manifest_outside.referensi')
            ->where('tb_manifest.tanggal', $tgl)
            // ->where('check_out','>',$limit)
            ->whereNull('tb_manifest.keluar');

        if ($shift) $query->where('shift', $shift);
        if ($dept_code) $query->where('dept_code', $dept_code);
        if ($ap) $query->where('ap', $ap);
        if ($search_name) $query->where('employee_name', 'LIKE', '%' . $search_name . '%');

        $manifests = $query->orderBy('dept_code')->orderBy('employee_name')->get()->groupBy('dept_code');

        return view('manifest.index', compact('menu', 'manifests', 'tgl', 'shifts', 'departments', 'aps', 'search_name'));
    }

    public function updateStatus(Request $request, $id)
    {
        DB::table('tb_manifest')
            ->where('id', $id)
            ->update(['status' => $request->status]);

        return response()->json(['success' => true]);
    }

    public function updateTL(Request $request, $id)
    {
        DB::table('tb_manifest')
            ->where('id', $id)
            ->update(['tugas_luar' => $request->tugas_luar]);

        return response()->json(['success' => true]);
    }

    public function calculation($tgl)
    {
        $nama=Auth::user()->name;
        if($tgl==0)$tgl=date('Y-m-d');
        $periode=date('Y-m',strtotime($tgl));
        $now=date('Y-m-d H:i:s');
        $day = date('d',strtotime($tgl));
        $kolom = "D".$day;

        $tb1 = DB::table('view_manifest')
        ->leftjoin('tb_work_code', 'view_manifest.' . $kolom, '=', 'tb_work_code.work_code')
        ->select(
            'view_manifest.id_employee',
            'view_manifest.NIK',
            'view_manifest.PIN',
            'view_manifest.employee_name',
            'view_manifest.dept_code',
            'view_manifest.periode', 
            'view_manifest.plant', 
            'view_manifest.area', 
            'view_manifest.ap', 
            'view_manifest.plan_actual',
            'view_manifest.' . $kolom . ' as kode_kerja_hari_ini',
            'tb_work_code.category',
            'tb_work_code.category_detail',
            'tb_work_code.source_check',
        )
        ->where('view_manifest.periode', $periode)
        ->get();
        
        foreach ($tb1 as $dt1) {
            $qty=DB::table('tb_manifest')
            ->where('id_employee', $dt1->id_employee)
            ->where('tanggal', $tgl)
            ->count();
            if($dt1->category=='Abcent')$ijin=$dt1->source_check;
            else $ijin=null;
            if($dt1->source_check=='By Upload')$tl=1;
            else $tl=null;
            if($qty==0){
                $add=DB::table('tb_manifest')->insert([
                    'periode' => $dt1->periode,
                    'tanggal' => $tgl,
                    'id_employee' => $dt1->id_employee,
                    'NIK'=>$dt1->NIK,
                    'PIN'=>$dt1->PIN,
                    'employee_name'=>$dt1->employee_name,
                    'dept_code'=>$dt1->dept_code,
                    'plant'=>$dt1->plant,
                    'area'=>$dt1->area,
                    'ap'=>$dt1->ap,
                    'work_code' => $dt1->kode_kerja_hari_ini,
                    'work_time' => null,
                    'category'=>$dt1->category,
                    'category_detail'=>$dt1->category_detail,
                    'source_check'=>$dt1->source_check,
                    'ijin'=>$ijin,
                    'tugas_luar'=>$tl,
                    'created_at' => $now,
                    'created_by' => $nama,
                ]);
            }else{
                $update=DB::table('tb_manifest')->where('id_employee', $dt1->id_employee)->where('tanggal', $tgl)->update([
                    'periode' => $dt1->periode,
                    'tanggal' => $tgl,
                    'id_employee' => $dt1->id_employee,
                    'NIK'=>$dt1->NIK,
                    'PIN'=>$dt1->PIN,
                    'employee_name'=>$dt1->employee_name,
                    'dept_code'=>$dt1->dept_code,
                    'plant'=>$dt1->plant,
                    'area'=>$dt1->area,
                    'ap'=>$dt1->ap,
                    'work_code' => $dt1->kode_kerja_hari_ini,
                    'work_time' => null,
                    'category'=>$dt1->category,
                    'category_detail'=>$dt1->category_detail,
                    'source_check'=>$dt1->source_check,
                    'ijin'=>$ijin,
                    'tugas_luar'=>$tl,
                    'created_at' => $now,
                    'created_by' => $nama,
                ]);
            } 

        }
        $tb2 = DB::table('view_manifest_plan')
        ->join('tb_work_time', 'view_manifest_plan.' . $kolom, '=', 'tb_work_time.id')
        ->select(
            'view_manifest_plan.id_employee',
            'view_manifest_plan.periode', 
            'view_manifest_plan.' . $kolom . ' as shift_hari_ini',
            'tb_work_time.category',
            'tb_work_time.check_in',
            'tb_work_time.check_out',
            'tb_work_time.is_advance'
        )
        ->where('view_manifest_plan.periode', $periode)
        ->get();
        // return $tb2;
        foreach ($tb2 as $dt2) {
            $update=DB::table('tb_manifest')->where('id_employee', $dt2->id_employee)->where('tanggal', $tgl)->update([
                'work_time' => $dt2->shift_hari_ini,
                'shift' => $dt2->category,
                'check_in' => $dt2->check_in,
                'check_out' => $dt2->check_out,
                'is_advance' => $dt2->is_advance,
            ]);
        }
    }
    public function updateFinger($tgl){
        // Copy from iClock to EMS
            $tb1=DB::table('tb_iclock')->max('checktime');
            \Log::info("Last Update ".$tb1);
            $now=date('Y-m-d H:i:s');

            $tb_absen = DB::connection('fingerPrint')->table('checkinout')
            ->leftJoin('userinfo', 'checkinout.userid', '=', 'userinfo.userid')
            ->select(
                'checkinout.userid', 'userinfo.name','userinfo.badgenumber','checkinout.checktime','checkinout.checktype','checkinout.WorkCode','checkinout.sensorid'
            )
            ->where('checkinout.checktime', '>=', $tb1)
            ->where('checkinout.checktime', '<=', $now)
            ->orderby('checkinout.checktime','asc')
            ->get();
            if ($tb_absen->isNotEmpty()) {
                $dataToInsert = $tb_absen->map(function ($item) {
                    return (array) $item;
                })->toArray();
                DB::table('tb_iclock')->insert($dataToInsert);
            }
        // End Copy
        $tb1=DB::table('tb_manifest')->where('tanggal', $tgl)->where('masuk',null)->limit(50)->get();
        foreach ($tb1 as $dt1) {
            if($dt1->work_time>0){
                if($dt1->is_advance==0)$tanggal=$tgl;
                else $tanggal=date('Y-m-d',strtotime('-1 days',strtotime($tgl)));
                $check_in=$tanggal.' '.$dt1->check_in;
                $check_out=$tgl.' '.$dt1->check_out;

                //Return Checkin Range
                $ncdatein=$check_in;
                $ncdateindown= date('Y-m-d H:i:s',strtotime('-5 hours',strtotime($ncdatein)));
                $ncdateinup= date('Y-m-d H:i:s',strtotime('+5 hours',strtotime($ncdatein)));

                //Return Checkout Range
                $ncdateout=$check_out;
                $ncdateoutdown= date('Y-m-d H:i:s',strtotime('-2 hours',strtotime($ncdateout)));
                $ncdateoutup= date('Y-m-d H:i:s',strtotime('+5 hours',strtotime($ncdateout)));

                //Read Finger Print
                $actual_in=null;
                $actual_out=null;
                $PIN=$dt1->PIN;
                $lenbadge=strlen($PIN);
                $nullbadge=9-$lenbadge;
                $p='';
                for($q=1;$q<=$nullbadge;$q++){
                    $p.='0';
                }
                $badge=$p.$PIN;

                $tb1=DB::table('tb_iclock')->where('badgenumber',$badge)->where('checktime','>=',$ncdateindown)->where('checktime','<=',$ncdateinup)->orderby('checktime','asc')->limit(1)->get();
                foreach($tb1 as $row){
                    $actual_in=$row->checktime;
                }

                $tb2=DB::table('tb_iclock')->where('badgenumber',$badge)->where('checktime','>=',$ncdateoutdown)->where('checktime','<=',$ncdateoutup)->orderby('checktime','desc')->limit(1)->get();
                foreach($tb2 as $row){
                    $actual_out=$row->checktime;
                }

                //Update Manifest
                $update=DB::table('tb_manifest')->where('id_employee', $dt1->id_employee)->where('tanggal', $tgl)->update([
                    'masuk' => $actual_in,
                    'keluar' => $actual_out,
                ]);

            }
            
        }
        
        return redirect()->back()->with('success', 'Data Finger berhasil diupdate.');
    }

    public function syncManifest($tgl)
    {
        $this->calculation($tgl);
        return $this->updateFinger($tgl);
    }

    public function exportPdf(Request $request)
    {
        $tgl = $request->tgl ?: date('Y-m-d');
        
        $query = DB::table('tb_manifest')
                   ->leftJoin('tb_manifest_outside', function($join) {
                        $join->on('tb_manifest.id_employee', '=', 'tb_manifest_outside.id_employee')
                             ->on('tb_manifest.tanggal', '=', 'tb_manifest_outside.tanggal');
                    })
                   ->select('tb_manifest.*', 'tb_manifest_outside.outside_status', 'tb_manifest_outside.referensi')
                   ->where('tb_manifest.tanggal', $tgl);

        if ($request->filled('shift')) {
            $query->where('shift', $request->shift);
        }
        if ($request->filled('dept_code')) {
            $query->where('dept_code', $request->dept_code);
        }
        if ($request->filled('ap')) {
            $query->where('ap', $request->ap);
        }

        $manifestData = $query->orderBy('dept_code')->get();

        // Group by dept_code
        $manifests = [];
        foreach ($manifestData as $item) {
            $manifests[$item->dept_code][] = $item;
        }

        $pdf = \PDF::loadView('manifest.pdf', compact('manifests', 'tgl'))->setPaper('a4', 'portrait');
        return $pdf->download('Data-Manifest-'.$tgl.'.pdf');
    }
}

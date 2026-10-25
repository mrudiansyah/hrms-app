<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use DateTime;
use Auth;
use PDF;
use App\Http\Controllers\tms_controller;


class tms_AbsencyRate_optimize extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'sg:tms_AbsencyRate_optimize';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Optimized Command description for AbsencyRate';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        \Log::info("Calculate Absency Rate Start Optimized");
        $now=date('Y-m-d H:i:s');
        $day=date('d');
        $jam=date('H');

        $tgl1=date('Y-m-01');
        $periode=date('Y-m',strtotime('-1 days',strtotime($tgl1)));
        \Log::info($periode);
        $manual=1;
        // $periode='2026-08';
        $department='';
        $id_employee='';

        if($manual==1||($day<='3'&&$jam<'7')){
            
            \Log::info("Pre-fetching initial data...");
            // CACHE DATA - Mengurangi Puluhan Ribu Query di dalam Loop
            $work_times_cache = DB::connection('tms')->table('tb_work_time')->get()->keyBy('id')->toArray();
            
            // Build Base Query Filters
            $we_plan_query = DB::connection('tms')->table('tb_work_entries')
                ->leftjoin('tb_work_contract','tb_work_contract.id_employee','=','tb_work_entries.id_employee')
                ->where('periode', $periode)
                ->where('plan_actual', 'plan')
                ->where('tb_work_contract.is_draft', '0')
                ->where('tb_work_contract.jabatan','<>','PASB');
            if($department!='') $we_plan_query->where('department',$department);
            if($id_employee!='') $we_plan_query->where('tb_work_entries.id_employee',$id_employee);
            
            $plan_entries_cache = $we_plan_query->get(['tb_work_entries.*'])->keyBy('id_employee')->toArray();
            \Log::info("Plan cache keys: ".implode(',', array_keys($plan_entries_cache)));
                
            $izins_cache = [];
            $izins_raw = DB::connection('mysql')->table('tb_izins')
                ->where('apply_date', 'like', $periode.'%')
                ->where('status_personalia', '1')
                ->get();
            foreach($izins_raw as $izin) {
                if(!isset($izins_cache[$izin->id_employee])) $izins_cache[$izin->id_employee] = [];
                if(!isset($izins_cache[$izin->id_employee][$izin->apply_date])) $izins_cache[$izin->id_employee][$izin->apply_date] = [];
                $izins_cache[$izin->id_employee][$izin->apply_date][] = $izin;
            }
            
            $departments_cache = DB::connection('mysql')->table('tb_departments')->get()->keyBy('dept_code')->toArray();


            $tb_work_entries=DB::connection('tms')->table('tb_work_entries')
            ->leftjoin('tb_work_contract','tb_work_contract.id_employee','=','tb_work_entries.id_employee')
            ->leftjoin('tb_work_shift','tb_work_shift.id','tb_work_contract.id_work_shift')
            ->where([['periode',$periode],['tb_work_contract.is_draft','0'],['tb_work_contract.jabatan','<>','PASB']]);
            if($department!=''){
                $tb_work_entries=$tb_work_entries->where('department',$department);
            }
            if($id_employee!=''){
                $tb_work_entries=$tb_work_entries->where('tb_work_entries.id_employee',$id_employee);
            }
            $tb_work_entries=$tb_work_entries->where('plan_actual', 'actual')
            ->orderby('tb_work_entries.id','asc')->orderby('department','asc')
            ->get(['tb_work_entries.*','tb_work_contract.NIK','tb_work_contract.PIN','tb_work_contract.nama_karyawan','tb_work_contract.department','tb_work_contract.jabatan','tb_work_contract.id as id_contract','tb_work_contract.id_work_shift','tb_work_shift.shift_code','tb_work_shift.working_perweek','tb_work_contract.suggest_ws','tb_work_contract.position_index']);

            //Update Work Entries
                \Log::info("Update Qty: Plan, Present, Shift, Absen, Permit, Late");
                $dept='';
                foreach($tb_work_entries as $dt){
                    

                    if($dept!=$dt->department){
                        \Log::info("Dept: ".$dt->department);
                    }
                    $dept=$dt->department;
                    $working_day=0; $qty_shift=0; 
                    $qty_entry=0; $qty_absent=0; $shortage_times=0; $qty_leave=0; $permit_times=0;
                    
                    $plan_row = isset($plan_entries_cache[$dt->id_employee]) ? $plan_entries_cache[$dt->id_employee] : null;

                    for($i=1;$i<=31;$i++){
                        $j = (strlen($i)==1) ? 'D0'.$i : 'D'.$i;
                        
                        // From Plan logic
                        if($plan_row && isset($plan_row->$j) && $plan_row->$j > 0&&$dt->$j!='') {
                            $working_day++;
                            if(isset($work_times_cache[$plan_row->$j])) {
                                $dt2 = $work_times_cache[$plan_row->$j];
                                if($dt2->is_shift == 1) {
                                    $val = (string)$dt->$j;
                                    if(in_array($val, ['51','52','71','72','73','731','732','733','510','520','710','720','730','7310','7320','7330'])) {
                                        $qty_shift++;
                                    }
                                }
                            }
                        }
                        
                        // From actual logic
                        $val = (string)$dt->$j;
                        if(in_array($val, ['51','52','71','72','73','731','732','733','510','520','710','720','730','7310','7320','7330','98','981','982'])) $qty_entry++;
                        if($val == "99") $qty_absent++;
                        if(in_array($val, ["71", "72", "710", "720"])) $shortage_times++;
                        if($val == "53") $qty_leave++;
                        if($val == "55") $permit_times++;
                    }

                    // Attach calculated working_day to object for later use in Absensi Rate
                    $dt->working_day = $working_day;
                    $dt->qty_entry = $qty_entry;
                    $dt->qty_shift = $qty_shift;
                    $dt->qty_absent = $qty_absent;
                    $dt->shortage_times = $shortage_times;
                    $dt->qty_leave = $qty_leave;
                    $dt->permit_times = $permit_times;
                    
                    DB::connection('tms')->table('tb_work_entries')->where('id_employee',$dt->id_employee)->where('periode',$periode)->where('plan_actual','actual')
                    ->update([
                        'working_day'=>$working_day,
                        'qty_entry'=>$qty_entry,
                        'qty_shift'=>$qty_shift,
                        'qty_absent'=>$qty_absent,
                        'shortage_times'=>$shortage_times,
                        'permit_times'=>$permit_times,
                        'admin'=>'Task Scheduler',
                        'updated_at'=>$now
                    ]);
                }

                // Add Work Day Periode
                $cek=DB::connection('tms')->table('tb_work_day')->where('periode',$periode)->where('day_week','5')->count();
                if($cek==0){
                    $working_day = count($tb_work_entries) > 0 ? $tb_work_entries[0]->working_day : 0;
                    $add=DB::connection('tms')->table('tb_work_day')->insert([
                        'periode'=>$periode,
                        'working_day'=>$working_day,
                    ]);
                }
                
                \Log::info("Update Minute Late");
                $dept='';
                foreach($tb_work_entries as $dt){
                    if($dept!=$dt->department){
                        \Log::info("Update Minute Late Dept: ".$dt->department);
                    }
                    $dept=$dt->department;
                    $minute_shortage=0;
                    $plan_row = isset($plan_entries_cache[$dt->id_employee]) ? $plan_entries_cache[$dt->id_employee] : null;
                    
                    for($i=1;$i<=31;$i++){
                        $j = (strlen($i)==1) ? 'D0'.$i : 'D'.$i;
                        $Tgl = (strlen($i)==1) ? $periode."-0".$i : $periode."-".$i;
                        $val = (string)$dt->$j;
                        
                        if(in_array($val, ["71", "72", "710", "720"]) && $plan_row){
                            $plan_work = clone $plan_row;
                            $jumlah_selisih = 0;
                            
                            if($plan_work->$j > 0 && isset($work_times_cache[$plan_work->$j])){
                                $dt3 = $work_times_cache[$plan_work->$j];
                                $tanggal = ($dt3->is_advance == 0) ? $Tgl : date('Y-m-d',strtotime('-1 days',strtotime($Tgl)));
                                $check_in = $tanggal.' '.$dt3->check_in;
                                $check_out = $Tgl.' '.$dt3->check_out;
                                
                                if($check_in != '' && $check_in != $check_out){
                                    $ncdatein = $check_in;
                                    $ncdateindown = date('Y-m-d H:i:s',strtotime('-3 hours',strtotime($ncdatein)));
                                    $ncdateinup = date('Y-m-d H:i:s',strtotime('+5 hours',strtotime($ncdatein)));
                                    
                                    $badge = str_pad($dt->PIN, 9, "0", STR_PAD_LEFT);
                                    $actual = '';
                                    
                                    if(in_array($val, ["71", "710"])){
                                        $actual_record = DB::connection('tms')->table('tb_iclock')->where('badgenumber',$badge)->where('checktime','>=',$ncdateindown)->where('checktime','<=',$ncdateinup)->orderby('checktime','desc')->first();
                                        if($actual_record) $actual = $actual_record->checktime;
                                    }
                                    
                                    if(in_array($val, ["72", "720"])){
                                        $masuk = DB::connection('mysql')->table('tb_checktimes')
                                            ->where('NIK',$dt->NIK)->where('checktime','>=',$ncdateindown)->where('checktime','<=',$ncdateinup)
                                            ->orderby('checktime','desc')->first();
                                        if($masuk) $actual = $masuk->checktime;
                                    }
                                    
                                    if($actual!=''){
                                        $datetime1 = date_create($actual);
                                        $datetime2 = date_create($ncdatein);
                                        if ($datetime1 > $datetime2) { 
                                            $selisih = date_diff($datetime1, $datetime2);
                                            $selisih_jam=$selisih->h;
                                            $selisih_menit=$selisih->i;
                                            $jumlah_selisih=($selisih_jam*60)+$selisih_menit;
                                        }
                                    }
                                }
                            }
                            $minute_shortage += $jumlah_selisih;
                        }
                    }
                    $dt->minute_shortage = $minute_shortage;
                    DB::connection('tms')->table('tb_work_entries')->where('id_employee',$dt->id_employee)->where('periode',$periode)->where('plan_actual','actual')
                    ->update([
                        'minute_shortage'=>$minute_shortage
                    ]);
                }

                \Log::info("Update Minutes Permit, Qty Leave, Qty ABCD");
                $dept='';
                foreach($tb_work_entries as $dt){
                    if($dept!=$dt->department){
                        \Log::info("Update Dept: ".$dt->department);
                    }
                    $dept=$dt->department;
                    $minute_permit=0; $permit_times=0; $qty_leave=0; $qty_sick=0;
                    $qty_a=0; $qty_b=0; $qty_c=0; $qty_d=0;
                    
                    for($i=1;$i<=31;$i++){
                        $j = (strlen($i)==1) ? 'D0'.$i : 'D'.$i;
                        $Tgl = (strlen($i)==1) ? $periode."-0".$i : $periode."-".$i;
                        $val = (string)$dt->$j;
                        
                        if($val == "53") $qty_leave++;
                        if($val == "54") $qty_sick++;
                        if(in_array($val, ["55","73","730","731","732","733","7310","7320","7330"])){
                            $jumlah_menit=0;
                            if($val == "55"){ $jumlah_menit+=480; $qty_a++; $permit_times++; }
                            if(in_array($val, ["731", "7310"])){ $jumlah_menit+=240; $qty_b++; $permit_times++; }
                            
                            if(isset($izins_cache[$dt->id_employee][$Tgl])) {
                                foreach($izins_cache[$dt->id_employee][$Tgl] as $dt2){
                                    if($dt2->category=='C'){ $jumlah_menit+=$dt2->minutes; $qty_c++; $permit_times++; }
                                    if($dt2->category=='D'){ $jumlah_menit+=$dt2->minutes; $qty_d++; $permit_times++; }
                                }
                            }
                            $minute_permit += $jumlah_menit;
                        }
                    }
                    
                    $dt->minute_permit = $minute_permit;
                    $dt->qty_sick = $qty_sick;
                    $dt->qty_a = $qty_a;
                    $dt->qty_b = $qty_b;

                    DB::connection('tms')->table('tb_work_entries')->where('id_employee',$dt->id_employee)->where('periode',$periode)->where('plan_actual','actual')
                    ->update([
                        'minute_permit'=>$minute_permit,
                        'permit_times'=>$permit_times,
                        'qty_leave'=>$qty_leave,
                        'qty_sick'=>$qty_sick,
                        'qty_a'=>$qty_a,
                        'qty_b'=>$qty_b,
                        'qty_c'=>$qty_c,
                        'qty_d'=>$qty_d,
                    ]);
                }
                
            //End Update Work Entries

            //Update Absensi Rate
                \Log::info('Update Absensi Rate '.$periode);
                $kalendar=CAL_GREGORIAN;
                $thn=date('Y',strtotime($periode.'-01'));
                $bln=date('m',strtotime($periode.'-01'));
                $hariakhir=cal_days_in_month($kalendar,$bln,$thn);
        
                $now=date('Y-m-d H:i:s');
                $start=date('Y-m-d',strtotime($periode.'-01'));
                $finish=date('Y-m-d',strtotime($periode.'-'.$hariakhir));

                $dept='';
                $no=0;
                foreach($tb_work_entries as $dt){
                    $no++;
                    
                    $dept_id = 0; $divisi = ''; $dept_code = $dt->department;
                    if(isset($departments_cache[$dt->department])) {
                        $dt2 = $departments_cache[$dt->department];
                        $dept_id = $dt2->id;
                        $divisi = $dt2->divisi;
                    }
                    if($dt->jabatan=='PSAB'){
                        $dept_id=17;
                        $dept_code='PSAB';
                    }
                    if($dt->jabatan=='Assistant Manager'||$dt->jabatan=='Manager'||$dt->jabatan=='Asst General Manager'||$dt->jabatan=='General Manager'||$dt->jabatan=='HR Manager'){
                        $dept_id=29;
                        $dept_code='MANAGEMENT';
                    }
                    
                    $form_a_minutes=$dt->qty_a*480;
                    $setengah_minutes=$dt->qty_b*240;
                    $keluar_minutes=$dt->minute_permit-($form_a_minutes+$setengah_minutes);
                    $present_actual=$dt->qty_entry+$dt->qty_leave;
                    if($dt->working_day==''||$dt->working_day==0)$pesent_rate=0;
                    else $pesent_rate=$present_actual/$dt->working_day*100;
                    $hour_plan=$dt->working_day*8*60;
                    
                    $hour_actual=($present_actual*8*60)-($setengah_minutes+$keluar_minutes);
                    if($hour_plan==''||$hour_plan==0)$hour_rate=0;
                    else $hour_rate=$hour_actual/$hour_plan*100;
                    $jumlah_absen=$dt->qty_absent+$dt->qty_a+$dt->qty_sick;

                    $cek=DB::connection('emsAbsensi')->table('tb_absensi_rate')->where('periode',$periode)->where('id_employee',$dt->id_employee)->count();
                    if($dt->working_day!=''&&$dt->working_day>0){
                        if($cek>0){
                            $update=DB::connection('emsAbsensi')->table('tb_absensi_rate')->where('periode',$periode)->where('id_employee',$dt->id_employee)->update([
                                'hari_kerja'=>$dt->working_perweek,
                                'dept_id'=>$dept_id,
                                'NIK'=>$dt->NIK,
                                'employee_name'=>$dt->nama_karyawan,
                                'jabatan'=>$dt->jabatan,
                                'dept_code'=>$dept_code,
                                'divisi'=>$divisi,
                                'start'=>$start,
                                'end'=>$finish,
                                'terlambat'=>$dt->shortage_times,
                                'terlambat_minutes'=>$dt->minute_shortage,
                                'setengah_minutes'=>$setengah_minutes,
                                'keluar_minutes'=>$keluar_minutes,
                                'cuti'=>$dt->qty_leave,
                                'sakit'=>$dt->qty_sick,
                                'izin'=>$dt->qty_a,
                                'alpa'=>$dt->qty_absent,
                                'present_plan'=>$dt->working_day,
                                'present_actual'=>$present_actual,
                                'present_rate'=>$pesent_rate,
                                'hour_plan'=>$hour_plan,
                                'hour_actual'=>$hour_actual,
                                'hour_rate'=>$hour_rate,
                                'absent'=>$jumlah_absen,
                                'position_index'=>$dt->position_index,
                            ]);
                            if($update) \Log::info("Update Absensi Rate Dept: Update ".$no." ".$dt->department." ".$dt->id_employee);

                        }else{
                            $add=DB::connection('emsAbsensi')->table('tb_absensi_rate')->insert([
                                'periode'=>$periode,
                                'hari_kerja'=>$dt->working_perweek,
                                'dept_id'=>$dept_id,
                                'id_employee'=>$dt->id_employee,
                                'NIK'=>$dt->NIK,
                                'employee_name'=>$dt->nama_karyawan,
                                'jabatan'=>$dt->jabatan,
                                'dept_code'=>$dept_code,
                                'divisi'=>$divisi,
                                'start'=>$start,
                                'end'=>$finish,
                                'terlambat'=>$dt->shortage_times,
                                'terlambat_minutes'=>$dt->minute_shortage,
                                'setengah_minutes'=>$setengah_minutes,
                                'keluar_minutes'=>$keluar_minutes,
                                'cuti'=>$dt->qty_leave,
                                'sakit'=>$dt->qty_sick,
                                'izin'=>$dt->qty_a,
                                'alpa'=>$dt->qty_absent,
                                'present_plan'=>$dt->working_day,
                                'present_actual'=>$present_actual,
                                'present_rate'=>$pesent_rate,
                                'hour_plan'=>$hour_plan,
                                'hour_actual'=>$hour_actual,
                                'hour_rate'=>$hour_rate,
                                'absent'=>$jumlah_absen,
                                'admin'=>'Task Scheduler',
                                'position_index'=>$dt->position_index,
                                'updated_at'=>$now
                            ]);
                            if($add) \Log::info("Update Absensi Rate Dept: Add ".$no." ".$dt->department." ".$dt->id_employee);
                        }
                    }else{
                        \Log::info("SKIP ".$no." ".$dt->department." ".$dt->id_employee);
                    }
                }
            //End Update Absensi Rate

            //Update Kumulatif Dept
                \Log::info('Update Absensi Rate Summary per Department');
                
                $std=DB::table('tb_utilities')->where('atribut','standar_absensi_rate')->value('status');
                $std_ar = $std ? $std : 0;
                
                $tb_rate=DB::connection('emsAbsensi')->table('tb_absensi_rate')
                ->select('dept_id','hari_kerja','divisi','dept_code','start','end')
                ->where('periode',$periode)->where('present_plan','>','0')->where('jabatan','<>','PSAB');
                if($department!=''){
                    $tb_rate=$tb_rate->where('dept_code',$department);
                }
                if($id_employee!=''){
                    $tb_rate=$tb_rate->where('tb_absensi_rate.id_employee',$id_employee);
                }
                $tb_rate=$tb_rate->groupby('dept_id','hari_kerja','divisi','dept_code','start','end')->get();
                
                foreach($tb_rate as $dt){
                    $check=DB::connection('emsAbsensi')->table('tb_absensi_rate_kumulatif')->where('periode',$periode)->where('dept_id',$dt->dept_id)->count();                
                    if($check==0){
                        DB::connection('emsAbsensi')->table('tb_absensi_rate_kumulatif')->insert([
                            'periode'=>$periode,
                            'hari_kerja'=>$dt->hari_kerja,
                            'dept_id'=>$dt->dept_id,
                            'dept_code'=>$dt->dept_code,
                            'divisi'=>$dt->divisi,
                            'start'=>$dt->start,
                            'end'=>$dt->end,
                            'total_absen'=>'0',
                            'present_plan'=>'0',
                            'total_employee'=>'0',
                            'absensi_rate'=>'0',
                            'parameter'=>'0',
                            'kriteria'=>'0',
                            'admin'=>'Scheduller',
                            'status'=>'0',
                            'created_at'=>$now,
                            'updated_at'=>$now
                        ]);
                    }
                    
                    $tb_kumulatif= DB::connection('emsAbsensi')->table('tb_absensi_rate_kumulatif')->where('periode',$periode)->where('dept_id',$dt->dept_id)->get();
                    
                    foreach ($tb_kumulatif as $dt2){
                        $jumlah_absen=0;
                        $plan=0;
                        $jumlah_employee=0;
                        $tb_rate_items=DB::connection('emsAbsensi')->table('tb_absensi_rate')->where('periode',$periode)->where('dept_id',$dt2->dept_id)->get();
                        foreach($tb_rate_items as $dt_rate){
                            $jumlah_absen+=$dt_rate->absent;
                            if($dt_rate->present_plan>0)$plan=$dt_rate->present_plan;
                            if($plan>$dt_rate->present_plan&&$dt_rate->present_plan>0)$plan=$dt_rate->present_plan;
                            $jumlah_employee++;
                        }
                        if($plan* $jumlah_employee==0)$absensi_rate=0;
                        else $absensi_rate=number_format($jumlah_absen/($plan* $jumlah_employee)*100,2);
                        
                        $paramater=$std_ar;
                        if($absensi_rate<=$paramater)$kriteria='Baik';
                        else $kriteria='Buruk';
                        
                        $sekarang=date('Y-m-d H:i:s');
                        $satu=date('Ym');
                        $dua=date('Ym',strtotime($periode.'01'));
                        if($satu>$dua)$status=1;
                        else $status=0;
                        
                        $simpan=DB::connection('emsAbsensi')->table('tb_absensi_rate_kumulatif')->where('dept_id',$dt->dept_id)->where('periode',$periode)->update([
                            'total_absen'=>$jumlah_absen,
                            'dept_code'=>$dt->dept_code,
                            'present_plan'=>$plan,
                            'total_employee'=>$jumlah_employee,
                            'absensi_rate'=>$absensi_rate,
                            'parameter'=>$paramater,
                            'kriteria'=>$kriteria,
                            'admin'=>'Task Scheduler',
                            'status'=>$status,
                            'updated_at'=>$sekarang
                        ]);
                        if($simpan)
                        \Log::info("Update Absensi Rate Dept: ".$dt->dept_id.": ".$absensi_rate);
                        else \Log::info("SKIPt: ".$dt->dept_id.": ".$absensi_rate);
                    } 
                }
            //End Update Kumulatif Dept

            \Log::info("Calculate Absency Rate Finish");
        }else{
            \Log::info("Calculate Absency Rate Skip");
        }
    }
}

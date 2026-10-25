<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

use Illuminate\Http\Request;
use App\Models\tb_employee;
use App\Models\tb_presence;
use App\Models\tb_shift;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use DateTime;
use Auth;
use PDF;
use App\Http\Controllers\tms_controller;
use App\Mail\tms_share;
use App\Mail\tms_share_all;


class tms_payroll extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'sg:tms_payroll';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

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
        \Log::info("Start Calculate Work Entry for Payroll");
        $now=date('Y-m-d H:i:s');
        $day=date('d');
        $id_employee='';
        if($day>=20){

            $periode=date('Y-m');
            $tgl_satu=date('Y-m-d',strtotime($periode.'-01'));
            $akhir_bulan=date('Y-m-d',strtotime('-1 days',strtotime($tgl_satu)));
            $periode_lama=date('Y-m',strtotime($akhir_bulan));
            if($day>=20){
                $periode=date('Y-m');
            }else{
                $periode=date('Y-m',strtotime($akhir_bulan));
            }
            \Log::info("Start Calculate Work Entry for Payroll Karyawan");
            $table1=DB::table('tb_employees')
            ->leftjoin('tb_positions','tb_positions.id','=','tb_employees.position_id')
            ->where('position_id','<>','22')
            ->where('position_id','<>','23')
            ->where('position_id','<>','38')
            ->where('tb_employees.delete','0');
            if($id_employee!=''){
                $table1=$table1->where('tb_employees.id',$id_employee);
            }
            $table1=$table1->get(['tb_employees.*','tb_positions.position_name']);
            foreach($table1 as $dt1){
                $table2=DB::connection('payroll')->table('tb_salary_contract')->where('id_employee',$dt1->id)->count();
                if($table2==0){
                    \Log::info('Module Payroll, Belum terdaftar '.$dt1->NIK.' '.$dt1->position_name.' '.$dt1->employee_name);
                }else{
                    $tipe=DB::connection('payroll')->table('tb_salary_contract')->where('id_employee',$dt1->id)->where('status','1')->value('tipe_kontrak');
                    $table3=DB::connection('payroll')->table('tb_work_entry')->where('id_employee',$dt1->id)->where('periode',$periode)->count();
                    if($table3==0){
                        $table3=DB::connection('payroll')->table('tb_work_entry')->insert([
                            'id_employee'=>$dt1->id,
                            'periode'=>$periode,
                            'tipe_kontrak'=>$tipe,
                        ]);
                        //\Log::info('Module Payroll, Add Work Entry '.$dt1->NIK.' '.$dt1->position_name.' '.$dt1->employee_name);
                    }
                    $table3=DB::connection('payroll')->table('tb_work_entry_tua')->where('id_employee',$dt1->id)->where('periode',$periode)->count();
                    if($table3==0){
                        $table3=DB::connection('payroll')->table('tb_work_entry_tua')->insert([
                            'id_employee'=>$dt1->id,
                            'tipe_kontrak'=>$tipe,
                            'periode'=>$periode
                        ]);
                        //\Log::info('Module Payroll, Add Work Entry '.$dt1->NIK.' '.$dt1->position_name.' '.$dt1->employee_name);
                    }
                    $table3=DB::connection('payroll')->table('tb_work_entry_muda')->where('id_employee',$dt1->id)->where('periode',$periode)->count();
                    if($table3==0){
                        $table3=DB::connection('payroll')->table('tb_work_entry_muda')->insert([
                            'id_employee'=>$dt1->id,
                            'tipe_kontrak'=>$tipe,
                            'periode'=>$periode
                        ]);
                        //\Log::info('Module Payroll, Add Work Entry '.$dt1->NIK.' '.$dt1->position_name.' '.$dt1->employee_name);
                    }
                }
            }
            \Log::info('Payroll, Update Work Entry '.$periode);

            $tgl_satu=date('Y-m-d',strtotime($periode.'-01'));
            $akhir_bulan=date('Y-m-d',strtotime('-1 days',strtotime($tgl_satu)));
            $periode_tua=date('Y-m',strtotime($akhir_bulan));

            $table4=DB::connection('payroll')->table('tb_salary_utility')->where('item','cut_off_date')->get(['nilai']);
            foreach($table4 as $dt4){
                $cut_off_date=$dt4->nilai;
            }

            $table5=DB::connection('payroll')->table('tb_work_entry')->where('periode',$periode)->get();
            $no=0;
            foreach($table5 as $dt5){
                $table6=DB::table('tb_employees')->where('id',$dt5->id_employee)->where('delete','1')->count();
                if($table6>0)$no++;
            }
            if($no>0)\Log::info('Payroll, Problem: Employee sudah tidak terdaftar di payroll '.$periode);
            else{
                $table7=DB::connection('payroll')->table('tb_work_entry')->where('periode',$periode);
                if($id_employee!=''){
                    $table7=$table7->where('tb_work_entry.id_employee',$id_employee);
                }
                $table7=$table7->get();
                $proses=0;
                foreach($table7 as $dt7){
                    //\Log::info('Module Payroll, Update Work Entry ...';
                    $hari_kerja_tua=0;
                    $aktual_kerja_tua=0;
                    $aktual_kerja_add_tua=0;
                    $ijin=0;
                    $mangkir=0;
                    $cuti=0;
                    $sakit=0;
                    $shift=0;
                    if($cut_off_date!=1){
                        //Check WE Periode Tua
                        $ijin_tua=0;
                        $mangkir_tua=0;
                        $cuti_tua=0;
                        $sakit_tua=0;
                        $shift_tua=0;
                        $shift_add_tua=0;
                        $table8=DB::connection('tms')->table('tb_work_entries')->where('id_employee',$dt7->id_employee)->where('periode',$periode_tua)->orderby('plan_actual','desc')->get();
                        foreach($table8 as $dt8){
                            if($dt8->plan_actual=='plan'){
                                for($i=$cut_off_date;$i<=31;$i++){
                                    $kolom='D'.$i;
                                    if($dt8->$kolom>0)$hari_kerja_tua++;
                                }
                            }
                            if($dt8->plan_actual=='actual'){
                                for($i=$cut_off_date;$i<=31;$i++){
                                    $kolom='D'.$i;
                                    if($dt8->$kolom=='51'||$dt8->$kolom=='52'||$dt8->$kolom=='71'||$dt8->$kolom=='72'||$dt8->$kolom=='73'||$dt8->$kolom=='731'||$dt8->$kolom=='732'||$dt8->$kolom=='733'||$dt8->$kolom=='510'||$dt8->$kolom=='520'||$dt8->$kolom=='710'||$dt8->$kolom=='720'||$dt8->$kolom=='730'||$dt8->$kolom=='7310'||$dt8->$kolom=='7320'||$dt8->$kolom=='7330'){
                                        $aktual_kerja_tua++;
                                        if($dt8->$kolom=='510'||$dt8->$kolom=='520'||$dt8->$kolom=='710'||$dt8->$kolom=='720'||$dt8->$kolom=='730'||$dt8->$kolom=='7310'||$dt8->$kolom=='7320'||$dt8->$kolom=='7330'){
                                            $shift_tua++;                                 
                                        }                                        
                                    }
                                    if($dt8->$kolom=='55'){$ijin++;$ijin_tua++;}
                                    if($dt8->$kolom=='99'){$mangkir++;$mangkir_tua++;}
                                    if($dt8->$kolom=='53'){$cuti++;$cuti_tua++;}
                                    if($dt8->$kolom=='54'){$sakit++;$sakit_tua++;}
                                }
                            }
                        }
                        //OT calculation tua
                        $start_ot_tua=$periode_lama.'-25';
                        $end_ot_tua=$akhir_bulan;
                        $tb1=DB::table('tb_overtime_details')->where('date_on','>=',$start_ot_tua)->where('date_on','<=',$end_ot_tua)->where('ot_category','2')->where('id_employee',$dt7->id_employee)->get();
                        foreach($tb1 as $dt1){
                            $aktual_kerja_add_tua++;
                            $jam_awal=date('H',strtotime($dt1->start_act));
                            if($jam_awal>=15)$shift_add_tua++;
                        }
                        $tb1=DB::table('tb_overtime_spv')->where('ot_date','>=',$start_ot_tua)->where('ot_date','<=',$end_ot_tua)->where('amount','>=','400000')->where('id_employee',$dt7->id_employee)->get();
                        foreach($tb1 as $dt1){
                            $aktual_kerja_add_tua++;
                            $jam_awal=date('H',strtotime($dt1->start_act));
                            if($jam_awal>=15)$shift_add_tua++;
                        }
                        
                    }
                    //Check WE Periode Muda
                    $hari_kerja_muda=0;
                    $aktual_kerja_muda=0;
                    $aktual_kerja_add_muda=0;
                    $ijin_muda=0;
                    $mangkir_muda=0;
                    $cuti_muda=0;
                    $sakit_muda=0;
                    $shift_muda=0;
                    $shift_add_muda=0;
                    $qty_nopayment=0;
                    $table8=DB::connection('tms')->table('tb_work_entries')->where('id_employee',$dt7->id_employee)->where('periode',$periode)->orderby('plan_actual','desc')->get();
                    foreach($table8 as $dt8){
                        if($dt8->plan_actual=='plan'){
                            for($i=1;$i<$cut_off_date;$i++){
                                if(strlen($i)==1)$j='0'.$i;
                                else $j=$i;
                                $kolom='D'.$j;
                                if($dt8->$kolom>0)$hari_kerja_muda++;
                            }
                        }
                        if($dt8->plan_actual=='actual'){
                            for($i=1;$i<$cut_off_date;$i++){
                                if(strlen($i)==1)$j='0'.$i;
                                else $j=$i;
                                $kolom='D'.$j;
                                if($dt8->$kolom=='51'||$dt8->$kolom=='52'||$dt8->$kolom=='71'||$dt8->$kolom=='72'||$dt8->$kolom=='73'||$dt8->$kolom=='731'||$dt8->$kolom=='732'||$dt8->$kolom=='733'||$dt8->$kolom=='510'||$dt8->$kolom=='520'||$dt8->$kolom=='710'||$dt8->$kolom=='720'||$dt8->$kolom=='730'||$dt8->$kolom=='7310'||$dt8->$kolom=='7320'||$dt8->$kolom=='7330'){
                                    $aktual_kerja_muda++;
                                    if($dt8->$kolom=='510'||$dt8->$kolom=='520'||$dt8->$kolom=='710'||$dt8->$kolom=='720'||$dt8->$kolom=='730'||$dt8->$kolom=='7310'||$dt8->$kolom=='7320'||$dt8->$kolom=='7330'){
                                        $shift_muda++;                                 
                                    }                                        
                                }
                                if($dt8->$kolom=='55'){$ijin++;$ijin_muda++;}
                                if($dt8->$kolom=='99'){$mangkir++;$mangkir_muda++;}
                                if($dt8->$kolom=='53'){$cuti++;$cuti_muda++;}
                                if($dt8->$kolom=='54'){$sakit++;$sakit_muda++;}
                                if($dt8->$kolom=='97'){$qty_nopayment++;}
                            }
                        }
                    }

                    //OT calculation muda
                    $start_ot_muda=$periode.'-01';
                    $end_ot_muda=$periode.'-24';
                    $tb2=DB::table('tb_overtime_details')->where('date_on','>=',$start_ot_muda)->where('date_on','<=',$end_ot_muda)->where('ot_category','2')->where('id_employee',$dt7->id_employee)->get();
                    foreach($tb2 as $dt2){
                        $aktual_kerja_add_muda++;
                        $jam_awal=date('H',strtotime($dt2->start_act));
                        if($jam_awal>=15)$shift_add_muda++;
                    }
                    $tb3=DB::table('tb_overtime_spv')->where('ot_date','>=',$start_ot_muda)->where('ot_date','<=',$end_ot_muda)->where('amount','>=','400000')->where('id_employee',$dt7->id_employee)->get();
                    foreach($tb3 as $dt3){
                        $aktual_kerja_add_muda++;
                        $jam_awal=date('H',strtotime($dt3->start_act));
                        if($jam_awal>=15)$shift_add_muda++;
                    }
                    
                    // OT calculation combine
                    $aktual_kerja_add=$aktual_kerja_add_tua+$aktual_kerja_add_muda;
                    
                    $aktual_kerja_tua=$aktual_kerja_tua+$aktual_kerja_add_tua;
                    $aktual_kerja_muda=$aktual_kerja_muda+$aktual_kerja_add_muda;
                    $shift_tua=$shift_tua+$shift_add_tua;
                    $shift_muda=$shift_muda+$shift_add_muda;

                    $hari_kerja=$hari_kerja_tua+$hari_kerja_muda;
                    $aktual_kerja=$aktual_kerja_tua+$aktual_kerja_muda;
                    $absen=$ijin+$mangkir;
                    $absen_tua=$ijin_tua+$mangkir_tua;
                    $absen_muda=$ijin_muda+$mangkir_muda;
                    $shift=$shift_tua+$shift_muda;

                    if($dt7->tipe_kontrak=='MAGANG'){
                        $absen=$absen+$sakit;
                        $absen_tua=$absen_tua+$shift_tua;
                        $absen_muda=$absen_muda+$sakit_muda;
                    }

                    $proses++;
                    \Log::info('Update Work Entry '.$periode_tua.' '.$periode.' '.$id_employee.' '.$proses);
                    //Update Payroll Work Entry
                    $update1=DB::connection('payroll')->table('tb_work_entry')->where('id',$dt7->id)->update([
                        'hari_kerja'=>$hari_kerja,
                        'aktual_kerja'=>$aktual_kerja,
                        'ijin'=>$ijin,
                        'mangkir'=>$mangkir,
                        'absen'=>$absen,
                        'cuti'=>$cuti,
                        'aktual_shift'=>$shift,
                        'sakit'=>$sakit,
                        'import_status'=>'0',
                        'ot_weekend'=>$aktual_kerja_add,
                        'updated_at'=>$now
                    ]);
                    //if($update1)\Log::info('Update Work Entry Total'.$proses.' HK:'.$hari_kerja.' TRANS:'.$aktual_kerja.' SHIFT:'.$shift.' ABSEN:'.$absen.' IDEMPL:'.$dt7->id_employee);
                    $update2=DB::connection('payroll')->table('tb_work_entry_tua')->where('id_employee',$dt7->id_employee)->where('periode',$periode)->update([
                        'hari_kerja'=>$hari_kerja_tua,
                        'aktual_kerja'=>$aktual_kerja_tua,
                        'ijin'=>$ijin_tua,
                        'mangkir'=>$mangkir_tua,
                        'absen'=>$absen_tua,
                        'cuti'=>$cuti_tua,
                        'aktual_shift'=>$shift_tua,
                        'sakit'=>$sakit_tua,
                        'import_status'=>'0',
                        'ot_weekend'=>$aktual_kerja_add_tua,
                        'updated_at'=>$now
                    ]);
                    //if($update2)\Log::info('Update Work Entry Tua '.$proses.' HK:'.$hari_kerja_tua.' TRANS:'.$aktual_kerja_tua.' SHIFT:'.$shift_tua.' ABSEN:'.$absen_tua.' IDEMPL:'.$dt7->id_employee);
                    //else \Log::info('Gagal Update Work Entry Tua '.$proses.' idemployee:'.$dt7->id_employee.' periode:'.$aktual_kerja_tua.' ABSEN:'.$absen_tua.' IDEMPL:'.$dt7->id_employee);
                    $update3=DB::connection('payroll')->table('tb_work_entry_muda')->where('id_employee',$dt7->id_employee)->where('periode',$periode)->update([
                        'hari_kerja'=>$hari_kerja_muda,
                        'aktual_kerja'=>$aktual_kerja_muda,
                        'ijin'=>$ijin_muda,
                        'mangkir'=>$mangkir_muda,
                        'absen'=>$absen_muda,
                        'cuti'=>$cuti_muda,
                        'aktual_shift'=>$shift_muda,
                        'sakit'=>$sakit_muda,
                        'import_status'=>'0',
                        'ot_weekend'=>$aktual_kerja_add_muda,
                        'updated_at'=>$now
                    ]);
                    //if($update3)\Log::info('Update Work Entry Muda '.$proses.' HK:'.$hari_kerja_muda.' TRANS:'.$aktual_kerja_muda.' SHIFT:'.$shift_muda.' ABSEN:'.$absen_muda.' IDEMPL:'.$dt7->id_employee);
                }

            }

            \Log::info('Finish '.$periode);
            
        }

    }
}

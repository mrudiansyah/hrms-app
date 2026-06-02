<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use DateTime;
use Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Crypt;

class DashboardController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth','verified']);
        $this->site = $_SERVER['SCRIPT_NAME'];
    }
    function index(){
        $today=date('Y-m-d');

        //Expired Check
        $tb_utility=DB::table('tb_utilities')->where('atribut','expired_password')->where('status','1')->count();
        if($tb_utility>0){
            $locked=Auth::user()->locked_status;
            $expired_date=Auth::user()->expired_date;
            if($expired_date<$today) {
                return redirect('/ChangePassword');
            }
        }
        //Expired Check End

        $qty_ksk=$this->kskReminder();

        $notif = 0;
        $email=Auth::user()->email;
        $tb_email=DB::table('users')->where('email',$email)->get();
        foreach($tb_email as $dt){
            $notif=$dt->notif;
        }


        $qty_magang=DB::table('tb_statuses')
        ->leftjoin('tb_employees','tb_employees.id','=','tb_statuses.id_employee')
        ->where('tb_employees.position_id','<>','37')
        ->where('contract_name','Magang')->where('active','1')->count();

        $qty_kontrak=DB::table('tb_statuses')->where('contract_name','Kontrak')->where('active','1')->count();
        $qty_sab=DB::table('tb_statuses')->where('contract_name','SAB')->where('active','1')->count();
        // $qty_magang=DB::table('tb_statuses')->where('contract_name','Magang')->where('active','1')->count();
        $qty_permanent=DB::table('tb_statuses')->where([['contract_name','Permanen'],['active','1']])->count();
        $qty_other=DB::table('tb_statuses')->where('contract_name','Other')->where('active','1')->count();
        $qty_other_sab=$qty_sab+$qty_other;
        $qty_kontraks=$qty_kontrak;
        $qty_sai=$qty_permanent+$qty_kontraks;
        // $qty_active=$qty_sai+$qty_magang+$qty_other_sab;
        $qty_active=$qty_sai+$qty_magang;
        $data['permanen_pre']=number_format($qty_permanent/$qty_active*100,2);
        $data['kontrak_pre']=number_format($qty_kontrak/$qty_active*100,2);
        $data['magang_pre']=number_format($qty_magang/$qty_active*100,2);

        if($qty_sai>0){
            $pre_permanent=number_format($qty_permanent/$qty_sai*100,2);
            $pre_kontraks=number_format($qty_kontraks/$qty_sai*100,2);
            $pre_magang_comp=number_format($qty_magang/$qty_sai*100,2);

            $pre_kontrak=number_format($qty_kontrak/$qty_active*100,2);
            $pre_sab=number_format($qty_sab/$qty_active*100,2);
            $pre_other=number_format($qty_other/$qty_active*100,2);
            $pre_other_sab=number_format($qty_other_sab/$qty_active*100,2);
            $pre_magang=number_format($qty_magang/$qty_active*100,2);
            $pre_sai=number_format($qty_sai/$qty_active*100,2);
        }else{
            if($qty_active==0){
                $pre_permanent=0;
                $pre_kontraks=0;
                $pre_magang_comp=0;

                $pre_kontrak=0;
                $pre_sab=0;
                $pre_magang=0;
                $pre_sai=0;
            }else{
                $pre_kontrak=number_format($qty_kontrak/$qty_active*100,2);
                $pre_sab=number_format($qty_sab/$qty_active*100,2);
                $pre_magang=number_format($qty_magang/$qty_active*100,2);
                $pre_sai=number_format($qty_sai/$qty_active*100,2);
            }

        }

        $batas=date('Y-m-d',strtotime('+31 days',strtotime($today)));

        $tb_status=DB::table('tb_statuses')
        ->select('finish_contract', DB::raw('COUNT(id_employee) as total_end'))
        ->groupBy('finish_contract')
        ->where('active','1')
        ->where('finish_contract','<=',$batas)
        ->where(function ($qry){
            $qry->where('contract_name','Kontrak')->orwhere('contract_name','Magang');
        })
        ->orderby('finish_contract','asc')
        ->get();

        $tb_status1=DB::table('tb_statuses')
        ->select('finish_contract', DB::raw('COUNT(id_employee) as total_end'))
        ->groupBy('finish_contract')
        ->where('active','1')
        ->where(function ($qry){
            $qry->where('contract_name','Kontrak');
        })
        ->orderby('finish_contract','asc')
        ->get();
        $tb_status2=DB::table('tb_statuses')
        ->select('finish_contract', DB::raw('COUNT(id_employee) as total_end'))
        ->groupBy('finish_contract')
        ->where('active','1')
        ->where(function ($qry){
            $qry->where('contract_name','Magang');
        })
        ->orderby('finish_contract','asc')
        ->get();
        $tb_status3=DB::table('tb_statuses')
        ->select('finish_contract', DB::raw('COUNT(id_employee) as total_end'))
        ->groupBy('finish_contract')
        ->where('active','1')
        ->where(function ($qry){
            $qry->where('contract_name','Pembaharuan');
        })
        ->orderby('finish_contract','asc')
        ->get();
        $periode=$today.' ~ '.$batas;
        $tb_kontraks=DB::table('tb_statuses')
        ->leftjoin('tb_employees','tb_employees.id','=','tb_statuses.id_employee')
        ->leftJoin('tb_departments','tb_departments.id','=','tb_employees.dept_id')
        ->select('tb_statuses.*','tb_employees.NIK','tb_employees.PIN','tb_employees.employee_name','tb_departments.dept_code')
        ->where('active','1')
        ->where('finish_contract','<=',$batas)
        ->where(function ($qry){
            $qry->where('contract_name','Kontrak')->orwhere('contract_name','Magang');
        })
        ->orderby('finish_contract','asc');

        $tb_kontrak=$tb_kontraks->get();
        //$qty_kontrak=$tb_kontraks->count();
        $tb_freeday=DB::table('tb_freedays')->get();
        $tb_address_group=DB::table('tb_address_groups')->get();

        foreach ($tb_address_group as $dt) {
            $kolom=$dt->kolom;
            $nilai=$dt->nilai;
        }

        $n_area=DB::table('tb_addresses')
        ->leftjoin('tb_employees','tb_employees.id','=','tb_addresses.id_employee')
        ->where($kolom,$nilai)
        ->where('tb_employees.status','1')
        ->count();
        $n_luar=DB::table('tb_addresses')
        ->leftjoin('tb_employees','tb_employees.id','=','tb_addresses.id_employee')
        ->where($kolom,'!=',$nilai)
        ->where('tb_employees.status','1')
        ->count();
        $n_total=$n_area+$n_luar;
        if($n_total>0){
            $p_area=number_format($n_area/$n_total*100,2);
            $p_luar=number_format($n_luar/$n_total*100,2);
        }else{
            $p_area=0;
            $p_luar=0;
        }

        $tb_dept=DB::table('tb_employees')
        ->leftjoin('tb_departments','tb_departments.id','=','tb_employees.dept_id')
        ->select('tb_departments.divisi', DB::raw('COUNT(tb_employees.id) as total_employee'))
        ->groupBy('tb_departments.divisi')
        ->where('tb_employees.status','1')
        ->orderby('total_employee','desc')
        ->get();
        
        // Chart Data logic
        $date_labels = [];
        $chart_kontrak = [];
        $chart_magang = [];
        $bln = '';
        foreach ($tb_status as $dt) {
            $date = $dt->finish_contract;
            $periode = date('Y-m', strtotime($date));
            if ($bln == $periode) {
                $date_labels[] = date('d', strtotime($date));
            } else {
                $date_labels[] = date('M-d', strtotime($date));
            }
            $bln = $periode;

            $chart_kontrak[] = DB::table('tb_statuses')
                ->where('contract_name', 'Kontrak')
                ->where('finish_contract', $date)
                ->where('active', '1')
                ->count();
            
            $chart_magang[] = DB::table('tb_statuses')
                ->where('contract_name', 'Magang')
                ->where('finish_contract', $date)
                ->where('active', '1')
                ->count();
        }

        $upcomingHolidays = $tb_freeday->filter(function($row) {
            return $row->date_off >= date('Y-m-d');
        })->sortBy('date_off')->take(5)->map(function($holiday) {
            $holiday->short_description = \Illuminate\Support\Str::limit($holiday->description, 20);
            return $holiday;
        });

        return view('dashboard.index',[
            'data'=>$data,
            'date_labels' => $date_labels,
            'chart_kontrak' => $chart_kontrak,
            'chart_magang' => $chart_magang,
            'upcomingHolidays' => $upcomingHolidays,
            'nilai'=>$nilai,
            'n_area'=>$n_area,
            'n_luar'=>$n_luar,
            'p_area'=>$p_area,
            'p_luar'=>$p_luar,
            'n_total'=>$n_total,
            'tb_address_group'=>$tb_address_group,
            'tb_freeday'=>$tb_freeday,
            'qty_kontrak'=>$qty_kontrak,
            'periode'=>$periode,
            'tb_kontrak'=>$tb_kontrak,
            'tb_status'=>$tb_status,
            'tb_status1'=>$tb_status1,
            'tb_status2'=>$tb_status2,
            'tb_status3'=>$tb_status3,
            'qty_permanent'=>$qty_permanent,
            'qty_kontrak'=>$qty_kontrak,
            'qty_sab'=>$qty_sab,
            'qty_other'=>$qty_other,
            'qty_other_sab'=>$qty_other_sab,
            'qty_kontraks'=>$qty_kontraks,
            'qty_magang'=>$qty_magang,
            'qty_sai'=>$qty_sai,
            'qty_active'=>$qty_active,
            'batas'=>$batas,
            'pre_permanent'=>$pre_permanent,
            'pre_kontrak'=>$pre_kontrak,
            'pre_sab'=>$pre_sab,
            'pre_other'=>$pre_other,
            'pre_other_sab'=>$pre_other_sab,
            'pre_kontraks'=>$pre_kontraks,
            'pre_magang'=>$pre_magang,
            'pre_magang_comp'=>$pre_magang_comp,
            'pre_sai'=>$pre_sai,
            'qty_ksk'=>$qty_ksk,
            'notif'=>$notif,
            'email'=>$email,
            'menu'=>'dashboard'
        ]);
    }
    public function kskReminder(){
        $email=Auth::user()->email;
        $cek1=DB::table('tb_emails')->where('email_address',$email)->get();
        $id_user='';
        foreach($cek1 as $dt){$id_user=$dt->id_employee;}

        $qty_ksk=DB::table('tb_ksk')
        ->where(function($query1)use($id_user){
            $query1->where('approval1',$id_user)->where('approval1_status','0')->where('distribute_status','1');
        })
        ->orwhere(function($query2)use($id_user){
            $query2->where('approval2',$id_user)->where('approval2_status','0')->where('distribute_status','1');
        })
        ->orwhere(function($query3)use($id_user){
            $query3->where('approval3',$id_user)->where('approval3_status','0')->where('distribute_status','1');
        })
        ->orwhere(function($query4)use($id_user){
            $query4->where('approval4',$id_user)->where('approval4_status','0')->where('distribute_status','1');
        })
        ->orwhere(function($query5)use($id_user){
            $query5->where('approval5',$id_user)->where('approval5_status','0')->where('distribute_status','1');
        })
        ->orwhere(function($query6)use($id_user){
            $query6->where('approval6',$id_user)->where('approval6_status','0')->where('distribute_status','1');
        })
        ->count();
        return $qty_ksk;
    }

    public function notifikasi(){
        $id_employee = '';
        $email = Auth::user()->email;
        $cek = DB::table('tb_emails')->where('email_address', $email)->first();
        if ($cek) {
            $id_employee = $cek->id_employee;
        }

        $countKSK = $this->kskReminder();
        // Since other counts are requested by the view but logic wasn't provided, 
        // we'll set them to 0 or provide a basic query if possible.
        // For now, let's keep them at 0 to avoid errors.
        $countSPL = 0; 
        $countLeave = 0;

        return response()->json([
            'countKSK' => $countKSK,
            'countSPL' => $countSPL,
            'countLeave' => $countLeave
        ]);
    }
    function change_password(){
        $data['email']=Auth::user()->email;
        return view('change_password',[
            'data'=>$data,
            'pesan'=>'',
            'menu'=>'dashboard'
        ]);

    }
    function confirm_password(Request $data){
        $data['password']=Auth::user()->password;
        $data['email']=Auth::user()->email;
        $old=$data->old_password;
        //$check=Hash::check($old, $data['password']);
        $check=1;
        if($check==1){
            $new1=$data->new_password;
            $new2=$data->password_confirmation;
            if($old==$new1){
                $teks= "Maaf, password baru tidak boleh sama dengan password lama";
                return redirect()->back()->with(['pesan'=>$teks]);
            }elseif($new1!=$new2){
                $teks= "Maaf, password baru anda masih belum sesuai, pastikan password & confirmation sama";
                return redirect()->back()->with(['pesan'=>$teks]);
            }else{
                $today=date('Y-m-d');
                $sixMonthsLater=date('Y-m-d',strtotime('+3 months',strtotime($today)));
                $new=Hash::make($new1);
                $encryptedData = Crypt::encrypt($new1);
                $update=DB::table('users')->where('email',$data['email'])->update([
                    'password'=>$new,
                    'last_password'=>$data['password'],
                    'original_text'=>$encryptedData,
                    'reset_date'=>$today,
                    'expired_date'=>$sixMonthsLater,
                ]);
                if($update){
                    return redirect('login');
                }
            }
        }else{
            return redirect()->back()->with(['pesan'=>"Maaf, password lama anda masih belum sesuai"]);
        }
        //return $old.' '.$old_encripted;
    }
    function password_show($password){
        $result = Crypt::decrypt($password);
        return $result;
    }
 
}

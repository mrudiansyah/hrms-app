<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use DateTime;
use Auth;
use PDF;

class meal_controller extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth','verified']);
    }
    function index($periode,$id_employee){
        $kalendar=CAL_GREGORIAN;

        $data_a=explode('-',$periode);
        $thn=$data_a[0];
        $bln=$data_a[1];

        $hariakhir=cal_days_in_month($kalendar,$bln,$thn);

        $Tglawal=date('Y-m-d',strtotime($thn.'-'.$bln.'-01'));
        $Tglakhir=date('Y-m-d',strtotime($thn.'-'.$bln.'-'.$hariakhir));

        //$tb_meal=DB::table('tb_meals')->leftjoin('tb_employees','tb_employees.id','=','tb_meals.id_employee')->select('tb_meals.*','tb_employees.NIK','tb_employees.employee_name')->where('date_on','>=',$Tglawal)->where('date_on','<=',$Tglakhir)->orderby('tb_meals.id','desc')->get();

        if($id_employee!='0'){
            $tb_meal=DB::table('tb_meals')->leftjoin('tb_employees','tb_employees.id','=','tb_meals.id_employee')->select('tb_meals.*','tb_employees.NIK','tb_employees.employee_name')->where('date_on','>=',$Tglawal)->where('date_on','<=',$Tglakhir)->where('id_employee',$id_employee)
            ->where('is_deleted','0')
            ->orderby('date_on','desc')->get();
        }else{
            $tb_meal=DB::table('tb_meals')
            ->leftjoin('tb_employees','tb_employees.id','=','tb_meals.id_employee')
            ->select('tb_meals.id_employee','tb_employees.NIK','tb_employees.employee_name',DB::raw('SUM(tb_meals.meal) as meal,COUNT(tb_meals.meal) as times'))
            ->groupBy('tb_meals.id_employee','tb_employees.NIK','tb_employees.employee_name')
            ->where('date_on','>=',$Tglawal)->where('date_on','<=',$Tglakhir)
            ->where('is_deleted','0')
            ->orderby('meal','desc')->get();
        }

        $tb_employee=DB::table('tb_employees')->where('status','1')->get();
        return view('page/admin/m_meal/meal',['tb_employee'=>$tb_employee,'id_employee'=>$id_employee,'tb_meal'=>$tb_meal,'periode'=>$periode,'menu'=>'meal']);
    }
    function checkDouble($periode){
        $kalendar=CAL_GREGORIAN;

        $data_a=explode('-',$periode);
        $thn=$data_a[0];
        $bln=$data_a[1];

        $hariakhir=cal_days_in_month($kalendar,$bln,$thn);

        $Tglawal=date('Y-m-d',strtotime($thn.'-'.$bln.'-01'));
        $Tglakhir=date('Y-m-d',strtotime($thn.'-'.$bln.'-'.$hariakhir));

        $tb_meal=DB::table('tb_meals')->leftjoin('tb_employees','tb_employees.id','=','tb_meals.id_employee')->select('tb_meals.*','tb_employees.NIK','tb_employees.employee_name')
        ->where('date_on','>=',$Tglawal)->where('date_on','<=',$Tglakhir)
        ->orderByDesc('tb_meals.date_on')
        ->orderByDesc('tb_meals.id_employee')
        ->get();

        $tb_employee=DB::table('tb_employees')->where('status','1')->get();
        return view('page/admin/m_meal/meal_double',['tb_employee'=>$tb_employee,'tb_meal'=>$tb_meal,'periode'=>$periode,'menu'=>'meal']);
    }

    function createData(Request $data){
        $this->validate($data,[
            'date_on'=>'required',
            'id_employee'=>'required',
            'category'=>'required'
        ]);
        $check=DB::table('tb_meals')->where('id_employee',$data->id_employee)->where('date_on',$data->date_on)->where('category',$data->category)->count();
        $isi=$data->employee_name.' sudah pernah entry';
        if($check>0)return redirect()->back()->with(['success' => $isi]);
        $simpan=DB::table('tb_meals')->insert([
            'id_employee'=>$data->id_employee,
            'date_on'=>$data->date_on,
            'category'=>$data->category,
            'ref_id'=>'0',
            'meal'=>$data->meal
        ]);
        if($simpan){
            $teks=$data->employee_name.' berhasil disimpan';
            return redirect()->back()->with(['success' => $teks]);
        }
    }
    function deleteData($id){
        $delete=DB::table('tb_meals')->where('id',$id)->delete();
        if($delete)
        return redirect()->back()->with(['success' => 'Delete data berhasil']);
    }
    function shiftMeals($Tgl,$show){
        date_default_timezone_set("Asia/Jakarta");
        if($Tgl==0)$Tgl=date('Y-m-d');
        $Tgl_awal=date('Y-m-d',strtotime('-2 weeks',strtotime($Tgl)));
        $Tgl_akhir=date('Y-m-d',strtotime('2 weeks',strtotime($Tgl)));
    
        $tb_employee=DB::table('tb_employees')
        ->leftjoin('tb_departments','tb_departments.id','=','tb_employees.dept_id')
        ->leftjoin('tb_positions','tb_positions.id','=','tb_employees.position_id')
        ->leftjoin('tb_employee_shifts','tb_employee_shifts.id_employee','=','tb_employees.id')
        ->leftjoin('tb_group_shifts','tb_group_shifts.id','=','tb_employee_shifts.id_shift')
        ->leftjoin('tb_groups','tb_groups.group','=','tb_group_shifts.group')
        ->where('tb_employees.status','1')
        ->where('tb_employee_shifts.status','1')
        ->get(['tb_employees.*','tb_departments.dept_code','tb_positions.position_name','tb_employee_shifts.shift_code','tb_employee_shifts.id_shift','tb_group_shifts.group','tb_group_shifts.start_implement','tb_groups.cycle','tb_groups.nshift']);
        //return $tb_employee;
        return view('page/admin/m_meal/meal_shift',['Tgl'=>$Tgl,'Tgl_awal'=>$Tgl_awal,'Tgl_akhir'=>$Tgl_akhir,'tb_employee'=>$tb_employee,'show'=>$show,'menu'=>'meal']);
    }
    function tempMeal($Tgl,$show){
        $ip_address=$_SERVER['REMOTE_ADDR'];
        $reset=DB::table('temp_shift')->where('ip_address',$ip_address)->delete();

        date_default_timezone_set("Asia/Jakarta");
        if($Tgl==0)$Tgl=date('Y-m-d');
    
        $tb_employee=DB::table('tb_employees')
        ->leftjoin('tb_departments','tb_departments.id','=','tb_employees.dept_id')
        ->leftjoin('tb_positions','tb_positions.id','=','tb_employees.position_id')
        ->leftjoin('tb_employee_shifts','tb_employee_shifts.id_employee','=','tb_employees.id')
        ->leftjoin('tb_group_shifts','tb_group_shifts.id','=','tb_employee_shifts.id_shift')
        ->leftjoin('tb_groups','tb_groups.group','=','tb_group_shifts.group')
        ->where('tb_employees.status','1')
        ->where('tb_employee_shifts.status','1')
        ->get(['tb_employees.*','tb_departments.dept_code','tb_positions.position_name','tb_employee_shifts.id_shift','tb_group_shifts.group','tb_group_shifts.start_implement','tb_groups.cycle']);
        
        foreach($tb_employee as $dt){
            if($dt->id_shift>0){
                $shift="";
                $host= mysqli_connect("192.168.1.4","ems","123456","db_ems");
                $host1 = mysqli_connect("192.168.121.4:83306","cahyudin","123456","adms_db");

                date_default_timezone_set("Asia/Jakarta");
                //$Tgl=date('Y-m-d');

                $tgl1 = new DateTime($dt->start_implement);
                $tgl2 = new DateTime($Tgl);
                $diffdays = $tgl2->diff($tgl1)->days;

                $cycle=$dt->cycle;
                if($cycle==0){
                    $diffcycle=0;
                    $modcycle=0;
                }
                else {
                    $diffcycle=Floor($diffdays/$cycle);
                    $modcycle=$diffdays%$cycle;
                }
                $awal=$modcycle;
                $modcycle++;

                $group=$dt->group;
                $query="select * from tb_cycles where tb_cycles.group='$group' and days='$modcycle'";
                $qry=mysqli_query($host,$query)or die(mysqli_error($host));
                while($dt3=mysqli_fetch_array($qry)){

                    $shift=$dt3['shift'];
                    $check_in=date('H:i',strtotime($dt3['check_in']));
                    $check_out=date('H:i',strtotime($dt3['check_out']));
                    $advance=$dt3['advance'];
                    $cross=$dt3['cross'];

                    //Return Checkin Range
                    if(isset($check_in)&&$check_in!=''){
                        $cdate=date('Y-m-d',strtotime($Tgl));
                        if($advance==1){
                            $date = date_create($cdate);
                            date_add($date, date_interval_create_from_date_string('-1 days'));
                            $cdate= date_format($date, 'Y-m-d');
                        }
                        $ncdatein=$cdate." ".$check_in;
                        //Reduce 2 Hour
                        $date = date_create($ncdatein);
                        date_add($date, date_interval_create_from_date_string('-2 hours'));
                        $ncdateindown= date_format($date, 'Y-m-d H:i:s');
                        //Increas 5 Hour
                        $date = date_create($ncdatein);
                        date_add($date, date_interval_create_from_date_string('5 hours'));
                        $ncdateinup= date_format($date, 'Y-m-d H:i:s');
                        //echo $ncdatein.' ';
                    }
                    //Return Checkout Range
                    if(isset($check_out)&&$check_out!=''){
                        $cdate=date('Y-m-d',strtotime($Tgl));
                        if($cross==1){
                            $date = date_create($cdate);
                            date_add($date, date_interval_create_from_date_string('+1 days'));
                            $cdate= date_format($date, 'Y-m-d');
                        }
                        $ncdateout=$cdate." ".$check_out;
                        //Reduce 2 Hour
                        $date = date_create($ncdateout);
                        date_add($date, date_interval_create_from_date_string('-2 hours'));
                        $ncdateoutdown= date_format($date, 'Y-m-d H:i:s');
                        //Increas 5 Hour
                        $date = date_create($ncdateout);
                        date_add($date, date_interval_create_from_date_string('4 hours 30 minutes'));
                        $ncdateoutup= date_format($date, 'Y-m-d H:i:s');
                        //echo $ncdateout.' ';
                    }
                    $qry2=mysqli_query($host1,"select checkinout.checktime,checkinout.userid,userinfo.name from checkinout left join userinfo on checkinout.userid=userinfo.userid where userinfo.userid='$dt->PIN' and checkinout.checktime>='$ncdateindown' and checkinout.checktime<='$ncdateoutup' order by checkinout.checktime asc limit 1")or die(mysqli_error($host1));
                    $hadir=0;
                    $act_in=$ncdatein;
                    $act_out=$ncdateout;
                    while($dt2=mysqli_fetch_array($qry2)){
                        $ctime=$dt2['checktime'];
                        $act_in=$ctime;
                        $act_out=$ctime;
                        //Conditionl IN-OUT
                        if($ctime>=$ncdateindown and $ctime<=$ncdateinup){$in_status="1";$act_in=$ctime;}
                        if($ctime>=$ncdateoutdown and $ctime<=$ncdateoutup){$out_status="1";$act_out=$ctime;}
                        if($in_status="1"||$out_status="1")$hadir=1;

                    }

                }
                //Start Insert
                if($group==1&&$shift==1)$kondisi='Pagi';
                else if($shift==1)$kondisi="Malam";
                else if($shift==2)$kondisi="Pagi";
                else if($shift==3)$kondisi="Sore";
                else if($sekarang=='Libur')$kondisi="Libur";
                else $kondisi="Unset";
                if($kondisi==$show){
                    $create=DB::table('temp_shift')->insert([
                        'ip_address'=>$ip_address,
                        'id_employee'=>$dt->id,
                        'tanggal'=>$Tgl,
                        'plan_cin'=>$ncdatein,
                        'min_cin'=>$ncdateindown,
                        'max_cin'=>$ncdateinup,
                        'plan_cout'=>$ncdateout,
                        'min_cout'=>$ncdateoutdown,
                        'max_cout'=>$ncdateoutup,
                        'act_cin'=>$act_in,
                        'act_cout'=>$act_out,
                        'finger'=>$hadir,
                        'manual'=>'0',
                        'status'=>$hadir
                    ]);
                }
                //End Insert
            }

        }
        return redirect('/MealsShift/Detail/'.$Tgl.'/'.$show)->with(['success','Export Success']);
    }
    function shiftDetail($Tgl,$show){
        $ip_address=$_SERVER['REMOTE_ADDR'];

        $temp_shift=DB::table('temp_shift')
        ->leftjoin('tb_employees','tb_employees.id','=','temp_shift.id_employee')
        ->leftjoin('tb_departments','tb_departments.id','=','tb_employees.dept_id')
        ->leftjoin('tb_positions','tb_positions.id','=','tb_employees.position_id')
        ->where('tb_employees.status','1')
        ->where('ip_address',$ip_address)
        ->get(['temp_shift.*','tb_employees.PIN','tb_employees.NIK','tb_employees.employee_name','tb_departments.dept_code','tb_positions.position_name']);
        return view('page/admin/m_meal/meal_shift_detail',['temp_shift'=>$temp_shift,'Tgl'=>$Tgl,'show'=>$show,'menu'=>'meal']);
    
    }    
    function shiftCheck(){
        $ip_address=$_SERVER['REMOTE_ADDR'];

        $temp_shift=DB::table('temp_shift')
        ->leftjoin('tb_employees','tb_employees.id','=','temp_shift.id_employee')
        ->leftjoin('tb_departments','tb_departments.id','=','tb_employees.dept_id')
        ->leftjoin('tb_positions','tb_positions.id','=','tb_employees.position_id')
        ->where('tb_employees.status','1')
        ->where('ip_address',$ip_address)
        ->get(['temp_shift.*','tb_employees.PIN','tb_employees.NIK','tb_employees.employee_name','tb_departments.dept_code','tb_positions.position_name']);
        foreach($temp_shift as $dt){
            $tb_checktime=DB::table('tb_checktimes')->where('PIN',$dt->PIN)->where('checktime','>=',$dt->min_cin)->where('checktime','<=',$dt->max_cout)->count();
            if($tb_checktime>0){
                $update=DB::table('temp_shift')->where('id',$dt->id)->update(['manual'=>'1','status'=>'1']);
            }
        }
        return redirect()->back()->with(['success'=>'Success import data from manual check']);
    }    
    function shiftCreate($ref){
        $ip_address=$_SERVER['REMOTE_ADDR'];
        date_default_timezone_set("Asia/Jakarta");
        $sekarang=date('Y-m-d H:i:s');

        $temp_shift=DB::table('temp_shift')
        ->leftjoin('tb_employees','tb_employees.id','=','temp_shift.id_employee')
        ->leftjoin('tb_departments','tb_departments.id','=','tb_employees.dept_id')
        ->leftjoin('tb_positions','tb_positions.id','=','tb_employees.position_id')
        ->where('tb_employees.status','1')
        ->where('ip_address',$ip_address)
        ->get(['temp_shift.*','tb_employees.PIN','tb_employees.NIK','tb_employees.employee_name','tb_departments.dept_code','tb_positions.position_name']);
        foreach($temp_shift as $dt){
            if($dt->status==1){
                $tb_meal=DB::table('tb_meals')->where('id_employee',$dt->id_employee)->where('date_on',$dt->tanggal)->where('category','meal_off')->count();
                if($tb_meal==0){
                    $simpan=DB::table('tb_meals')->insert([
                        'id_employee'=>$dt->id_employee,
                        'date_on'=>$dt->tanggal,
                        'category'=>'meal_off',
                        'ref_id'=>$ref,
                        'meal'=>'15000',
                        'created_at'=>$sekarang,
                        'updated_at'=>$sekarang,
                    ]);
                }
            }
        }
        return redirect()->back()->with(['success'=>'Success create meal allowance']);
    }    
    function shiftDelete($ref){
        $ip_address=$_SERVER['REMOTE_ADDR'];

        $temp_shift=DB::table('temp_shift')
        ->where('ip_address',$ip_address)
        ->limit(1)
        ->get();
        foreach($temp_shift as $dt){
            $tanggal=$dt->tanggal;
        }
        $delete=DB::table('tb_meals')->where('date_on',$tanggal)->where('ref_id',$ref)->where('category','meal_off')->delete();
        return redirect()->back()->with(['success'=>'Success delete meal allowance']);
    }    
    function mealMonth($periode,$id_employee){
        $kalendar=CAL_GREGORIAN;

        $data_a=explode('-',$periode);
        $thn=$data_a[0];
        $bln=$data_a[1];

        $hariakhir=cal_days_in_month($kalendar,$bln,$thn);

        $Tglawal=date('Y-m-d',strtotime($thn.'-'.$bln.'-01'));
        $Tglakhir=date('Y-m-d',strtotime($thn.'-'.$bln.'-'.$hariakhir));

        //Create Tanggal
        $ut_tanggal_count=DB::table('ut_tanggal')->where('periode',$periode)->count();
        if($ut_tanggal_count==0){
            for($i=1;$i<=$hariakhir;$i++){
                if(strlen($i)==1)$j='0'.$i;
                else $j=$i;
                $tanggal=$thn.'-'.$bln.'-'.$j;
                $add_tangal=DB::table('ut_tanggal')->insert([
                    'periode'=>$periode,
                    'tanggal'=>$tanggal
                ]);
            }
        }
        //End Create Tanggal

        $tb_meal='';
        $employee_name='';
        $nik='';
        $meal_totals=[];
        if($id_employee!='0'){

            $tb_meal=DB::table('ut_tanggal')
            ->leftjoin('tb_meals', function ($join) use($id_employee,$Tglawal,$Tglakhir){
                $join->on('tb_meals.date_on','=','ut_tanggal.tanggal')
                    ->where('tb_meals.id_employee',$id_employee)
                    ->where('tb_meals.date_on','>=',$Tglawal)->where('tb_meals.date_on','<=',$Tglakhir)
                    ;
            })
            ->leftjoin('tb_employees','tb_employees.id','=','tb_meals.id_employee')
            ->select('ut_tanggal.tanggal','tb_meals.meal','tb_meals.category','tb_employees.NIK','tb_employees.employee_name')
            ->where('ut_tanggal.periode',$periode)
            ->orderby('tanggal','asc')->get();
            
            
            $tb_employee=DB::table('tb_employees')->where('id',$id_employee)->get();
            foreach($tb_employee as $dt){
                $nik=$dt->NIK;
                $employee_name=$dt->employee_name;
            }

            $meal_records=DB::table('tb_meals')
            ->select('date_on','category',DB::raw('SUM(meal) as total'))
            ->where('id_employee',$id_employee)
            ->where('date_on','>=',$Tglawal)
            ->where('date_on','<=',$Tglakhir)
            ->where('is_deleted','0')
            ->groupBy('date_on','category')
            ->get();

            foreach($meal_records as $record){
                $date=date('Y-m-d',strtotime($record->date_on));
                $meal_totals[$date][$record->category]=(float)$record->total;
            }
        }
        $ut_tanggal=DB::table('ut_tanggal')->where('periode',$periode)->orderby('tanggal','asc')->get();
        //return $tb_meal;
        $tb_employee=DB::table('tb_employees')->where('status','1')->get();
        return view('page/admin/m_meal/meal_month',['ut_tanggal'=>$ut_tanggal,'nik'=>$nik,'tb_employee'=>$tb_employee,'id_employee'=>$id_employee,'employee_name'=>$employee_name,'tb_meal'=>$tb_meal,'meal_totals'=>$meal_totals,'periode'=>$periode,'menu'=>'meal']);
    }
    function mealUpdate(Request $data){
        $admin=Auth::user()->name;
        $now=date('Y-m-d H:i:s');
        $action='';
        if($data->nilai==1){
            //$delete=DB::table('tb_meals')->where('id_employee',$data->idemployee)->where('date_on',$data->tanggal)->where('category',$data->category)->delete();
            $action='review';
        }else{
            $check=DB::table('tb_meals')->where('id_employee',$data->idemployee)->where('date_on',$data->tanggal)->where('category',$data->category)->count();
            if($check==0){
                $update=DB::table('tb_meals')->insert([
                    'id_employee'=>$data->idemployee,
                    'date_on'=>$data->tanggal,
                    'category'=>$data->category,
                    'ref_id'=>'0',
                    'meal'=>'15000'
                ]);
                $action='add';
            }else{
                $update=DB::table('tb_meals')->where('id_employee',$data->idemployee)->where('date_on',$data->tanggal)->where('category',$data->category)->update([
                    'is_deleted'=>'0',
                ]);
                $action='add';
            }
        }
        if($data->nilai==1){
            // $delete=DB::table('tb_meals')->where('id_employee',$data->idemployee)->where('date_on',$data->tanggal)->where('category',$data->category)->delete();
            $delete=DB::table('tb_meals')->where('id_employee',$data->idemployee)->where('date_on',$data->tanggal)->where('category',$data->category)->update([
                'is_deleted'=>'1',
                'deleted_by'=>$admin,
                'updated_at'=>$now
            ]);
        }
    }
    function vendor_stay($periode,$id_employee){
        $kalendar=CAL_GREGORIAN;

        $data_a=explode('-',$periode);
        $thn=$data_a[0];
        $bln=$data_a[1];

        $hariakhir=cal_days_in_month($kalendar,$bln,$thn);

        $Tglawal=date('Y-m-d',strtotime($thn.'-'.$bln.'-01'));
        $Tglakhir=date('Y-m-d',strtotime($thn.'-'.$bln.'-'.$hariakhir));

        //$tb_meal=DB::table('tb_meals')->leftjoin('tb_employees','tb_employees.id','=','tb_meals.id_employee')->select('tb_meals.*','tb_employees.NIK','tb_employees.employee_name')->where('date_on','>=',$Tglawal)->where('date_on','<=',$Tglakhir)->orderby('tb_meals.id','desc')->get();

        if($id_employee!='0'){
            $tb_meal=DB::table('tb_meals')->leftjoin('tb_employees','tb_employees.id','=','tb_meals.id_employee')->select('tb_meals.*','tb_employees.NIK','tb_employees.employee_name')->where('date_on','>=',$Tglawal)->where('date_on','<=',$Tglakhir)->where('id_employee',$id_employee)
            ->where('is_deleted','0')
            ->orderby('date_on','desc')->get();
        }else{
            $tb_meal=DB::table('tb_meals')
            ->leftjoin('tb_employees','tb_employees.id','=','tb_meals.id_employee')
            ->leftjoin('tb_employee_vendorstay','tb_employee_vendorstay.id_employee','=','tb_meals.id_employee')
            ->select('tb_meals.id_employee','tb_employees.NIK','tb_employees.employee_name',DB::raw('SUM(tb_meals.meal) as meal,COUNT(tb_meals.meal) as times'))
            ->groupBy('tb_meals.id_employee','tb_employees.NIK','tb_employees.employee_name')
            ->where('date_on','>=',$Tglawal)->where('date_on','<=',$Tglakhir)
            ->where('is_deleted','0')
            ->whereNotNull('tb_employee_vendorstay.id')
            ->where('tb_employee_vendorstay.status','1')
            ->orderby('meal','desc')->get();
        }

        $tb_employee=DB::table('tb_employees')->where('status','1')->get();
        $tb1=DB::table('tb_employee_vendorstay as a')->leftjoin('tb_employees as b','b.id','=','a.id_employee')->where('a.status','1')->get(['a.*','b.NIK','b.employee_name']);
        return view('page/admin/m_meal/vendor_stay',['tb_employee'=>$tb_employee,'tb1'=>$tb1,'id_employee'=>$id_employee,'tb_meal'=>$tb_meal,'periode'=>$periode,'menu'=>'meal']);
    }
    function addVendorStay(Request $data){
        $check=DB::table('tb_employee_vendorstay')->where('id_employee',$data->id_employee)->where('status','1')->count();
        if($check==0){
            $simpan=DB::table('tb_employee_vendorstay')->insert([
                'id_employee'=>$data->id_employee,
            ]);
            if($simpan){
                return redirect()->back();
            }
        }else{
            return redirect()->back()->with(['success' => 'Sudah terdaftar']);
        }
    }
    function deleteVendorStay($id){
        $check=DB::table('tb_employee_vendorstay')->where('id',$id)->update(['status'=>'0']);
        return redirect()->back()->with(['success' => 'Delete data berhasil']);
    }

}

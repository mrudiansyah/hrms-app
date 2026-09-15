<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
 use Illuminate\Support\Facades\Auth;
 use Illuminate\Support\Facades\Crypt;
use PDF;
 use Carbon\Carbon;
 use Validator;

class ApprovalSPLController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        //$this->middleware('auth');
        $this->middleware(['auth','verified']);
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
    */

    #region PlanSPL
    public function index(Request $request)
    {
        //$cek_lock=0;
        $cek_lock=DB::table('tb_utilities')->where('atribut','limit_approval_ot')->where('status','1')->count();
        //if($cek_lock==1){
            $x=$this->autoCancel();
            //return $x;
        //}
        $email=Auth::user()->email;
        $tb_email=DB::table('tb_emails')->where('email_address',$email)->get();
        $id_employee='';
        foreach($tb_email as $dt){
            $id_employee=$dt->id_employee;
        }
        $jmlot_plan=DB::table('tb_overtimes')
        ->leftjoin('tb_employees as tb1','tb1.id','=','tb_overtimes.diperintah')
        ->leftjoin('tb_employees as tb2','tb2.id','=','tb_overtimes.disetujui')
        ->leftjoin('tb_employees as tb3','tb3.id','=','tb_overtimes.diketahui')
        ->select('tb_overtimes.*','tb1.employee_name as nm1','tb2.employee_name as nm2','tb3.employee_name as nm3')
        ->leftjoin('tb_departments','tb_departments.id','=','tb_overtimes.dept_id')
        ->where([['tb_overtimes.diperintah',$id_employee],['tb_overtimes.status','1'],['tb_overtimes.status_diperintah','<=','3'],['tb_overtimes.status_disetujui','<=','3'],['tb_overtimes.status_diketahui','<=','3'],['tb_overtimes.status_dicatat','0'],['tb_overtimes.status_paid','0'],['tb_departments.isTrial','1'],['tb_overtimes.isDelete',0]])
        ->orwhere([['tb_overtimes.disetujui',$id_employee],['tb_overtimes.status','1'],['tb_overtimes.status_diperintah','<=','3'],['tb_overtimes.status_disetujui','<=','3'],['tb_overtimes.status_diketahui','<=','3'],['tb_overtimes.status_dicatat','0'],['tb_overtimes.status_paid','0'],['tb_departments.isTrial','1'],['tb_overtimes.isDelete',0]])
        ->orwhere([['tb_overtimes.diketahui',$id_employee],['tb_overtimes.status','1'],['tb_overtimes.status_diperintah','<=','3'],['tb_overtimes.status_disetujui','<=','3'],['tb_overtimes.status_diketahui','<=','3'],['tb_overtimes.status_dicatat','0'],['tb_overtimes.status_paid','0'],['tb_departments.isTrial','1'],['tb_overtimes.isDelete',0]])
        ->orwhere([['tb_overtimes.dicatat',$id_employee],['tb_overtimes.status','1'],['tb_overtimes.status_diperintah','<=','3'],['tb_overtimes.status_disetujui','<=','3'],['tb_overtimes.status_diketahui','<=','3'],['tb_overtimes.status_dicatat','0'],['tb_overtimes.status_paid','0'],['tb_departments.isTrial','1'],['tb_overtimes.isDelete',0]])
        ->orwhere([['tb_overtimes.approve',$id_employee],['tb_overtimes.status','1'],['tb_overtimes.status_diperintah','<=','3'],['tb_overtimes.status_disetujui','<=','3'],['tb_overtimes.status_diketahui','<=','3'],['tb_overtimes.status_dicatat','0'],['tb_overtimes.status_paid','0'],['tb_departments.isTrial','1'],['tb_overtimes.isDelete',0]])
        ->orwhere([['tb_overtimes.paid',$id_employee],['tb_overtimes.status','1'],['tb_overtimes.status_approve','0'],['tb_overtimes.status_paid','0'],['tb_departments.isTrial','1'],['tb_overtimes.isDelete',0]])
        ->get(['tb_overtimes.*','tb_departments.dept_name'])->count(); 
        $jmlot_actual=DB::table('tb_overtimes')
        ->leftjoin('tb_departments','tb_departments.id','=','tb_overtimes.dept_id')
        ->where([['tb_overtimes.diperintah',$id_employee],['status','1'],['tb_overtimes.status_diperintah','1'],['tb_overtimes.status_dicatat','0'],['tb_departments.isTrial','1'],['tb_overtimes.isDelete',0]])
        ->orwhere([['tb_overtimes.disetujui',$id_employee],['status','1'],['tb_overtimes.status_diperintah','1'],['tb_overtimes.status_dicatat','0'],['tb_departments.isTrial','1'],['tb_overtimes.isDelete',0]])
        ->orwhere([['tb_overtimes.diketahui',$id_employee],['status','1'],['tb_overtimes.status_diperintah','1'],['tb_overtimes.status_dicatat','0'],['tb_departments.isTrial','1'],['tb_overtimes.isDelete',0]])
        ->orwhere([['tb_overtimes.dicatat',$id_employee],['status','1'],['tb_overtimes.status_diperintah','1'],['tb_overtimes.status_dicatat','0'],['tb_departments.isTrial','1'],['tb_overtimes.isDelete',0]])
        ->orwhere([['tb_overtimes.approve',$id_employee],['status','1'],['tb_overtimes.status_diperintah','1'],['tb_overtimes.status_dicatat','0'],['tb_departments.isTrial','1'],['tb_overtimes.isDelete',0]])
        ->orwhere([['tb_overtimes.paid',$id_employee],['status','1'],['tb_overtimes.status_diperintah','1'],['tb_overtimes.status_dicatat','0'],['tb_departments.isTrial','1'],['tb_overtimes.isDelete',0]])
        ->count();
        date_default_timezone_set("Asia/Jakarta");
        $kalendar=CAL_GREGORIAN;
        $periode = $request->periode;
        if($periode == 0 ){
            $periode=date('Y-m');
            $periode_teks=date('F Y');
            $thn=date('Y',strtotime($periode.'-01'));
            $bln=date('m',strtotime($periode.'-01'));
            $hariakhir=cal_days_in_month($kalendar,$bln,$thn);        
            $Tglawal=date('Y-m-d',strtotime($thn.'-'.$bln.'-01'));
            $Tglakhir=date('Y-m-d',strtotime($thn.'-'.$bln.'-'.$hariakhir));
            $total_sumot=DB::table('tb_overtime_details')->where('date_on','>=',$Tglawal)->where('date_on','<=',$Tglakhir)->where('status','>=','1')->where('status','<','90')->sum('hours_act');
            $total_ammount=DB::table('tb_overtime_details')->where('date_on','>=',$Tglawal)->where('date_on','<=',$Tglakhir)->where('status','>=','1')->where('status','<','90')->sum('ammount');
            $last=DB::table('tb_overtime_sales')->where('periode',$periode)->get();
            $last_sales = 0;
            $skip=0;
            if($skip==0){
                foreach($last as $dt_last){
                    $last_sales= $dt_last->amount_sales;
                    $last_target=$last_sales/100;
                    $act_persentase=number_format($total_ammount/$last_sales*100,2);
                    DB::table('tb_overtime_sales')->where('periode',$periode)->update([
                        'amount_sales'=>$last_sales,
                        'amount_overtime'=>$total_ammount,
                        'target_persentase'=>'1',
                        'aktual_persentase'=>$act_persentase,
                        'target_overtime'=>$last_target
                    ]);
                }
            }
            $data['menu'] = 'overtime';
            $data['submenu'] = 'approval';
            $data['periode'] = $periode;
            $data['salesammount'] = $last_sales;
            $data['cabang'] = 'plan';
            $data['jmlot_plan'] = $jmlot_plan;
            $data['jmlot_actual'] = $jmlot_actual;

        }else{

        }

        return view('page/admin/m_overtime/approvalspl_fixed',$data);    
    }

    public function GetDataPlanApproval(Request $request){
        $columns = array( 
            0 =>'',
            1 =>'id', 
            2 =>'id_overtime',
            3 =>'ot_date',
            4 =>'dept_name' ,
            5 =>'status_diperintah' ,   
            6 =>'disetujui' ,   
            7 =>'diketahui' ,   
            8 =>'status_dicatat' , 
            9 =>'status_approve' ,   
            10 =>'status_dicatat' ,   
            11 =>'status_paid'
          );   
        $user_email = Auth::user()->email;
        $tb_email= DB::table('tb_emails')->where('email_address',$user_email)->get();
        $id_emp = '';
        foreach($tb_email as $e){
            $id_emp = $e->id_employee;
        }
       
          $limit = $request->input('length');
          $start = $request->input('start');
          $order = ($request->input('order.0.column')==0 ? $columns[2] : $columns[$request->input('order.0.column')]);
          $dir = ($request->input('order.0.column')==0 ? 'desc' : $request->input('order.0.dir')) ; 
          $tb_overtime = DB::table('tb_overtimes')
          ->leftjoin('tb_employees as tb1','tb1.id','=','tb_overtimes.diperintah')
          ->leftjoin('tb_employees as tb2','tb2.id','=','tb_overtimes.disetujui')
          ->leftjoin('tb_employees as tb3','tb3.id','=','tb_overtimes.diketahui')
          ->leftjoin('tb_departments','tb_departments.id','=','tb_overtimes.dept_id')
          ->where(function ($query) use ($id_emp) {
            $query->where([['tb_overtimes.diperintah',$id_emp],['tb_overtimes.status','1'],['tb_overtimes.status_diperintah','<=','3'],['tb_overtimes.status_disetujui','<=','3'],['tb_overtimes.status_diketahui','<=','3'],['tb_overtimes.status_dicatat','0'],['tb_overtimes.status_paid','0'],['tb_departments.isTrial','1'],['tb_overtimes.isDelete',0]])
          ->orwhere([['tb_overtimes.disetujui',$id_emp],['tb_overtimes.status','1'],['tb_overtimes.status_diperintah','<=','3'],['tb_overtimes.status_disetujui','<=','3'],['tb_overtimes.status_diketahui','<=','3'],['tb_overtimes.status_dicatat','0'],['tb_overtimes.status_paid','0'],['tb_departments.isTrial','1'],['tb_overtimes.isDelete',0]])
          ->orwhere([['tb_overtimes.diketahui',$id_emp],['tb_overtimes.status','1'],['tb_overtimes.status_diperintah','<=','3'],['tb_overtimes.status_disetujui','<=','3'],['tb_overtimes.status_diketahui','<=','3'],['tb_overtimes.status_dicatat','0'],['tb_overtimes.status_paid','0'],['tb_departments.isTrial','1'],['tb_overtimes.isDelete',0]])
          ->orwhere([['tb_overtimes.dicatat',$id_emp],['tb_overtimes.status','1'],['tb_overtimes.status_diperintah','<=','3'],['tb_overtimes.status_disetujui','<=','3'],['tb_overtimes.status_diketahui','<=','3'],['tb_overtimes.status_dicatat','0'],['tb_overtimes.status_paid','0'],['tb_departments.isTrial','1'],['tb_overtimes.isDelete',0]])
          ->orwhere([['tb_overtimes.approve',$id_emp],['tb_overtimes.status','1'],['tb_overtimes.status_diperintah','<=','3'],['tb_overtimes.status_disetujui','<=','3'],['tb_overtimes.status_diketahui','<=','3'],['tb_overtimes.status_dicatat','0'],['tb_overtimes.status_paid','0'],['tb_departments.isTrial','1'],['tb_overtimes.isDelete',0]])
          ->orwhere([['tb_overtimes.paid',$id_emp],['tb_overtimes.status','1'],['tb_overtimes.status_approve','0'],['tb_overtimes.status_paid','0'],['tb_departments.isTrial','1'],['tb_overtimes.isDelete','=',0]]);
          })
          ->select('tb_overtimes.*','tb1.employee_name as nm1','tb2.employee_name as nm2','tb3.employee_name as nm3');

          $totalData =$tb_overtime->get()->count(); 
          $totalFiltered = $totalData;  
          
          if(empty($request->input('search.value'))){
            $posts =$tb_overtime
            ->offset($start)
            ->limit($limit)
            ->orderBy($order,$dir)->get();
          } else {
          $search = $request->input('search.value');  
            $posts = $tb_overtime
            ->where(function ($query) use ($search,$start,$limit) {
                $query->where('tb_overtimes.dept_name','LIKE',"%$search%")
                ->orWhere('tb_overtimes.ot_date','LIKE',"%$search%")
                ->orWhere('tb_overtimes.id_overtime','LIKE',"%$search%");
            })
            ->offset($start)
            ->limit($limit)
            ->orderBy($order,$dir)->get();
            $totalFiltered = DB::table('tb_overtimes')
            ->leftjoin('tb_employees as tb1','tb1.id','=','tb_overtimes.diperintah')
            ->leftjoin('tb_employees as tb2','tb2.id','=','tb_overtimes.disetujui')
            ->leftjoin('tb_employees as tb3','tb3.id','=','tb_overtimes.diketahui')
            ->leftjoin('tb_departments','tb_departments.id','=','tb_overtimes.dept_id')
            ->where(function ($query) use ($id_emp) {
              $query->where([['tb_overtimes.diperintah',$id_emp],['tb_overtimes.status','1'],['tb_overtimes.status_diperintah','<=','3'],['tb_overtimes.status_disetujui','<=','3'],['tb_overtimes.status_diketahui','<=','3'],['tb_overtimes.status_dicatat','0'],['tb_overtimes.status_paid','0'],['tb_departments.isTrial','1'],['tb_overtimes.isDelete',0]])
            ->orwhere([['tb_overtimes.disetujui',$id_emp],['tb_overtimes.status','1'],['tb_overtimes.status_diperintah','<=','3'],['tb_overtimes.status_disetujui','<=','3'],['tb_overtimes.status_diketahui','<=','3'],['tb_overtimes.status_dicatat','0'],['tb_overtimes.status_paid','0'],['tb_departments.isTrial','1'],['tb_overtimes.isDelete',0]])
            ->orwhere([['tb_overtimes.diketahui',$id_emp],['tb_overtimes.status','1'],['tb_overtimes.status_diperintah','<=','3'],['tb_overtimes.status_disetujui','<=','3'],['tb_overtimes.status_diketahui','<=','3'],['tb_overtimes.status_dicatat','0'],['tb_overtimes.status_paid','0'],['tb_departments.isTrial','1'],['tb_overtimes.isDelete',0]])
            ->orwhere([['tb_overtimes.dicatat',$id_emp],['tb_overtimes.status','1'],['tb_overtimes.status_diperintah','<=','3'],['tb_overtimes.status_disetujui','<=','3'],['tb_overtimes.status_diketahui','<=','3'],['tb_overtimes.status_dicatat','0'],['tb_overtimes.status_paid','0'],['tb_departments.isTrial','1'],['tb_overtimes.isDelete',0]])
            ->orwhere([['tb_overtimes.approve',$id_emp],['tb_overtimes.status','1'],['tb_overtimes.status_diperintah','<=','3'],['tb_overtimes.status_disetujui','<=','3'],['tb_overtimes.status_diketahui','<=','3'],['tb_overtimes.status_dicatat','0'],['tb_overtimes.status_paid','0'],['tb_departments.isTrial','1'],['tb_overtimes.isDelete',0]])
            ->orwhere([['tb_overtimes.paid',$id_emp],['tb_overtimes.status','1'],['tb_overtimes.status_approve','0'],['tb_overtimes.status_paid','0'],['tb_departments.isTrial','1'],['tb_overtimes.isDelete','=',0]]);
            })
            ->where(function ($query) use ($search,$start,$limit) {
                $query->orWhere('tb_overtimes.dept_name','LIKE',"%$search%")
                ->orWhere('tb_overtimes.ot_date','LIKE',"%$search%")
                ->orWhere('tb_overtimes.id_overtime','LIKE',"%$search%");
            })
            ->count();
        } 
        $data = array();
        if(!empty($posts))
            { 
                $no = $start ;
            foreach($posts as $post){
                // /Admin/Overtime/Draft/  -Link button ke draft ketika submenu realisation
                // href='{{$tujuan}}{{$dt->id}}
                // <?php if(isset($id_employee)&&($id_employee=='122'||$id_employee=='101'))echo " 
                /* onclick="<?php echo "mymFunction(".$no.");"?>" */
                $no++; 
                $id = "'".str_replace("=","-", Crypt::encryptString($post->id)).'_'.$no."'"  ;   
                $button = '<a  title="Show" href="/Admin/Overtime/Approval/'.$post->id.'" class="btn btn-primary btn-xs" id="btnEdit" target="_blank"><i class="fa fa-edit"></i></a> <a title="Print"  href="/Admin/Overtime/Preview/'.$post->id.'" class="btn btn-primary btn-xs" id="btnPrint" target="_blank";"><i class="fa fa-print"></i></a>
                ';
                $nestedData['action'] = $button ;  
                $nestedData['no'] = $no ; 
                $nestedData['id'] = $post->id ;   
                $nestedData['id_overtime'] = $post->id_overtime ;   
                $nestedData['ot_date'] = $post->ot_date ;   
                $nestedData['dept_name'] = $post->dept_name ;   
                //   $nestedData['status_diperintah'] =  ($post->status_diperintah > 0 ? "<i class='fa fa-check-square-o' title='".$post->nm1."'></i>" : "<i class='fa fa-square-o' title='".$post->nm1."'></i>" );   
                //   $nestedData['disetujui'] = $post->disetujui > 0 ?  ($post->status_disetujui == 1 ? "<i class='fa fa-check-square-o' title='".$post->nm2."'></i>" : "<i class='fa fa-square-o' title='".$post->nm2."'></i>" ): '' ;   
                //   $nestedData['diketahui'] = $post->diketahui > 0 ? ($post->status_diketahui == 1 ? "<i class='fa fa-check-square-o' title='".$post->nm3."'></i>" : "<i class='fa fa-square-o' title='".$post->nm3."'></i>") : '' ; 

                $nestedData['status_diperintah'] = ($post->status_diperintah == 1 
                    ? "<i class='fa fa-check-square-o' title='".$post->nm1."'></i>" 
                    : ($post->status_diperintah == 2 
                        ? "<i class='fa fa-times' title='".$post->nm1."'></i>"
                        : ($post->status_diperintah == 3 
                            ? "<i class='fa fa-lock' title='".$post->nm1."'></i>" 
                            : "<i class='fa fa-square-o' title='".$post->nm1."'></i>"
                        )
                    )
                );

                $nestedData['disetujui'] = $post->disetujui > 0 
                    ? ($post->status_disetujui == 1 
                        ? "<i class='fa fa-check-square-o' title='".$post->nm2."'></i>"
                        : ($post->status_disetujui == 2 
                            ? "<i class='fa fa-times' title='".$post->nm2."'></i>"
                            : ($post->status_disetujui == 3 
                                ? "<i class='fa fa-lock' title='".$post->nm2."'></i>"
                                : "<i class='fa fa-square-o' title='".$post->nm2."'></i>"
                            )
                        )
                    )
                : '';

                $nestedData['diketahui'] = $post->diketahui > 0 
                    ? ($post->status_diketahui == 1 
                        ? "<i class='fa fa-check-square-o' title='".$post->nm3."'></i>"
                        : ($post->status_diketahui == 2 
                            ? "<i class='fa fa-times' title='".$post->nm3."'></i>"
                            : ($post->status_diketahui == 3 
                                ? "<i class='fa fa-lock' title='".$post->nm3."'></i>"
                                : "<i class='fa fa-square-o' title='".$post->nm3."'></i>"
                            )
                        )
                    )
                : '';

                $nestedData['status_dicatat'] = $post->status_dicatat == 1 ? "<i class='fa fa-check-square-o'></i>" : "<i class='fa fa-square-o' ></i>" ;   
                $nestedData['status_approve'] = $post->status_approve == 1 ? "<i class='fa fa-check-square-o'></i>" : "<i class='fa fa-square-o' ></i>" ;   
                $nestedData['status_paid'] = $post->status_paid ? "<i class='fa fa-check-square-o'></i>" : "<i class='fa fa-square-o' ></i>" ;   
                $data[] = $nestedData; 
            }
            }
            $json_data = array(
                "draw"            => intval($request->input('draw')),  
                "recordsTotal"    => intval($totalData),  
                "recordsFiltered" => intval($totalFiltered), 
                "data"            => $data   
                ); 
                echo json_encode($json_data);
    }

    public function ChartApproval(Request $request){
        date_default_timezone_set("Asia/Jakarta");
        $kalendar=CAL_GREGORIAN;
        $periode = $request->periode;
        $paretto = $request->paretto;
        if($periode == 0 ){
            $periode=date('Y-m');
            $periode_teks=date('F Y'); 
        }
        $periode_teks=''; 

            $thn=date('Y',strtotime($periode.'-01'));
            $bln=date('m',strtotime($periode.'-01'));
        $email=Auth::user()->email;
        $tb_email=DB::table('tb_emails')->where('email_address',$email)->get();
        $id_employee='';
        foreach($tb_email as $dt){
            $id_employee=$dt->id_employee;
        }


        $thn=date('Y',strtotime($periode.'-01'));
        $bln=date('m',strtotime($periode.'-01'));
        
        $hariakhir=cal_days_in_month($kalendar,$bln,$thn);

        $Tglawal=date('Y-m-d',strtotime($thn.'-'.$bln.'-01'));
        $Tglakhir=date('Y-m-d',strtotime($thn.'-'.$bln.'-'.$hariakhir));
        if($paretto > 0 ){
            $tb_sumot_detail= DB::select("SELECT id,dept_code,
            (select sum(ammount) from tb_overtime_details left join tb_overtimes on tb_overtime_details.id_ot=tb_overtimes.id where tb_overtimes.dept_id=tb_departments.id and tb_overtime_details.date_on>='$Tglawal' and tb_overtime_details.date_on<='$Tglakhir' and tb_overtime_details.status<'90')as total_act,
            (select sum(ammount) from tb_overtime_details left join tb_overtimes on tb_overtime_details.id_ot=tb_overtimes.id where tb_overtimes.dept_id=tb_departments.id and tb_overtimes.status_approve='1' and tb_overtime_details.date_on>='$Tglawal' and tb_overtime_details.date_on<='$Tglakhir' and tb_overtime_details.status<'90')as total_act2,
            (select sum(ammount) from tb_overtime_details left join tb_overtimes on tb_overtime_details.id_ot=tb_overtimes.id where tb_overtimes.dept_id=tb_departments.id and tb_overtimes.status_approve='0' and tb_overtime_details.date_on>='$Tglawal' and tb_overtime_details.date_on<='$Tglakhir' and tb_overtime_details.status<'90')as total_act3,
            (select TOP 1 tb_overtime_target.ammount from tb_overtime_target left join tb_overtimes on tb_overtime_target.dept_id=tb_overtimes.dept_id where tb_overtime_target.dept_id=tb_departments.id and tb_overtime_target.periode='$periode')as ammount_target
            FROM tb_departments
            where EXISTS(select * from tb_admins where tb_admins.dept_id=tb_departments.id and tb_admins.id_employee='$id_employee') order by total_act desc");
        $total_sumot=DB::table('tb_overtime_details')->where('date_on','>=',$Tglawal)->where('date_on','<=',$Tglakhir)->where('status','>=','1')->where('status','<','90')->sum('hours_act');
        $data['tb_sumot_detail'] = $tb_sumot_detail;
        $data['total_sumot'] = $total_sumot;
        $data['periode_teks'] = $periode_teks;
        }else{
        $data['tb_sumot_detail'] = '';
        $data['total_sumot'] = '';
        $data['periode_teks'] = '';
        }
        $qty_sumot=DB::table('tb_overtime_sales')->count();
        $take=12;
        $skip=$qty_sumot-$take;
        if($skip<0)$skip=0;

        $tb_sumot=DB::table('tb_overtime_sales')->orderby('periode','asc')->take($take)->skip($skip)->get();
        $Tglawal='2200-01';
        $Tglakhir='2000-01';
        foreach($tb_sumot as $dt){
            if($Tglawal>$dt->periode)$Tglawal=$dt->periode;
            if($Tglakhir<$dt->periode)$Tglakhir=$dt->periode;
        }
        $data['tanggal_awal']= $Tglawal;
        $data['tanggal_akhir']= $Tglakhir;
        $data['tb_sumot'] = $tb_sumot;
        if($paretto > 0){
            return view('page/admin/m_overtime/approvalspl_chartParetto',$data);    

        }else{
            return view('page/admin/m_overtime/approvalspl_chartFixed',$data);    
        }
    }
            
    function GetDataCompleteApproval(Request $request){
        $user_email = Auth::user()->email;
        $tb_email= DB::table('tb_emails')->where('email_address',$user_email)->get();
        $id_employee = '';
        foreach($tb_email as $e){
            $id_employee = $e->id_employee;
        }
        $periode = $request->periode;
        date_default_timezone_set("Asia/Jakarta");
        $kalendar=CAL_GREGORIAN;
        if($periode==0){$periode=date('Y-m');$periode_teks=date('F Y');}
        else $periode_teks=date('F Y',strtotime($periode.'-01'));
        $thn=date('Y',strtotime($periode.'-01'));
        $bln=date('m',strtotime($periode.'-01'));
        $hariakhir=cal_days_in_month($kalendar,$bln,$thn);
        $periode_awal=date('Y-m-d',strtotime($thn.'-'.$bln.'-01'));
        $periode_akhir=date('Y-m-d',strtotime($thn.'-'.$bln.'-'.$hariakhir));

        $columns = array( 
            0 =>'',
            1 =>'id', 
            2 =>'id_overtime',
            3 =>'ot_date',
            4 =>'dept_name' ,
            5 =>'status_diperintah' ,   
            6 =>'disetujui' ,   
            7 =>'diketahui' ,   
            8 =>'status_dicatat' , 
            9 =>'status_approve' ,   
            10 =>'status_dicatat' ,   
            11 =>'status_paid'

          );   
        $tb_overtime = DB::table('tb_overtimes')
        ->leftjoin('tb_departments','tb_departments.id','=','tb_overtimes.dept_id')
        ->where([['tb_overtimes.diperintah',$id_employee],['status','1'],['tb_overtimes.status_paid','1'],['tb_departments.isTrial','1'],['tb_departments.isTrial','1'],['ot_date','>=',$periode_awal],['ot_date','<=',$periode_akhir],['tb_overtimes.isDelete',0]])
        ->orwhere([['tb_overtimes.disetujui',$id_employee],['status','1'],['tb_overtimes.status_paid','1'],['tb_departments.isTrial','1'],['tb_departments.isTrial','1'],['ot_date','>=',$periode_awal],['ot_date','<=',$periode_akhir],['tb_overtimes.isDelete',0]])
        ->orwhere([['tb_overtimes.diketahui',$id_employee],['status','1'],['tb_overtimes.status_paid','1'],['tb_departments.isTrial','1'],['tb_departments.isTrial','1'],['ot_date','>=',$periode_awal],['ot_date','<=',$periode_akhir],['tb_overtimes.isDelete',0]])
        ->orwhere([['tb_overtimes.dicatat',$id_employee],['status','1'],['tb_overtimes.status_paid','1'],['tb_departments.isTrial','1'],['tb_departments.isTrial','1'],['ot_date','>=',$periode_awal],['ot_date','<=',$periode_akhir],['tb_overtimes.isDelete',0]])
        ->orwhere([['tb_overtimes.approve',$id_employee],['status','1'],['tb_overtimes.status_paid','1'],['tb_departments.isTrial','1'],['tb_departments.isTrial','1'],['ot_date','>=',$periode_awal],['ot_date','<=',$periode_akhir],['tb_overtimes.isDelete',0]])
        ->orwhere([['tb_overtimes.paid',$id_employee],['status','1'],['tb_overtimes.status_paid','1'],['tb_departments.isTrial','1'],['tb_departments.isTrial','1'],['ot_date','>=',$periode_awal],['ot_date','<=',$periode_akhir],['tb_overtimes.isDelete',0]])
        ->select(['tb_overtimes.*'],['tb_departments.dept_name']);

        $totalData = $tb_overtime->count();
        $totalFiltered = $totalData;  
        $limit = $request->input('length');
        $start = $request->input('start');
        $order = ($request->input('order.0.column')==0 ? $columns[3] : $columns[$request->input('order.0.column')]);
        $dir = ($request->input('order.0.column')==0 ? 'desc' : $request->input('order.0.dir')) ; 

        if(empty($request->input('search.value')))
        {  
            $posts = $tb_overtime
            ->offset($start)
        ->limit($limit)
        ->orderBy($order,$dir)
        ->get();
        }else{
            $search = $request->input('search.value');  
            $posts = DB::table('tb_overtimes')
            ->leftjoin('tb_departments','tb_departments.id','=','tb_overtimes.dept_id')
            ->where(function ($query) use ($id_employee,$periode_awal,$periode_akhir) {
                $query->where([['tb_overtimes.diperintah',$id_employee],['status','1'],['tb_overtimes.status_paid','1'],['tb_departments.isTrial','1'],['tb_departments.isTrial','1'],['ot_date','>=',$periode_awal],['ot_date','<=',$periode_akhir],['tb_overtimes.isDelete',0]])
                ->orWhere([['tb_overtimes.disetujui',$id_employee],['status','1'],['tb_overtimes.status_paid','1'],['tb_departments.isTrial','1'],['tb_departments.isTrial','1'],['ot_date','>=',$periode_awal],['ot_date','<=',$periode_akhir],['tb_overtimes.isDelete',0]])
                ->orwhere([['tb_overtimes.diketahui',$id_employee],['status','1'],['tb_overtimes.status_paid','1'],['tb_departments.isTrial','1'],['tb_departments.isTrial','1'],['ot_date','>=',$periode_awal],['ot_date','<=',$periode_akhir],['tb_overtimes.isDelete',0]])
                ->orwhere([['tb_overtimes.dicatat',$id_employee],['status','1'],['tb_overtimes.status_paid','1'],['tb_departments.isTrial','1'],['tb_departments.isTrial','1'],['ot_date','>=',$periode_awal],['ot_date','<=',$periode_akhir],['tb_overtimes.isDelete',0]])
                ->orwhere([['tb_overtimes.approve',$id_employee],['status','1'],['tb_overtimes.status_paid','1'],['tb_departments.isTrial','1'],['tb_departments.isTrial','1'],['ot_date','>=',$periode_awal],['ot_date','<=',$periode_akhir],['tb_overtimes.isDelete',0]])
                ->orwhere([['tb_overtimes.paid',$id_employee],['status','1'],['tb_overtimes.status_paid','1'],['tb_departments.isTrial','1'],['tb_departments.isTrial','1'],['ot_date','>=',$periode_awal],['ot_date','<=',$periode_akhir],['tb_overtimes.isDelete',0]]);
            })->offset($start)
            ->orWhere('tb_overtimes.dept_name','LIKE',"%$search%")
            ->orWhere('tb_overtimes.ot_date','LIKE',"%$search%")
            ->orWhere('tb_overtimes.id_overtime','LIKE',"%$search%")
            ->limit($limit)
            ->orderBy($order,$dir)->get();
            $totalFiltered = DB::table('tb_overtimes')
            ->leftjoin('tb_departments','tb_departments.id','=','tb_overtimes.dept_id')
            ->where(function ($query) use ($id_employee,$periode_awal,$periode_akhir) {
                $query->where([['tb_overtimes.diperintah',$id_employee],['status','1'],['tb_overtimes.status_paid','1'],['tb_departments.isTrial','1'],['tb_departments.isTrial','1'],['ot_date','>=',$periode_awal],['ot_date','<=',$periode_akhir],['tb_overtimes.isDelete',0]])
                ->orWhere([['tb_overtimes.disetujui',$id_employee],['status','1'],['tb_overtimes.status_paid','1'],['tb_departments.isTrial','1'],['tb_departments.isTrial','1'],['ot_date','>=',$periode_awal],['ot_date','<=',$periode_akhir],['tb_overtimes.isDelete',0]])
                ->orwhere([['tb_overtimes.diketahui',$id_employee],['status','1'],['tb_overtimes.status_paid','1'],['tb_departments.isTrial','1'],['tb_departments.isTrial','1'],['ot_date','>=',$periode_awal],['ot_date','<=',$periode_akhir],['tb_overtimes.isDelete',0]])
                ->orwhere([['tb_overtimes.dicatat',$id_employee],['status','1'],['tb_overtimes.status_paid','1'],['tb_departments.isTrial','1'],['tb_departments.isTrial','1'],['ot_date','>=',$periode_awal],['ot_date','<=',$periode_akhir],['tb_overtimes.isDelete',0]])
                ->orwhere([['tb_overtimes.approve',$id_employee],['status','1'],['tb_overtimes.status_paid','1'],['tb_departments.isTrial','1'],['tb_departments.isTrial','1'],['ot_date','>=',$periode_awal],['ot_date','<=',$periode_akhir],['tb_overtimes.isDelete',0]])
                ->orwhere([['tb_overtimes.paid',$id_employee],['status','1'],['tb_overtimes.status_paid','1'],['tb_departments.isTrial','1'],['tb_departments.isTrial','1'],['ot_date','>=',$periode_awal],['ot_date','<=',$periode_akhir],['tb_overtimes.isDelete',0]]);
            })
            ->where(function ($query) use ($search) {
                $query->where('tb_overtimes.dept_name','LIKE',"%$search%")
                ->orWhere('tb_overtimes.ot_date','LIKE',"%$search%")
                ->orWhere('tb_overtimes.id_overtime','LIKE',"%$search%");
            })->count();   
        }
        $data = array();
        if(!empty($posts))
              { 
                   $no = $start ;
                foreach($posts as $post){
                  // /Admin/Overtime/Draft/  -Link button ke draft ketika submenu realisation
                  // href='{{$tujuan}}{{$dt->id}}
                  // <?php if(isset($id_employee)&&($id_employee=='122'||$id_employee=='101'))echo " 
                  /* onclick="<?php echo "myFunction(".$no.");"?>" */
                  $no++; 
                  $id = "'".str_replace("=","-", Crypt::encryptString($post->id)).'_'.$no."'"  ;   
                  $button = '<a  title="Show" href="/Admin/Overtime/Approval/'.$post->id.'" class="btn btn-primary btn-xs" id="btnEdit" target="_blank"><i class="fa fa-edit"></i></a> <a title="Print"  href="/Admin/Overtime/Preview/'.$post->id.'" class="btn btn-primary btn-xs" id="btnPrint" target="_blank";"><i class="fa fa-print"></i></a>
                  ';
                    $nestedData['action'] = $button ;  
                    $nestedData['no'] = $no ; 
                    $nestedData['id'] = $post->id ;   
                    $nestedData['id_overtime'] = $post->id_overtime ;   
                    $nestedData['ot_date'] = $post->ot_date ;   
                    $nestedData['dept_name'] = $post->dept_name ;   
                    $nestedData['status_diperintah'] = ($post->status_diperintah > 0 ? "<i class='fa fa-check-square-o' ></i>" : "<i class='fa fa-square-o' ></i>" );   
                    $nestedData['disetujui'] = $post->disetujui > 0 ?  ($post->status_disetujui == 1 ? "<i class='fa fa-check-square-o'></i>" : "<i class='fa fa-square-o'></i>" ): '' ;   
                  $nestedData['diketahui'] = $post->disetujui > 0 ? ( $post->status_diketahui == 1 ? "<i class='fa fa-check-square-o'></i>" : "<i class='fa fa-square-o'></i>") : '' ;     
                    $nestedData['status_dicatat'] = $post->status_dicatat == 1 ? "<i class='fa fa-check-square-o'></i>" : "<i class='fa fa-square-o' ></i>" ;   
                    $nestedData['status_approve'] = $post->status_approve == 1 ? "<i class='fa fa-check-square-o'></i>" : "<i class='fa fa-square-o' ></i>" ;   
                    $nestedData['status_paid'] = $post->status_paid ? "<i class='fa fa-check-square-o'></i>" : "<i class='fa fa-square-o' ></i>" ;   
                    $data[] = $nestedData; 
                }
              }
              $json_data = array(
                  "draw"            => intval($request->input('draw')),  
                  "recordsTotal"    => intval($totalData),  
                  "recordsFiltered" => intval($totalFiltered), 
                  "data"            => $data   
                  ); 
                  echo json_encode($json_data);
    }
    public function updateSales(Request $data){
        $simpan=DB::table('tb_overtime_sales')->where('periode',$data->periodesales)->update(['amount_sales'=>$data->salesammount]);
        if($simpan){
            $dt['amount_sales'] = number_format($data->salesammount);
            $dt['periode'] = $data->periodesales;

            $dt['successText']= 'Sales Amount Updated';
            $dt['success']= '1';
        }else{
            $dt['periode'] = '';
            $dt['amount_sales'] = '';
            $dt['successText']= 'Failed';
            $dt['success']= '0';
        }
        return json_encode($dt);
    }
    public function GetSalesAmmount(Request $request){
        $last=DB::table('tb_overtime_sales')->where('periode',$request->periode)->get();
        $last_sales = 0;
        foreach($last as $dt_last){
            $last_sales= $dt_last->amount_sales;
            $last_target=$last_sales/100;
        }       
        $data['ammount_sales'] = $last_sales;
        $data['periode'] = $request->periode;

        return json_encode($data);
    }
    #endregion

    #region Legalize
    public function LegalizeSPL(Request $request){
        $email=Auth::user()->email;
        $tb_email=DB::table('tb_emails')->where('email_address',$email)->get();
        $id_employee='';
        foreach($tb_email as $dt){
            $id_employee=$dt->id_employee;
        }
        $tb_overtime_plan=DB::table('tb_overtimes')
        ->leftjoin('tb_employees as tb1','tb1.id','=','tb_overtimes.diperintah')
        ->leftjoin('tb_employees as tb2','tb2.id','=','tb_overtimes.disetujui')
        ->leftjoin('tb_employees as tb3','tb3.id','=','tb_overtimes.diketahui')
        ->select('tb_overtimes.*','tb1.employee_name as nm1','tb2.employee_name as nm2','tb3.employee_name as nm3')
        ->leftjoin('tb_departments','tb_departments.id','=','tb_overtimes.dept_id')
        ->where([['tb_overtimes.diperintah',$id_employee],['tb_overtimes.status','1'],['tb_overtimes.status_diperintah','<=','3'],['tb_overtimes.status_disetujui','<=','3'],['tb_overtimes.status_diketahui','<=','3'],['tb_overtimes.status_dicatat','1'],['tb_overtimes.status_approve','0'],['tb_overtimes.status_paid','0'],['tb_departments.isTrial','1'],['tb_overtimes.isDelete',0]])
        ->orwhere([['tb_overtimes.disetujui',$id_employee],['tb_overtimes.status','1'],['tb_overtimes.status_diperintah','<=','3'],['tb_overtimes.status_disetujui','<=','3'],['tb_overtimes.status_diketahui','<=','3'],['tb_overtimes.status_dicatat','1'],['tb_overtimes.status_approve','0'],['tb_overtimes.status_paid','0'],['tb_departments.isTrial','1'],['tb_overtimes.isDelete',0]])
        ->orwhere([['tb_overtimes.diketahui',$id_employee],['tb_overtimes.status','1'],['tb_overtimes.status_diperintah','<=','3'],['tb_overtimes.status_disetujui','<=','3'],['tb_overtimes.status_diketahui','<=','3'],['tb_overtimes.status_dicatat','1'],['tb_overtimes.status_approve','0'],['tb_overtimes.status_paid','0'],['tb_departments.isTrial','1'],['tb_overtimes.isDelete',0]])
        ->orwhere([['tb_overtimes.dicatat',$id_employee],['tb_overtimes.status','1'],['tb_overtimes.status_diperintah','<=','3'],['tb_overtimes.status_disetujui','<=','3'],['tb_overtimes.status_diketahui','<=','3'],['tb_overtimes.status_dicatat','1'],['tb_overtimes.status_approve','0'],['tb_overtimes.status_paid','0'],['tb_departments.isTrial','1'],['tb_overtimes.isDelete',0]])
        ->orwhere([['tb_overtimes.approve',$id_employee],['tb_overtimes.status','1'],['tb_overtimes.status_diperintah','<=','3'],['tb_overtimes.status_disetujui','<=','3'],['tb_overtimes.status_diketahui','<=','3'],['tb_overtimes.status_dicatat','1'],['tb_overtimes.status_approve','0'],['tb_overtimes.status_paid','0'],['tb_departments.isTrial','1'],['tb_overtimes.isDelete',0]])
        ->orwhere([['tb_overtimes.paid',$id_employee],['tb_overtimes.status','1'],['tb_overtimes.status_dicatat','1'],['tb_overtimes.status_approve','0'],['tb_overtimes.status_paid','0'],['tb_departments.isTrial','1'],['tb_overtimes.isDelete',0]]);
        // ->get(['tb_overtimes.*','tb_departments.dept_name']);
        $jmlot_plan = $tb_overtime_plan->get(['tb_overtimes.*','tb_departments.dept_name'])->count();
        $cek_lock=DB::table('tb_utilities')->where('atribut','limit_approval_ot')->where('status','1')->count();
        //if($cek_lock==1){
            $this->autoCancel();
        //}
        $data = array(
            'cabang'=>'Plan',
            'menu'=>'overtime',
            'submenu'=>'approval',
            'jmlot_plan'=>$jmlot_plan
        ); //$data

        return view('page/admin/m_overtime/legalizespl_fixed',$data);    
    }
    public function LegalizeSPLAll(Request $request){
        $today = date('Y-m-d');
        $last_month = date('Y-m-d', strtotime('-2 month'));
        $tb_overtime = DB::table('tb_overtimes')
            ->where('ot_date', '>=', $last_month)
            ->where('ot_date', '<=', $today)
            ->where('status_dicatat','1')
            ->where('status_approve','0')
            ->where('status', '1')
            ->where('isDelete', '0')
            ->whereNotExists(function ($query) {
                $query->select(DB::raw(1))
                    ->from('tb_overtime_details')
                    ->whereColumn('tb_overtime_details.id_ot', 'tb_overtimes.id')
                    ->where('tb_overtime_details.sign_after', 0)
                    ->where('tb_overtime_details.status', '<', 90);
            })
            ->get(['id','id_overtime','ot_date','dept_id']);
        foreach($tb_overtime as $dt){
            $update=DB::table('tb_overtimes')
            ->where('id',$dt->id)
            ->update([
                'status_approve'=>'1',
                //'approve'=>Auth::user()->id,
                'date_approve'=>date('Y-m-d H:i:s')
            ]);

        }
        
        return redirect()->route('LegalizeSPL')->with('success','Data berhasil diupdate');
    }
    public function ApproveSPLAll(Request $request){
        $today = date('Y-m-d');
        $last_month = date('Y-m-d', strtotime('-2 month'));
        $tb_overtime = DB::table('tb_overtimes')
            ->where('ot_date', '>=', $last_month)
            ->where('ot_date', '<=', $today)
            ->where('status', '1')
            ->where('isDelete', '0')
            ->where('status_dicatat','0')
            ->where(function($query) {
                $query->where(function($q) {
                    $q->where('status_diperintah', '1')
                    ->whereNull('disetujui');
                })->orWhere(function($q) {
                    $q->where('status_disetujui', '1')
                    ->whereNull('diketahui');
                })->orWhere(function($q) {
                    $q->where('status_diketahui', '1');
                });
            })
            ->get(['id', 'id_overtime', 'ot_date', 'dept_id']);

        foreach($tb_overtime as $dt){
            $update=DB::table('tb_overtimes')
            ->where('id',$dt->id)
            ->update([
                'status_dicatat'=>'1',
                //'dicatat'=>Auth::user()->id,
                'date_dicatat'=>date('Y-m-d H:i:s')
            ]);

        }
        
        return redirect()->back();
    }


    public function GetDataLegalize(Request $request){
        $columns = array( 
            0 =>'',
            1 =>'id', 
            2 =>'id_overtime',
            3 =>'ot_date',
            4 =>'dept_name' ,
            5 =>'status_diperintah' ,   
            6 =>'disetujui' ,   
            7 =>'diketahui' ,   
            8 =>'status_dicatat' , 
            9 =>'status_approve' ,   
            10 =>'status_dicatat' ,   
            11 =>'status_paid'
          );   
        $email=Auth::user()->email;
        $tb_email=DB::table('tb_emails')->where('email_address',$email)->get();
        $id_employee='';
        foreach($tb_email as $dt){
            $id_employee=$dt->id_employee;
        }
        $tb_overtime_plan=DB::table('tb_overtimes')
        ->leftjoin('tb_employees as tb1','tb1.id','=','tb_overtimes.diperintah')
        ->leftjoin('tb_employees as tb2','tb2.id','=','tb_overtimes.disetujui')
        ->leftjoin('tb_employees as tb3','tb3.id','=','tb_overtimes.diketahui')
        ->leftjoin('tb_departments','tb_departments.id','=','tb_overtimes.dept_id')
        ->where(function ($query) use ($id_employee) {
        $query->where([['tb_overtimes.diperintah',$id_employee],['tb_overtimes.status','1'],['tb_overtimes.status_diperintah','<=','3'],['tb_overtimes.status_disetujui','<=','3'],['tb_overtimes.status_diketahui','<=','3'],['tb_overtimes.status_dicatat','1'],['tb_overtimes.status_approve','0'],['tb_overtimes.status_paid','0'],['tb_departments.isTrial','1'],['tb_overtimes.isDelete',0]])
            ->orwhere([['tb_overtimes.disetujui',$id_employee],['tb_overtimes.status','1'],['tb_overtimes.status_diperintah','<=','3'],['tb_overtimes.status_disetujui','<=','3'],['tb_overtimes.status_diketahui','<=','3'],['tb_overtimes.status_dicatat','1'],['tb_overtimes.status_approve','0'],['tb_overtimes.status_paid','0'],['tb_departments.isTrial','1'],['tb_overtimes.isDelete',0]])
            ->orwhere([['tb_overtimes.diketahui',$id_employee],['tb_overtimes.status','1'],['tb_overtimes.status_diperintah','<=','3'],['tb_overtimes.status_disetujui','<=','3'],['tb_overtimes.status_diketahui','<=','3'],['tb_overtimes.status_dicatat','1'],['tb_overtimes.status_approve','0'],['tb_overtimes.status_paid','0'],['tb_departments.isTrial','1'],['tb_overtimes.isDelete',0]])
            ->orwhere([['tb_overtimes.dicatat',$id_employee],['tb_overtimes.status','1'],['tb_overtimes.status_diperintah','<=','3'],['tb_overtimes.status_disetujui','<=','3'],['tb_overtimes.status_diketahui','<=','3'],['tb_overtimes.status_dicatat','1'],['tb_overtimes.status_approve','0'],['tb_overtimes.status_paid','0'],['tb_departments.isTrial','1'],['tb_overtimes.isDelete',0]])
            ->orwhere([['tb_overtimes.approve',$id_employee],['tb_overtimes.status','1'],['tb_overtimes.status_diperintah','<=','3'],['tb_overtimes.status_disetujui','<=','3'],['tb_overtimes.status_diketahui','<=','3'],['tb_overtimes.status_dicatat','1'],['tb_overtimes.status_approve','0'],['tb_overtimes.status_paid','0'],['tb_departments.isTrial','1'],['tb_overtimes.isDelete',0]])
            ->orwhere([['tb_overtimes.paid',$id_employee],['tb_overtimes.status','1'],['tb_overtimes.status_dicatat','1'],['tb_overtimes.status_approve','0'],['tb_overtimes.status_paid','0'],['tb_departments.isTrial','1'],['tb_overtimes.isDelete',0]]);
        })
        ->select('tb_overtimes.*','tb1.employee_name as nm1','tb2.employee_name as nm2','tb3.employee_name as nm3');
        // ->get(['tb_overtimes.*','tb_departments.dept_name']);
        $totalData = $tb_overtime_plan->get(['tb_overtimes.*','tb_departments.dept_name'])->count();
        $totalFiltered = $totalData;
        $limit = $request->input('length');
        $start = $request->input('start');
        $order = ($request->input('order.0.column')==0 ? $columns[3] : $columns[$request->input('order.0.column')]);
        $dir = ($request->input('order.0.column')==0 ? 'desc' : $request->input('order.0.dir')) ; 
        if(empty($request->input('search.value'))){
            $posts =  $tb_overtime_plan->offset($start)
            ->limit($limit)
            ->orderBy($order,$dir)->get(['tb_overtimes.*','tb_departments.dept_name']);
        }else{
            $totalFiltered = 0;
            $search = $request->input('search.value');  
            $posts = $tb_overtime_plan
            ->where(function ($query) use ($search) {
            $query->orWhere('tb_overtimes.dept_name','LIKE',"%$search%")
                    ->orWhere('tb_overtimes.ot_date','LIKE',"%$search%")
                    ->orWhere('tb_overtimes.id_overtime','LIKE',"%$search%");
                    })->offset($start)
                    ->limit($limit)
                    ->orderBy($order,$dir)->get(['tb_overtimes.*','tb_departments.dept_name']);
                    
        $totalFiltered = DB::table('tb_overtimes')
        ->leftjoin('tb_employees as tb1','tb1.id','=','tb_overtimes.diperintah')
        ->leftjoin('tb_employees as tb2','tb2.id','=','tb_overtimes.disetujui')
        ->leftjoin('tb_employees as tb3','tb3.id','=','tb_overtimes.diketahui')
        ->leftjoin('tb_departments','tb_departments.id','=','tb_overtimes.dept_id')
        ->where(function ($query) use ($id_employee) {
        $query->where([['tb_overtimes.diperintah',$id_employee],['tb_overtimes.status','1'],['tb_overtimes.status_diperintah','<=','3'],['tb_overtimes.status_disetujui','<=','3'],['tb_overtimes.status_diketahui','<=','3'],['tb_overtimes.status_dicatat','1'],['tb_overtimes.status_approve','0'],['tb_overtimes.status_paid','0'],['tb_departments.isTrial','1'],['tb_overtimes.isDelete',0]])
            ->orwhere([['tb_overtimes.disetujui',$id_employee],['tb_overtimes.status','1'],['tb_overtimes.status_diperintah','<=','3'],['tb_overtimes.status_disetujui','<=','3'],['tb_overtimes.status_diketahui','<=','3'],['tb_overtimes.status_dicatat','1'],['tb_overtimes.status_approve','0'],['tb_overtimes.status_paid','0'],['tb_departments.isTrial','1'],['tb_overtimes.isDelete',0]])
            ->orwhere([['tb_overtimes.diketahui',$id_employee],['tb_overtimes.status','1'],['tb_overtimes.status_diperintah','<=','3'],['tb_overtimes.status_disetujui','<=','3'],['tb_overtimes.status_diketahui','<=','3'],['tb_overtimes.status_dicatat','1'],['tb_overtimes.status_approve','0'],['tb_overtimes.status_paid','0'],['tb_departments.isTrial','1'],['tb_overtimes.isDelete',0]])
            ->orwhere([['tb_overtimes.dicatat',$id_employee],['tb_overtimes.status','1'],['tb_overtimes.status_diperintah','<=','3'],['tb_overtimes.status_disetujui','<=','3'],['tb_overtimes.status_diketahui','<=','3'],['tb_overtimes.status_dicatat','1'],['tb_overtimes.status_approve','0'],['tb_overtimes.status_paid','0'],['tb_departments.isTrial','1'],['tb_overtimes.isDelete',0]])
            ->orwhere([['tb_overtimes.approve',$id_employee],['tb_overtimes.status','1'],['tb_overtimes.status_diperintah','<=','3'],['tb_overtimes.status_disetujui','<=','3'],['tb_overtimes.status_diketahui','<=','3'],['tb_overtimes.status_dicatat','1'],['tb_overtimes.status_approve','0'],['tb_overtimes.status_paid','0'],['tb_departments.isTrial','1'],['tb_overtimes.isDelete',0]])
            ->orwhere([['tb_overtimes.paid',$id_employee],['tb_overtimes.status','1'],['tb_overtimes.status_dicatat','1'],['tb_overtimes.status_approve','0'],['tb_overtimes.status_paid','0'],['tb_departments.isTrial','1'],['tb_overtimes.isDelete',0]]);
        })
        ->where(function ($query) use ($search) {
        $query->orWhere('tb_overtimes.dept_name','LIKE',"%$search%")
                ->orWhere('tb_overtimes.ot_date','LIKE',"%$search%")
                ->orWhere('tb_overtimes.id_overtime','LIKE',"%$search%");
                })  
        ->orWhere('tb_overtimes.dept_name','LIKE',"%$search%")
        ->orWhere('tb_overtimes.id_overtime','LIKE',"%$search%")
        ->count();
                 }
        $data = array();
        if(!empty($posts))
        { 
             $no = $start ;
          foreach($posts as $post){
            // /Admin/Overtime/Draft/  -Link button ke draft ketika submenu realisation
            // href='{{$tujuan}}{{$dt->id}}
            // <?php if(isset($id_employee)&&($id_employee=='122'||$id_employee=='101'))echo " 
            /* onclick="<?php echo "mymFunction(".$no.");"?>" */
            $qtynull = DB::table('tb_overtime_details')
                ->where('id_ot',$post->id)
                ->where('sign_after','=','0')
                ->where('status','<','90')
                ->count();
                $status_approve = '';
                if ($post->status_approve == 1) {
                     $status_approve = "<i class='fa fa-check-square-o'></i>"; 
                    } else if($post->status_dicatat == 0 ) {  $status_approve = "<i class='fa fa-square-o' style='color:black;'></i>";  }
                    else if($qtynull > 0 ){
                        $status_approve = "<i class='fa fa-square-o' style='color:red;'></i>";
                    }  else{
                        $status_approve = "<i class='fa fa-square-o' style='color:blue;'></i>";
                    }
            $no++; 
            $id = "'".str_replace("=","-", Crypt::encryptString($post->id)).'_'.$no."'"  ;   
            $button = '<a  title="Show" href="/Admin/Overtime/Approval/'.$post->id.'" class="btn btn-primary btn-xs" id="btnEdit" target="_blank"><i class="fa fa-edit"></i></a> <a title="Print"  href="/Admin/Overtime/Preview/'.$post->id.'" class="btn btn-primary btn-xs" id="btnPrint" target="_blank";"><i class="fa fa-print"></i></a>
            ';
              $nestedData['action'] = $button ;  
              $nestedData['no'] = $no ; 
              $nestedData['id'] = $post->id ;   
              $nestedData['id_overtime'] = $post->id_overtime ;   
              $nestedData['ot_date'] = $post->ot_date ;   
              $nestedData['dept_name'] = $post->dept_name ;   
              $nestedData['status_diperintah'] = ($post->status_diperintah > 0 ? "<i class='fa fa-check-square-o' title='".$post->nm1."'></i>" : "<i class='fa fa-square-o' title='".$post->nm1."'></i>" );   
              $nestedData['disetujui'] = $post->disetujui > 0 ? ($post->status_disetujui == 1 ? "<i class='fa fa-check-square-o' title='".$post->nm2."'></i>" : "<i class='fa fa-square-o' title='".$post->nm2."'></i>"): '' ;   
              $nestedData['diketahui'] =  $post->diketahui > 0 ? ($post->status_diketahui == 1 ? "<i class='fa fa-check-square-o' title='".$post->nm3."'></i>" : "<i class='fa fa-square-o' title='".$post->nm3."'></i>") : '' ;   
              $nestedData['status_dicatat'] = $post->status_dicatat == 1 ? "<i class='fa fa-check-square-o'></i>" : "<i class='fa fa-square-o' ></i>" ;   
              $nestedData['status_approve'] =  $status_approve;   
              $nestedData['status_paid'] = $post->status_paid ? "<i class='fa fa-check-square-o'></i>" : "<i class='fa fa-square-o' ></i>" ;   
              $data[] = $nestedData; 
          }
        }
        $json_data = array(
            "draw"            => intval($request->input('draw')),  
            "recordsTotal"    => intval($totalData),  
            "recordsFiltered" => intval($totalFiltered), 
            "data"            => $data   
            ); 
            echo json_encode($json_data);
    }
    #endregion
    public function autoCancel(){
        $cek_lock=DB::table('tb_utilities')->where('atribut','limit_approval_ot')->where('status','1')->count();
        if($cek_lock==1){
            date_default_timezone_set("Asia/Jakarta");
            $tgl_sekarang=date('Y-m-d H:i:s');
            //$tgl_sekarang='2022-04-05 15:00:00';
            $ot_diperintah=DB::table('tb_overtimes')
            ->where('autoCancel','0')
            ->where('status','1')
            ->where('status_diperintah','0')
            //->where('dept_id','99')
            ->where('created_at','>','2022-01-01 00:00:01')
            ->get();
            foreach($ot_diperintah as $row){
                $tgl_ot=date('Y-m-d H:i:s',strtotime($row->ot_date.' 20:00:00'));
                for($i=1;$i<30;$i++){
                    $batas=date('Y-m-d H:i:s',strtotime('+'.$i.' days',strtotime($tgl_ot)));
                    $batas_tgl=date('Y-m-d',strtotime($batas));
                    $wday=date('w',strtotime($batas));
                    $libur=DB::table('tb_freedays')->where('date_off',$batas_tgl)->count();
                    if($wday>0&&$wday<6&&$libur==0)break;
                }
                if($batas<$tgl_sekarang){
                    $cancel=DB::table('tb_overtimes')->where('id',$row->id)->update(['status_diperintah'=>'3','date_diperintah'=>$batas,'autoCancel'=>'1']);
                }
            }
            $ot_disetujui=DB::table('tb_overtimes')
            ->where('autoCancel','0')
            ->where('status','1')
            ->where('disetujui','>','0')
            ->where('status_disetujui','0')
            //->where('dept_id','99')
            ->where('created_at','>','2022-01-01 00:00:01')
            ->get();
            foreach($ot_disetujui as $row){
                $tgl_ot=date('Y-m-d H:i:s',strtotime($row->ot_date.' 20:00:00'));
                for($i=1;$i<30;$i++){
                    $batas=date('Y-m-d H:i:s',strtotime('+'.$i.' days',strtotime($tgl_ot)));
                    $batas_tgl=date('Y-m-d',strtotime($batas));
                    $wday=date('w',strtotime($batas));
                    $libur=DB::table('tb_freedays')->where('date_off',$batas_tgl)->count();
                    if($wday>0&&$wday<6&&$libur==0)break;
                }
                if($batas<$tgl_sekarang){
                    $cancel=DB::table('tb_overtimes')->where('id',$row->id)->update(['status_disetujui'=>'3','date_disetujui'=>$batas,'autoCancel'=>'1']);
                }
            }
            $ot_ketahui=DB::table('tb_overtimes')
            ->where('autoCancel','0')
            ->where('status','1')
            ->where('diketahui','>','0')
            ->where('status_diketahui','0')
            //->where('dept_id','99')
            ->where('created_at','>','2022-01-01 00:00:01')
            ->get();
            foreach($ot_ketahui as $row){
                $tgl_ot=date('Y-m-d H:i:s',strtotime($row->ot_date.' 20:00:00'));
                for($i=1;$i<30;$i++){
                    $batas=date('Y-m-d H:i:s',strtotime('+'.$i.' days',strtotime($tgl_ot)));
                    $batas_tgl=date('Y-m-d',strtotime($batas));
                    $wday=date('w',strtotime($batas));
                    $libur=DB::table('tb_freedays')->where('date_off',$batas_tgl)->count();
                    if($wday>0&&$wday<6&&$libur==0)break;
                }
                if($batas<$tgl_sekarang){
                    $cancel=DB::table('tb_overtimes')->where('id',$row->id)->update(['status_diketahui'=>'3','date_diketahui'=>$batas,'autoCancel'=>'1']);
                }
            }
        }
        //$nama=Auth::user()->name;
        $now=date('Y-m-d H:i:s');
        $cek=DB::table('tb_utilities_exception')->where('id_utility','9')->where('start','<=',$now)->where('end','>=',$now)->where('status','1')->where('start','<=',$now)->where('end','>=',$now)->get();
        foreach($cek as $dt){
            $ot=DB::table('tb_overtimes')
            ->where('autoCancel','1')
            ->where('status','1')
            ->where('admin',$dt->admin)
            ->where('created_at','>','2025-06-01 00:00:01')
            ->get();
            //return $ot;
            foreach($ot as $dt2){
                if($dt2->status_diperintah==3)$update=DB::table('tb_overtimes')->where('id',$dt2->id)->update(['status_diperintah'=>'0','date_diperintah'=>Null,'autoCancel'=>'0']);
                if($dt2->status_disetujui==3)$update=DB::table('tb_overtimes')->where('id',$dt2->id)->update(['status_disetujui'=>'0','date_disetujui'=>Null,'autoCancel'=>'0']);
                if($dt2->status_diketahui==3)$update=DB::table('tb_overtimes')->where('id',$dt2->id)->update(['status_diketahui'=>'0','date_diketahui'=>Null,'autoCancel'=>'0']);
            }
        }

    }

    #region Verification SPL
   public function VerificationSPL(Request $request){
        $email=Auth::user()->email;
        $tb_email=tb_email::where('email_address',$email)->get();
        $id_employee='';
        foreach($tb_email as $dt){
            $id_employee=$dt->id_employee;
        }    
        $jmlot_plan=DB::table('tb_overtimes')
        ->leftjoin('tb_departments','tb_departments.id','=','tb_overtimes.dept_id')
        ->where('status_paid','0')
        ->where('tb_overtimes.status','1')
		->where('tb_departments.isTrial','1')
        ->where('tb_overtimes.isDelete','0')
        ->count();
        $jmlot_actual=DB::table('tb_overtimes')
        ->leftjoin('tb_departments','tb_departments.id','=','tb_overtimes.dept_id')
        ->where([['status_approve','1'],['status_paid','0']])
		->where('tb_departments.isTrial','1')
        ->where('tb_overtimes.isDelete','0')
        ->count();
        date_default_timezone_set("Asia/Jakarta");
        $kalendar=CAL_GREGORIAN;
            $periode = $request->periode;
        if($periode == 0 ){
            $periode=date('Y-m');
            $periode_teks=date('F Y');
            $thn=date('Y',strtotime($periode.'-01'));
            $bln=date('m',strtotime($periode.'-01'));
            $hariakhir=cal_days_in_month($kalendar,$bln,$thn);        
        $Tglawal=date('Y-m-d',strtotime($thn.'-'.$bln.'-01'));
        $Tglakhir=date('Y-m-d',strtotime($thn.'-'.$bln.'-'.$hariakhir));
        }
       
        $status_lock_ot=DB::connection('mysql')->table('tb_utilities')->where('atribut','limit_approval_ot')->get();
        foreach($status_lock_ot as $dt){$slatus_lock=$dt->status;}
         $data = array(
            'jmlot_plan' => $jmlot_plan,
            'cabang'=>'Verifications',
            'menu'=>'overtime',
            'submenu'=>'verification'
            ,
            'jmlot_actual'=>$jmlot_actual,
            'periode'=>$periode,
            'status_lock'=>$slatus_lock,
         );
        return view('page/admin/m_overtime/verification_fixed',$data);    
        
        }
    public function GetDataVerification(Request $request){
        $columns = array( 
            0 =>'',
            1 =>'id', 
            2 =>'id_overtime',
            3 =>'ot_date',
            4 =>'dept_name' ,
            5 =>'status_diperintah' ,   
            6 =>'disetujui' ,   
            7 =>'diketahui' ,   
            8 =>'status_dicatat' , 
            9 =>'status_approve' ,   
            10 =>'status_dicatat' ,   
            11 =>'status_paid'
            );   
        
            $tb_overtime_actual=DB::table('tb_overtimes')
            ->leftjoin('tb_employees as tb1','tb1.id','=','tb_overtimes.diperintah')
            ->leftjoin('tb_employees as tb2','tb2.id','=','tb_overtimes.disetujui')
            ->leftjoin('tb_employees as tb3','tb3.id','=','tb_overtimes.diketahui')
            ->leftjoin('tb_departments','tb_departments.id','=','tb_overtimes.dept_id')
            ->where('status_paid','0')
            ->where('tb_overtimes.status','1')
            ->where('tb_overtimes.isDelete','0')
            ->where('tb_departments.isTrial','1')
        //  ->where('tb_overtimes.isDelete','0')
            ->select(['tb_overtimes.*','tb_departments.dept_name','tb1.employee_name as nm1','tb2.employee_name as nm2','tb3.employee_name as nm3']);
        
        
        $totalData = $tb_overtime_actual->get(['tb_overtimes.*','tb_departments.dept_name'])->count();
        $totalFiltered = $totalData;
        $limit = $request->input('length');
        $start = $request->input('start');
        $order = ($request->input('order.0.column')==0 ? $columns[2] : $columns[$request->input('order.0.column')]);
        $dir = ($request->input('order.0.column')==0 ? 'asc' : $request->input('order.0.dir')) ; 

        if(empty($request->input('search.value'))){
            $posts =  $tb_overtime_actual
            ->offset($start)
            ->limit($limit)
            ->orderBy($order,$dir)->get();
            // $totalFiltered = $posts->count();     
        }else{
            $search = $request->input('search.value');  
            $posts = $tb_overtime_actual
                    ->offset($start)
                    ->where('tb_overtimes.dept_name','LIKE',"%$search%")
                    ->orWhere('tb_overtimes.id_overtime','LIKE',"%$search%")
                    ->orWhere('tb_overtimes.ot_date','LIKE',"%$search%")
                    ->limit($limit)
                    ->orderBy($order,$dir)->get();
            $totalFiltered = DB::table('tb_overtimes')
            ->leftjoin('tb_employees as tb1','tb1.id','=','tb_overtimes.diperintah')
            ->leftjoin('tb_employees as tb2','tb2.id','=','tb_overtimes.disetujui')
            ->leftjoin('tb_employees as tb3','tb3.id','=','tb_overtimes.diketahui')
            ->leftjoin('tb_departments','tb_departments.id','=','tb_overtimes.dept_id')
            ->where('status_paid','0')
            ->where('tb_overtimes.status','1')
            ->where('tb_overtimes.isDelete','0')
            ->where('tb_departments.isTrial','1')
            // ->where('tb_overtimes.isDelete','0')
            ->where('tb_overtimes.dept_name','LIKE',"%$search%")
            ->orWhere('tb_overtimes.ot_date','LIKE',"%$search%")
            ->orWhere('tb_overtimes.id_overtime','LIKE',"%$search%")->count();     
        }

        $data = array();
       
        if(!empty($posts)){
        $tujuan="/Admin/Overtime/Verification/";
            $no = $start ;
            foreach($posts as $post){
                $status_lock_ot=DB::connection('mysql')->table('tb_utilities')->where('atribut','limit_approval_ot')->get();
                    foreach($status_lock_ot as $dt){
                $status_lock=$dt->status;
            }
            $button ='';
            $no++; 
            $id = "'".str_replace("=","-", Crypt::encryptString($post->id)).'_'.$no."'"  ;   
                $qtynull = DB::table('tb_overtime_details')
                ->where('id_ot',$post->id)
                ->where('sign_after','=','0')
                ->where('status','<','90')
                ->count();
                $status_approve = '';
                if ($post->status_approve == 1) {
                        $status_approve = "<i class='fa fa-check-square-o'></i>"; 
                } else if($post->status_dicatat == 0 ) { 
                        $status_approve = "<i class='fa fa-square-o' style='color:black;'></i>";  }
                else if($qtynull > 0 ){
                    $status_approve = "<i class='fa fa-square-o' style='color:red;'></i>";
                } else{
                    $status_approve = "<i class='fa fa-square-o' style='color:blue;'></i>";
                }
                
                if($post->autoCancel == 0 ){
                    if($post->status_approve=='0'){
                        $button .= '';
                    }else{
                        $button .= '<a  title="Show" href="javascript:void(0)" onClick=GetDetail('.$id.'); class="btn btn-primary btn-xs" id="btnEdit" ><i class="fa fa-edit"></i></a>';
                    }
                }else{
                    if(request()->user()->hasRole('hr_access') && $status_lock==0){
                        // $button .='<button title="Open" type="button" class="open-modal btn btn-success btn-xs" data-openid="{{$dt->id}}" data-openname="{{$dt->id_overtime}}"><i class="fa fa-refresh"></i></button>
                        // ';
                        $button .='<button title="Open" type="button" class="open-modal btn btn-success btn-xs"><i class="fa fa-refresh"></i></button>
                        ';
                    }else{
                        $button .='<button class="btn btn-default btn-xs" onclick="alert("SPL ini sudah lebih dari 24 Jam sejak dibuat '.$post->created_at.'");"><i class="fa fa-info-circle" title="Created_at '.$post->created_at.'"></i></button>
                        ';
                    }
                }
                $button .= ' <a title="Print"  href="/Admin/Overtime/Preview/'.$post->id.'" class="btn btn-primary btn-xs" id="btnPrint" target="_blank";"><i class="fa fa-print"></i></a>';
                $nestedData['action'] = $button ;  
                $nestedData['no'] = $no ; 
                $nestedData['id'] = $post->id ;   
                $nestedData['id_overtime'] = $post->id_overtime ;   
                $nestedData['ot_date'] = $post->ot_date ;   
                $nestedData['dept_name'] = $post->dept_name ;   
                $nestedData['status_diperintah'] = ($post->status_diperintah > 0 ? "<i class='fa fa-check-square-o' ></i>" : "<i class='fa fa-square-o' title='".$post->nm1."'></i>" );   
                $nestedData['disetujui'] = $post->status_disetujui == 1 ? "<i class='fa fa-check-square-o'></i>" : "<i class='fa fa-square-o' title='".$post->nm2."'></i>" ;   
                $nestedData['diketahui'] = $post->diketahui > 0 ?( $post->status_diketahui == 1 ? "<i class='fa fa-check-square-o'></i>" : "<i class='fa fa-square-o' title='".$post->nm3."'></i>") : '';   
                $nestedData['status_dicatat'] = $post->status_dicatat == 1 ? "<i class='fa fa-check-square-o'></i>" : "<i class='fa fa-square-o' ></i>" ;   
                $nestedData['status_approve'] = $status_approve;
                $nestedData['status_paid'] = $post->status_paid== 1 ? "<i class='fa fa-check-square-o'></i>" : "<a href='".$tujuan.$post->id."'><i class='fa fa-square-o' ></i></a>" ;   
                $nestedData['autoCancel'] = $post->autoCancel;
                $nestedData['is_over'] = $post->is_over;
                $nestedData['del'] = '<button title="Delete" type="button" id="modalDel" class="delete-modal btn btn-danger btn-xs" data-delid="'.$post->id.'" data-delname="'.$post->id_overtime.'" ><i class="fa fa-trash"></i> </button>';

                $data[] = $nestedData; 
                
        }
    }
    $json_data = array(
        "draw"            => intval($request->input('draw')),  
        "recordsTotal"    => intval($totalData),  
        "recordsFiltered" => intval($totalFiltered), 
        "data"            => $data   
        ); 
        echo json_encode($json_data);
}
    public function GetDataVerification2(Request $request){
        $columns = array( 
            0 =>'',
            1 =>'id', 
            2 =>'id_overtime',
            3 =>'ot_date',
            4 =>'dept_name' ,
            5 =>'status_diperintah' ,   
            6 =>'disetujui' ,   
            7 =>'diketahui' ,   
            8 =>'status_dicatat' , 
            9 =>'status_approve' ,   
            10 =>'status_dicatat' ,   
            11 =>'status_paid'
          );   
        $tb_overtime_actual=DB::table('tb_overtimes')
        ->leftjoin('tb_departments','tb_departments.id','=','tb_overtimes.dept_id')
        ->where([['status_approve','1'],['status_paid','0']])
        ->where('tb_overtimes.status','1')
		->where('tb_departments.isTrial','1')
        ->where('tb_overtimes.isDelete','0')
        ->select(['tb_overtimes.*','tb_departments.dept_name']);
        // ->get(['tb_overtimes.*','tb_departments.dept_name']);
        
        $totalData = $tb_overtime_actual->get(['tb_overtimes.*','tb_departments.dept_name'])->count();
        $totalFiltered = $totalData;
        $limit = $request->input('length');
        $start = $request->input('start');
        $order = ($request->input('order.0.column')==0 ? $columns[2] : $columns[$request->input('order.0.column')]);
        $dir = ($request->input('order.0.column')==0 ? 'desc' : $request->input('order.0.dir')) ; 

        if(empty($request->input('search.value'))){
            $posts =  $tb_overtime_actual->offset($start)
             ->limit($limit)
             ->orderBy($order,$dir)->get();
         }else{
            $search = $request->input('search.value');  
            $posts = DB::table('tb_overtimes')
            ->leftjoin('tb_departments','tb_departments.id','=','tb_overtimes.dept_id')
            ->where([['status_approve','1'],['status_paid','0']])
            ->where('tb_overtimes.status','1')
            ->where('tb_departments.isTrial','1')
            ->where('tb_overtimes.isDelete','0')
            ->where(function ($query) use ($search) {
                $query->where('tb_overtimes.dept_name','LIKE',"%$search%")
                ->orWhere('tb_overtimes.ot_date','LIKE',"%$search%")
                ->orWhere('tb_overtimes.id_overtime','LIKE',"%$search%");
            })
                    ->offset($start)
                    ->limit($limit)
                    ->orderBy($order,$dir)->get(['tb_overtimes.*','tb_departments.dept_name']);
                    
            $totalFiltered = DB::table('tb_overtimes')
            ->leftjoin('tb_departments','tb_departments.id','=','tb_overtimes.dept_id')
            ->where([['status_approve','1'],['status_paid','0']])
            ->where('tb_overtimes.status','1')
            ->where('tb_departments.isTrial','1')
            ->where('tb_overtimes.isDelete','0')
            ->where(function ($query) use ($search) {
                $query->where('tb_overtimes.dept_name','LIKE',"%$search%")
                ->orWhere('tb_overtimes.ot_date','LIKE',"%$search%")
                ->orWhere('tb_overtimes.id_overtime','LIKE',"%$search%");
            })
            ->count();     
        
        }
        $data = array();
        if(!empty($posts))
        { 
         $no = $start ;
          foreach($posts as $post){
            // /Admin/Overtime/Draft/  -Link button ke draft ketika submenu realisation
            // href='{{$tujuan}}{{$dt->id}}
            // <?php if(isset($id_employee)&&($id_employee=='122'||$id_employee=='101'))echo " 
            /* onclick="<?php echo "mymFunction(".$no.");"?>" */
            $qtynull = DB::table('tb_overtime_details')
            ->where('id_ot',$post->id)
            ->where('sign_after','=','0')
            ->where('status','<','90')
            ->count();
                $status_approve = '';
                if ($post->status_approve == 1) {
                     $status_approve = "<i class='fa fa-check-square-o'></i>"; 
                    } 
            $no++; 
            $id = "'".str_replace("=","-", Crypt::encryptString($post->id)).'_'.$no."'"  ;   
            $bShow = ($post->status_approve=='1') ? '<a  title="Show" href="javascript:void(0)" onClick=GetDetail('.$id.'); class="btn btn-primary btn-xs" id="btnEdit" ><i class="fa fa-edit"></i></a>':'';
            $button = $bShow.' <a title="Print"  href="/Admin/Overtime/Preview/'.$post->id.'" class="btn btn-primary btn-xs" id="btnPrint" target="_blank";><i class="fa fa-print"></i></a>
            ';
            $nestedData['action'] = $button ;  
            $nestedData['no'] = $no ; 
            $nestedData['id'] = $post->id ;   
            $nestedData['id_overtime'] = $post->id_overtime ;   
            $nestedData['ot_date'] = $post->ot_date ;   
            $nestedData['dept_name'] = $post->dept_name ;   
            $nestedData['status_diperintah'] = ($post->status_diperintah > 0 ? "<i class='fa fa-check-square-o' ></i>" : "<i class='fa fa-square-o' ></i>" );   
            $nestedData['disetujui'] = $post->status_disetujui == 1 ? "<i class='fa fa-check-square-o'></i>" : "<i class='fa fa-square-o'></i>" ;   
            $nestedData['diketahui'] = $post->diketahui > 0 ? ( $post->status_diketahui == 1 ? "<i class='fa fa-check-square-o'></i>" : "<i class='fa fa-square-o'></i>") : '' ;   
            $nestedData['status_dicatat'] = $post->status_dicatat == 1 ? "<i class='fa fa-check-square-o'></i>" : "<i class='fa fa-square-o' ></i>" ;   
            $nestedData['status_approve'] = $status_approve;
            $nestedData['status_paid'] = $post->status_paid ? "<i class='fa fa-check-square-o'></i>" : "<i class='fa fa-square-o' ></i>" ;   
            $nestedData['autoCancel'] = $post->autoCancel;
            $nestedData['is_over'] = $post->is_over;
            $data[] = $nestedData; 

          }
        }
        $json_data = array(
            "draw"            => intval($request->input('draw')),  
            "recordsTotal"    => intval($totalData),  
            "recordsFiltered" => intval($totalFiltered), 
            "data"            => $data   
            ); 
            echo json_encode($json_data);
    }
    #endregion
    public function GetVerificationDetail(Request $request){
        date_default_timezone_set("Asia/Jakarta");
        $kalendar=CAL_GREGORIAN;

      
        $data['id'] = $request->id;
        $periode=date('Y-m');
        $thn=date('Y',strtotime($periode.'-01'));
        $bln=date('m',strtotime($periode.'-01'));
        $hariakhir=cal_days_in_month($kalendar,$bln,$thn);
        $periode_awal=date('Y-m-d',strtotime($thn.'-'.$bln.'-01'));
        $periode_akhir=date('Y-m-d',strtotime($thn.'-'.$bln.'-'.$hariakhir));
        $data['periode'] = $periode;
        $data['cabang'] = 'Verification';
        $data['menu'] = 'overtime';
        return view('page/admin/m_overtime/verification_detailFixed',$data);  
    }
    public function GetDataVerificationDetail(Request $request){
        $str = explode("_", $request->id) ;   
        // if($str[0] == ""){
        $id = Crypt::decryptString(str_replace("-", "=", $str[0])) ;    
        $columns = array( 
            0 =>'',
            1 =>'id_overtime', 
            2 =>'date_on',
            3 =>'NIK',
            4 =>'employee_name' ,
            5 =>'start_act' ,   
            6 =>'start_act' ,   
            7 =>'finish_act' ,   
            8 =>'hours_act' , 
            9 =>'hours_convertion' ,   
            10 =>'id'
          );   
        // dd($id);
        $tb_overtime_detail=DB::table('tb_overtime_details')
        ->leftjoin('tb_overtimes','tb_overtimes.id','=','tb_overtime_details.id_ot')
        ->leftjoin('tb_employees','tb_employees.id','=','tb_overtime_details.id_employee')
        ->join('tb_employee_shifts', function ($join) {
            $join->on('tb_employee_shifts.id_employee', '=', 'tb_employees.id')
                 ->where('tb_employee_shifts.status',1);
        })
        ->leftjoin('tb_group_shifts','tb_group_shifts.id','=','tb_employee_shifts.id_shift')
        ->where('tb_overtime_details.id_ot',$id)
        //->where('badgenumber','6083')
        ->select(['tb_overtime_details.*','tb_employees.PIN','tb_employees.NIK','tb_employees.employee_name','tb_overtimes.status_diketahui','tb_overtimes.status_dicatat','tb_group_shifts.hari_kerja','tb_employees.badgenumber']);

        $totalData = $tb_overtime_detail->get(['tb_overtimes.*','tb_departments.dept_name'])->count();
        $totalFiltered = $totalData;
        $limit = $request->input('length');
        $start = $request->input('start');
        $order = ($request->input('order.0.column')==0 ? $columns[2] : $columns[$request->input('order.0.column')]);
        $dir = ($request->input('order.0.column')==0 ? 'desc' : $request->input('order.0.dir')) ; 

        if(empty($request->input('search.value'))){
            $posts =  $tb_overtime_detail
            ->offset($start)
            ->limit($limit)
            ->orderBy($order,$dir)->get();
            // $totalFiltered = $posts->count();     
        }else{
            $search = $request->input('search.value');  
            $posts = $tb_overtime_detail->orWhere('tb_overtimes.dept_name','LIKE',"%$search%")
                    ->orWhere('tb_overtimes.ot_date','LIKE',"%$search%")
                    ->orWhere('tb_overtimes.id_overtime','LIKE',"%$search%")
                    ->offset($start)
                    ->limit($limit)
                    ->orderBy($order,$dir)->get(['tb_overtimes.*','tb_departments.dept_name']);
            $totalFiltered =DB::table('tb_overtime_details')
            ->leftjoin('tb_overtimes','tb_overtimes.id','=','tb_overtime_details.id_ot')
            ->leftjoin('tb_employees','tb_employees.id','=','tb_overtime_details.id_employee')
            ->join('tb_employee_shifts', function ($join) {
                $join->on('tb_employee_shifts.id_employee', '=', 'tb_employees.id')
                     ->where('tb_employee_shifts.status',1);
            })
            ->leftjoin('tb_group_shifts','tb_group_shifts.id','=','tb_employee_shifts.id_shift')
            ->where('tb_overtime_details.id_ot',$id)
            ->where(function ($query) use ($search) {
            $query->where('tb_overtimes.dept_name','LIKE',"%$search%")
            ->orWhere('tb_overtimes.ot_date','LIKE',"%$search%")
            ->orWhere('tb_overtimes.id_overtime','LIKE',"%$search%");
            })->count();     
        }
        $data = array();
        if(!empty($posts)){
            $no = $start ;
            foreach($posts as $post){
                #region Get Finger Hours
                $checkin_act='';
                $checkout_act='';
                $status = '';
                $cin='';
                $cout='';
                $ncdatein=$post->start_act;
                //Reduce 2 Hour
                $date = date_create($ncdatein);
                date_add($date, date_interval_create_from_date_string('-2 hours'));
                $ncdateindown= date_format($date, 'Y-m-d H:i:s');
                //Increas 2 Hour
                $date = date_create($ncdatein);
                date_add($date, date_interval_create_from_date_string('2 hours'));
                $ncdateinup= date_format($date, 'Y-m-d H:i:s');
                //echo $ncdatein.' ';

                $ncdateout=$post->finish_act;
                //Reduce 3 Hour
                $date = date_create($ncdateout);
                date_add($date, date_interval_create_from_date_string('-3 hours'));
                $ncdateoutdown= date_format($date, 'Y-m-d H:i:s');
                //Increas 3 Hour
                $date = date_create($ncdateout);
                date_add($date, date_interval_create_from_date_string('3 hours'));
                $ncdateoutup= date_format($date, 'Y-m-d H:i:s');
                //echo $ncdateout.' ';
                $lenbadge=strlen($post->badgenumber);
                $nullbadge=9-$lenbadge;
                $j='';
                for($i=1;$i<=$nullbadge;$i++){
                    $j.='0';
                }
                $badge=$j.$post->badgenumber;
                $sambungan=0;
                $host1 = mysqli_connect("192.168.1.4","ems","123456","db_ems");
                $qry_finger=mysqli_query($host1,"select * from tb_utilities where atribut='iclock_connection'")
                or die(mysqli_error($host1));
                while($dt_finger=mysqli_fetch_array($qry_finger))
                {$sambungan=$dt_finger['status'];}
                if($sambungan==1){
                    $host = mysqli_connect("192.168.121.4:83306","cahyudin","123456","adms_db");

                    //$qry5=mysqli_query($host,"select checktime from checkinout where userid='$dt->PIN' and checktime>='$ncdateindown' and checktime<='$ncdateinup' order by checktime asc limit 1")or die(mysqli_error($host));
                    $qry5=mysqli_query($host,"select checktime from checkinout left join userinfo on userinfo.userid=checkinout.userid where badgenumber='$badge' and checktime>='$ncdateindown' and checktime<='$ncdateinup' order by checktime asc limit 1")or die(mysqli_error($host));
                    while($dt5=mysqli_fetch_array($qry5)){
                        $checkin_act=$dt5['checktime'];
                        $status='Present';
                        //if($cin=='')
                        $cin=$checkin_act;
                    }
                    $qry5=mysqli_query($host,"select checktime from checkinout left join userinfo on userinfo.userid=checkinout.userid where badgenumber='$badge' and checktime>='$ncdateoutdown' and checktime<='$ncdateoutup' order by checktime desc limit 1")or die(mysqli_error($host));
                    while($dt5=mysqli_fetch_array($qry5)){
                        $checkout_act=$dt5['checktime'];
                        $status='Present';
                        //if($cout=='')
                        $cout=$checkout_act;
                    }


                    $qry6=mysqli_query($host1,"select * from tb_employee_freedays where id_employee='$post->id_employee' and date_off='$post->date_on'")or die(mysqli_error($host1));
                    while($dt6=mysqli_fetch_array($qry6)){
                        $category=$dt6['category'];
                        $description=$dt6['description'];
                        $status=$category;
                    }
                }
                //Absen Manual Start
                if($checkin_act==''){
                    //$qry7=mysqli_query($host1,"select * from tb_checktimes where badgenumber='$post->badgenumber' and checktime>='$ncdateindown' and checktime<='$ncdateinup' order by checktime asc limit 1")or die(mysqli_error($host1));
                    $qry7=mysqli_query($host1,"select * from tb_checktimes where NIK='$post->NIK' and checktime>='$ncdateindown' and checktime<='$ncdateinup' order by checktime asc limit 1")or die(mysqli_error($host1));
                    while($dt7=mysqli_fetch_array($qry7)){
                        $checkin_act=$dt7['checktime'];
                        $status=$dt7['status_kerja'];
                        //if($checkin_act<$cin)
                        $cin=$checkin_act;
                    }
                }
                if($checkout_act==''){
                    $qry7=mysqli_query($host1,"select * from tb_checktimes where NIK='$post->NIK' and checktime>='$ncdateoutdown' and checktime<='$ncdateoutup' order by checktime desc limit 1")or die(mysqli_error($host1));
                    while($dt7=mysqli_fetch_array($qry7)){
                        $checkout_act=$dt7['checktime'];
                        $status=$dt7['status_kerja'];
                        //if($checkout_act>$cout)
                        $cout=$checkout_act;
                    }
                }
                #endregion

                ($post->sign_after=='1'||$post->sign_after=='3') ? $strdate = date('H:i',strtotime($post->start_act)).' ~ '.date('H:i',strtotime($post->finish_act)) : $strdate = '';
                if($checkin_act!=''&&$cin!=''){
                    if($status=='Present'){
                        if($checkin_act<=$post->start_act)
                        $checkin = "<span class='badge bg-green' title='Masuk: {{$cin}}'>".date('H:i',strtotime($cin))."</span>";
                        else $checkin =  "<span class='badge bg-yellow' title='Masuk: {{$cin}}'>".date('H:i',strtotime($cin))."</span>&nbsp;";
                    }else{
                        $checkin =  "<span class='badge bg-blue' title='Masuk: {{$cin}}'>".date('H:i',strtotime($cin))."</span>&nbsp;";
                    }
                }else{
                    $checkin = '';
                }
                if($checkout_act!=''&&$cout!=''){
                    if($status=='Present'){
                        if($checkout_act>=$post->finish_act)
                        $checkout = "<span class='badge bg-green' title='Pulang: {{$cout}}'>".date('H:i',strtotime($cout))."</span>";
                        else $checkout = "<span class='badge bg-yellow' title='Pulang: {{$cout}}'>".date('H:i',strtotime($cout))."</span>&nbsp;";
                    }else{
                        $checkout = "<span class='badge bg-blue' title='Pulang: {{$cout}}'>".date('H:i',strtotime($cout))."</span>&nbsp;";
                    }
                }else{
                    $checkout = '';
                }
            $no++; 
            if($post->sign_before=='1'&&$post->status_dicatat=='1'){
                $tgl_awal=date('Y-m-d',strtotime($post->start_act));
                $jam_awal=date('H:i',strtotime($post->start_act));
                $start_act=$tgl_awal.'T'.$jam_awal;
                $tgl_akhir=date('Y-m-d',strtotime($post->finish_act));
                $jam_akhir=date('H:i',strtotime($post->finish_act));
                $finish_act=$tgl_akhir.'T'.$jam_akhir;
                if($post->sign_after=='1'||$post->sign_after=='0'||$post->sign_after=='3'){
                    $button ='<button type="button" class="btn btn-primary btn-xs update-modal" data-dateon='.$post->date_on.' data-id_employee='.$post->id_employee.' data-idafter='.$post->id.' data-startact='.$start_act.' data-finishact='.$finish_act.' data-otcategory='.$post->ot_category.' data-hoursact='.$post->hours_act.' data-hoursconvertion='.$post->hours_convertion.' data-otisoma='.$post->minutes_break.' data-harikerja='.$post->hari_kerja.'><i class="fa fa-edit"></i></button>'; 
                    if($post->hari_kerja!=6){
                    $button .= '<button title="Yes" id="yes-dip'.$no.'" type="button" class="btn btn-success btn-xs yes-dip" data-nilai="'.$no.'" data-detailid="'.$post->id.'"><i class="fa fa-check"></i></button>
                    ';
                    }
                    $button .= '<button title="No" id="no-dip'.$no.'" type="button" class="btn btn-danger btn-xs no-dip" data-nilai="'.$no.'" data-detailid="'.$post->id.'"><i class="fa fa-close"></i></button>
                    ';
                }else{
                $button = "<i class='fa fa-info-circle' title='Approval Overtime in belum lengkap'> Waiting</i>";
                }
            }else{
                $button = "<i class='fa fa-info-circle' title='Approval Overtime in belum lengkap'> Waiting</i>";
            }
            
                // $nestedData['action'] = $button ;  
                $nestedData['no'] = $no ; 
                $nestedData['id_overtime'] = $post->id_overtime ;   
                $nestedData['date_on'] = $post->date_on ;   
                $nestedData['NIK'] = $post->NIK ;   
                $nestedData['employee_name'] = $post->employee_name;   
                $nestedData['strdate'] = $strdate;   
                $nestedData['checkin'] = $checkin ;   
                $nestedData['checkout'] = $checkout;   
                $nestedData['hours_act'] = $post->hours_act;
                $nestedData['hours_convertion'] = $post->hours_convertion;
                $nestedData['button'] = $button;
                $nestedData['ot_category'] = $post->ot_category;

                $data[] = $nestedData; 
            }
        }
        $json_data = array(
            "draw"            => intval($request->input('draw')),  
            "recordsTotal"    => intval($totalData),  
            "recordsFiltered" => intval($totalFiltered), 
            "data"            => $data   
            ); 
            echo json_encode($json_data);
    }
    #endregion
}

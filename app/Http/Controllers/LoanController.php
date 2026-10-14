<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Auth;

class LoanController extends Controller
{
    public function __construct(){
        $this->middleware(['auth','verified']);
        $this->site = $_SERVER['SCRIPT_NAME'];
    }
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request, $periode = null, $department = null, $employee = null)
    {
        // Get periode from request or use current month
        $currentPeriode = $periode ?? $request->get('periode', date('Y-m'));
        
        // Get employees data (assuming you have employee table)
        $employees = DB::table('tb_salary_contract')->get();
        
        // Get loans data with employee details
        $loans = DB::table('tb_loans as l')
            ->join('tb_salary_contract as e', 'l.id_employee', '=', 'e.id_employee')
            ->select(
                'l.*',
                'e.NIK',
                'e.nama_karyawan',
                'e.jabatan',
                'e.department',
                'e.status_pajak',
                DB::raw('CONVERT(char(7), l.created_at, 126) as periode')
            )
            ->orderBy('l.created_at', 'desc')
            ->get();
        
        // Calculate remaining balance and total payment
        foreach ($loans as $loan) {
            // Get total paid
            $totalPaid = DB::table('tb_loan_details')
                ->where('id_loan', $loan->id)
                ->where('status', 0) // 0 = paid
                ->sum('amount_per_month');
            
            // Get total unpaid
            $totalUnpaid = DB::table('tb_loan_details')
                ->where('id_loan', $loan->id)
                ->where('status', 1) // 1 = unpaid
                ->sum('amount_per_month');
            
            $loan->total_paid = $totalPaid;
            $loan->remaining_balance = $loan->amount_loan - $totalPaid;
            $loan->total_unpaid = $totalUnpaid;
            
            // Get count of installments
            $loan->total_installments = DB::table('tb_loan_details')
                ->where('id_loan', $loan->id)
                ->count();
                
            $loan->paid_installments = DB::table('tb_loan_details')
                ->where('id_loan', $loan->id)
                ->where('status', 0)
                ->count();
        }
        $site=$this->site;
        $menu='payroll_other';
        $periode=$currentPeriode;
        $juduls='Employee Loans';
        $tb_department=$this->tb_department_salary_contract('1');
        $tipe='SAI';

        return view('loans.index', compact('loans', 'employees', 'currentPeriode','site','menu','periode','juduls','tb_department','department','tipe'));
    }

    /**
     * Show the form for creating a new loan.
     */
    public function create()
    {
        $employees = DB::table('tb_salary_contract')->get();
        $site=$this->site;
        $menu='payroll_other';
        $currentPeriode=date('Y-m');
        $periode=$currentPeriode;
        $juduls='Employee Loans';
        $tb_department=$this->tb_department_salary_contract('1');
        $department='0';
        $tipe='SAI';
        $admin=Auth::user()->name;
        return view('loans.create', compact('employees', 'currentPeriode','site','menu','periode','juduls','tb_department','department','tipe','admin'));
    }

    /**
     * Store a newly created loan and generate loan details.
     */
    public function store(Request $request)
    {
        $normalizeAmount = static function ($value) {
            if (!is_string($value) && !is_numeric($value)) {
                return $value;
            }

            return str_replace(',', '.', str_replace('.', '', (string) $value));
        };

        $request->merge([
            'amount_loan' => $normalizeAmount($request->input('amount_loan')),
            'amount_per_month' => $normalizeAmount($request->input('amount_per_month')),
        ]);

        $validator = Validator::make($request->all(), [
            'id_employee' => 'required|exists:tb_salary_contract,id_employee',
            'amount_loan' => 'required|numeric|min:0',
            'amount_per_month' => 'required|numeric|min:0',
            'start_periode' => 'required|date_format:Y-m',
            'total_months' => 'required|integer|min:1',
            'admin' => 'nullable|string|max:255'
        ]);
    
        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }
    
        try {
            DB::beginTransaction();
    
            $amountLoan = $request->amount_loan;
            $amountPerMonth = $request->amount_per_month;
    
            // Insert into tb_loans
            $loanId = DB::table('tb_loans')->insertGetId([
                'id_employee' => $request->id_employee,
                'amount_loan' => $amountLoan,
                'amount_per_month' => $amountPerMonth,
                'admin' => $request->admin ?? 'system',
                'created_at' => now(),
                'updated_at' => now()
            ]);
    
            // Generate loan details with total loan amount
            $this->generateLoanDetails(
                $loanId,
                $request->start_periode,
                $request->total_months,
                $amountPerMonth,
                $amountLoan // Pass total loan amount
            );
    
            DB::commit();
    
            return redirect()->route('loans.index')
                ->with('success', 'Loan created successfully with ' . $request->total_months . ' installments.');
    
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()
                ->with('error', 'Failed to create loan: ' . $e->getMessage())
                ->withInput();
        }
    }    /**
     * Generate loan details based on loan data.
     */
    private function generateLoanDetails($loanId, $startPeriode, $totalMonths, $amountPerMonth, $totalLoan)
    {
        $startDate = \DateTime::createFromFormat('Y-m', $startPeriode);
        $remainingBalance = $totalLoan;
        
        for ($i = 0; $i < $totalMonths; $i++) {
            // Calculate payment date (25th of each month)
            $periodeDate = clone $startDate;
            $periodeDate->modify("+{$i} months");
            $periode = $periodeDate->format('Y-m');
            
            // Payment date is always 25th
            $paymentDate = $periode . '-25';
            
            // Sequence starts from 1
            $sequence = $i + 1;
            
            // Calculate amount for this installment
            // If this is the last installment, pay the remaining balance
            if ($i == $totalMonths - 1) {
                $installmentAmount = $remainingBalance;
            } else {
                $installmentAmount = min($amountPerMonth, $remainingBalance);
            }
            
            // Deduct from remaining balance
            $remainingBalance -= $installmentAmount;
            
            // Insert loan detail
            $save=DB::table('tb_loan_details')->insert([
                'id_loan' => $loanId,
                'sequance' => $sequence,
                'periode' => $periode,
                'payment_date' => $paymentDate,
                'amount_per_month' => $installmentAmount,
                'status' => 1, // 1 = unpaid (default)
                'admin' => 'system',
                'created_at' => now(),
                'updated_at' => now()
            ]);
            $remark='Nomor Pinjaman '.$loanId.', Angsuran ke-'.$sequence;
            $now=date('Y-m-d H:i:s');
            $admin=Auth::user()->name;

            if($save){
                $qty=DB::table('tb_salary_deduction')->where('id_loan',$loanId)->where('periode',$periode)->count();
                if($qty==0){
                    $id_employee=DB::table('tb_loans')->where('id',$loanId)->value('id_employee');
                    $add=DB::table('tb_salary_deduction')->insert([
                        'id_employee'=>$id_employee,
                        'tipe_kontrak'=>'SAI',
                        'periode'=>$periode,
                        'deduction_amount'=>$installmentAmount,
                        'component_name'=>'lain-lain',
                        'description'=>$remark,
                        'created_at'=>$now,
                        'admin'=>$admin,
                        'updated_at'=>$now,
                        'id_loan'=>$loanId,
                    ]);
                }
            }
        }
    }    /**
     * Display the specified resource.
     */
    public function show($id)
    {
        $loan = DB::table('tb_loans as l')
            ->join('tb_salary_contract as e', 'l.id_employee', '=', 'e.id_employee')
            ->select('l.*', 'e.NIK', 'e.nama_karyawan', 'e.jabatan', 'e.department')
            ->where('l.id', $id)
            ->first();

        if (!$loan) {
            return redirect()->route('loans.index')
                ->with('error', 'Loan not found.');
        }

        $loanDetails = DB::table('tb_loan_details')
            ->where('id_loan', $id)
            ->orderBy('sequance')
            ->get();

        $site=$this->site;
        $menu='payroll_other';
        $currentPeriode=date('Y-m');
        $periode=$currentPeriode;
        $juduls='Employee Loans';
        $tb_department=$this->tb_department_salary_contract('1');
        $department='0';
        $tipe='SAI';
    
        return view('loans.show', compact('loan', 'loanDetails', 'currentPeriode','site','menu','periode','juduls','tb_department','department','tipe'));
    }

    /**
     * Get loan details for a specific loan.
     */
    public function getDetails($id)
    {
        $loan = DB::table('tb_loans as l')
            ->join('tb_salary_contract as e', 'l.id_employee', '=', 'e.id_employee')
            ->select('l.*', 'e.NIK', 'e.nama_karyawan')
            ->where('l.id', $id)
            ->first();

        if (!$loan) {
            return response()->json(['error' => 'Loan not found'], 404);
        }

        $details = DB::table('tb_loan_details')
            ->where('id_loan', $id)
            ->orderBy('sequance')
            ->get();

        return response()->json([
            'loan' => $loan,
            'details' => $details
        ]);
    }

    public function destroy($id)
    {
        try {
            DB::beginTransaction();
    
            // Cek apakah loan exists
            $loan = DB::table('tb_loans')->where('id', $id)->first();
            if (!$loan) {
                return response()->json([
                    'success' => false,
                    'message' => 'Loan not found.'
                ], 404);
            }
    
            // Hapus loan details
            DB::table('tb_loan_details')->where('id_loan', $id)->delete();
            
            // Hapus loan
            $delete=DB::table('tb_loans')->where('id', $id)->delete();
            if($delete){
                DB::table('tb_salary_deduction')->where('id_loan',$id)->delete();
            }
    
            DB::commit();
    
            return response()->json([
                'success' => true,
                'message' => 'Loan deleted successfully.'
            ]);
    
        } catch (\Exception $e) {
            DB::rollBack();
            
            \Log::error('Delete loan error: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => 'Failed to delete loan: ' . $e->getMessage()
            ], 500);
        }
    }
      /**
     * Update loan detail status (mark as paid/unpaid).
     */
    public function updateDetailStatus(Request $request, $detailId)
    {
        $validator = Validator::make($request->all(), [
            'status' => 'required|in:0,1'
        ]);

        if ($validator->fails()) {
            return response()->json(['error' => $validator->errors()], 422);
        }

        try {
            DB::table('tb_loan_details')
                ->where('id', $detailId)
                ->update([
                    'status' => $request->status,
                    'updated_at' => now()
                ]);
            $id_loans=DB::table('tb_loan_details')->where('id',$detailId)->value('id_loan');
            if($request->status==0){
                DB::table('tb_loans')->where('id',$id_loans)->update(['progress'=>'1']);
            }else{
                $qty=DB::table('tb_loan_details')->where('id',$detailId)->where('status','0')->count();
                if($qty==0){
                    DB::table('tb_loans')->where('id',$id_loans)->update(['progress'=>'0']);
                }
            }

            return response()->json(['success' => 'Status updated successfully.']);

        } catch (\Exception $e) {
            return response()->json(['error' => 'Failed to update status: ' . $e->getMessage()], 500);
        }
    }

    public function tb_department_salary_contract($status){
        $tabel=DB::table('tb_salary_contract')
       ->leftjoin('tb_pph21_status','tb_pph21_status.id','=','tb_salary_contract.status_pajak')
        ->select('tb_salary_contract.department')
        ->groupby('tb_salary_contract.department');
        $tabel=$tabel->get();
        return $tabel;
    }
}
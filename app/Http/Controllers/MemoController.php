<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class MemoController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth','verified']);
    }
    public function index(Request $request)
    {
        $query = DB::table('tb_memo_ot');
        
        // Filter berdasarkan id_memo jika parameter ada
        if ($request->has('id_memo')) {
            $query->where('id_memo', $request->id_memo);
        }
        
        $memos = $query->get();
        return view('memos.index', compact('memos'));
    }
    public function create(Request $request)
    {
        $id_memo = $request->id_memo; // Ambil dari URL parameter
        $menu='Memo';
        $tb_reason=DB::table('tb_reason_ots')->where('is_active','1')->get();

        return view('memos.create', compact('id_memo','menu','tb_reason'));
    }
    public function store(Request $request)
    {

        DB::table('tb_memo_ot')->insert([
            'id_memo' => $request->id_memo,
            'part_no' => $request->part_no,
            'part_name' => $request->part_name,
            'lines' => $request->lines,
            'jph_gsph' => $request->jph_gsph,
            'process' => $request->process,
            'date_ot' => $request->date_ot,
            'start_ot' => $request->start_ot,
            'finish_ot' => $request->finish_ot,
            'plan_qty' => $request->plan_qty,
            'reason_ot' => $request->reason_ot,
            'remark' => $request->remark,
            'status' => $request->status,
            'created_at' => now(),
            'updated_at' => now()
        ]);

        return redirect('/GeneralMemo/Detail/'.$request->id_memo);
    }

    public function show($id)
    {
        $memo = DB::table('tb_memo_ot')->where('id', $id)->first();
        return view('memos.show', compact('memo'));
    }

    public function edit($id)
    {
        // 1. Ambil id_memo sebelum menghapus
        $memo = DB::table('tb_memo_ot')
                  ->where('id', $id)
                  ->first();
        
        $id_memo = $memo->id_memo ?? null;
        $menu='Memo';
        $tb_reason=DB::table('tb_reason_ots')->where('is_active','1')->get();

        $memo = DB::table('tb_memo_ot')->where('id', $id)->first();
        $memo2 = DB::table('tb_memo_ot')->leftjoin('tb_memo','tb_memo.id','=','tb_memo_ot.id_memo')->where('tb_memo_ot.id', $id)->get(['tb_memo.is_draft']);
        //return $memo;
        foreach($memo2 as $dt){
            //return $dt->is_draft;
            if($dt->is_draft==1)
            return view('memos.edit', compact('memo','id_memo','menu','tb_reason'));
            else
            return view('memos.edit_reason', compact('memo','id_memo','menu','tb_reason'));
        }
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'id_memo' => 'required|integer',
            // ... validasi lainnya sama seperti sebelumnya
        ]);

        DB::table('tb_memo_ot')
            ->where('id', $id)
            ->update([
                'id_memo' => $request->id_memo,
                'part_no' => $request->part_no,
                'part_name' => $request->part_name,
                'lines' => $request->lines,
                'jph_gsph' => $request->jph_gsph,
                'process' => $request->process,
                'date_ot' => $request->date_ot,
                'start_ot' => $request->start_ot,
                'finish_ot' => $request->finish_ot,
                'plan_qty' => $request->plan_qty,
                'reason_ot' => $request->reason_ot,
                'remark' => $request->remark,
                'status' => $request->status,
                'updated_at' => now()
            ]);

        return redirect('/GeneralMemo/Detail/'.$request->id_memo);
        // return redirect()->route('memos.index')
        //                  ->with('success', 'Memo updated successfully');
    }

    public function destroy($id)
    {
        // 1. Ambil id_memo sebelum menghapus
        $memo = DB::table('tb_memo_ot')
                  ->where('id', $id)
                  ->first();
        
        $id_memo = $memo->id_memo ?? null;
    
        // 2. Hapus data
        DB::table('tb_memo_ot')
          ->where('id', $id)
          ->delete();
    
        // 3. Redirect dengan parameter id_memo
        return redirect()->route('memos.index', ['id_memo' => $id_memo])
                         ->with('success', 'Memo deleted successfully');
    }    
}
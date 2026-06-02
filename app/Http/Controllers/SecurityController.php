<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Auth;

class SecurityController extends Controller
{
    public function __construct() { $this->middleware('auth'); }

    public function assignment()
    {
        $menu = 'scurity';
        $tgl_today = date('Y-m-d');
        $tgl_yesterday = date('Y-m-d', strtotime('-1 days'));

        $data = DB::table('tb_tugasluar_detail')
            ->join('tb_tugasluar', 'tb_tugasluar_detail.id_tugasluar', '=', 'tb_tugasluar.id')
            ->join('tb_work_contract', 'tb_tugasluar_detail.id_employee', '=', 'tb_work_contract.id_employee')
            ->select(
                'tb_tugasluar_detail.*',
                'tb_tugasluar.book_id',
                'tb_tugasluar.nopol',
                'tb_work_contract.nama_karyawan',
                'tb_work_contract.NIK',
                'tb_work_contract.department'
            )
            ->whereIn('tb_tugasluar_detail.tanggal', [$tgl_today, $tgl_yesterday])
            ->orderBy('tb_tugasluar_detail.tanggal', 'desc')
            ->orderBy('tb_tugasluar.id', 'desc')
            ->orderBy('tb_work_contract.nama_karyawan')
            ->get()
            ->groupBy('id_tugasluar');

        return view('security.assignment', compact('menu', 'data'));
    }

    public function bulkUpdateAssignment(Request $request)
    {
        $id_tugasluar = $request->id_tugasluar;
        $type = $request->type; // checkout or checkin
        $now = date('Y-m-d H:i:s');
        $timestamp = time();

        $details = DB::table('tb_tugasluar_detail')
            ->where('id_tugasluar', $id_tugasluar)
            ->get();

        DB::beginTransaction();
        try {
            foreach ($details as $detail) {
                $updateData = [];
                if ($type == 'checkout' && empty($detail->checkout_time)) {
                    $updateData['checkout_time'] = $timestamp;
                    $updateData['outside_status'] = 1;
                } elseif ($type == 'checkin' && !empty($detail->checkout_time) && empty($detail->checkin_time)) {
                    $updateData['checkin_time'] = $timestamp;
                    $updateData['outside_status'] = 2;
                }

                if (!empty($updateData)) {
                    DB::table('tb_tugasluar_detail')->where('id', $detail->id)->update($updateData);

                    // Sync with tb_manifest_outside
                    $exists = DB::table('tb_manifest_outside')
                        ->where('tanggal', $detail->tanggal)
                        ->where('id_employee', $detail->id_employee)
                        ->first();

                    $syncData = [
                        'tanggal' => $detail->tanggal,
                        'id_employee' => $detail->id_employee,
                        'outside_status' => $updateData['outside_status'] ?? ($exists->outside_status ?? 1),
                        'created_at' => $now,
                        'checked_by' => Auth::user() ? Auth::user()->name : 'Security',
                        'referensi' => 'TL'
                    ];

                    if ($type == 'checkout') {
                        $syncData['checkout_time'] = $now;
                    } else {
                        $syncData['checkin_time'] = $now;
                    }

                    if ($exists) {
                        DB::table('tb_manifest_outside')
                            ->where('id', $exists->id)
                            ->update($syncData);
                    } else {
                        // Handle NOT NULL constraint in tb_manifest_outside
                        if ($type == 'checkin' && !isset($syncData['checkout_time'])) {
                            $syncData['checkout_time'] = $now;
                        }
                        DB::table('tb_manifest_outside')->insert($syncData);
                    }
                }
            }

            DB::commit();
            return response()->json([
                'success' => true,
                'time' => date('H:i', $timestamp),
                'message' => 'Bulk update successful',
                'status' => $type == 'checkout' ? 1 : 2,
                'statusText' => $type == 'checkout' ? 'Out' : 'Kembali'
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['success' => false, 'message' => $e->getMessage()]);
        }
    }

    public function updateAssignment(Request $request)
    {
        $id = (int)$request->id;
        $type = $request->type; // checkout or checkin
        $now = date('Y-m-d H:i:s');
        $timestamp = time();
        
        $detail = DB::table('tb_tugasluar_detail')->where('id', $id)->first();
        if (!$detail) return response()->json(['success' => false, 'message' => 'Data detail tidak ditemukan (ID: '.$id.')']);

        $updateData = [];
        if ($type == 'checkout') {
            $updateData['checkout_time'] = $timestamp;
            $updateData['outside_status'] = 1;
        } else {
            $updateData['checkin_time'] = $timestamp;
            $updateData['outside_status'] = 2;
        }

        DB::beginTransaction();
        try {
            // Update Detail Table
            $updated = DB::table('tb_tugasluar_detail')->where('id', $id)->update($updateData);
            
            if ($updated === 0) {
                // If 0, check if it was already updated with the same value
                $current = DB::table('tb_tugasluar_detail')->where('id', $id)->first();
                $field = $type . '_time';
                if (!$current->$field) {
                     throw new \Exception("Gagal mengupdate database tb_tugasluar_detail");
                }
            }

            // Sync with tb_manifest_outside
            $exists = DB::table('tb_manifest_outside')
                ->where('tanggal', $detail->tanggal)
                ->where('id_employee', $detail->id_employee)
                ->first();

            $syncData = [
                'tanggal' => $detail->tanggal,
                'id_employee' => $detail->id_employee,
                'outside_status' => $updateData['outside_status'],
                'created_at' => $now,
                'checked_by' => Auth::user() ? Auth::user()->name : 'Security',
                'referensi' => 'TL'
            ];

            if ($type == 'checkout') {
                $syncData['checkout_time'] = $now;
            } else {
                $syncData['checkin_time'] = $now;
            }

            if ($exists) {
                DB::table('tb_manifest_outside')
                    ->where('id', $exists->id)
                    ->update($syncData);
            } else {
                // If checkin without checkout, we need to provide checkout_time for NOT NULL field
                if ($type == 'checkin' && !isset($syncData['checkout_time'])) {
                    $syncData['checkout_time'] = $now; 
                }
                DB::table('tb_manifest_outside')->insert($syncData);
            }

            DB::commit();
            return response()->json([
                'success' => true,
                'time' => date('H:i', $timestamp),
                'id' => $id,
                'status' => $updateData['outside_status'],
                'statusText' => $updateData['outside_status'] == 1 ? 'Out' : 'Kembali'
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['success' => false, 'message' => $e->getMessage()]);
        }
    }

    public function permit()
    {
        $menu = 'scurity';
        $tgl_today = date('Y-m-d');
        $tgl_yesterday = date('Y-m-d', strtotime('-1 days'));

        $data = DB::table('tb_izins')
            ->leftJoin('tb_employees', 'tb_employees.id', '=', 'tb_izins.id_employee')
            ->leftJoin('tb_departments', 'tb_departments.id', '=', 'tb_employees.dept_id')
            ->leftJoin('tb_manifest_outside', function($join) {
                $join->on('tb_manifest_outside.id_employee', '=', 'tb_izins.id_employee')
                     ->on('tb_manifest_outside.tanggal', '=', 'tb_izins.apply_date');
            })
            ->select(
                'tb_izins.*',
                'tb_employees.employee_name',
                'tb_employees.NIK',
                'tb_departments.dept_name as department',
                'tb_manifest_outside.checkout_time as mo_checkout',
                'tb_manifest_outside.checkin_time as mo_checkin'
            )
            ->whereIn('tb_izins.apply_date', [$tgl_today, $tgl_yesterday])
            ->whereIn('tb_izins.category', ['B', 'C'])
            ->orderBy('tb_izins.apply_date', 'desc')
            ->get();
            return $data;
        return view('security.permit', compact('menu', 'data'));
    }

    public function updatePermit(Request $request)
    {
        $id = (int)$request->id;
        $type = $request->type; // checkout or checkin
        $now = date('Y-m-d H:i:s');
        $timestamp = time();

        $detail = DB::table('tb_izins')->where('id', $id)->first();
        if (!$detail) return response()->json(['success' => false, 'message' => 'Data ijin tidak ditemukan']);

        $updateData = [];
        $outside_status = 0;
        if ($type == 'checkout') {
            $updateData['scurity'] = 689;
            $updateData['status_scurity'] = 1;
            $updateData['date_security'] = $now;
            $updateData['start_izin'] = $now;
            $outside_status = 1;
        } else {
            $updateData['finish_izin'] = $now;
            $outside_status = 2;
        }

        DB::beginTransaction();
        try {
            DB::table('tb_izins')->where('id', $id)->update($updateData);

            // Sync with tb_manifest_outside
            $exists = DB::table('tb_manifest_outside')
                ->where('tanggal', $detail->apply_date)
                ->where('id_employee', $detail->id_employee)
                ->first();

            $syncData = [
                'tanggal' => $detail->apply_date,
                'id_employee' => $detail->id_employee,
                'outside_status' => $outside_status,
                'created_at' => $now,
                'checked_by' => Auth::user() ? Auth::user()->name : 'Security',
                'referensi' => 'Permit'
            ];

            if ($type == 'checkout') {
                $syncData['checkout_time'] = $now;
            } else {
                $syncData['checkin_time'] = $now;
            }

            if ($exists) {
                DB::table('tb_manifest_outside')
                    ->where('id', $exists->id)
                    ->update($syncData);
            } else {
                if ($type == 'checkin' && !isset($syncData['checkout_time'])) {
                    $syncData['checkout_time'] = $now; 
                }
                DB::table('tb_manifest_outside')->insert($syncData);
            }

            DB::commit();
            return response()->json([
                'success' => true,
                'time' => date('H:i'),
                'message' => 'Update successful'
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['success' => false, 'message' => $e->getMessage()]);
        }
    }
}

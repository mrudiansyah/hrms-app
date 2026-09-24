<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Cache;

class StructureController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth', 'verified']);
    }

    public function index(Request $request)
    {
        $refresh = $request->has('refresh');

        if ($refresh) {
            Cache::forget('org_structure_tree_data');
        }

        // Cache org chart tree calculation for 5 minutes (300 seconds)
        $treeData = Cache::remember('org_structure_tree_data', 300, function () {
            // Lightweight 1-pass query without photo joins
            $employees = DB::table('tb_employees as e')
                ->leftJoin('tb_departments as d', 'd.id', '=', 'e.dept_id')
                ->leftJoin('tb_positions as p', 'p.id', '=', 'e.position_id')
                ->where('e.status', '1')
                ->where('e.delete', '0')
                ->get([
                    'e.id',
                    'e.leader_id',
                    'e.NIK',
                    'e.employee_name',
                    'd.dept_code',
                    'p.position_name'
                ]);

            // Build adjacency map: leader_id => array of child employee IDs
            $childrenMap = [];
            $empDict = [];
            $validIds = [];

            foreach ($employees as $emp) {
                $empDict[$emp->id] = (array) $emp;
                $validIds[$emp->id] = true;
                $leaderId = $emp->leader_id ? (int) $emp->leader_id : 0;
                $childrenMap[$leaderId][] = (int) $emp->id;
            }

            // Memoized recursive total subordinates calculation
            $memo = [];
            $calcTotalSubordinates = function ($id) use (&$calcTotalSubordinates, &$childrenMap, &$memo) {
                if (isset($memo[$id])) {
                    return $memo[$id];
                }
                $total = 0;
                if (isset($childrenMap[$id])) {
                    foreach ($childrenMap[$id] as $childId) {
                        $total += 1 + $calcTotalSubordinates($childId);
                    }
                }
                $memo[$id] = $total;
                return $total;
            };

            // Attach direct_count and total_count to each employee
            foreach ($empDict as $id => &$empData) {
                $empData['direct_count'] = isset($childrenMap[$id]) ? count($childrenMap[$id]) : 0;
                $empData['total_count'] = $calcTotalSubordinates($id);
            }
            unset($empData);

            // Build tree roots
            $tree = $this->buildTree($childrenMap, $empDict, 0, $validIds);

            return [
                'tree' => $tree,
                'allEmployees' => array_values($empDict)
            ];
        });

        $departments = DB::table('tb_departments')->where('isDelete', 0)->orderBy('dept_name', 'asc')->get();

        return view('page/admin/structure/index', [
            'tree' => $treeData['tree'],
            'allEmployees' => $treeData['allEmployees'],
            'departments' => $departments,
            'menu' => 'recruitment'
        ]);
    }

    /**
     * Fetch detail info + photo for Modal view on-demand
     */
    public function detail($id)
    {
        $employee = DB::table('tb_employees as e')
            ->leftJoin('tb_departments as d', 'd.id', '=', 'e.dept_id')
            ->leftJoin('tb_positions as p', 'p.id', '=', 'e.position_id')
            ->leftJoin('tb_employees as l', 'l.id', '=', 'e.leader_id')
            ->where('e.id', $id)
            ->first([
                'e.id',
                'e.NIK',
                'e.employee_name',
                'e.gender',
                'e.join_date',
                'd.dept_name',
                'd.dept_code',
                'p.position_name',
                'l.employee_name as leader_name'
            ]);

        if (!$employee) {
            return response()->json(['status' => 'error', 'message' => 'Employee not found'], 4404);
        }

        // Get latest photo
        $photoRow = DB::table('tb_photos')
            ->where('id_employee', $id)
            ->orderBy('id', 'desc')
            ->first(['nama_photo']);

        return response()->json([
            'status' => 'success',
            'data' => [
                'id' => $employee->id,
                'NIK' => $employee->NIK,
                'employee_name' => $employee->employee_name,
                'gender' => $employee->gender,
                'join_date' => $employee->join_date,
                'dept_name' => $employee->dept_name,
                'dept_code' => $employee->dept_code,
                'position_name' => $employee->position_name,
                'leader_name' => $employee->leader_name ?? '-',
                'photo' => $photoRow ? $photoRow->nama_photo : null
            ]
        ]);
    }

    private function buildTree(&$childrenMap, &$empDict, $parentId, &$validIds)
    {
        $branch = [];

        if ($parentId === 0) {
            $rootIds = [];
            if (isset($childrenMap[0])) {
                $rootIds = array_merge($rootIds, $childrenMap[0]);
            }
            foreach ($empDict as $id => $emp) {
                $leaderId = (int) $emp['leader_id'];
                if ($leaderId > 0 && !isset($validIds[$leaderId]) && !in_array($id, $rootIds)) {
                    $rootIds[] = $id;
                }
            }
            $rootIds = array_unique($rootIds);

            foreach ($rootIds as $childId) {
                if (isset($empDict[$childId])) {
                    $node = $empDict[$childId];
                    $node['children'] = $this->buildTree($childrenMap, $empDict, $childId, $validIds);
                    $branch[] = $node;
                }
            }
        } else {
            if (isset($childrenMap[$parentId])) {
                foreach ($childrenMap[$parentId] as $childId) {
                    if (isset($empDict[$childId])) {
                        $node = $empDict[$childId];
                        $node['children'] = $this->buildTree($childrenMap, $empDict, $childId, $validIds);
                        $branch[] = $node;
                    }
                }
            }
        }

        return $branch;
    }
}

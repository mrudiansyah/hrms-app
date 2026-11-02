<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;

class QccTeamController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index()
    {
        return $this->masterTeams();
    }

    public function masterTeams()
    {
        $this->authorizeImprovement();

        $teams = DB::table('tb_qcc_team')
            ->orderBy('group_name')
            ->get();

        $menu = 'qcc';

        return view('qcc.teams_master', compact('menu', 'teams'));
    }

    public function periodTeams(Request $request)
    {
        $this->authorizeImprovement();

        $periods = DB::table('tb_qcc_periode')
            ->orderByDesc('periode_start')
            ->orderByDesc('id')
            ->get();
        $selectedPeriod = null;
        $teamPeriods = collect();
        $members = collect();
        $positions = DB::table('tb_position_team')
            ->orderBy('position_name')
            ->get();
        $employees = DB::table('tb_employees')
            ->where('delete', '0')
            ->orderBy('employee_name')
            ->get(['id', 'NIK', 'employee_name']);

        $teams = DB::table('tb_qcc_team')
            ->orderBy('group_name')
            ->get();

        if ($request->filled('periode_id')) {
            $selectedPeriod = DB::table('tb_qcc_periode')
                ->where('id', $request->input('periode_id'))
                ->first();
            abort_unless($selectedPeriod, 404, 'Periode QCC tidak ditemukan.');
        } else {
            $selectedPeriod = $periods->first();
        }

        if ($selectedPeriod) {
            $teamPeriods = DB::table('tb_qcc_periode_team')
                ->where('id_qcc_periode', $selectedPeriod->id)
                ->get()
                ->keyBy('id_qcc_team');

            if ($teamPeriods->isNotEmpty()) {
                $members = DB::table('tb_qcc_periode_team_member')
                    ->leftJoin('tb_employees', 'tb_employees.id', '=', 'tb_qcc_periode_team_member.id_employee')
                    ->leftJoin('tb_position_team', 'tb_position_team.id', '=', 'tb_qcc_periode_team_member.id_position_team')
                    ->whereIn('tb_qcc_periode_team_member.id_qcc_team_periode', $teamPeriods->pluck('id'))
                    ->get([
                        'tb_qcc_periode_team_member.*',
                        'tb_employees.NIK',
                        'tb_employees.employee_name',
                        'tb_position_team.position_name',
                    ])
                    ->groupBy('id_qcc_team_periode');
            }
        }

        $menu = 'qcc';

        return view('qcc.teams_period', compact(
            'menu',
            'teams',
            'periods',
            'selectedPeriod',
            'teamPeriods',
            'positions',
            'employees',
            'members',
        ));
    }

    public function storeTeam(Request $request)
    {
        $this->authorizeImprovement();
        $data = $request->validate([
            'group_name' => ['required', 'string', 'max:255'],
            'registered_at' => ['required', 'date'],
        ]);
        $now = now();

        DB::table('tb_qcc_team')->insert([
            ...$data,
            'created_at' => $now,
            'updated_at' => $now,
            'updated_by' => Auth::user()->name,
        ]);

        return redirect('/qcc_team')->with('success', 'Team berhasil ditambahkan.');
    }

    public function updateTeam(Request $request, int $id)
    {
        $this->authorizeImprovement();
        abort_unless(DB::table('tb_qcc_team')->where('id', $id)->exists(), 404);

        $data = $request->validate([
            'group_name' => ['required', 'string', 'max:255'],
            'registered_at' => ['required', 'date'],
        ]);

        DB::table('tb_qcc_team')->where('id', $id)->update([
            ...$data,
            'updated_at' => now(),
            'updated_by' => Auth::user()->name,
        ]);

        return redirect('/qcc_team')->with('success', 'Team berhasil diperbarui.');
    }

    public function destroyTeam(int $id)
    {
        $this->authorizeImprovement();
        abort_unless(DB::table('tb_qcc_team')->where('id', $id)->exists(), 404);

        if (DB::table('tb_qcc_periode_team')->where('id_qcc_team', $id)->exists()) {
            return redirect('/qcc_team')->with('error', 'Team masih terdaftar pada periode QCC dan tidak dapat dihapus.');
        }

        DB::table('tb_qcc_team')->where('id', $id)->delete();

        return redirect('/qcc_team')->with('success', 'Team berhasil dihapus.');
    }

    public function toggleTeamPeriod(Request $request)
    {
        $this->authorizeImprovement();

        $data = $request->validate([
            'id_qcc_periode' => ['required', 'integer', Rule::exists('tb_qcc_periode', 'id')],
            'id_qcc_team' => ['required', 'integer', Rule::exists('tb_qcc_team', 'id')],
            'registered' => ['required', 'boolean'],
        ]);

        $isRegistered = filter_var($data['registered'], FILTER_VALIDATE_BOOLEAN);
        $teamPeriods = DB::table('tb_qcc_periode_team')
            ->where('id_qcc_periode', $data['id_qcc_periode'])
            ->where('id_qcc_team', $data['id_qcc_team'])
            ->get();

        if ($isRegistered && $teamPeriods->isEmpty()) {
            $now = now();
            DB::table('tb_qcc_periode_team')->insert([
                'id_qcc_periode' => $data['id_qcc_periode'],
                'id_qcc_team' => $data['id_qcc_team'],
                'created_at' => $now,
                'updated_at' => $now,
                'updated_by' => Auth::user()->name,
            ]);
        } elseif (!$isRegistered && $teamPeriods->isNotEmpty()) {
            $teamPeriodIds = $teamPeriods->pluck('id');
            DB::transaction(function () use ($teamPeriodIds) {
                DB::table('tb_qcc_periode_team_member')
                    ->whereIn('id_qcc_team_periode', $teamPeriodIds)
                    ->delete();
                DB::table('tb_qcc_periode_team')->whereIn('id', $teamPeriodIds)->delete();
            });
        }

        return redirect('/qcc_team/periode?periode_id=' . $data['id_qcc_periode'])
            ->with('success', $isRegistered ? 'Team ditambahkan ke periode.' : 'Team dihapus dari periode beserta anggotanya.');
    }

    public function storeMember(Request $request, int $teamPeriodId)
    {
        $this->authorizeImprovement();
        $teamPeriod = DB::table('tb_qcc_periode_team')->where('id', $teamPeriodId)->first();
        abort_unless($teamPeriod, 404);

        $data = $request->validate([
            'id_employee' => [
                'required',
                'integer',
                Rule::exists('tb_employees', 'id')->where('delete', '0'),
                Rule::unique('tb_qcc_periode_team_member', 'id_employee')
                    ->where(fn ($query) => $query->where('id_qcc_team_periode', $teamPeriodId)),
            ],
            'id_position_team' => ['required', 'integer', Rule::exists('tb_position_team', 'id')],
        ]);
        $now = now();

        DB::table('tb_qcc_periode_team_member')->insert([
            ...$data,
            'id_qcc_team_periode' => $teamPeriodId,
            'created_at' => $now,
            'updated_at' => $now,
            'updated_by' => Auth::user()->name,
        ]);

        return $this->redirectToPeriod($teamPeriod->id_qcc_periode)
            ->with('success', 'Anggota berhasil ditambahkan.');
    }

    public function updateMember(Request $request, int $id)
    {
        $this->authorizeImprovement();
        $member = DB::table('tb_qcc_periode_team_member')->where('id', $id)->first();
        abort_unless($member, 404);

        $data = $request->validate([
            'id_employee' => [
                'required',
                'integer',
                Rule::exists('tb_employees', 'id')->where(
                    fn ($query) => $query->where('delete', '0')->orWhere('id', $member->id_employee),
                ),
                Rule::unique('tb_qcc_periode_team_member', 'id_employee')
                    ->where(fn ($query) => $query->where('id_qcc_team_periode', $member->id_qcc_team_periode))
                    ->ignore($id),
            ],
            'id_position_team' => ['required', 'integer', Rule::exists('tb_position_team', 'id')],
        ]);

        DB::table('tb_qcc_periode_team_member')->where('id', $id)->update([
            ...$data,
            'updated_at' => now(),
            'updated_by' => Auth::user()->name,
        ]);

        $periodeId = DB::table('tb_qcc_periode_team')
            ->where('id', $member->id_qcc_team_periode)
            ->value('id_qcc_periode');

        return $this->redirectToPeriod($periodeId)->with('success', 'Data anggota berhasil diperbarui.');
    }

    public function destroyMember(int $id)
    {
        $this->authorizeImprovement();
        $member = DB::table('tb_qcc_periode_team_member')->where('id', $id)->first();
        abort_unless($member, 404);
        $periodeId = DB::table('tb_qcc_periode_team')
            ->where('id', $member->id_qcc_team_periode)
            ->value('id_qcc_periode');
        $deleted = DB::table('tb_qcc_periode_team_member')->where('id', $id)->delete();
        abort_unless($deleted, 404);

        return $this->redirectToPeriod($periodeId)->with('success', 'Anggota berhasil dihapus.');
    }

    private function redirectToPeriod($periodeId)
    {
        abort_unless($periodeId, 404);

        return redirect('/qcc_team/periode?periode_id=' . $periodeId);
    }

    private function authorizeImprovement(): void
    {
        abort_unless(Auth::user()?->hasRole('improvement'), 403, 'Anda tidak punya akses.');
    }
}

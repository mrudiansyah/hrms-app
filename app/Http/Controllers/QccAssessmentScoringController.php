<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;

class QccAssessmentScoringController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index(Request $request)
    {
        $this->authorizeCommittee();

        $periods = DB::table('tb_qcc_periode')
            ->orderByDesc('periode_start')
            ->orderByDesc('id')
            ->get();
        $categories = DB::table('tb_asses_category')
            ->orderBy('asses_category')
            ->get();
        $selectedPeriodId = $this->selectedId($request, 'periode_id', $periods);
        $selectedCategoryId = $this->selectedId($request, 'category_id', $categories);

        $teamPeriods = collect();
        if ($selectedPeriodId) {
            $teamPeriods = DB::table('tb_qcc_periode_team')
                ->join('tb_qcc_team', 'tb_qcc_team.id', '=', 'tb_qcc_periode_team.id_qcc_team')
                ->where('tb_qcc_periode_team.id_qcc_periode', $selectedPeriodId)
                ->orderBy('tb_qcc_team.group_name')
                ->get([
                    'tb_qcc_periode_team.id',
                    'tb_qcc_periode_team.theme_team',
                    'tb_qcc_team.group_name',
                    'tb_qcc_team.department',
                ]);
        }

        $selectedTeamPeriodId = null;
        if ($request->filled('team_period_id')) {
            abort_unless(
                $teamPeriods->contains(fn ($teamPeriod) => (string) $teamPeriod->id === (string) $request->query('team_period_id')),
                404,
            );
            $selectedTeamPeriodId = (int) $request->query('team_period_id');
        } else {
            $selectedTeamPeriodId = $teamPeriods->first()?->id;
        }

        $assessors = collect();
        if ($selectedPeriodId && $selectedCategoryId) {
            $assessors = DB::table('tb_qcc_assesor')
                ->where('id_qcc_periode', $selectedPeriodId)
                ->where('id_asses_category', $selectedCategoryId)
                ->orderBy('assesor_name')
                ->orderBy('id')
                ->get();
        }

        $selectedAssessorId = null;
        if ($request->filled('assesor_id')) {
            abort_unless(
                $assessors->contains(fn ($assessor) => (string) $assessor->id === (string) $request->query('assesor_id')),
                404,
            );
            $selectedAssessorId = (int) $request->query('assesor_id');
        } else {
            $selectedAssessorId = $assessors->first()?->id;
        }

        $details = collect();
        $criteriaByDetail = collect();
        $scoresByDetail = collect();
        if ($selectedCategoryId && $selectedTeamPeriodId && $selectedAssessorId) {
            $details = DB::table('tb_qcc_assesment_detail')
                ->join('tb_qcc_assesment', 'tb_qcc_assesment.id', '=', 'tb_qcc_assesment_detail.id_assesment')
                ->where('tb_qcc_assesment.id_asses_category', $selectedCategoryId)
                ->orderBy('tb_qcc_assesment.index_assesment')
                ->orderBy('tb_qcc_assesment.id')
                ->orderBy('tb_qcc_assesment_detail.id')
                ->get([
                    'tb_qcc_assesment_detail.id',
                    'tb_qcc_assesment_detail.id_assesment',
                    'tb_qcc_assesment_detail.asses_description_detail',
                    'tb_qcc_assesment_detail.bobot',
                    'tb_qcc_assesment.asses_description',
                    'tb_qcc_assesment.index_assesment',
                ]);

            if ($details->isNotEmpty()) {
                $criteriaByDetail = DB::table('tb_qcc_assesment_criteria')
                    ->whereIn('id_assesment_detail', $details->pluck('id'))
                    ->orderBy('id')
                    ->get()
                    ->groupBy('id_assesment_detail');
                $scoresByDetail = DB::table('tb_qcc_assesment_team')
                    ->where('id_periode_team', $selectedTeamPeriodId)
                    ->where('id_assesor', $selectedAssessorId)
                    ->whereIn('id_assesment_detail', $details->pluck('id'))
                    ->get()
                    ->keyBy('id_assesment_detail');
            }
        }

        $selectedPeriod = $periods->firstWhere('id', $selectedPeriodId);
        $selectedCategory = $categories->firstWhere('id', $selectedCategoryId);
        $selectedTeamPeriod = $teamPeriods->firstWhere('id', $selectedTeamPeriodId);
        $selectedAssessor = $assessors->firstWhere('id', $selectedAssessorId);
        $menu = 'qcc';

        return view('qcc.assessment_scoring', compact(
            'menu',
            'periods',
            'categories',
            'teamPeriods',
            'assessors',
            'selectedPeriodId',
            'selectedCategoryId',
            'selectedTeamPeriodId',
            'selectedAssessorId',
            'selectedPeriod',
            'selectedCategory',
            'selectedTeamPeriod',
            'selectedAssessor',
            'details',
            'criteriaByDetail',
            'scoresByDetail',
        ));
    }

    public function summary(Request $request)
    {
        $this->authorizeCommittee();

        $periods = DB::table('tb_qcc_periode')
            ->orderByDesc('periode_start')
            ->orderByDesc('id')
            ->get();
        $categories = DB::table('tb_asses_category')
            ->orderBy('asses_category')
            ->get();
        $selectedPeriodId = $this->selectedId($request, 'periode_id', $periods);
        $allCategories = $request->query('category_id') === 'all';
        $selectedCategoryId = $allCategories
            ? null
            : $this->selectedId($request, 'category_id', $categories);

        $teamPeriods = collect();
        $assessments = collect();
        $pointsByTeamAssessment = collect();

        if ($selectedPeriodId) {
            $teamPeriods = DB::table('tb_qcc_periode_team')
                ->join('tb_qcc_team', 'tb_qcc_team.id', '=', 'tb_qcc_periode_team.id_qcc_team')
                ->where('tb_qcc_periode_team.id_qcc_periode', $selectedPeriodId)
                ->orderBy('tb_qcc_team.group_name')
                ->get([
                    'tb_qcc_periode_team.id',
                    'tb_qcc_team.group_name',
                    'tb_qcc_team.department',
                ]);
        }

        $assessmentsQuery = DB::table('tb_qcc_assesment')
            ->join('tb_asses_category', 'tb_asses_category.id', '=', 'tb_qcc_assesment.id_asses_category')
            ->orderBy('tb_asses_category.asses_category')
            ->orderBy('tb_qcc_assesment.index_assesment')
            ->orderBy('tb_qcc_assesment.id');
        if (!$allCategories && $selectedCategoryId) {
            $assessmentsQuery->where('tb_qcc_assesment.id_asses_category', $selectedCategoryId);
        }
        if ($allCategories || $selectedCategoryId) {
            $assessments = $assessmentsQuery->get([
                'tb_qcc_assesment.id',
                'tb_qcc_assesment.id_asses_category',
                'tb_asses_category.asses_category',
                'tb_qcc_assesment.index_assesment',
                'tb_qcc_assesment.asses_description',
            ]);
        }

        if ($selectedPeriodId && ($selectedCategoryId || $allCategories) && $teamPeriods->isNotEmpty() && $assessments->isNotEmpty()) {
            $assessorAssessmentScores = DB::table('tb_qcc_assesment_team as score')
                ->join('tb_qcc_periode_team as team_period', 'team_period.id', '=', 'score.id_periode_team')
                ->join('tb_qcc_assesor as assessor', 'assessor.id', '=', 'score.id_assesor')
                ->join('tb_qcc_assesment_detail as detail', 'detail.id', '=', 'score.id_assesment_detail')
                ->join('tb_qcc_assesment as assessment', 'assessment.id', '=', 'detail.id_assesment')
                ->where('team_period.id_qcc_periode', $selectedPeriodId)
                ->where('assessor.id_qcc_periode', $selectedPeriodId)
                ->whereColumn('assessor.id_asses_category', 'assessment.id_asses_category')
                ->select(
                    'score.id_periode_team',
                    'assessment.id as id_assesment',
                    'score.id_assesor',
                    DB::raw('SUM(score.point) as assessor_total'),
                )
                ->groupBy('score.id_periode_team', 'assessment.id', 'score.id_assesor');
            if ($selectedCategoryId) {
                $assessorAssessmentScores->where('assessment.id_asses_category', $selectedCategoryId);
            }
            $assessorAssessmentScores = $assessorAssessmentScores->get();

            $pointsByTeamAssessment = $assessorAssessmentScores
                ->groupBy(fn ($score) => $score->id_periode_team . ':' . $score->id_assesment)
                ->map(fn ($scores) => (float) $scores->avg('assessor_total'));
        }

        $summaryRows = $teamPeriods->map(function ($teamPeriod) use ($assessments, $pointsByTeamAssessment) {
            $assessmentPoints = [];
            $totalPoint = 0;
            $hasScores = false;

            foreach ($assessments as $assessment) {
                $key = $teamPeriod->id . ':' . $assessment->id;
                $point = $pointsByTeamAssessment->get($key);
                $assessmentPoints[$assessment->id] = $point;
                if ($point !== null) {
                    $totalPoint += $point;
                    $hasScores = true;
                }
            }

            return (object) [
                'team' => $teamPeriod,
                'assessmentPoints' => $assessmentPoints,
                'totalPoint' => $hasScores ? $totalPoint : null,
            ];
        })->sort(function ($left, $right) {
            if ($left->totalPoint === null || $right->totalPoint === null) {
                if ($left->totalPoint !== $right->totalPoint) {
                    return $left->totalPoint === null ? 1 : -1;
                }
            } else {
                $totalComparison = $right->totalPoint <=> $left->totalPoint;
                if ($totalComparison !== 0) {
                    return $totalComparison;
                }
            }

            return strcasecmp($left->team->group_name, $right->team->group_name);
        })->values();

        $selectedPeriod = $periods->firstWhere('id', $selectedPeriodId);
        $selectedCategory = $categories->firstWhere('id', $selectedCategoryId);
        $menu = 'qcc';

        return view('qcc.assessment_summary', compact(
            'menu',
            'periods',
            'categories',
            'selectedPeriodId',
            'selectedCategoryId',
            'allCategories',
            'selectedPeriod',
            'selectedCategory',
            'assessments',
            'summaryRows',
        ));
    }

    public function save(Request $request)
    {
        $this->authorizeCommittee();
        $data = $request->validate([
            'id_periode_team' => ['required', 'integer', Rule::exists('tb_qcc_periode_team', 'id')],
            'id_assesor' => ['required', 'integer', Rule::exists('tb_qcc_assesor', 'id')],
            'scores' => ['required', 'array', 'min:1'],
            'scores.*' => ['required', 'array'],
            'scores.*.id_assesment_criteria' => ['required', 'integer', Rule::exists('tb_qcc_assesment_criteria', 'id')],
            'scores.*.remark' => ['nullable', 'string'],
        ]);

        $detailIds = array_map('intval', array_keys($data['scores']));
        abort_unless(
            count(array_unique($detailIds)) === count($detailIds)
            && collect($detailIds)->every(fn ($id) => $id > 0),
            422,
            'Detail assessment tidak valid.',
        );

        $teamPeriod = DB::table('tb_qcc_periode_team')
            ->where('id', $data['id_periode_team'])
            ->first(['id_qcc_periode']);
        abort_unless($teamPeriod, 404);

        $details = DB::table('tb_qcc_assesment_detail')
            ->join('tb_qcc_assesment', 'tb_qcc_assesment.id', '=', 'tb_qcc_assesment_detail.id_assesment')
            ->whereIn('tb_qcc_assesment_detail.id', $detailIds)
            ->get([
                'tb_qcc_assesment_detail.id',
                'tb_qcc_assesment.id_asses_category',
            ])
            ->keyBy('id');
        abort_unless($details->count() === count($detailIds), 422, 'Detail assessment tidak sesuai.');

        $categoryIds = $details->pluck('id_asses_category')->unique();
        abort_unless($categoryIds->count() === 1, 422, 'Semua detail harus berasal dari kategori assessment yang sama.');
        $categoryId = (int) $categoryIds->first();

        $expectedDetailIds = DB::table('tb_qcc_assesment_detail')
            ->join('tb_qcc_assesment', 'tb_qcc_assesment.id', '=', 'tb_qcc_assesment_detail.id_assesment')
            ->join('tb_qcc_assesment_criteria', 'tb_qcc_assesment_criteria.id_assesment_detail', '=', 'tb_qcc_assesment_detail.id')
            ->where('tb_qcc_assesment.id_asses_category', $categoryId)
            ->distinct()
            ->pluck('tb_qcc_assesment_detail.id')
            ->map(fn ($id) => (int) $id)
            ->sort()
            ->values();
        $submittedDetailIds = collect($detailIds)->sort()->values();
        abort_unless(
            $expectedDetailIds->all() === $submittedDetailIds->all(),
            422,
            'Semua detail yang memiliki criteria harus dinilai.',
        );

        $assessor = DB::table('tb_qcc_assesor')
            ->where('id', $data['id_assesor'])
            ->first(['id_qcc_periode', 'id_asses_category']);
        abort_unless(
            $assessor
            && (int) $assessor->id_qcc_periode === (int) $teamPeriod->id_qcc_periode
            && (int) $assessor->id_asses_category === $categoryId,
            422,
            'Assessor harus terdaftar untuk periode team dan kategori assessment yang dipilih.',
        );

        $criteriaIds = collect($data['scores'])
            ->pluck('id_assesment_criteria')
            ->map(fn ($id) => (int) $id)
            ->unique()
            ->all();
        $criteriaById = DB::table('tb_qcc_assesment_criteria')
            ->join('tb_qcc_assesment_detail', 'tb_qcc_assesment_detail.id', '=', 'tb_qcc_assesment_criteria.id_assesment_detail')
            ->join('tb_qcc_assesment', 'tb_qcc_assesment.id', '=', 'tb_qcc_assesment_detail.id_assesment')
            ->whereIn('tb_qcc_assesment_criteria.id', $criteriaIds)
            ->get([
                'tb_qcc_assesment_criteria.id',
                'tb_qcc_assesment_criteria.id_assesment_detail',
                'tb_qcc_assesment_criteria.point',
                'tb_qcc_assesment.id_asses_category',
            ])
            ->keyBy('id');

        $now = now();
        DB::transaction(function () use ($data, $details, $criteriaById, $teamPeriod, $categoryId, $now) {
            foreach ($data['scores'] as $detailId => $scoreData) {
                $detailId = (int) $detailId;
                $criteria = $criteriaById->get((int) $scoreData['id_assesment_criteria']);
                abort_unless(
                    $criteria
                    && (int) $criteria->id_assesment_detail === $detailId
                    && (int) $criteria->id_asses_category === $categoryId
                    && $details->has($detailId),
                    422,
                    'Criteria tidak sesuai dengan detail assessment.',
                );
                abort_unless($criteria->point !== null, 422, 'Point criteria belum diisi.');

                $values = [
                    'id_periode_team' => $data['id_periode_team'],
                    'id_assesor' => $data['id_assesor'],
                    'id_assesment_detail' => $detailId,
                    'id_assesment_criteria' => $scoreData['id_assesment_criteria'],
                    'point' => $criteria->point,
                    'remark' => $scoreData['remark'] ?? null,
                    'updated_at' => $now,
                    'updated_by' => Auth::user()->name,
                ];

                $score = DB::table('tb_qcc_assesment_team')
                    ->where('id_periode_team', $data['id_periode_team'])
                    ->where('id_assesor', $data['id_assesor'])
                    ->where('id_assesment_detail', $detailId)
                    ->first();

                if ($score) {
                    DB::table('tb_qcc_assesment_team')->where('id', $score->id)->update($values);
                } else {
                    DB::table('tb_qcc_assesment_team')->insert([
                        ...$values,
                        'created_at' => $now,
                    ]);
                }
            }
        });

        return redirect($this->scoringUrl(
            (int) $teamPeriod->id_qcc_periode,
            $categoryId,
            (int) $data['id_periode_team'],
            (int) $data['id_assesor'],
        ))->with('success', 'Penilaian berhasil disimpan untuk ' . count($data['scores']) . ' detail.');
    }

    public function destroy(int $id)
    {
        $this->authorizeCommittee();
        $score = DB::table('tb_qcc_assesment_team')
            ->join('tb_qcc_periode_team', 'tb_qcc_periode_team.id', '=', 'tb_qcc_assesment_team.id_periode_team')
            ->join('tb_qcc_assesor', 'tb_qcc_assesor.id', '=', 'tb_qcc_assesment_team.id_assesor')
            ->where('tb_qcc_assesment_team.id', $id)
            ->first([
                'tb_qcc_assesment_team.id',
                'tb_qcc_periode_team.id_qcc_periode',
                'tb_qcc_assesor.id_asses_category',
                'tb_qcc_assesment_team.id_periode_team',
                'tb_qcc_assesment_team.id_assesor',
            ]);
        abort_unless($score, 404);

        DB::table('tb_qcc_assesment_team')->where('id', $id)->delete();

        return redirect($this->scoringUrl(
            (int) $score->id_qcc_periode,
            (int) $score->id_asses_category,
            (int) $score->id_periode_team,
            (int) $score->id_assesor,
        ))->with('success', 'Nilai assessment berhasil dihapus.');
    }

    private function selectedId(Request $request, string $key, $items): ?int
    {
        $queryId = $request->query($key);
        if ($request->filled($key)) {
            abort_unless($items->contains(fn ($item) => (string) $item->id === (string) $queryId), 404);

            return (int) $queryId;
        }

        return $items->first()?->id;
    }

    private function authorizeCommittee(): void
    {
        abort_unless(Auth::user()?->hasRole('committee'), 403, 'Anda tidak punya akses.');
    }

    private function scoringUrl(int $periodId, int $categoryId, int $teamPeriodId, int $assessorId): string
    {
        return '/qcc_assesment_team?periode_id=' . $periodId
            . '&category_id=' . $categoryId
            . '&team_period_id=' . $teamPeriodId
            . '&assesor_id=' . $assessorId;
    }
}

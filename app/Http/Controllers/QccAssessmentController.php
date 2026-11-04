<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;

class QccAssessmentController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index(Request $request)
    {
        $this->authorizeImprovement();

        $categories = DB::table('tb_asses_category')
            ->orderBy('asses_category')
            ->get();
        $selectedCategoryId = $request->query('category_id');
        if ($selectedCategoryId !== null) {
            abort_unless($categories->contains(fn ($category) => (string) $category->id === (string) $selectedCategoryId), 404);
            $selectedCategoryId = (int) $selectedCategoryId;
        }

        $assessmentsQuery = DB::table('tb_qcc_assesment')
            ->orderBy('id_asses_category')
            ->orderBy('index_assesment')
            ->orderBy('id');
        if ($selectedCategoryId !== null) {
            $assessmentsQuery->where('id_asses_category', $selectedCategoryId);
        }
        $assessments = $assessmentsQuery->get();

        $details = collect();
        $criteria = collect();
        if ($assessments->isNotEmpty()) {
            $details = DB::table('tb_qcc_assesment_detail')
                ->whereIn('id_assesment', $assessments->pluck('id'))
                ->orderBy('id')
                ->get();

            if ($details->isNotEmpty()) {
                $criteria = DB::table('tb_qcc_assesment_criteria')
                    ->whereIn('id_assesment_detail', $details->pluck('id'))
                    ->orderBy('id')
                    ->get();
            }
        }

        $assessmentsByCategory = $assessments->groupBy('id_asses_category');
        $detailsByAssessment = $details->groupBy('id_assesment');
        $criteriaByDetail = $criteria->groupBy('id_assesment_detail');
        $menu = 'qcc';

        return view('qcc.assessment', compact(
            'menu',
            'categories',
            'selectedCategoryId',
            'assessmentsByCategory',
            'detailsByAssessment',
            'criteriaByDetail',
        ));
    }

    public function storeCategory(Request $request)
    {
        $this->authorizeImprovement();
        $data = $request->validate([
            'asses_category' => ['required', 'string', 'max:50', Rule::unique('tb_asses_category', 'asses_category')],
        ]);

        $id = DB::table('tb_asses_category')->insertGetId($data);

        return redirect($this->assessmentUrl($id))->with('success', 'Kategori berhasil ditambahkan.');
    }

    public function updateCategory(Request $request, int $id)
    {
        $this->authorizeImprovement();
        abort_unless(DB::table('tb_asses_category')->where('id', $id)->exists(), 404);
        $data = $request->validate([
            'asses_category' => ['required', 'string', 'max:50', Rule::unique('tb_asses_category', 'asses_category')->ignore($id)],
        ]);

        DB::table('tb_asses_category')->where('id', $id)->update($data);

        return redirect($this->assessmentUrl($id))->with('success', 'Kategori berhasil diperbarui.');
    }

    public function destroyCategory(int $id)
    {
        $this->authorizeImprovement();
        abort_unless(DB::table('tb_asses_category')->where('id', $id)->exists(), 404);

        if (DB::table('tb_qcc_assesment')->where('id_asses_category', $id)->exists()) {
            return redirect($this->assessmentUrl($id))
                ->with('error', 'Kategori masih memiliki assessment dan tidak dapat dihapus.');
        }

        DB::table('tb_asses_category')->where('id', $id)->delete();

        return redirect('/qcc_assesment')->with('success', 'Kategori berhasil dihapus.');
    }

    public function storeAssessment(Request $request)
    {
        $this->authorizeImprovement();
        $data = $request->validate([
            'id_asses_category' => ['required', 'integer', Rule::exists('tb_asses_category', 'id')],
            'index_assesment' => ['required', 'string', 'size:1', 'regex:/^[A-Z]$/'],
            'asses_description' => ['required', 'string'],
            'bobot' => ['required', 'numeric', 'min:0'],
        ]);
        $now = now();

        DB::table('tb_qcc_assesment')->insert([
            ...$data,
            'created_at' => $now,
            'updated_at' => $now,
            'updated_by' => Auth::user()->name,
        ]);

        return redirect($this->assessmentUrl($data['id_asses_category']))->with('success', 'Assessment berhasil ditambahkan.');
    }

    public function updateAssessment(Request $request, int $id)
    {
        $this->authorizeImprovement();
        $assessment = DB::table('tb_qcc_assesment')->where('id', $id)->first();
        abort_unless($assessment, 404);
        $data = $request->validate([
            'id_asses_category' => ['required', 'integer', Rule::exists('tb_asses_category', 'id')],
            'index_assesment' => ['required', 'string', 'size:1', 'regex:/^[A-Z]$/'],
            'asses_description' => ['required', 'string'],
            'bobot' => ['required', 'numeric', 'min:0'],
        ]);

        if (
            (int) $assessment->id_asses_category !== (int) $data['id_asses_category']
            && DB::table('tb_qcc_assesment_team')
                ->join('tb_qcc_assesment_criteria', 'tb_qcc_assesment_criteria.id', '=', 'tb_qcc_assesment_team.id_assesment_criteria')
                ->join('tb_qcc_assesment_detail', 'tb_qcc_assesment_detail.id', '=', 'tb_qcc_assesment_criteria.id_assesment_detail')
                ->where('tb_qcc_assesment_detail.id_assesment', $id)
                ->exists()
        ) {
            return redirect($this->assessmentUrl($assessment->id_asses_category))
                ->with('error', 'Kategori assessment yang sudah dinilai tidak dapat diubah.');
        }

        DB::table('tb_qcc_assesment')->where('id', $id)->update([
            ...$data,
            'updated_at' => now(),
            'updated_by' => Auth::user()->name,
        ]);

        return redirect($this->assessmentUrl($data['id_asses_category']))->with('success', 'Assessment berhasil diperbarui.');
    }

    public function destroyAssessment(int $id)
    {
        $this->authorizeImprovement();
        $assessment = DB::table('tb_qcc_assesment')->where('id', $id)->first();
        abort_unless($assessment, 404);

        if (DB::table('tb_qcc_assesment_detail')->where('id_assesment', $id)->exists()) {
            return redirect($this->assessmentUrl($assessment->id_asses_category))
                ->with('error', 'Assessment masih memiliki detail dan tidak dapat dihapus.');
        }

        DB::table('tb_qcc_assesment')->where('id', $id)->delete();

        return redirect($this->assessmentUrl($assessment->id_asses_category))->with('success', 'Assessment berhasil dihapus.');
    }

    public function storeDetail(Request $request)
    {
        $this->authorizeImprovement();
        $data = $request->validate([
            'id_assesment' => ['required', 'integer', Rule::exists('tb_qcc_assesment', 'id')],
            'asses_description_detail' => ['required', 'string'],
            'bobot' => ['required', 'numeric', 'min:0'],
        ]);
        $now = now();

        DB::table('tb_qcc_assesment_detail')->insert([
            ...$data,
            'cretaed_at' => $now,
            'updated_at' => $now,
            'updated_by' => Auth::user()->name,
        ]);

        return redirect($this->assessmentUrlForAssessment($data['id_assesment']))
            ->with('success', 'Detail assessment berhasil ditambahkan.');
    }

    public function updateDetail(Request $request, int $id)
    {
        $this->authorizeImprovement();
        $detail = DB::table('tb_qcc_assesment_detail')->where('id', $id)->first();
        abort_unless($detail, 404);
        $data = $request->validate([
            'id_assesment' => ['required', 'integer', Rule::exists('tb_qcc_assesment', 'id')],
            'asses_description_detail' => ['required', 'string'],
            'bobot' => ['required', 'numeric', 'min:0'],
        ]);

        if (
            (int) $detail->id_assesment !== (int) $data['id_assesment']
            && DB::table('tb_qcc_assesment_team')
                ->join('tb_qcc_assesment_criteria', 'tb_qcc_assesment_criteria.id', '=', 'tb_qcc_assesment_team.id_assesment_criteria')
                ->where('tb_qcc_assesment_criteria.id_assesment_detail', $id)
                ->exists()
        ) {
            return redirect($this->assessmentUrlForAssessment((int) $detail->id_assesment))
                ->with('error', 'Detail yang sudah dinilai tidak dapat dipindahkan ke assessment lain.');
        }

        DB::table('tb_qcc_assesment_detail')->where('id', $id)->update([
            ...$data,
            'updated_at' => now(),
            'updated_by' => Auth::user()->name,
        ]);

        return redirect($this->assessmentUrlForAssessment($data['id_assesment']))
            ->with('success', 'Detail assessment berhasil diperbarui.');
    }

    public function destroyDetail(int $id)
    {
        $this->authorizeImprovement();
        $detail = DB::table('tb_qcc_assesment_detail')->where('id', $id)->first();
        abort_unless($detail, 404);
        $categoryId = $this->categoryIdForAssessment((int) $detail->id_assesment);

        if (DB::table('tb_qcc_assesment_criteria')->where('id_assesment_detail', $id)->exists()) {
            return redirect($this->assessmentUrl($categoryId))
                ->with('error', 'Detail masih memiliki criteria dan tidak dapat dihapus.');
        }

        DB::table('tb_qcc_assesment_detail')->where('id', $id)->delete();

        return redirect($this->assessmentUrl($categoryId))->with('success', 'Detail assessment berhasil dihapus.');
    }

    public function storeCriteria(Request $request)
    {
        $this->authorizeImprovement();
        $data = $request->validate([
            'id_assesment_detail' => ['required', 'integer', Rule::exists('tb_qcc_assesment_detail', 'id')],
            'criteria_description' => ['required', 'string'],
            'point' => ['required', 'numeric', 'min:0'],
        ]);
        $now = now();

        DB::table('tb_qcc_assesment_criteria')->insert([
            ...$data,
            'created_at' => $now,
            'updated_at' => $now,
            'updated_by' => Auth::user()->name,
        ]);

        return redirect($this->assessmentUrlForDetail($data['id_assesment_detail']))
            ->with('success', 'Criteria berhasil ditambahkan.');
    }

    public function updateCriteria(Request $request, int $id)
    {
        $this->authorizeImprovement();
        $criteria = DB::table('tb_qcc_assesment_criteria')->where('id', $id)->first();
        abort_unless($criteria, 404);
        $data = $request->validate([
            'id_assesment_detail' => ['required', 'integer', Rule::exists('tb_qcc_assesment_detail', 'id')],
            'criteria_description' => ['required', 'string'],
            'point' => ['required', 'numeric', 'min:0'],
        ]);

        if (
            (float) $criteria->point !== (float) $data['point']
            && DB::table('tb_qcc_assesment_team')->where('id_assesment_criteria', $id)->exists()
        ) {
            return redirect($this->assessmentUrlForDetail((int) $criteria->id_assesment_detail))
                ->with('error', 'Point criteria yang sudah digunakan pada penilaian tidak dapat diubah.');
        }

        DB::table('tb_qcc_assesment_criteria')->where('id', $id)->update([
            ...$data,
            'updated_at' => now(),
            'updated_by' => Auth::user()->name,
        ]);

        return redirect($this->assessmentUrlForDetail($data['id_assesment_detail']))
            ->with('success', 'Criteria berhasil diperbarui.');
    }

    public function destroyCriteria(int $id)
    {
        $this->authorizeImprovement();
        $criteria = DB::table('tb_qcc_assesment_criteria')->where('id', $id)->first();
        abort_unless($criteria, 404);
        $categoryId = $this->categoryIdForDetail((int) $criteria->id_assesment_detail);

        if (DB::table('tb_qcc_assesment_team')->where('id_assesment_criteria', $id)->exists()) {
            return redirect($this->assessmentUrl($categoryId))
                ->with('error', 'Criteria yang sudah digunakan pada penilaian tidak dapat dihapus.');
        }

        DB::table('tb_qcc_assesment_criteria')->where('id', $id)->delete();

        return redirect($this->assessmentUrl($categoryId))->with('success', 'Criteria berhasil dihapus.');
    }

    private function authorizeImprovement(): void
    {
        abort_unless(Auth::user()?->hasRole('improvement'), 403, 'Anda tidak punya akses.');
    }

    private function categoryIdForAssessment(int $assessmentId): int
    {
        $categoryId = DB::table('tb_qcc_assesment')
            ->where('id', $assessmentId)
            ->value('id_asses_category');
        abort_unless($categoryId, 404);

        return (int) $categoryId;
    }

    private function categoryIdForDetail(int $detailId): int
    {
        $assessmentId = DB::table('tb_qcc_assesment_detail')
            ->where('id', $detailId)
            ->value('id_assesment');
        abort_unless($assessmentId, 404);

        return $this->categoryIdForAssessment((int) $assessmentId);
    }

    private function assessmentUrlForAssessment(int $assessmentId): string
    {
        return $this->assessmentUrl($this->categoryIdForAssessment($assessmentId));
    }

    private function assessmentUrlForDetail(int $detailId): string
    {
        return $this->assessmentUrl($this->categoryIdForDetail($detailId));
    }

    private function assessmentUrl(int $categoryId): string
    {
        return '/qcc_assesment?category_id=' . $categoryId;
    }
}

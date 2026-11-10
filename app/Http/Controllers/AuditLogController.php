<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class AuditLogController extends Controller
{
    private const SUBJECT_TYPES = [
        'tb_employee_detail',
        'tb_employee_family',
        'tb_addresses',
        'tb_domiciles',
        'tb_address_darurat',
        'tb_educations',
        'tb_salary_contract',
    ];

    public function __construct()
    {
        $this->middleware(['auth', 'verified']);
    }

    public function index(Request $request)
    {
        abort_unless($request->user()->hasRole('root'), 403, 'Anda tidak punya akses');

        $filters = $request->validate([
            'name' => ['nullable', 'string', 'max:50'],
            'event' => ['nullable', 'in:created,updated,deleted'],
            'subject_type' => ['nullable', 'in:'.implode(',', self::SUBJECT_TYPES)],
            'subject_id' => ['nullable', 'integer', 'min:1'],
            'description' => ['nullable', 'string', 'max:255'],
            'ip_address' => ['nullable', 'string', 'max:50'],
            'date_from' => ['nullable', 'date_format:Y-m-d'],
            'date_to' => ['nullable', 'date_format:Y-m-d'],
        ]);
        $defaultDateRange = empty($filters['date_from']) && empty($filters['date_to']);
        if ($defaultDateRange) {
            $filters['date_from'] = now()->subMonth()->toDateString();
            $filters['date_to'] = now()->toDateString();
        }

        if (!empty($filters['date_from']) && !empty($filters['date_to'])
            && $filters['date_to'] < $filters['date_from']) {
            throw \Illuminate\Validation\ValidationException::withMessages([
                'date_to' => 'Tanggal akhir tidak boleh sebelum tanggal awal.',
            ]);
        }

        $query = DB::table('audit_logs');

        if (!empty($filters['name'])) {
            $query->where('name', 'like', '%'.$filters['name'].'%');
        }
        if (!empty($filters['event'])) {
            $query->where('event', $filters['event']);
        }
        if (!empty($filters['subject_type'])) {
            $query->where('subject_type', $filters['subject_type']);
        }
        if (!empty($filters['subject_id'])) {
            $query->where('subject_id', $filters['subject_id']);
        }
        if (!empty($filters['description'])) {
            $query->where('description', 'like', '%'.$filters['description'].'%');
        }
        if (!empty($filters['ip_address'])) {
            $query->where('ip_address', 'like', '%'.$filters['ip_address'].'%');
        }
        if (!empty($filters['date_from'])) {
            $query->whereDate('created-at', '>=', $filters['date_from']);
        }
        if (!empty($filters['date_to'])) {
            $query->whereDate('created-at', '<=', $filters['date_to']);
        }

        $logs = $query
            ->orderBy('created-at', 'desc')
            ->orderBy('id', 'desc')
            ->paginate(50)
            ->withQueryString();

        return view('page.admin.audit_logs', [
            'logs' => $logs,
            'filters' => $filters,
            'defaultDateRange' => $defaultDateRange,
            'subjectTypes' => self::SUBJECT_TYPES,
            'menu' => 'audit_logs',
            'juduls' => 'Audit Log',
        ]);
    }
}

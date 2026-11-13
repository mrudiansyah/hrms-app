<?php

namespace App\Http\Controllers;

use App\Mail\CandidateEmailVerification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Symfony\Component\Mailer\Exception\TransportExceptionInterface;

class CandidateOnboardingController extends Controller
{
    private const EDUCATION_LEVELS = ['SD', 'SMP', 'SMA', 'SMK', 'D1', 'D2', 'D3', 'S1', 'S2', 'S3'];
    private const FAMILY_RELATIONSHIPS = ['Istri', 'Suami', 'Anak ke-1', 'Anak ke-2', 'Anak ke-3', 'Anak ke-4'];

    public function pins(Request $request)
    {
        $this->ensureHrAccess($request);

        $pins = DB::table('tb_cand_pin')
            ->orderByDesc('created_at')
            ->limit(1000)
            ->get();

        return view('page.candidate.pins', compact('pins'));
    }

    public function generatePin(Request $request)
    {
        $this->ensureHrAccess($request);
        $skipEmailVerification = $request->boolean('skip_email_verification');

        $pin = null;
        for ($attempt = 0; $attempt < 50; $attempt++) {
            $candidatePin = (string) random_int(1000, 9999);
            if (! DB::table('tb_cand_pin')->where('pin_access', $candidatePin)->exists()) {
                $pin = $candidatePin;
                break;
            }
        }

        if ($pin === null) {
            return back()->withErrors(['pin' => 'PIN unik tidak dapat dibuat. Coba kembali.']);
        }

        DB::table('tb_cand_pin')->insert([
            'pin_access' => $pin,
            'created_by' => $request->user()->getAuthIdentifier(),
            'created_at' => now(),
            'expired_at' => now()->addHour(),
            'skip_email_verification' => $skipEmailVerification,
        ]);

        $message = "PIN {$pin} berhasil dibuat dan berlaku selama 1 jam.";
        if ($skipEmailVerification) {
            $message .= ' Verifikasi email dilewati untuk PIN ini.';
        }

        return redirect()->route('candidate.pins')->with('success', $message);
    }

    public function expirePin(Request $request, $id)
    {
        $this->ensureHrAccess($request);

        $updated = DB::table('tb_cand_pin')
            ->where('id', $id)
            ->update(['expired_at' => now()]);

        abort_unless($updated, 404);

        return redirect()->route('candidate.pins')->with('success', 'PIN berhasil dinonaktifkan.');
    }

    public function showAccessForm()
    {
        return view('page.candidate.access');
    }

    public function regionOptions(Request $request, string $level)
    {
        $levels = [
            'regency' => ['parent_length' => 2, 'child_length' => 5],
            'district' => ['parent_length' => 5, 'child_length' => 8],
            'village' => ['parent_length' => 8, 'child_length' => 13],
        ];
        abort_unless(isset($levels[$level]), 404);

        $data = $request->validate([
            'id' => ['required', 'string', 'regex:/^\d{2}(?:\.\d{2}){0,2}$/'],
        ]);
        $parentCode = $data['id'];
        $levelConfig = $levels[$level];

        if (strlen($parentCode) !== $levelConfig['parent_length']) {
            return response()->json(['message' => 'Kode wilayah induk tidak valid.'], 422);
        }

        $regions = DB::table('wilayah_2020')
            ->where('kode', 'like', $parentCode.'.%')
            ->orderBy('nama')
            ->get(['kode', 'nama'])
            ->filter(function ($region) use ($parentCode, $levelConfig) {
                return strlen($region->kode) === $levelConfig['child_length']
                    && substr($region->kode, 0, strlen($parentCode) + 1) === $parentCode.'.';
            })
            ->map(fn ($region) => ['code' => $region->kode, 'name' => $region->nama])
            ->values();

        return response()->json($regions);
    }

    public function sendVerification(Request $request)
    {
        $data = $request->validate([
            'pin_access' => ['required', 'digits:4'],
            'email_address' => ['required', 'email', 'max:50'],
            'employee_name' => ['required', 'string', 'max:50'],
            'gender' => ['required', 'in:Laki-laki,Perempuan'],
        ]);

        $email = mb_strtolower($data['email_address']);
        $activePin = DB::table('tb_cand_pin')
            ->where('pin_access', $data['pin_access'])
            ->where('expired_at', '>', now())
            ->first();

        if (! $activePin) {
            return back()->withInput($request->except('pin_access'))->withErrors([
                'pin_access' => 'PIN tidak valid atau sudah kedaluwarsa.',
            ]);
        }

        if (! $this->emailIsAvailable($email)) {
            return back()->withInput($request->except('pin_access'))->withErrors([
                'email_address' => 'Email sudah terdaftar dan tidak dapat digunakan kembali.',
            ]);
        }

        $request->session()->forget([
            'candidate_employee_id',
            'candidate_email_verification_skipped_for',
        ]);

        if ($activePin->skip_email_verification) {
            $candidateId = DB::transaction(function () use ($email, $data) {
                if (! $this->emailIsAvailable($email)) {
                    return null;
                }

                return DB::table('tb_cand_employees')->insertGetId([
                    'email_address' => $email,
                    'employee_name' => $data['employee_name'],
                    'gender' => $data['gender'],
                    'updated_by' => $data['employee_name'],
                    'created_at' => now(),
                    'updated_at' => now(),
                    'verified_at' => null,
                ]);
            });

            if ($candidateId === null) {
                return back()->withInput($request->except('pin_access'))->withErrors([
                    'email_address' => 'Email sudah terdaftar dan tidak dapat digunakan kembali.',
                ]);
            }

            $request->session()->put('candidate_employee_id', $candidateId);
            $request->session()->put('candidate_email_verification_skipped_for', $candidateId);

            return redirect()->route('candidate.profile')->with(
                'success',
                'Verifikasi email dilewati oleh HR. Anda dapat langsung melengkapi data kandidat.'
            );
        }

        $token = Str::random(64);
        $cacheKey = 'candidate-email-verification:'.$token;
        Cache::put($cacheKey, [
            'email_address' => $email,
            'employee_name' => $data['employee_name'],
            'gender' => $data['gender'],
        ], now()->addHour());

        $verificationUrl = URL::temporarySignedRoute(
            'candidate.email.verify',
            now()->addHour(),
            ['token' => $token]
        );

        try {
            Mail::to($email)->send(new CandidateEmailVerification(
                $data['employee_name'],
                $verificationUrl
            ));
        } catch (TransportExceptionInterface $exception) {
            Cache::forget($cacheKey);
            Log::error('Candidate verification email could not be sent.', [
                'exception_class' => get_class($exception),
            ]);

            return back()->withInput($request->except('pin_access'))->withErrors([
                'email_address' => 'Email verifikasi gagal dikirim. Periksa konfigurasi SMTP lalu coba lagi.',
            ]);
        }

        return redirect()->route('candidate.access')->with(
            'success',
            'Link verifikasi telah dikirim. Silakan buka email Anda dalam 1 jam untuk melanjutkan.'
        );
    }

    public function verifyEmail(Request $request, string $token)
    {
        $cacheKey = 'candidate-email-verification:'.$token;
        $pending = Cache::get($cacheKey);
        if (! is_array($pending)) {
            return redirect()->route('candidate.access')->withErrors([
                'email_address' => 'Link verifikasi tidak valid atau sudah kedaluwarsa. Silakan mulai kembali.',
            ]);
        }

        if (isset($pending['candidate_id'])) {
            $candidateId = $pending['candidate_id'];
            $candidateExists = DB::table('tb_cand_employees')
                ->where('id', $candidateId)
                ->whereNotNull('verified_at')
                ->exists();

            if ($candidateExists) {
                $request->session()->put('candidate_employee_id', $candidateId);
                $request->session()->forget('candidate_email_verification_skipped_for');

                return redirect()->route('candidate.profile');
            }
        }

        $candidateId = DB::transaction(function () use ($pending) {
            if (! $this->emailIsAvailable($pending['email_address'])) {
                return null;
            }

            return DB::table('tb_cand_employees')->insertGetId([
                'email_address' => $pending['email_address'],
                'employee_name' => $pending['employee_name'],
                'gender' => $pending['gender'],
                'updated_by' => $pending['employee_name'],
                'created_at' => now(),
                'updated_at' => now(),
                'verified_at' => now(),
            ]);
        });

        if ($candidateId === null) {
            Cache::forget($cacheKey);

            return redirect()->route('candidate.access')->withErrors([
                'email_address' => 'Email sudah terdaftar dan tidak dapat digunakan kembali.',
            ]);
        }

        Cache::put($cacheKey, ['candidate_id' => $candidateId], now()->addHour());
        $request->session()->put('candidate_employee_id', $candidateId);
        $request->session()->forget('candidate_email_verification_skipped_for');

        return redirect()->route('candidate.profile');
    }

    public function showProfileForm(Request $request)
    {
        $candidate = $this->verifiedCandidate($request);
        if (! $candidate) {
            return redirect()->route('candidate.access')->withErrors([
                'email_address' => 'Verifikasi email diperlukan sebelum mengisi data kandidat.',
            ]);
        }

        if (DB::table('tb_cand_employee_detail')->where('id_cand_employee', $candidate->id)->exists()) {
            return redirect()->route('candidate.complete');
        }

        $religions = DB::table('tb_religion')->orderBy('religion')->pluck('religion');
        $taxStatuses = DB::table('tb_pph21_status')->orderBy('status_pajak')->pluck('status_pajak');

        $provinces = DB::table('wilayah_2020')
            ->where('kode', 'not like', '%.%')
            ->orderBy('nama')
            ->get(['kode', 'nama']);

        return view('page.candidate.profile', [
            'candidate' => $candidate,
            'religions' => $religions,
            'taxStatuses' => $taxStatuses,
            'educationLevels' => self::EDUCATION_LEVELS,
            'familyRelationships' => self::FAMILY_RELATIONSHIPS,
            'provinces' => $provinces,
        ]);
    }

    public function saveProfile(Request $request)
    {
        $candidate = $this->verifiedCandidate($request);
        if (! $candidate) {
            return redirect()->route('candidate.access')->withErrors([
                'email_address' => 'Verifikasi email diperlukan sebelum mengisi data kandidat.',
            ]);
        }

        if (DB::table('tb_cand_employee_detail')->where('id_cand_employee', $candidate->id)->exists()) {
            return redirect()->route('candidate.complete');
        }

        $religions = DB::table('tb_religion')->pluck('religion')->all();
        $taxStatuses = DB::table('tb_pph21_status')->pluck('status_pajak')->all();
        $educationLevels = self::EDUCATION_LEVELS;
        $familyRelationships = self::FAMILY_RELATIONSHIPS;

        $data = $request->validate([
            'tempat_lahir' => ['required', 'string', 'max:50'],
            'tanggal_lahir' => ['required', 'date', 'before_or_equal:today'],
            'agama' => ['required', Rule::in($religions)],
            'golongan_darah' => ['required', 'in:A,B,AB,O'],
            'ibu_kandung' => ['required', 'string', 'max:50'],
            'nomor_ktp' => ['required', 'digits:16'],
            'nomor_kk' => ['required', 'digits:16'],
            'nomor_npwp' => ['nullable', 'regex:/^\d{1,19}$/'],
            'kode_status' => ['required', Rule::in($taxStatuses)],
            'nomor_bpjs_kes' => ['nullable', 'regex:/^\d{1,19}$/'],
            'nomor_bpjs_ket' => ['nullable', 'regex:/^\d{1,19}$/'],
            'nomor_rekening' => ['nullable', 'regex:/^\d{1,19}$/'],
            'nama_bank' => ['nullable', 'string', 'max:50'],
            'nomor_telepon' => ['required', 'string', 'max:30'],
            'addresses' => ['required', 'array'],
            'addresses.ktp' => ['required', 'array'],
            'addresses.domicile' => ['required', 'array'],
            'addresses.*.provinsi' => ['required', 'string', 'max:13'],
            'addresses.*.kabupaten' => ['required', 'string', 'max:13'],
            'addresses.*.kecamatan' => ['required', 'string', 'max:13'],
            'addresses.*.kelurahan' => ['required', 'string', 'max:13'],
            'addresses.*.detail' => ['required', 'string', 'max:4000'],
            'addresses.*.map_address' => ['nullable', 'string', 'max:600'],
            'educations' => ['required', 'array', 'min:1'],
            'educations.*.level_education' => ['required', Rule::in($educationLevels)],
            'educations.*.institute' => ['required', 'string', 'max:100'],
            'educations.*.prodi' => ['required', 'string', 'max:50'],
            'educations.*.year' => ['required', 'integer', 'min:1900', 'max:'.now()->year],
            'educations.*.graduate_year' => ['nullable', 'integer', 'min:1900', 'max:'.(now()->year + 10)],
            'educations.*.remark' => ['nullable', 'string', 'max:150'],
            'educations.*.is_top' => ['sometimes', 'boolean'],
            'emergency_contacts' => ['required', 'array', 'min:1'],
            'emergency_contacts.*.nama_keluarga' => ['required', 'string', 'max:50'],
            'emergency_contacts.*.hubungan' => ['required', 'string', 'max:50'],
            'emergency_contacts.*.nomor_kontak' => ['required', 'string', 'max:50'],
            'emergency_contacts.*.provinsi_kontak' => ['required', 'string', 'max:13'],
            'emergency_contacts.*.kabupaten_kontak' => ['required', 'string', 'max:13'],
            'emergency_contacts.*.kecamatan_kontak' => ['required', 'string', 'max:13'],
            'emergency_contacts.*.kelurahan_kontak' => ['required', 'string', 'max:13'],
            'emergency_contacts.*.detail_kontak' => ['required', 'string', 'max:4000'],
            'families' => ['sometimes', 'array'],
            'families.*.nama_keluarga' => ['required', 'string', 'max:50'],
            'families.*.hubungan' => ['required', Rule::in($familyRelationships)],
            'families.*.tanggal_lahir' => ['required', 'date', 'before_or_equal:today'],
        ]);

        $topEducation = collect($data['educations'])
            ->filter(fn ($education) => ! empty($education['is_top']))
            ->values();

        if ($topEducation->count() !== 1) {
            return back()->withInput()->withErrors([
                'educations' => 'Pilih tepat satu pendidikan sebagai pendidikan tertinggi.',
            ]);
        }

        $resolvedAddresses = [];
        foreach ($data['addresses'] as $addressKey => $address) {
            $resolvedAddresses[$addressKey] = $this->resolveCandidateWilayah(
                $address,
                ['province' => 'provinsi', 'regency' => 'kabupaten', 'district' => 'kecamatan', 'village' => 'kelurahan'],
                'addresses.'.$addressKey
            );
        }

        $resolvedEmergencyAddresses = [];
        foreach ($data['emergency_contacts'] as $index => $contact) {
            $resolvedEmergencyAddresses[$index] = $this->resolveCandidateWilayah(
                $contact,
                [
                    'province' => 'provinsi_kontak',
                    'regency' => 'kabupaten_kontak',
                    'district' => 'kecamatan_kontak',
                    'village' => 'kelurahan_kontak',
                ],
                'emergency_contacts.'.$index
            );
        }

        $topEducation = $topEducation->first();
        $now = now();

        DB::transaction(function () use ($candidate, $data, $topEducation, $resolvedAddresses, $resolvedEmergencyAddresses, $now) {
            DB::table('tb_cand_employee_detail')->insert([
                'id_cand_employee' => $candidate->id,
                'tempat_lahir' => $data['tempat_lahir'],
                'tanggal_lahir' => $data['tanggal_lahir'],
                'agama' => $data['agama'],
                'golongan_darah' => $data['golongan_darah'],
                'ibu_kandung' => $data['ibu_kandung'],
                'nomor_ktp' => $data['nomor_ktp'],
                'nomor_kk' => $data['nomor_kk'],
                'nomor_npwp' => $data['nomor_npwp'] ?? null,
                'kode_status' => $data['kode_status'],
                'nomor_bpjs_kes' => $data['nomor_bpjs_kes'] ?? null,
                'nomor_bpjs_ket' => $data['nomor_bpjs_ket'] ?? null,
                'nomor_rekening' => $data['nomor_rekening'] ?? null,
                'nama_bank' => $data['nama_bank'] ?? null,
                'nomor_telepon' => $data['nomor_telepon'],
                'top_education' => $topEducation['level_education'],
                'prodi' => $topEducation['prodi'],
                'status_bpjs_kes' => ($data['nomor_bpjs_kes'] ?? null) === null ? 0 : 1,
                'status_bpjs_ket' => ($data['nomor_bpjs_ket'] ?? null) === null ? 0 : 1,
                'updated_at' => $now,
            ]);

            foreach ([
                ['table' => 'tb_cand_addresses', 'key' => 'ktp'],
                ['table' => 'tb_cand_domiciles', 'key' => 'domicile'],
            ] as $addressRecord) {
                $address = $data['addresses'][$addressRecord['key']];
                $location = $resolvedAddresses[$addressRecord['key']];
                DB::table($addressRecord['table'])->insert([
                    'id_cand_employee' => $candidate->id,
                    'id_ktp' => $data['nomor_ktp'],
                    'provinsi' => $location['province'],
                    'kabupaten' => $location['regency'],
                    'kecamatan' => $location['district'],
                    'kelurahan' => $location['village'],
                    'detail' => $address['detail'],
                    'map_address' => $address['map_address'] ?? null,
                    'status' => 1,
                    'created_at' => $now,
                    'updated_at' => $now,
                ]);
            }

            foreach ($data['educations'] as $education) {
                DB::table('tb_cand_educations')->insert([
                    'id_cand_employee' => $candidate->id,
                    'level_education' => $education['level_education'],
                    'institute' => $education['institute'],
                    'prodi' => $education['prodi'],
                    'year' => $education['year'],
                    'graduate_year' => $education['graduate_year'] ?? null,
                    'remark' => $education['remark'] ?? null,
                    'is_top' => ! empty($education['is_top']) ? 1 : 0,
                    'created_at' => $now,
                    'updated_at' => $now,
                ]);
            }

            foreach ($data['emergency_contacts'] as $index => $contact) {
                $location = $resolvedEmergencyAddresses[$index];
                DB::table('tb_cand_address_darurat')->insert([
                    'id_cand_employee' => $candidate->id,
                    'nama_keluarga' => $contact['nama_keluarga'],
                    'hubungan' => $contact['hubungan'],
                    'nomor_kontak' => $contact['nomor_kontak'],
                    'provinsi_kontak' => $location['province'],
                    'kabupaten_kontak' => $location['regency'],
                    'kecamatan_kontak' => $location['district'],
                    'kelurahan_kontak' => $location['village'],
                    'detail_kontak' => $contact['detail_kontak'],
                    'status' => 1,
                ]);
            }

            foreach ($data['families'] ?? [] as $family) {
                DB::table('tb_cand_employee_family')->insert([
                    'id_cand_employee' => $candidate->id,
                    'nama_keluarga' => $family['nama_keluarga'],
                    'hubungan' => $family['hubungan'],
                    'tanggal_lahir' => $family['tanggal_lahir'],
                    'status' => 1,
                ]);
            }

            DB::table('tb_cand_employees')
                ->where('id', $candidate->id)
                ->update([
                    'updated_at' => $now,
                    'updated_by' => $candidate->employee_name,
                ]);
        });

        return redirect()->route('candidate.complete');
    }

    public function complete(Request $request)
    {
        $candidate = $this->verifiedCandidate($request);
        if (! $candidate) {
            return redirect()->route('candidate.access');
        }

        return view('page.candidate.complete', compact('candidate'));
    }

    private function emailIsAvailable(string $email): bool
    {
        $email = mb_strtolower($email);

        return ! DB::table('users')->whereRaw('LOWER(email) = ?', [$email])->exists()
            && ! DB::table('tb_cand_employees')->whereRaw('LOWER(email_address) = ?', [$email])->exists();
    }

    private function resolveCandidateWilayah(array $codes, array $fields, string $errorPrefix): array
    {
        $lengths = [
            'province' => 2,
            'regency' => 5,
            'district' => 8,
            'village' => 13,
        ];
        $names = [];
        $parentCode = null;

        foreach ($lengths as $level => $length) {
            $field = $fields[$level];
            $code = $codes[$field] ?? null;
            $region = is_string($code)
                ? DB::table('wilayah_2020')->select('kode', 'nama')->where('kode', $code)->first()
                : null;

            $isValid = $region && strlen($region->kode) === $length;
            if ($isValid && $parentCode !== null) {
                $isValid = substr($region->kode, 0, strlen($parentCode) + 1) === $parentCode.'.';
            }

            if (! $isValid) {
                throw \Illuminate\Validation\ValidationException::withMessages([
                    $errorPrefix.'.'.$field => 'Pilihan wilayah tidak valid atau tidak sesuai urutannya.',
                ]);
            }

            $names[$level] = $region->nama;
            $parentCode = $region->kode;
        }

        return $names;
    }

    private function verifiedCandidate(Request $request)
    {
        $candidateId = $request->session()->get('candidate_employee_id');
        if (! $candidateId) {
            return null;
        }

        $candidate = DB::table('tb_cand_employees')
            ->where('id', $candidateId)
            ->first();

        if (! $candidate) {
            return null;
        }

        $emailVerified = $candidate->verified_at !== null;
        $verificationSkipped = (string) $request->session()->get('candidate_email_verification_skipped_for') === (string) $candidateId;

        return $emailVerified || $verificationSkipped ? $candidate : null;
    }

    private function ensureHrAccess(Request $request): void
    {
        abort_unless($request->user() && $request->user()->hasRole('hr_access'), 403);
    }
}

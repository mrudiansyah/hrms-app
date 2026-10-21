<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;
use SimpleSoftwareIO\QrCode\Facades\QrCode;
use Barryvdh\DomPDF\Facade\Pdf;

class EventController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth', 'verified']);
    }

    public function index($id)
    {
        $this->authorizeEventAccess();

        $event = $id > 0 ? DB::table('tb_events')->where('id', $id)->first() : null;
        abort_if($id > 0 && !$event, 404);

        $events = DB::table('tb_events')
            ->orderByDesc('date')
            ->orderByDesc('id')
            ->paginate(20);

        $schedules = DB::table('tb_training_schedule')
            ->leftJoin('tb_training_list', 'tb_training_list.id', '=', 'tb_training_schedule.id_training')
            ->where(function ($query) use ($event) {
                $query->where('tb_training_schedule.is_delete', '0');
                if ($event && $event->id_training_schedule) {
                    $query->orWhere('tb_training_schedule.id', $event->id_training_schedule);
                }
            })
            ->orderByDesc('tb_training_schedule.tanggal')
            ->get([
                'tb_training_schedule.id',
                'tb_training_schedule.tanggal',
                'tb_training_schedule.start',
                'tb_training_schedule.finish',
                'tb_training_list.training_name',
            ]);

        $attendances = $event
            ? DB::table('tb_event_attendances')->where('id_event', $event->id)->orderBy('id')->get()
            : collect();

        return view('page.event.index', [
            'event' => $event,
            'events' => $events,
            'schedules' => $schedules,
            'attendances' => $attendances,
            'site' => request()->server('SCRIPT_NAME'),
            'menu' => 'event',
            'juduls' => 'Event Participant',
        ]);
    }

    public function scanQrPage()
    {
        $this->authorizeEventAccess();

        $scannedAttendances = DB::table('tb_event_attendances')
            ->join('tb_events', 'tb_events.id', '=', 'tb_event_attendances.id_event')
            ->whereNotNull('tb_event_attendances.checkin')
            ->orderByDesc('tb_event_attendances.checkout')
            ->orderByDesc('tb_event_attendances.checkin')
            ->limit(100)
            ->get([
                'tb_event_attendances.id',
                'tb_event_attendances.entrant_code',
                'tb_event_attendances.entrant_name',
                'tb_event_attendances.institution',
                'tb_event_attendances.checkin',
                'tb_event_attendances.checkout',
                'tb_event_attendances.status',
                'tb_events.event_name',
            ]);

        return view('page.event.scan_qr', [
            'scannedAttendances' => $scannedAttendances,
            'site' => request()->server('SCRIPT_NAME'),
            'menu' => 'event',
            'juduls' => 'Register Participant',
        ]);
    }

    public function registerQrScan(Request $request)
    {
        $this->authorizeEventAccess();
        $validated = $request->validate([
            'entrant_code' => ['required', 'string', 'max:32'],
        ]);

        $result = DB::transaction(function () use ($validated) {
            $attendance = DB::table('tb_event_attendances')
                ->join('tb_events', 'tb_events.id', '=', 'tb_event_attendances.id_event')
                ->where('tb_event_attendances.entrant_code', trim($validated['entrant_code']))
                ->lockForUpdate()
                ->first([
                    'tb_event_attendances.id',
                    'tb_event_attendances.id_event',
                    'tb_event_attendances.entrant_code',
                    'tb_event_attendances.entrant_name',
                    'tb_event_attendances.checkin',
                    'tb_event_attendances.checkout',
                    'tb_event_attendances.status',
                    'tb_events.event_name',
                ]);

            if (!$attendance) {
                return [
                    'error' => 'Kode QR tidak ditemukan. Pastikan QR Code peserta berasal dari event yang terdaftar.',
                    'status' => 404,
                ];
            }

            $scannedAt = now();
            if ((int) $attendance->status === 0 || !$attendance->checkin) {
                DB::table('tb_event_attendances')->where('id', $attendance->id)->update([
                    'checkin' => $scannedAt,
                    'status' => 1,
                ]);
                $attendance->checkin = $scannedAt->toDateTimeString();
                $attendance->checkout = null;
                $attendance->status = 1;
                $action = 'checkin';
                $message = 'Check-in berhasil dicatat.';
            } elseif ((int) $attendance->status === 1) {
                if (Carbon::parse($attendance->checkin)->addMinutes(3)->isAfter($scannedAt)) {
                    return [
                        'error' => 'Check-out hanya dapat dicatat minimal 3 menit setelah check-in.',
                        'status' => 429,
                    ];
                }

                DB::table('tb_event_attendances')->where('id', $attendance->id)->update([
                    'checkout' => $scannedAt,
                    'status' => 2,
                ]);
                $attendance->checkout = $scannedAt->toDateTimeString();
                $attendance->status = 2;
                $action = 'checkout';
                $message = 'Check-out berhasil dicatat.';
            } else {
                return [
                    'error' => 'Peserta ini sudah check-out. Tidak ada perubahan data.',
                    'status' => 409,
                ];
            }

            return [
                'action' => $action,
                'message' => $message,
                'attendance' => $attendance,
            ];
        });

        if (isset($result['error'])) {
            return response()->json([
                'message' => $result['error'],
            ], $result['status']);
        }

        return response()->json($result);
    }

    public function save(Request $request)
    {
        $this->authorizeEventAccess();

        $eventId = $request->input('id');
        $event = $eventId ? DB::table('tb_events')->where('id', $eventId)->first() : null;
        abort_if($eventId && !$event, 404);

        $scheduleId = $request->input('id_training_schedule');
        $existingScheduleId = $event ? $event->id_training_schedule : null;
        if ($event && (string) $scheduleId !== (string) $existingScheduleId) {
            throw ValidationException::withMessages([
                'id_training_schedule' => 'Jadwal training tidak dapat diubah setelah event dibuat.',
            ]);
        }

        $rules = [
            'id_training_schedule' => ['nullable', 'integer', 'exists:tb_training_schedule,id'],
        ];
        if (!$scheduleId) {
            $rules += [
                'category' => ['required', 'in:Training,Meeting'],
                'event_name' => ['required', 'string', 'max:255'],
                'date' => ['required', 'date'],
                'start_plan' => ['required', 'date_format:H:i'],
                'finish_plan' => ['required', 'date_format:H:i'],
            ];
        }
        $validated = $request->validate($rules);

        $schedule = null;
        if ($scheduleId) {
            $schedule = DB::table('tb_training_schedule')
                ->leftJoin('tb_training_list', 'tb_training_list.id', '=', 'tb_training_schedule.id_training')
                ->where('tb_training_schedule.id', $scheduleId)
                ->where('tb_training_schedule.is_delete', '0')
                ->first([
                    'tb_training_schedule.id',
                    'tb_training_schedule.tanggal',
                    'tb_training_schedule.start',
                    'tb_training_schedule.finish',
                    'tb_training_list.training_name',
                ]);

            if (
                !$schedule ||
                !$schedule->training_name ||
                !$schedule->tanggal ||
                !$schedule->start ||
                !$schedule->finish
            ) {
                throw ValidationException::withMessages([
                    'id_training_schedule' => 'Jadwal training tidak ditemukan atau data nama, tanggal, dan waktunya belum lengkap.',
                ]);
            }
        }

        $eventData = $schedule
            ? [
                'id_training_schedule' => $schedule->id,
                'category' => 'Training',
                'event_name' => $schedule->training_name,
                'date' => Carbon::parse($schedule->tanggal)->toDateString(),
                'start_plan' => $this->scheduleTime($schedule->start, 'start'),
                'finish_plan' => $this->scheduleTime($schedule->finish, 'finish'),
            ]
            : [
                'id_training_schedule' => null,
                'category' => $validated['category'],
                'event_name' => $validated['event_name'],
                'date' => Carbon::parse($validated['date'])->toDateString(),
                'start_plan' => $validated['start_plan'] . ':00',
                'finish_plan' => $validated['finish_plan'] . ':00',
            ];

        $oldDate = $event ? Carbon::parse($event->date)->toDateString() : null;
        $admin = Auth::user()->name;
        $eventId = DB::transaction(function () use ($event, $eventData, $schedule, $admin, $oldDate) {
            if ($event) {
                DB::table('tb_events')->where('id', $event->id)->update($eventData);
                $this->rebuildEventCodes($oldDate);
                if ($oldDate !== $eventData['date']) {
                    $this->rebuildEventCodes($eventData['date']);
                }
                return $event->id;
            }

            $dailyCount = DB::table('tb_events')
                ->whereDate('date', $eventData['date'])
                ->lockForUpdate()
                ->count();
            $this->eventEntrantCode($eventData['category'], $eventData['date'], $dailyCount + 1);

            $eventData['created_at'] = now();
            $eventData['created_by'] = $admin;
            $newEventId = DB::table('tb_events')->insertGetId($eventData);

            if ($schedule) {
                $eventCode = $this->eventEntrantCode($eventData['category'], $eventData['date'], $dailyCount + 1);
                $invitations = DB::table('tb_training_invitation')
                    ->where('id_training_schedule', $schedule->id)
                    ->get(['id_employee', 'nama_karyawan']);

                if ($invitations->count() > 999) {
                    throw ValidationException::withMessages([
                        'id_training_schedule' => 'Maksimal 999 peserta dapat dimasukkan dalam satu event.',
                    ]);
                }

                foreach ($invitations as $index => $invitation) {
                    DB::table('tb_event_attendances')->insert([
                        'id_event' => $newEventId,
                        'id_entrant' => $invitation->id_employee,
                        'entrant_name' => $invitation->nama_karyawan,
                        'institution' => 'PT.SAI',
                        'checkin' => null,
                        'checkout' => null,
                        'created_at' => now(),
                        'created_by' => $admin,
                        'status' => 0,
                        'entrant_code' => $this->attendanceEntrantCode($eventCode, $index + 1),
                    ]);
                }
            }

            $this->rebuildEventCodes($eventData['date']);

            return $newEventId;
        });

        return redirect('/Event/' . $eventId)->with('success', $event ? 'Event berhasil diperbarui.' : 'Event berhasil dibuat.');
    }

    public function addAttendance(Request $request, $id)
    {
        $this->authorizeEventAccess();
        $event = DB::table('tb_events')->where('id', $id)->first();
        abort_if(!$event, 404);

        $validated = $request->validate($this->attendanceRules());
        DB::transaction(function () use ($event, $validated) {
            $lockedEvent = DB::table('tb_events')->where('id', $event->id)->lockForUpdate()->first();
            $attendanceCount = DB::table('tb_event_attendances')->where('id_event', $event->id)->count();
            if ($attendanceCount >= 999) {
                throw ValidationException::withMessages([
                    'id_entrant' => 'Maksimal 999 peserta dapat dimasukkan dalam satu event.',
                ]);
            }

            $eventCode = $this->eventCodeForExistingEvent($lockedEvent);
            DB::table('tb_event_attendances')->insert($this->attendanceData($validated) + [
                'id_event' => $event->id,
                'created_at' => now(),
                'created_by' => Auth::user()->name,
                'entrant_code' => $this->attendanceEntrantCode($eventCode, $attendanceCount + 1),
            ]);
        });

        return redirect('/Event/' . $id)->with('success', 'Peserta berhasil ditambahkan.');
    }

    public function updateAttendance(Request $request, $id, $attendanceId)
    {
        $this->authorizeEventAccess();
        $event = DB::table('tb_events')->where('id', $id)->first();
        abort_if(!$event, 404);

        $attendance = DB::table('tb_event_attendances')
            ->where('id', $attendanceId)
            ->where('id_event', $id)
            ->first();
        abort_if(!$attendance, 404);

        $validated = $request->validate($this->attendanceRules());
        DB::table('tb_event_attendances')
            ->where('id', $attendanceId)
            ->update($this->attendanceData($validated));

        return redirect('/Event/' . $id)->with('success', 'Data peserta berhasil diperbarui.');
    }

    public function deleteAttendance($id, $attendanceId)
    {
        $this->authorizeEventAccess();
        $eventExists = DB::table('tb_events')->where('id', $id)->exists();
        abort_if(!$eventExists, 404);

        DB::transaction(function () use ($id, $attendanceId) {
            $event = DB::table('tb_events')->where('id', $id)->lockForUpdate()->first();
            DB::table('tb_event_attendances')
                ->where('id', $attendanceId)
                ->where('id_event', $id)
                ->delete();
            $this->rebuildEventCodes(Carbon::parse($event->date)->toDateString());
        });

        return redirect('/Event/' . $id)->with('success', 'Peserta berhasil dihapus.');
    }

    public function attendanceQr($attendanceId)
    {
        $this->authorizeEventAccess();

        $attendance = DB::table('tb_event_attendances')
            ->join('tb_events', 'tb_events.id', '=', 'tb_event_attendances.id_event')
            ->where('tb_event_attendances.id', $attendanceId)
            ->first([
                'tb_events.event_name',
                'tb_events.date',
                'tb_events.start_plan',
                'tb_event_attendances.entrant_name',
                'tb_event_attendances.entrant_code',
            ]);
        abort_if(!$attendance, 404);

        $qrCode = QrCode::format('svg')
            ->size(300)
            ->margin(1)
            ->generate($attendance->entrant_code);

        return Pdf::loadView('page.event.entrant_qr', [
            'attendance' => $attendance,
            'qrCode' => base64_encode($qrCode),
        ])->setPaper([0, 0, 400,510])->stream('entrant-' . $attendance->entrant_code . '.pdf');
    }

    public function delete($id)
    {
        $this->authorizeEventAccess();
        $event = DB::table('tb_events')->where('id', $id)->first();
        abort_if(!$event, 404);

        $eventDate = Carbon::parse($event->date)->toDateString();
        DB::transaction(function () use ($event, $eventDate) {
            DB::table('tb_event_attendances')->where('id_event', $event->id)->delete();
            DB::table('tb_events')->where('id', $event->id)->delete();
            $this->rebuildEventCodes($eventDate);
        });

        return redirect('/Event/0')->with('success', 'Event dan seluruh data pesertanya berhasil dihapus.');
    }

    private function authorizeEventAccess(): void
    {
        $user = request()->user();
        abort_unless($user && ($user->hasRole('root') || $user->hasRole('training')), 403, 'Anda tidak punya akses');
    }

    private function attendanceRules(): array
    {
        return [
            'id_entrant' => ['required', 'string', 'max:100'],
            'entrant_name' => ['required', 'string', 'max:255'],
            'institution' => ['required', 'string', 'max:255'],
            'checkin' => ['nullable', 'date'],
            'checkout' => ['nullable', 'date'],
            'status' => ['required', 'integer', 'min:0'],
        ];
    }

    private function attendanceData(array $validated): array
    {
        return [
            'id_entrant' => $validated['id_entrant'],
            'entrant_name' => $validated['entrant_name'],
            'institution' => $validated['institution'],
            'checkin' => $validated['checkin'] ?? null,
            'checkout' => $validated['checkout'] ?? null,
            'status' => $validated['status'],
        ];
    }

    private function eventCodeForExistingEvent(object $event): string
    {
        $ordinal = DB::table('tb_events')
            ->whereDate('date', Carbon::parse($event->date)->toDateString())
            ->where('id', '<=', $event->id)
            ->count();

        return $this->eventEntrantCode($event->category, $event->date, $ordinal);
    }

    private function scheduleTime(?string $value, string $field): string
    {
        $value = trim((string) $value);
        $timePattern = '/^\d{1,2}:\d{2}(?::\d{2}(?:\.\d+)?)?$/';
        $dateTimePattern = '/^\d{4}-\d{2}-\d{2}[ T]\d{1,2}:\d{2}(?::\d{2}(?:\.\d+)?)?$/';

        if (preg_match($timePattern, $value)) {
            return Carbon::parse($value)->format('H:i:s');
        }

        if (preg_match($dateTimePattern, $value)) {
            return Carbon::parse($value)->format('H:i:s');
        }

        throw ValidationException::withMessages([
            'id_training_schedule' => "Waktu {$field} pada jadwal training tidak valid. Periksa data start/finish jadwal (format HH:mm).",
        ]);
    }

    private function rebuildEventCodes(string $date): void
    {
        $events = DB::table('tb_events')->whereDate('date', $date)->orderBy('id')->get();
        if ($events->count() > 99) {
            throw ValidationException::withMessages([
                'date' => 'Maksimal 99 event dapat dibuat pada tanggal yang sama.',
            ]);
        }

        foreach ($events as $index => $event) {
            $eventCode = $this->eventEntrantCode($event->category, $date, $index + 1);
            $attendances = DB::table('tb_event_attendances')
                ->where('id_event', $event->id)
                ->orderBy('id')
                ->get(['id']);
            if ($attendances->count() > 999) {
                throw ValidationException::withMessages([
                    'date' => 'Maksimal 999 peserta dapat dimasukkan dalam satu event.',
                ]);
            }

            foreach ($attendances as $attendanceIndex => $attendance) {
                DB::table('tb_event_attendances')->where('id', $attendance->id)->update([
                    'entrant_code' => $this->attendanceEntrantCode($eventCode, $attendanceIndex + 1),
                ]);
            }
        }
    }

    private function eventEntrantCode(string $category, string $date, int $ordinal): string
    {
        if ($ordinal < 1 || $ordinal > 99) {
            throw ValidationException::withMessages([
                'date' => 'Maksimal 99 event dapat dibuat pada tanggal yang sama.',
            ]);
        }

        $prefix = $category === 'Training' ? 'A' : 'B';
        return $prefix . Carbon::parse($date)->format('ymd') . str_pad((string) $ordinal, 2, '0', STR_PAD_LEFT);
    }

    private function attendanceEntrantCode(string $eventCode, int $ordinal): string
    {
        if ($ordinal < 1 || $ordinal > 999) {
            throw ValidationException::withMessages([
                'id_entrant' => 'Maksimal 999 peserta dapat dimasukkan dalam satu event.',
            ]);
        }

        return $eventCode . str_pad((string) $ordinal, 3, '0', STR_PAD_LEFT);
    }
}

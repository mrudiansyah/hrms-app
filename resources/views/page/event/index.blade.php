@extends('layouts/admin')

@section('Contents')
<div class="content-wrapper">
    <section class="content-header">
        <h1>Event Participant</h1>
    </section>

    <section class="content">
        @if (session('success'))
            <div class="alert alert-success">{{ session('success') }}</div>
        @endif
        @if ($errors->any())
            <div class="alert alert-danger">
                <ul class="list-unstyled" style="margin-bottom:0;">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <div class="row">
            <div class="col-md-4">
                <div class="box box-primary">
                    <div class="box-header with-border">
                        <h3 class="box-title">{{ $event ? 'Edit Event' : 'Buat Event' }}</h3>
                        @if ($event)
                            <div class="box-tools pull-right">
                                <a href="/Event/0" class="btn btn-default btn-xs">Event Baru</a>
                            </div>
                        @endif
                    </div>
                    <form method="POST" action="/Event/Save">
                        @csrf
                        @if ($event)
                            <input type="hidden" name="id" value="{{ $event->id }}">
                            <input type="hidden" name="id_training_schedule" value="{{ $event->id_training_schedule }}">
                        @endif
                        <div class="box-body">
                            <div class="form-group">
                                <label for="schedule">Training Schedule (opsional)</label>
                                <select id="schedule" name="id_training_schedule" class="form-control"
                                    {{ $event ? 'disabled' : '' }}>
                                    <option value="">Isi event secara manual</option>
                                    @foreach ($schedules as $schedule)
                                        <option value="{{ $schedule->id }}"
                                            data-name="{{ $schedule->training_name }}"
                                            data-date="{{ $schedule->tanggal }}"
                                            data-start="{{ $schedule->start }}"
                                            data-finish="{{ $schedule->finish }}"
                                            {{ old('id_training_schedule', $event->id_training_schedule ?? '') == $schedule->id ? 'selected' : '' }}>
                                            {{ $schedule->tanggal }} - {{ $schedule->training_name }}
                                        </option>
                                    @endforeach
                                </select>
                                @if ($event)
                                    <small class="text-muted">Jadwal tidak dapat diubah setelah event dibuat.</small>
                                @endif
                            </div>

                            @php($trainingEvent = $event && $event->id_training_schedule)
                            <div class="form-group">
                                <label for="category">Category</label>
                                <select id="category" name="category" class="form-control"
                                    {{ $trainingEvent ? 'disabled' : '' }}>
                                    <option value="Training" {{ old('category', $event->category ?? 'Training') === 'Training' ? 'selected' : '' }}>Training</option>
                                    <option value="Meeting" {{ old('category', $event->category ?? '') === 'Meeting' ? 'selected' : '' }}>Meeting</option>
                                </select>
                            </div>
                            <div class="form-group">
                                <label for="event_name">Event Name</label>
                                <input id="event_name" type="text" name="event_name" class="form-control"
                                    value="{{ old('event_name', $event->event_name ?? '') }}" maxlength="255" required
                                    {{ $trainingEvent ? 'readonly' : '' }}>
                            </div>
                            <div class="form-group">
                                <label for="event_date">Date</label>
                                <input id="event_date" type="date" name="date" class="form-control"
                                    value="{{ old('date', isset($event) ? \Carbon\Carbon::parse($event->date)->format('Y-m-d') : '') }}" required
                                    {{ $trainingEvent ? 'readonly' : '' }}>
                            </div>
                            <div class="row">
                                <div class="col-xs-6">
                                    <div class="form-group">
                                        <label for="start_plan">Start</label>
                                        <input id="start_plan" type="time" name="start_plan" class="form-control"
                                            value="{{ old('start_plan', isset($event) ? substr($event->start_plan, 0, 5) : '') }}" required
                                            {{ $trainingEvent ? 'readonly' : '' }}>
                                    </div>
                                </div>
                                <div class="col-xs-6">
                                    <div class="form-group">
                                        <label for="finish_plan">Finish</label>
                                        <input id="finish_plan" type="time" name="finish_plan" class="form-control"
                                            value="{{ old('finish_plan', isset($event) ? substr($event->finish_plan, 0, 5) : '') }}" required
                                            {{ $trainingEvent ? 'readonly' : '' }}>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="box-footer">
                            <button type="submit" class="btn btn-primary">
                                <i class="fa fa-save"></i> {{ $event ? 'Simpan Perubahan' : 'Buat Event' }}
                            </button>
                        </div>
                    </form>
                </div>

                <div class="box box-default">
                    <div class="box-header with-border">
                        <h3 class="box-title">Daftar Event</h3>
                    </div>
                    <div class="box-body table-responsive">
                        <table class="table table-striped table-hover">
                            <thead>
                                <tr>
                                    <th>Tanggal</th>
                                    <th>Event</th>
                                    <th></th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($events as $item)
                                    <tr>
                                        <td>{{ \Carbon\Carbon::parse($item->date)->format('d-m-Y') }}</td>
                                        <td>{{ $item->event_name }}<br><small>{{ $item->category }}</small></td>
                                        <td><a class="btn btn-primary btn-xs" href="/Event/{{ $item->id }}">Buka</a></td>
                                    </tr>
                                @empty
                                    <tr><td colspan="3" class="text-center">Belum ada event.</td></tr>
                                @endforelse
                            </tbody>
                        </table>
                        {{ $events->links() }}
                    </div>
                </div>
            </div>

            @if ($event)
                <div class="col-md-8">
                    <div class="box box-info">
                        <div class="box-header with-border">
                            <h3 class="box-title">Peserta: {{ $event->event_name }}</h3>
                            <div class="box-tools pull-right">
                                <form method="POST" action="/Event/{{ $event->id }}/Delete"
                                    onsubmit="return confirm('Hapus event ini beserta seluruh pesertanya?')">
                                    @csrf
                                    <button type="submit" class="btn btn-danger btn-xs">
                                        <i class="fa fa-trash"></i> Hapus Event
                                    </button>
                                </form>
                            </div>
                        </div>
                        <div class="box-body">
                            @if ($event->id_training_schedule)
                                <p>Peserta dari training invitation telah dimasukkan otomatis. Peserta tambahan tetap dapat ditambahkan secara manual.</p>
                            @else
                                <p>Event manual: tambahkan data peserta melalui form berikut.</p>
                            @endif
                            <form method="POST" action="/Event/{{ $event->id }}/attendance" class="well">
                                @csrf
                                <div class="row">
                                    <div class="col-sm-3">
                                        <label>ID Peserta</label>
                                        <input type="number" name="id_entrant" value="0" class="form-control" required>
                                    </div>
                                    <div class="col-sm-3">
                                        <label>Nama Peserta</label>
                                        <input type="text" name="entrant_name" class="form-control" maxlength="255" required>
                                    </div>
                                    <div class="col-sm-3">
                                        <label>Institusi</label>
                                        <input type="text" name="institution" class="form-control" maxlength="255" required>
                                    </div>
                                    <div class="col-sm-3">
                                        <label>&nbsp;</label>
                                        <button type="submit" class="btn btn-success form-control">
                                            <i class="fa fa-plus"></i> Tambah Peserta
                                        </button>
                                    </div>
                                </div>
                                <input type="hidden" name="status" value="0">
                            </form>

                            <div class="table-responsive">
                                <table class="table table-bordered table-striped">
                                    <thead>
                                        <tr>
                                            <th>ID Peserta</th>
                                            <th>Nama Peserta</th>
                                            <th>Institusi</th>
                                            <th>Check-in</th>
                                            <th>Check-out</th>
                                            <th>Status</th>
                                            <th>Kode</th>
                                            <th>Aksi</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @forelse ($attendances as $attendance)
                                            <tr>
                                                <td>{{ $attendance->id_entrant }}</td>
                                                <td>{{ $attendance->entrant_name }}</td>
                                                <td>{{ $attendance->institution }}</td>
                                                <td>{{ $attendance->checkin }}</td>
                                                <td>{{ $attendance->checkout }}</td>
                                                <td>{{ $attendance->status }}</td>
                                                <td>{{ $attendance->entrant_code }}</td>
                                                <td class="text-nowrap">
                                                    <a href="/Event/QRCode/{{ $attendance->id }}"
                                                        class="btn btn-info btn-xs" title="Tampilkan QR Code PDF" target="_blank">
                                                        <i class="fa fa-qrcode"></i>
                                                    </a>
                                                    <form method="POST" action="/Event/{{ $event->id }}/attendance/{{ $attendance->id }}/Delete"
                                                        style="display:inline;" onsubmit="return confirm('Hapus peserta ini?')">
                                                        @csrf
                                                        <button type="submit" class="btn btn-danger btn-xs"><i class="fa fa-trash"></i></button>
                                                    </form>
                                                </td>
                                            </tr>
                                        @empty
                                            <tr><td colspan="8" class="text-center">Belum ada peserta.</td></tr>
                                        @endforelse
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            @endif
        </div>
    </section>
</div>

@if ($event)
    <div class="modal fade" id="attendance-modal" tabindex="-1" role="dialog" aria-labelledby="attendance-modal-title">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <form id="attendance-form" method="POST">
                    @csrf
                    <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                        <h4 class="modal-title" id="attendance-modal-title">Edit Peserta</h4>
                    </div>
                    <div class="modal-body">
                        <div class="form-group">
                            <label>ID Peserta</label>
                            <input id="edit-entrant" type="number" name="id_entrant" value="0" class="form-control" required>
                        </div>
                        <div class="form-group">
                            <label>Nama Peserta</label>
                            <input id="edit-name" type="text" name="entrant_name" class="form-control" maxlength="255" required>
                        </div>
                        <div class="form-group">
                            <label>Institusi</label>
                            <input id="edit-institution" type="text" name="institution" class="form-control" maxlength="255" required>
                        </div>
                        <div class="row">
                            <div class="col-sm-6 form-group">
                                <label>Check-in</label>
                                <input id="edit-checkin" type="datetime-local" name="checkin" class="form-control">
                            </div>
                            <div class="col-sm-6 form-group">
                                <label>Check-out</label>
                                <input id="edit-checkout" type="datetime-local" name="checkout" class="form-control">
                            </div>
                        </div>
                        <div class="form-group">
                            <label>Status</label>
                            <input id="edit-status" type="number" name="status" class="form-control" min="0" required>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-default" data-dismiss="modal">Batal</button>
                        <button type="submit" class="btn btn-primary">Simpan</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endif

<script>
    (function () {
        var eventId = @json($event->id ?? null);
        var scheduleSelect = document.getElementById('schedule');
        if (scheduleSelect && !scheduleSelect.disabled) {
            var eventFields = [
                document.getElementById('event_name'),
                document.getElementById('event_date'),
                document.getElementById('start_plan'),
                document.getElementById('finish_plan')
            ];
            function setManualMode(manual) {
                for (var fieldIndex = 0; fieldIndex < eventFields.length; fieldIndex++) {
                    eventFields[fieldIndex].readOnly = !manual;
                }
                document.getElementById('category').disabled = !manual;
            }

            scheduleSelect.addEventListener('change', function () {
                var selected = this.options[this.selectedIndex];
                if (!this.value) {
                    document.getElementById('event_name').value = '';
                    document.getElementById('event_date').value = '';
                    document.getElementById('start_plan').value = '';
                    document.getElementById('finish_plan').value = '';
                    setManualMode(true);
                    return;
                }

                setManualMode(false);
                document.getElementById('category').value = 'Training';
                document.getElementById('event_name').value = selected.dataset.name || '';
                document.getElementById('event_date').value = (selected.dataset.date || '').substring(0, 10);
                document.getElementById('start_plan').value = (selected.dataset.start || '').substring(0, 5);
                document.getElementById('finish_plan').value = (selected.dataset.finish || '').substring(0, 5);
            });
            if (scheduleSelect.value) {
                scheduleSelect.dispatchEvent(new Event('change'));
            }
        }

        var editButtons = document.querySelectorAll('.edit-attendance');
        for (var i = 0; i < editButtons.length; i++) {
            editButtons[i].addEventListener('click', function () {
                var button = this;
                var form = document.getElementById('attendance-form');
                form.action = '/Event/' + eventId + '/attendance/' + button.dataset.id;
                document.getElementById('edit-entrant').value = button.dataset.entrant || '0';
                document.getElementById('edit-name').value = button.dataset.name;
                document.getElementById('edit-institution').value = button.dataset.institution;
                document.getElementById('edit-checkin').value = button.dataset.checkin;
                document.getElementById('edit-checkout').value = button.dataset.checkout;
                document.getElementById('edit-status').value = button.dataset.status;
                if (window.jQuery_3_2_1) {
                    window.jQuery_3_2_1('#attendance-modal').modal('show');
                }
            });
        }
    })();
</script>
@endsection

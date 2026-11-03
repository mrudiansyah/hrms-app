@extends('layouts/admin')

@section('Contents')
<div class="content-wrapper">
    <section class="content-header">
        <h1>QCC <small>Periode Team</small></h1>
    </section>
    <section class="content">
        @include('qcc._team_navigation')
        <div style="height:15px;"></div>
        @include('qcc._messages')

        <div class="box box-primary">
            <div class="box-header with-border">
                <h3 class="box-title">Pilih Periode QCC</h3>
            </div>
            <div class="box-body">
                <form action="{{ url('/qcc_team/periode') }}" method="GET" class="form-inline">
                    <div class="form-group">
                        <label for="periode_id">Periode</label>
                        <select class="form-control" id="periode_id" name="periode_id" required>
                            <option value="">Pilih periode QCC</option>
                            @foreach($periods as $period)
                                <option value="{{ $period->id }}" @selected($selectedPeriod && (int) $selectedPeriod->id === (int) $period->id)>
                                    {{ $period->sugested_thema }}
                                    ({{ \Illuminate\Support\Carbon::parse($period->periode_start)->format('d/m/Y') }} - {{ \Illuminate\Support\Carbon::parse($period->periode_finish)->format('d/m/Y') }})
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <button class="btn btn-primary" type="submit"><i class="fa fa-search"></i> Tampilkan</button>
                </form>
            </div>
        </div>

        @if($selectedPeriod)
            <div class="box box-primary">
                <div class="box-header with-border">
                    <h3 class="box-title">Daftar Team untuk: {{ $selectedPeriod->sugested_thema }}</h3>
                </div>
                <div class="box-body table-responsive">
                    <table class="table table-bordered table-hover">
                        <thead>
                            <tr class="bg-gray">
                                <th style="width:70px;" class="text-center">Terdaftar</th>
                                <th>Nama Team</th>
                                <th style="width:180px;">Tanggal Registrasi</th>
                                <th style="width:120px;" class="text-center">Anggota</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($teams as $team)
                                @php($teamPeriod = $teamPeriods->get($team->id))
                                @php($detailsId = 'team-members-' . $team->id)
                                <tr>
                                    <td class="text-center">
                                        @if($canManageTeams)
                                            @if($canManageMembers)
                                                <form action="{{ url('/qcc_team_periode/toggle') }}" method="POST">
                                                    @csrf
                                                    <input type="hidden" name="id_qcc_periode" value="{{ $selectedPeriod->id }}">
                                                    <input type="hidden" name="id_qcc_team" value="{{ $team->id }}">
                                                    <input type="hidden" name="registered" value="{{ $teamPeriod ? 0 : 1 }}">
                                                    <input type="checkbox" aria-label="Daftarkan {{ $team->group_name }} pada periode ini"
                                                        @checked($teamPeriod)
                                                        onchange="if (!this.checked && !confirm('Hapus team beserta semua anggotanya dari periode ini?')) { this.checked = true; return; } this.form.elements.registered.value = this.checked ? 1 : 0; this.form.submit();">
                                                </form>
                                            @else
                                                <input type="checkbox" disabled @checked($teamPeriod) aria-label="{{ $teamPeriod ? 'Terdaftar' : 'Belum terdaftar' }}">
                                            @endif
                                        @else
                                            <i class="fa {{ $teamPeriod ? 'fa-check text-success' : 'fa-minus text-muted' }}"
                                                aria-label="{{ $teamPeriod ? 'Terdaftar' : 'Belum terdaftar' }}"></i>
                                        @endif
                                    </td>
                                    <td>{{ $team->group_name }}</td>
                                    <td>{{ $team->registered_at ? \Illuminate\Support\Carbon::parse($team->registered_at)->format('d/m/Y') : '-' }}</td>
                                    <td class="text-center">
                                        <button class="btn btn-default btn-xs" type="button" data-toggle="modal" data-target="#{{ $detailsId }}">
                                            <i class="fa fa-users"></i> Lihat Anggota
                                        </button>
                                    </td>
                                </tr>
                            @empty
                                <tr><td colspan="4" class="text-center text-muted">Belum ada team QCC.</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            @foreach($teams as $team)
                @php($teamPeriod = $teamPeriods->get($team->id))
                @php($detailsId = 'team-members-' . $team->id)
                <div class="modal fade" id="{{ $detailsId }}" tabindex="-1" role="dialog" aria-labelledby="{{ $detailsId }}-title">
                    <div class="modal-dialog modal-lg" role="document">
                        <div class="modal-content">
                            <div class="modal-header">
                                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                                <h4 class="modal-title" id="{{ $detailsId }}-title">
                                    Anggota Team {{ $team->group_name }} - {{ $selectedPeriod->sugested_thema }}
                                </h4>
                            </div>
                            <div class="modal-body" style="min-height: 300px;">
                                @if(!$teamPeriod)
                                    <p class="text-muted">Centang team terlebih dahulu untuk mendaftarkan team pada periode ini dan mengelola anggotanya.</p>
                                @else
                                    @if(!$canManageMembers)
                                        <div class="alert alert-info">
                                            Periode terkunci. Hanya committee yang dapat menambah atau menghapus anggota.
                                        </div>
                                    @endif
                                    <div class="qcc-member-feedback alert" role="status" aria-live="polite" style="display:none;"></div>
                                    <div class="table-responsive">
                                        <table class="table table-bordered table-hover">
                                            <thead>
                                                <tr>
                                                    <th>Karyawan</th>
                                                    <th>Posisi Team</th>
                                                    @if($canManageMembers)
                                                        <th style="width:145px;">Aksi</th>
                                                    @endif
                                                </tr>
                                            </thead>
                                            <tbody class="qcc-member-list">
                                                @foreach($members->get($teamPeriod->id, collect()) as $member)
                                                    <tr>
                                                        <td>{{ $member->NIK }} - {{ $member->employee_name ?? 'Karyawan tidak ditemukan' }}</td>
                                                        <td>{{ $member->position_name ?? '-' }}</td>
                                                        @if($canManageMembers)
                                                            <td>
                                                                <form action="{{ url('/qcc_team_member/'.$member->id) }}" method="POST" style="display:inline;">
                                                                    @csrf
                                                                    @method('DELETE')
                                                                    <button class="btn btn-danger btn-xs" type="submit" onclick="return confirm('Hapus anggota ini?');"><i class="fa fa-trash"></i> Hapus</button>
                                                                </form>
                                                            </td>
                                                        @endif
                                                    </tr>
                                                @endforeach
                                                @if($canManageMembers)
                                                    @php($addMemberFormId = 'add-member-' . $teamPeriod->id)
                                                    <tr>
                                                        <td>
                                                            <form id="{{ $addMemberFormId }}" class="qcc-add-member-form" action="{{ url('/qcc_team_periode/'.$teamPeriod->id.'/member') }}" method="POST">
                                                                @csrf
                                                            </form>
                                                            <select class="form-control selectpicker qcc-employee-select" name="id_employee" form="{{ $addMemberFormId }}" aria-label="Pilih karyawan" data-live-search="true" data-width="100%" data-container="#{{ $detailsId }}" data-size="10" required>
                                                                <option value="">Pilih karyawan</option>
                                                                @foreach($employees as $employee)
                                                                    <option value="{{ $employee->id }}">{{ $employee->NIK }} - {{ $employee->employee_name }}</option>
                                                                @endforeach
                                                            </select>
                                                        </td>
                                                        <td>
                                                            <select class="form-control" name="id_position_team" form="{{ $addMemberFormId }}" aria-label="Pilih posisi team" required>
                                                                <option value="">Pilih posisi team</option>
                                                                @foreach($positions as $position)
                                                                    <option value="{{ $position->id }}">{{ $position->position_name }}</option>
                                                                @endforeach
                                                            </select>
                                                        </td>
                                                        <td>
                                                            <button class="btn btn-success btn-sm" type="submit" form="{{ $addMemberFormId }}">
                                                                <i class="fa fa-plus"></i> Tambah
                                                            </button>
                                                        </td>
                                                    </tr>
                                                @endif
                                            </tbody>
                                        </table>
                                    </div>
                                @endif
                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-default" data-dismiss="modal">Tutup</button>
                            </div>
                        </div>
                    </div>
                </div>
            @endforeach
        @endif
    </section>
</div>
@endsection

@section('Scripts')
<script>
    (function ($) {
        $('.qcc-employee-select').selectpicker();

        document.querySelectorAll('.qcc-add-member-form').forEach(function (form) {
            form.addEventListener('submit', async function (event) {
                event.preventDefault();

                var modal = form.closest('.modal');
                var feedback = modal.querySelector('.qcc-member-feedback');
                var employeeSelect = modal.querySelector('select[name="id_employee"]');
                var positionSelect = modal.querySelector('select[name="id_position_team"]');
                var submitButton = modal.querySelector('button[form="' + form.id + '"]');

                if (form.dataset.submitting === 'true') {
                    return;
                }

                form.dataset.submitting = 'true';
                submitButton.disabled = true;
                feedback.style.display = 'none';

                try {
                    var response = await fetch(form.action, {
                        method: 'POST',
                        headers: {
                            'Accept': 'application/json',
                            'X-Requested-With': 'XMLHttpRequest'
                        },
                        body: new FormData(form)
                    });
                    var result = {};

                    try {
                        result = await response.json();
                    } catch (error) {
                        result = {};
                    }

                    if (!response.ok) {
                        var validationMessages = result.errors
                            ? Object.values(result.errors).flat().join(' ')
                            : '';
                        throw new Error(validationMessages || result.message || 'Anggota gagal ditambahkan.');
                    }

                    var row = document.createElement('tr');
                    var employeeCell = document.createElement('td');
                    var positionCell = document.createElement('td');
                    var actionCell = document.createElement('td');
                    var employeeName = result.member.employee_name || 'Karyawan tidak ditemukan';
                    employeeCell.textContent = (result.member.NIK || '') + ' - ' + employeeName;
                    positionCell.textContent = result.member.position_name || '-';

                    var deleteForm = document.createElement('form');
                    deleteForm.action = '{{ url('/qcc_team_member') }}/' + encodeURIComponent(result.member.id);
                    deleteForm.method = 'POST';
                    deleteForm.style.display = 'inline';

                    var csrfToken = document.createElement('input');
                    csrfToken.type = 'hidden';
                    csrfToken.name = '_token';
                    csrfToken.value = form.querySelector('input[name="_token"]').value;
                    deleteForm.appendChild(csrfToken);

                    var method = document.createElement('input');
                    method.type = 'hidden';
                    method.name = '_method';
                    method.value = 'DELETE';
                    deleteForm.appendChild(method);

                    var deleteButton = document.createElement('button');
                    deleteButton.className = 'btn btn-danger btn-xs';
                    deleteButton.type = 'submit';
                    deleteButton.innerHTML = '<i class="fa fa-trash"></i> Hapus';
                    deleteButton.addEventListener('click', function (clickEvent) {
                        if (!confirm('Hapus anggota ini?')) {
                            clickEvent.preventDefault();
                        }
                    });
                    deleteForm.appendChild(deleteButton);
                    actionCell.appendChild(deleteForm);
                    row.appendChild(employeeCell);
                    row.appendChild(positionCell);
                    row.appendChild(actionCell);

                    var memberList = modal.querySelector('.qcc-member-list');
                    memberList.insertBefore(row, memberList.lastElementChild);
                    employeeSelect.value = '';
                    positionSelect.value = '';
                    $(employeeSelect).selectpicker('refresh');
                    feedback.className = 'qcc-member-feedback alert alert-success';
                    feedback.textContent = result.message;
                    feedback.style.display = 'block';
                } catch (error) {
                    feedback.className = 'qcc-member-feedback alert alert-danger';
                    feedback.textContent = error.message || 'Anggota gagal ditambahkan.';
                    feedback.style.display = 'block';
                } finally {
                    form.dataset.submitting = 'false';
                    submitButton.disabled = false;
                }
            });
        });
    })(jQuery_3_2_1);
</script>
@endsection

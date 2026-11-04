@extends('layouts/admin')

@section('Contents')
<div class="content-wrapper">
    <section class="content-header">
        <h1>QCC <small>Master Team</small></h1>
    </section>
    <section class="content">
        @include('qcc._team_navigation')
        <div style="height:15px;"></div>
        @include('qcc._messages')

        <div class="box box-primary">
            <div class="box-header with-border">
                <h3 class="box-title"><i class="fa fa-users text-blue"></i> Daftar Team</h3>
                <div class="box-tools pull-right">
                    <button class="btn btn-primary btn-sm" type="button" data-toggle="modal" data-target="#add-team-modal">
                        <i class="fa fa-plus"></i> Tambah Team
                    </button>
                </div>
            </div>
            <div class="box-body table-responsive">
                <table class="table table-bordered table-hover">
                    <thead>
                        <tr class="bg-gray">
                            <th style="width:75px;">ID</th>
                            <th>Nama Team</th>
                            <th>Department</th>
                            <th style="width:190px;">Tanggal Registrasi</th>
                            <th style="width:150px;" class="text-center">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($teams as $team)
                            @php($modalId = 'edit-team-' . $team->id)
                            <tr>
                                <td><span class="text-muted">#{{ $team->id }}</span></td>
                                <td><strong>{{ $team->group_name }}</strong></td>
                                <td>{{ $team->department ?: '-' }}</td>
                                <td>
                                    <i class="fa fa-calendar text-muted"></i>
                                    {{ $team->registered_at ? \Illuminate\Support\Carbon::parse($team->registered_at)->format('d/m/Y') : '-' }}
                                </td>
                                <td class="text-center">
                                    <button class="btn btn-warning btn-xs" type="button" data-toggle="modal" data-target="#{{ $modalId }}" title="Edit team">
                                        <i class="fa fa-pencil"></i> Edit
                                    </button>
                                    <form action="{{ url('/qcc_team/'.$team->id) }}" method="POST" style="display:inline;">
                                        @csrf
                                        @method('DELETE')
                                        <button class="btn btn-danger btn-xs" type="submit" onclick="return confirm('Hapus team ini? Team yang terdaftar pada periode tidak dapat dihapus.');" title="Hapus team">
                                            <i class="fa fa-trash"></i> Hapus
                                        </button>
                                    </form>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="text-center text-muted" style="padding:30px;">
                                    <i class="fa fa-users fa-2x"></i>
                                    <p style="margin:10px 0 0;">Belum ada team QCC. Klik <strong>Tambah Team</strong> untuk memulai.</p>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            @if($teams->isNotEmpty())
                <div class="box-footer text-muted">
                    <i class="fa fa-info-circle"></i> Total {{ $teams->count() }} team terdaftar.
                </div>
            @endif
        </div>

        <div class="modal fade" id="add-team-modal" tabindex="-1" role="dialog" aria-labelledby="add-team-modal-title">
            <div class="modal-dialog" role="document">
                <div class="modal-content">
                    <form action="{{ url('/qcc_team') }}" method="POST">
                        @csrf
                        <input type="hidden" name="form_context" value="add-team-modal">
                        <div class="modal-header bg-blue">
                            <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                            <h4 class="modal-title" id="add-team-modal-title"><i class="fa fa-users"></i> Tambah Team</h4>
                        </div>
                        <div class="modal-body">
                            <div class="form-group">
                                <label for="add_group_name">Nama Team</label>
                                <input class="form-control" id="add_group_name" name="group_name"
                                    value="{{ old('form_context') === 'add-team-modal' ? old('group_name') : '' }}"
                                    placeholder="Contoh: Productivity Up" required maxlength="255">
                            </div>
                            <div class="form-group">
                                <label for="add_department">Department</label>
                                <input class="form-control" id="add_department" name="department"
                                    value="{{ old('form_context') === 'add-team-modal' ? old('department') : '' }}"
                                    maxlength="255">
                            </div>
                            <div class="form-group" style="margin-bottom:0;">
                                <label for="add_registered_at">Tanggal Registrasi</label>
                                <input class="form-control" type="date" id="add_registered_at" name="registered_at"
                                    value="{{ old('form_context') === 'add-team-modal' ? old('registered_at') : now()->toDateString() }}" required>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-default" data-dismiss="modal">Batal</button>
                            <button class="btn btn-primary" type="submit"><i class="fa fa-save"></i> Simpan Team</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        @foreach($teams as $team)
            @php($modalId = 'edit-team-' . $team->id)
            @php($editingThisTeam = old('form_context') === $modalId)
            <div class="modal fade" id="{{ $modalId }}" tabindex="-1" role="dialog" aria-labelledby="{{ $modalId }}-title">
                <div class="modal-dialog" role="document">
                    <div class="modal-content">
                        <form action="{{ url('/qcc_team/'.$team->id) }}" method="POST">
                            @csrf
                            @method('PUT')
                            <input type="hidden" name="form_context" value="{{ $modalId }}">
                            <div class="modal-header bg-yellow">
                                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                                <h4 class="modal-title" id="{{ $modalId }}-title"><i class="fa fa-pencil"></i> Edit Team</h4>
                            </div>
                            <div class="modal-body">
                                <div class="form-group">
                                    <label for="group_name_{{ $team->id }}">Nama Team</label>
                                    <input class="form-control" id="group_name_{{ $team->id }}" name="group_name"
                                        value="{{ $editingThisTeam ? old('group_name') : $team->group_name }}" required maxlength="255">
                                </div>
                                <div class="form-group">
                                    <label for="department_{{ $team->id }}">Department</label>
                                    <input class="form-control" id="department_{{ $team->id }}" name="department"
                                        value="{{ $editingThisTeam ? old('department') : $team->department }}" maxlength="255">
                                </div>
                                <div class="form-group" style="margin-bottom:0;">
                                    <label for="registered_at_{{ $team->id }}">Tanggal Registrasi</label>
                                    <input class="form-control" type="date" id="registered_at_{{ $team->id }}" name="registered_at"
                                        value="{{ $editingThisTeam ? old('registered_at') : ($team->registered_at ? \Illuminate\Support\Carbon::parse($team->registered_at)->format('Y-m-d') : '') }}" required>
                                </div>
                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-default" data-dismiss="modal">Batal</button>
                                <button class="btn btn-warning" type="submit"><i class="fa fa-save"></i> Simpan Perubahan</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        @endforeach
    </section>
</div>
@endsection

@section('Scripts')
    @if($errors->any())
        <script>
            $(function () {
                var formContext = @json(old('form_context'));
                var modal = formContext ? document.getElementById(formContext) : null;
                if (modal) {
                    $(modal).modal('show');
                }
            });
        </script>
    @endif
@endsection

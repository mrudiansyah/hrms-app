@extends('layouts/admin')

@section('Contents')
<div class="content-wrapper">
    <section class="content-header">
        <h1>QCC <small>Assessor</small></h1>
    </section>
    <section class="content">
        @include('qcc._navigation')
        <div style="height:15px;"></div>
        @include('qcc._messages')

        <div class="box box-primary">
            <div class="box-header with-border">
                <h3 class="box-title">Pilih Periode dan Kategori</h3>
            </div>
            <div class="box-body">
                <form action="{{ url('/qcc_assesor') }}" method="GET" class="form-inline">
                    <div class="form-group">
                        <label for="assessor-period">Periode</label>
                        <select class="form-control" id="assessor-period" name="periode_id" required>
                            <option value="">Pilih periode</option>
                            @foreach($periods as $period)
                                <option value="{{ $period->id }}" @selected((int) $selectedPeriodId === (int) $period->id)>
                                    {{ $period->sugested_thema }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="form-group" style="margin-left:8px;">
                        <label for="assessor-category">Kategori</label>
                        <select class="form-control" id="assessor-category" name="category_id" required>
                            <option value="">Pilih kategori</option>
                            @foreach($categories as $category)
                                <option value="{{ $category->id }}" @selected((int) $selectedCategoryId === (int) $category->id)>
                                    {{ $category->asses_category }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <button class="btn btn-primary" type="submit"><i class="fa fa-search"></i> Tampilkan</button>
                </form>
            </div>
        </div>

        @if($periods->isEmpty() || $categories->isEmpty())
            <div class="callout callout-warning">
                <p>Tambahkan periode QCC dan kategori assessment sebelum mengelola assessor.</p>
            </div>
        @elseif($selectedPeriodId && $selectedCategoryId)
            <div class="box box-primary">
                <div class="box-header with-border">
                    <h3 class="box-title">Assessor per Kategori</h3>
                    <div class="box-tools pull-right">
                        <button class="btn btn-success btn-sm js-assessor-modal" type="button" data-toggle="modal"
                            data-target="#assessorModal" data-create="true">
                            <i class="fa fa-plus"></i> Tambah Assessor
                        </button>
                    </div>
                </div>
                <div class="box-body">
                    <p class="text-muted">
                        Periode: {{ $periods->firstWhere('id', $selectedPeriodId)->sugested_thema }}
                        | Kategori: {{ $categories->firstWhere('id', $selectedCategoryId)->asses_category }}
                    </p>
                    <div class="table-responsive">
                        <table class="table table-bordered table-hover">
                            <thead>
                                <tr class="bg-gray">
                                    <th style="width:60px;">ID</th>
                                    <th style="width:220px;">Nama Assessor</th>
                                    <th>Anggota Assessor</th>
                                    <th style="width:100px;">Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($assessors as $assessor)
                                    @php($members = $membersByAssessor->get($assessor->id, collect()))
                                    @php($availableEmployees = $availableEmployeesByAssessor->get($assessor->id, collect()))
                                    <tr>
                                        <td>{{ $assessor->id }}</td>
                                        <td>{{ $assessor->assesor_name }}</td>
                                        <td>
                                            @if($members->isNotEmpty())
                                                <ul class="list-unstyled" style="margin-bottom:8px;">
                                                    @foreach($members as $member)
                                                        <li style="margin-bottom:4px;">
                                                            <form action="{{ url('/qcc_assesor_member/' . $member->id) }}" method="POST"
                                                                style="display:inline;" onsubmit="return confirm('Hapus anggota assessor ini?')">
                                                                @csrf
                                                                @method('DELETE')
                                                                <button class="btn btn-default btn-xs" type="submit" title="Hapus anggota">
                                                                    <i class="fa fa-trash"></i>
                                                                </button>
                                                            </form>
                                                            <span>
                                                                @if($member->employee_name)
                                                                    {{ $member->employee_name }} ({{ $member->NIK }})
                                                                @else
                                                                    Karyawan tidak aktif/tidak ditemukan (#{{ $member->id_employee }})
                                                                @endif
                                                            </span>
                                                        </li>
                                                    @endforeach
                                                </ul>
                                            @else
                                                <span class="text-muted">Belum ada anggota.</span>
                                            @endif
                                            @if($availableEmployees->isNotEmpty())
                                                <span class="text-muted">Gunakan tombol Tambah Anggota untuk memilih karyawan.</span>
                                            @endif
                                        </td>
                                        <td>
                                            @if($availableEmployees->isNotEmpty())
                                                <button class="btn btn-success btn-xs" title="Tambah Anggota" type="button" data-toggle="modal"
                                                    data-target="#assessorMemberModal-{{ $assessor->id }}">
                                                    <i class="fa fa-user-plus"></i>
                                                </button>
                                            @else
                                                <span class="text-muted">Semua karyawan aktif sudah terdaftar.</span>
                                            @endif
                                            <button class="btn btn-warning btn-xs js-assessor-modal" type="button"
                                                data-toggle="modal" data-target="#assessorModal"
                                                data-id="{{ $assessor->id }}"
                                                data-period-id="{{ $assessor->id_qcc_periode }}"
                                                data-category-id="{{ $assessor->id_asses_category }}"
                                                data-name="{{ $assessor->assesor_name }}">
                                                <i class="fa fa-edit"></i>
                                            </button>
                                            <form action="{{ url('/qcc_assesor/' . $assessor->id) }}" method="POST"
                                                style="display:inline;" onsubmit="return confirm('Hapus assessor? Assessor dengan anggota atau riwayat nilai tidak dapat dihapus.')">
                                                @csrf
                                                @method('DELETE')
                                                <button class="btn btn-danger btn-xs" type="submit">
                                                    <i class="fa fa-trash"></i>
                                                </button>
                                            </form>
                                        </td>
                                    </tr>
                                @empty
                                    <tr><td colspan="4" class="text-center text-muted">Belum ada assessor pada periode dan kategori ini.</td></tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
            @foreach($assessors as $assessor)
                @php($availableEmployees = $availableEmployeesByAssessor->get($assessor->id, collect()))
                @if($availableEmployees->isNotEmpty())
                    <div class="modal fade assessor-member-modal" id="assessorMemberModal-{{ $assessor->id }}"
                        tabindex="-1" role="dialog" aria-labelledby="assessorMemberModalTitle-{{ $assessor->id }}">
                        <div class="modal-dialog" role="document">
                            <div class="modal-content">
                                <form action="{{ url('/qcc_assesor/' . $assessor->id . '/member') }}" method="POST">
                                    @csrf
                                    <input type="hidden" name="_form_type" value="member">
                                    <input type="hidden" name="_assessor_id" value="{{ $assessor->id }}">
                                    <div class="modal-header">
                                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                            <span aria-hidden="true">&times;</span>
                                        </button>
                                        <h4 class="modal-title" id="assessorMemberModalTitle-{{ $assessor->id }}">
                                            Tambah Anggota — {{ $assessor->assesor_name }}
                                        </h4>
                                    </div>
                                    <div class="modal-body">
                                        <div class="form-group">
                                            <label for="assessor-employee-{{ $assessor->id }}">Karyawan aktif</label>
                                            <select class="form-control js-searchable-employee"
                                                id="assessor-employee-{{ $assessor->id }}"
                                                name="id_employee" style="width:100%;" required>
                                                <option value="">Cari nama atau NIK karyawan</option>
                                                @foreach($availableEmployees as $employee)
                                                    <option value="{{ $employee->id }}"
                                                        @selected((string) old('_assessor_id') === (string) $assessor->id
                                                            && (string) old('id_employee') === (string) $employee->id)>
                                                        {{ $employee->employee_name }} ({{ $employee->NIK }})
                                                    </option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>
                                    <div class="modal-footer">
                                        <button type="button" class="btn btn-default" data-dismiss="modal">Batal</button>
                                        <button class="btn btn-primary" type="submit">
                                            <i class="fa fa-plus"></i> Tambah
                                        </button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                @endif
            @endforeach
        @endif
    </section>
</div>

<div class="modal fade" id="assessorModal" tabindex="-1" role="dialog" aria-labelledby="assessorModalTitle">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <form method="POST" action="{{ url('/qcc_assesor') }}" data-create-url="{{ url('/qcc_assesor') }}" data-update-url="{{ url('/qcc_assesor') }}">
                @csrf
                <input type="hidden" name="_method" value="PUT" disabled>
                <input type="hidden" name="_form_type" value="assessor">
                <input type="hidden" name="_entity_id">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                    <h4 class="modal-title" id="assessorModalTitle" data-entity="Assessor">Assessor</h4>
                </div>
                <div class="modal-body">
                    <div class="form-group">
                        <label for="assessor-modal-period">Periode QCC</label>
                        <select class="form-control" id="assessor-modal-period" name="id_qcc_periode" required>
                            @foreach($periods as $period)
                                <option value="{{ $period->id }}">{{ $period->sugested_thema }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="assessor-modal-category">Kategori</label>
                        <select class="form-control" id="assessor-modal-category" name="id_asses_category" required>
                            @foreach($categories as $category)
                                <option value="{{ $category->id }}">{{ $category->asses_category }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="assessor-modal-name">Nama assessor / kelompok assessor</label>
                        <input class="form-control" id="assessor-modal-name" name="assesor_name"
                            value="{{ old('assesor_name') }}" maxlength="50" required>
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
@endsection

@section('Scripts')
<script>
    $(function () {
        $('.js-assessor-modal').on('click', function () {
            var button = $(this);
            var form = $('#assessorModal form');
            var isEdit = Boolean(button.attr('data-id'));

            form[0].reset();
            form.attr('action', isEdit
                ? form.data('update-url') + '/' + button.attr('data-id')
                : form.data('create-url'));
            form.find('[name="_entity_id"]').val(isEdit ? button.attr('data-id') : '');
            form.find('[name="_method"]').prop('disabled', !isEdit);
            form.find('[name="id_qcc_periode"]').val(button.attr('data-period-id') || '{{ $selectedPeriodId }}');
            form.find('[name="id_asses_category"]').val(button.attr('data-category-id') || '{{ $selectedCategoryId }}');
            form.find('[name="assesor_name"]').val(button.attr('data-name') || '');
            $('#assessorModalTitle').text((isEdit ? 'Edit ' : 'Tambah ') + 'Assessor');
        });

        $('.assessor-member-modal').on('shown.bs.modal', function () {
            var modal = $(this);
            var select = modal.find('.js-searchable-employee');
            if (!select.hasClass('select2-hidden-accessible')) {
                select.select2({
                    dropdownParent: modal,
                    width: '100%',
                    placeholder: 'Cari nama atau NIK karyawan',
                    language: {
                        noResults: function () {
                            return 'Karyawan tidak ditemukan';
                        },
                        searching: function () {
                            return 'Mencari...';
                        }
                    }
                });
            }
        });

        @if($errors->any() && old('_form_type') === 'assessor')
            var form = $('#assessorModal form');
            var id = @json(old('_entity_id'));
            if (id) {
                form.attr('action', form.data('update-url') + '/' + id);
                form.find('[name="_method"]').prop('disabled', false);
            }
            $('#assessorModal').modal('show');
        @elseif($errors->any() && old('_form_type') === 'member' && old('_assessor_id'))
            $('#assessorMemberModal-{{ old('_assessor_id') }}').modal('show');
        @endif
    });
</script>
@endsection

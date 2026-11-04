@extends('layouts/admin')

@section('Contents')
<div class="content-wrapper">
    <section class="content-header">
        <h1>QCC <small>Assessment</small></h1>
    </section>
    <section class="content">
        @include('qcc._navigation')
        <div style="height:15px;"></div>
        @include('qcc._messages')

        <div class="box box-primary">
            <div class="box-header with-border">
                <h3 class="box-title">Kategori Assessment</h3>
                <div class="box-tools pull-right">
                    <button class="btn btn-default btn-sm" type="button" data-toggle="collapse" data-target="#assessmentCategories">
                        <i class="fa fa-list"></i> Kelola Kategori ({{ $categories->count() }})
                    </button>
                    <button class="btn btn-default btn-sm js-assessment-modal" type="button"
                        data-toggle="modal" data-target="#categoryModal" data-create="true">
                        <i class="fa fa-plus"></i> Tambah Kategori
                    </button>
                </div>
            </div>
            <div class="box-body">
                <div class="row">
                    <div class="col-md-6">
                        <form method="GET" action="{{ url('/qcc_assesment') }}">
                            <div class="form-group">
                                <label for="assessment-category-filter">Pilih kategori untuk dikelola</label>
                                <div class="input-group">
                                    <select class="form-control" id="assessment-category-filter" name="category_id">
                                        <option value="">Pilih kategori</option>
                                        @foreach($categories as $category)
                                            <option value="{{ $category->id }}" {{ (string) $selectedCategoryId === (string) $category->id ? 'selected' : '' }}>
                                                {{ $category->asses_category }}
                                            </option>
                                        @endforeach
                                    </select>
                                    <span class="input-group-btn">
                                        <button class="btn btn-primary" type="submit">Tampilkan</button>
                                    </span>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>

                <div class="collapse" id="assessmentCategories">
                    <div class="table-responsive">
                        <table class="table table-bordered table-hover">
                            <thead>
                                <tr class="bg-gray">
                                    <th style="width:80px;">ID</th>
                                    <th>Nama Kategori</th>
                                    <th style="width:150px;">Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($categories as $category)
                                    <tr>
                                        <td>{{ $category->id }}</td>
                                        <td>{{ $category->asses_category }}</td>
                                        <td>
                                            <button class="btn btn-warning btn-xs js-assessment-modal" type="button"
                                                data-toggle="modal" data-target="#categoryModal"
                                                data-id="{{ $category->id }}"
                                                data-name="{{ $category->asses_category }}">
                                                <i class="fa fa-edit"></i> Edit
                                            </button>
                                            <form action="{{ url('/qcc_assesment/category/' . $category->id) }}" method="POST"
                                                style="display:inline;" onsubmit="return confirm('Hapus kategori ini? Kategori yang masih digunakan tidak dapat dihapus.')">
                                                @csrf
                                                @method('DELETE')
                                                <button class="btn btn-danger btn-xs" type="submit" title="Hapus kategori">
                                                    <i class="fa fa-trash"></i>
                                                </button>
                                            </form>
                                        </td>
                                    </tr>
                                @empty
                                    <tr><td colspan="3" class="text-center text-muted">Belum ada kategori.</td></tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        @if($selectedCategoryId !== null)
            @php($selectedCategory = $categories->firstWhere('id', $selectedCategoryId))
            <div class="box box-primary">
                <div class="box-header with-border">
                    <h3 class="box-title">
                        Assessment — {{ $selectedCategory->asses_category }}
                    </h3>
                    <div class="box-tools pull-right">
                        <button class="btn btn-success btn-sm js-assessment-modal" type="button"
                            data-toggle="modal" data-target="#assessmentModal"
                            data-create="true" data-category-id="{{ $selectedCategoryId }}">
                            <i class="fa fa-plus"></i> Tambah Assessment
                        </button>
                    </div>
                </div>
                <div class="box-body">
                    @php($categoryAssessments = $assessmentsByCategory->get($selectedCategoryId, collect()))
                    @forelse($categoryAssessments as $assessment)
                        @php($assessmentDetails = $detailsByAssessment->get($assessment->id, collect()))
                        <div class="panel panel-primary">
                            <div class="panel-heading">
                                <div class="row">
                                    <div class="col-sm-8">
                                        <strong>{{ $assessment->index_assesment ? $assessment->index_assesment . '. ' : '' }}{{ $assessment->asses_description }}</strong>
                                        <span class="label label-info">Bobot: {{ $assessment->bobot }}</span>
                                    </div>
                                    <div class="col-sm-4 text-right">
                                        <button class="btn btn-warning btn-xs js-assessment-modal" type="button"
                                            data-toggle="modal" data-target="#assessmentModal"
                                            data-id="{{ $assessment->id }}"
                                            data-category-id="{{ $assessment->id_asses_category }}"
                                            data-index="{{ $assessment->index_assesment }}"
                                            data-description="{{ $assessment->asses_description }}"
                                            data-weight="{{ $assessment->bobot }}">
                                            <i class="fa fa-edit"></i> Edit
                                        </button>
                                        <form action="{{ url('/qcc_assesment/' . $assessment->id) }}" method="POST"
                                            style="display:inline;" onsubmit="return confirm('Hapus assessment ini? Assessment yang masih memiliki detail tidak dapat dihapus.')">
                                            @csrf
                                            @method('DELETE')
                                            <button class="btn btn-danger btn-xs" type="submit"><i class="fa fa-trash"></i></button>
                                        </form>
                                        <button class="btn btn-default btn-xs js-assessment-modal" type="button"
                                            data-toggle="modal" data-target="#detailModal"
                                            data-create="true" data-assessment-id="{{ $assessment->id }}">
                                            <i class="fa fa-plus"></i> Tambah Detail
                                        </button>
                                    </div>
                                </div>
                            </div>
                            <div class="panel-body">
                                @forelse($assessmentDetails as $detail)
                                    @php($detailCriteria = $criteriaByDetail->get($detail->id, collect()))
                                    <div class="panel panel-default">
                                        <div class="panel-heading">
                                            <div class="row">
                                                <div class="col-sm-8">
                                                    <strong>{{ $detail->asses_description_detail }}</strong>
                                                    <span class="label label-default">Bobot: {{ $detail->bobot }}</span>
                                                </div>
                                                <div class="col-sm-4 text-right">
                                                    <button class="btn btn-warning btn-xs js-assessment-modal" type="button"
                                                        data-toggle="modal" data-target="#detailModal"
                                                        data-id="{{ $detail->id }}"
                                                        data-assessment-id="{{ $detail->id_assesment }}"
                                                        data-description="{{ $detail->asses_description_detail }}"
                                                        data-weight="{{ $detail->bobot }}">
                                                        <i class="fa fa-edit"></i>
                                                    </button>
                                                    <form action="{{ url('/qcc_assesment/detail/' . $detail->id) }}" method="POST"
                                                        style="display:inline;" onsubmit="return confirm('Hapus detail ini? Detail yang masih memiliki criteria tidak dapat dihapus.')">
                                                        @csrf
                                                        @method('DELETE')
                                                        <button class="btn btn-danger btn-xs" type="submit"><i class="fa fa-trash"></i></button>
                                                    </form>
                                                    <button class="btn btn-success btn-xs js-assessment-modal" type="button"
                                                        data-toggle="modal" data-target="#criteriaModal"
                                                        data-create="true" data-detail-id="{{ $detail->id }}">
                                                        <i class="fa fa-plus"></i> Criteria
                                                    </button>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="table-responsive">
                                            <table class="table table-bordered table-condensed" style="margin-bottom:0;">
                                                <thead>
                                                    <tr class="bg-gray">
                                                        <th>Criteria</th>
                                                        <th style="width:110px;">Point</th>
                                                        <th style="width:110px;">Aksi</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    @forelse($detailCriteria as $item)
                                                        <tr>
                                                            <td>{{ $item->criteria_description }}</td>
                                                            <td>{{ $item->point }}</td>
                                                            <td>
                                                                <button class="btn btn-warning btn-xs js-assessment-modal" type="button"
                                                                    data-toggle="modal" data-target="#criteriaModal"
                                                                    data-id="{{ $item->id }}"
                                                                    data-detail-id="{{ $item->id_assesment_detail }}"
                                                                    data-description="{{ $item->criteria_description }}"
                                                                    data-point="{{ $item->point }}">
                                                                    <i class="fa fa-edit"></i>
                                                                </button>
                                                                <form action="{{ url('/qcc_assesment/criteria/' . $item->id) }}" method="POST"
                                                                    style="display:inline;" onsubmit="return confirm('Hapus criteria ini?')">
                                                                    @csrf
                                                                    @method('DELETE')
                                                                    <button class="btn btn-danger btn-xs" type="submit"><i class="fa fa-trash"></i></button>
                                                                </form>
                                                            </td>
                                                        </tr>
                                                    @empty
                                                        <tr><td colspan="3" class="text-center text-muted">Belum ada criteria.</td></tr>
                                                    @endforelse
                                                </tbody>
                                            </table>
                                        </div>
                                    </div>
                                @empty
                                    <p class="text-muted">Belum ada detail untuk assessment ini.</p>
                                @endforelse
                            </div>
                        </div>
                    @empty
                        <p class="text-center text-muted">Belum ada assessment untuk kategori ini. Tambahkan assessment untuk memulai.</p>
                    @endforelse
                </div>
            </div>
        @else
            <div class="callout callout-info">
                <h4>Mulai dari kategori</h4>
                <p>Pilih kategori di atas atau tambahkan kategori baru untuk mengelola struktur Assessment → Detail → Criteria.</p>
            </div>
        @endif
    </section>
</div>

<div class="modal fade" id="categoryModal" tabindex="-1" role="dialog" aria-labelledby="categoryModalTitle">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <form method="POST" action="{{ url('/qcc_assesment/category') }}" data-create-url="{{ url('/qcc_assesment/category') }}" data-update-url="{{ url('/qcc_assesment/category') }}">
                @csrf
                <input type="hidden" name="_method" value="PUT" disabled>
                <input type="hidden" name="_form_type" value="category">
                <input type="hidden" name="_entity_id">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                    <h4 class="modal-title" id="categoryModalTitle" data-entity="Kategori">Kategori</h4>
                </div>
                <div class="modal-body">
                    <div class="form-group">
                        <label for="category-name">Nama kategori</label>
                        <input class="form-control" id="category-name" name="asses_category" value="{{ old('asses_category') }}" maxlength="50" required>
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

<div class="modal fade" id="assessmentModal" tabindex="-1" role="dialog" aria-labelledby="assessmentModalTitle">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <form method="POST" action="{{ url('/qcc_assesment') }}" data-create-url="{{ url('/qcc_assesment') }}" data-update-url="{{ url('/qcc_assesment') }}">
                @csrf
                <input type="hidden" name="_method" value="PUT" disabled>
                <input type="hidden" name="_form_type" value="assessment">
                <input type="hidden" name="_entity_id">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                    <h4 class="modal-title" id="assessmentModalTitle" data-entity="Assessment">Assessment</h4>
                </div>
                <div class="modal-body">
                    <div class="form-group">
                        <label for="assessment-category">Kategori</label>
                        <select class="form-control" id="assessment-category" name="id_asses_category" required>
                            <option value="">Pilih kategori</option>
                            @foreach($categories as $category)
                                <option value="{{ $category->id }}" {{ (string) old('id_asses_category') === (string) $category->id ? 'selected' : '' }}>{{ $category->asses_category }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="assessment-index">Urutan Assessment (A-Z)</label>
                        <input class="form-control text-uppercase" type="text" id="assessment-index"
                            name="index_assesment" value="{{ old('index_assesment') }}"
                            maxlength="1" pattern="[A-Z]" title="Masukkan satu huruf kapital dari A sampai Z" required>
                    </div>
                    <div class="form-group">
                        <label for="assessment-description">Deskripsi assessment</label>
                        <textarea class="form-control" id="assessment-description" name="asses_description" rows="3" required>{{ old('asses_description') }}</textarea>
                    </div>
                    <div class="form-group">
                        <label for="assessment-weight">Bobot</label>
                        <input class="form-control" type="number" id="assessment-weight" name="bobot" value="{{ old('bobot') }}" min="0" step="any" required>
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

<div class="modal fade" id="detailModal" tabindex="-1" role="dialog" aria-labelledby="detailModalTitle">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <form method="POST" action="{{ url('/qcc_assesment/detail') }}" data-create-url="{{ url('/qcc_assesment/detail') }}" data-update-url="{{ url('/qcc_assesment/detail') }}">
                @csrf
                <input type="hidden" name="_method" value="PUT" disabled>
                <input type="hidden" name="_form_type" value="detail">
                <input type="hidden" name="_entity_id">
                <input type="hidden" name="id_assesment" value="{{ old('id_assesment') }}">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                    <h4 class="modal-title" id="detailModalTitle" data-entity="Detail Assessment">Detail Assessment</h4>
                </div>
                <div class="modal-body">
                    <div class="form-group">
                        <label for="detail-description">Deskripsi detail</label>
                        <textarea class="form-control" id="detail-description" name="asses_description_detail" rows="3" required>{{ old('asses_description_detail') }}</textarea>
                    </div>
                    <div class="form-group">
                        <label for="detail-weight">Bobot</label>
                        <input class="form-control" type="number" id="detail-weight" name="bobot" value="{{ old('bobot') }}" min="0" step="any" required>
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

<div class="modal fade" id="criteriaModal" tabindex="-1" role="dialog" aria-labelledby="criteriaModalTitle">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <form method="POST" action="{{ url('/qcc_assesment/criteria') }}" data-create-url="{{ url('/qcc_assesment/criteria') }}" data-update-url="{{ url('/qcc_assesment/criteria') }}">
                @csrf
                <input type="hidden" name="_method" value="PUT" disabled>
                <input type="hidden" name="_form_type" value="criteria">
                <input type="hidden" name="_entity_id">
                <input type="hidden" name="id_assesment_detail" value="{{ old('id_assesment_detail') }}">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                    <h4 class="modal-title" id="criteriaModalTitle" data-entity="Criteria">Criteria Assessment</h4>
                </div>
                <div class="modal-body">
                    <div class="form-group">
                        <label for="criteria-description">Deskripsi criteria</label>
                        <textarea class="form-control" id="criteria-description" name="criteria_description" rows="3" required>{{ old('criteria_description') }}</textarea>
                    </div>
                    <div class="form-group">
                        <label for="criteria-point">Point</label>
                        <input class="form-control" type="number" id="criteria-point" name="point" value="{{ old('point') }}" min="0" step="any" required>
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
        $('.js-assessment-modal').on('click', function () {
            var button = $(this);
            var modal = $(button.data('target'));
            var form = modal.find('form');
            var isEdit = Boolean(button.attr('data-id'));

            form[0].reset();
            form.attr('action', isEdit
                ? form.data('update-url') + '/' + button.attr('data-id')
                : form.data('create-url'));
            form.find('[name="_entity_id"]').val(isEdit ? button.attr('data-id') : '');
            form.find('[name="_method"]').prop('disabled', !isEdit);
            modal.find('.modal-title').text((isEdit ? 'Edit ' : 'Tambah ') + modal.find('.modal-title').attr('data-entity'));

            if (modal.attr('id') === 'categoryModal') {
                form.find('[name="asses_category"]').val(button.attr('data-name') || '');
            } else if (modal.attr('id') === 'assessmentModal') {
                form.find('[name="id_asses_category"]').val(button.attr('data-category-id') || '');
                form.find('[name="index_assesment"]').val(button.attr('data-index') || '');
                form.find('[name="asses_description"]').val(button.attr('data-description') || '');
                form.find('[name="bobot"]').val(button.attr('data-weight') || '');
            } else if (modal.attr('id') === 'detailModal') {
                form.find('[name="id_assesment"]').val(button.attr('data-assessment-id') || '');
                form.find('[name="asses_description_detail"]').val(button.attr('data-description') || '');
                form.find('[name="bobot"]').val(button.attr('data-weight') || '');
            } else {
                form.find('[name="id_assesment_detail"]').val(button.attr('data-detail-id') || '');
                form.find('[name="criteria_description"]').val(button.attr('data-description') || '');
                form.find('[name="point"]').val(button.attr('data-point') || '');
            }
        });

        $('#assessment-index').on('input', function () {
            this.value = this.value.toUpperCase();
        });

        @if($errors->any() && old('_form_type'))
            var validationModal = $('#{{ old('_form_type') }}Modal');
            var validationForm = validationModal.find('form');
            var validationEntityId = @json(old('_entity_id'));
            if (validationEntityId) {
                validationForm.attr('action', validationForm.data('update-url') + '/' + validationEntityId);
                validationForm.find('[name="_method"]').prop('disabled', false);
            }
            validationModal.modal('show');
        @endif
    });
</script>
@endsection

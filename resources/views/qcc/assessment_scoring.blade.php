@extends('layouts/admin')

@section('Contents')
<div class="content-wrapper">
    <section class="content-header">
        <h1>QCC <small>Penilaian Assessment</small></h1>
    </section>
    <section class="content">
        @include('qcc._navigation')
        <div style="height:15px;"></div>
        @include('qcc._messages')

        <div class="box box-primary">
            <div class="box-header with-border">
                <h3 class="box-title">Pilih Periode, Kategori, Team, dan Assessor</h3>
            </div>
            <div class="box-body">
                <form action="{{ url('/qcc_assesment_team') }}" method="GET">
                    <div class="row">
                        <div class="col-md-3">
                            <div class="form-group">
                                <label for="score-period">Periode</label>
                                <select class="form-control" id="score-period" name="periode_id" required>
                                    <option value="">Pilih periode</option>
                                    @foreach($periods as $period)
                                        <option value="{{ $period->id }}" @selected((int) $selectedPeriodId === (int) $period->id)>
                                            {{ $period->sugested_thema }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                <label for="score-category">Kategori</label>
                                <select class="form-control" id="score-category" name="category_id" required>
                                    <option value="">Pilih kategori</option>
                                    @foreach($categories as $category)
                                        <option value="{{ $category->id }}" @selected((int) $selectedCategoryId === (int) $category->id)>
                                            {{ $category->asses_category }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                <label for="score-team">Team QCC</label>
                                <select class="form-control" id="score-team" name="team_period_id" required>
                                    <option value="">Pilih team</option>
                                    @foreach($teamPeriods as $teamPeriod)
                                        <option value="{{ $teamPeriod->id }}" @selected((int) $selectedTeamPeriodId === (int) $teamPeriod->id)>
                                            {{ $teamPeriod->group_name }}{{ $teamPeriod->department ? ' — ' . $teamPeriod->department : '' }}{{ $teamPeriod->theme_team ? ' — ' . $teamPeriod->theme_team : '' }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                <label for="score-assessor">Assessor</label>
                                <select class="form-control" id="score-assessor" name="assesor_id" required>
                                    <option value="">Pilih assessor</option>
                                    @foreach($assessors as $assessor)
                                        <option value="{{ $assessor->id }}" @selected((int) $selectedAssessorId === (int) $assessor->id)>
                                            {{ $assessor->assesor_name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                    </div>
                    <button class="btn btn-primary" type="submit"><i class="fa fa-search"></i> Tampilkan</button>
                    <a class="btn btn-default"
                        href="{{ url('/qcc_assesor?periode_id=' . $selectedPeriodId . '&category_id=' . $selectedCategoryId) }}">
                        <i class="fa fa-users"></i> Kelola Assessor
                    </a>
                </form>
            </div>
        </div>

        @if($selectedTeamPeriod && $selectedAssessor)
            @php($hasScorableDetails = $details->contains(fn ($detail) => $criteriaByDetail->get($detail->id, collect())->isNotEmpty()))
            <div class="box box-primary">
                <div class="box-header with-border">
                    <div class="box-tools pull-right">
                        <form id="score-form" action="{{ url('/qcc_assesment_team') }}" method="POST">
                            @csrf
                            <input type="hidden" name="id_periode_team" value="{{ $selectedTeamPeriodId }}">
                            <input type="hidden" name="id_assesor" value="{{ $selectedAssessorId }}">
                            <button class="btn btn-primary" type="submit" @disabled(!$hasScorableDetails)>
                                <i class="fa fa-save"></i> Simpan Penilaian
                            </button>
                        </form>
                    </div>
                    <h3 class="box-title">
                        {{ $selectedTeamPeriod->group_name }}
                        @if($selectedTeamPeriod->department)
                            <small>— {{ $selectedTeamPeriod->department }}</small>
                        @endif
                        @if($selectedTeamPeriod->theme_team)
                            <small>— {{ $selectedTeamPeriod->theme_team }}</small>
                        @endif
                    </h3>
                    <p class="text-muted" style="margin-top:8px;">
                        {{ $selectedPeriod->sugested_thema }} · {{ $selectedCategory->asses_category }}
                        · Assessor: {{ $selectedAssessor->assesor_name }}
                    </p>
                </div>
                <div class="box-body table-responsive">
                    <table class="table table-bordered table-hover">
                        <thead>
                            <tr class="bg-gray">
                                <th style="width:25%;">Detail</th>
                                <th>Criteria (point menjadi nilai)</th>
                                <th style="width:25%;">Remark (opsional)</th>
                                <th style="width:50px;">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($details->groupBy('id_assesment') as $assessmentId => $assessmentDetails)
                                @php($assessment = $assessmentDetails->first())
                                @php($subtotal = $assessmentDetails->sum(fn ($detail) => (float) ($scoresByDetail->get($detail->id)->point ?? 0)))
                                <tr class="active">
                                    <th colspan="3">
                                        <div style="display:flex;justify-content:space-between;align-items:center;">
                                            <span>{{ $assessment->index_assesment ? $assessment->index_assesment . '. ' : '' }}{{ $assessment->asses_description }}</span>
                                            <span>Subtotal:</span>
                                        </div>
                                    </th>
                                    <th colspan="1">
                                        <div style="text-align:center;">
                                            <span data-assessment-subtotal="{{ $assessmentId }}">{{ $subtotal }}</span>
                                        </div>
                                    </th>
                                </tr>
                                @foreach($assessmentDetails as $detail)
                                    @php($criteria = $criteriaByDetail->get($detail->id, collect()))
                                    @php($score = $scoresByDetail->get($detail->id))
                                    <tr>
                                        <td>
                                            {{ $detail->asses_description_detail }}
                                            @if($detail->bobot !== null)
                                                <br><small class="text-muted">Bobot detail: {{ $detail->bobot }}</small>
                                            @endif
                                        </td>
                                        <td>
                                            @if($criteria->isNotEmpty())
                                                <select class="form-control js-score-criteria"
                                                    name="scores[{{ $detail->id }}][id_assesment_criteria]"
                                                    data-assessment-id="{{ $assessmentId }}"
                                                    form="score-form" required>
                                                    <option value="">Pilih satu criteria</option>
                                                    @foreach($criteria as $item)
                                                        <option value="{{ $item->id }}" data-point="{{ $item->point }}"
                                                            @selected((string) old('scores.' . $detail->id . '.id_assesment_criteria', $score->id_assesment_criteria ?? '') === (string) $item->id)>
                                                            {{ $item->point }} poin — {{ $item->criteria_description }}
                                                        </option>
                                                    @endforeach
                                                </select>
                                                <small class="text-muted">Nilai mengikuti point criteria yang dipilih.</small>
                                            @else
                                                <span class="text-warning">Belum ada criteria untuk detail ini.</span>
                                            @endif
                                        </td>
                                        <td>
                                            <textarea class="form-control"
                                                name="scores[{{ $detail->id }}][remark]" form="score-form" rows="2"
                                                placeholder="Catatan assessor (opsional)">{{ old('scores.' . $detail->id . '.remark', $score->remark ?? '') }}</textarea>
                                        </td>
                                        <td style="text-align:center;">
                                            @if($score)
                                                <form action="{{ url('/qcc_assesment_team/' . $score->id) }}" method="POST"
                                                    onsubmit="return confirm('Hapus nilai untuk detail ini?')">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button class="btn btn-danger btn-xs" type="submit">
                                                        <i class="fa fa-trash"></i>
                                                    </button>
                                                </form>
                                            @endif
                                        </td>
                                    </tr>
                                @endforeach
                            @empty
                                <tr><td colspan="4" class="text-center text-muted">Belum ada assessment detail untuk kategori ini.</td></tr>
                            @endforelse
                        </tbody>
                        @if($details->isNotEmpty())
                            @php($totalPoint = $details->sum(fn ($detail) => (float) ($scoresByDetail->get($detail->id)->point ?? 0)))
                            <tfoot>
                                <tr class="bg-gray">
                                    <th colspan="3" class="text-right">Total Point</th>
                                    <th class="text-center"><span id="score-total">{{ $totalPoint }}</span></th>
                                </tr>
                            </tfoot>
                        @endif
                    </table>
                </div>
            </div>
        @elseif($selectedPeriodId && $selectedCategoryId)
            <div class="callout callout-info">
                @if($teamPeriods->isEmpty())
                    <p>Belum ada team yang terdaftar pada periode ini.</p>
                @elseif($assessors->isEmpty())
                    <p>Belum ada assessor untuk periode dan kategori ini. Tambahkan assessor terlebih dahulu.</p>
                @else
                    <p>Pilih team dan assessor untuk mulai mengisi penilaian.</p>
                @endif
            </div>
        @else
            <div class="callout callout-info">
                <p>Pilih periode dan kategori assessment untuk memulai penilaian.</p>
            </div>
        @endif
    </section>
</div>
@endsection

@section('Scripts')
<script>
    $(function () {
        function updateScoreTotals() {
            var totalsByAssessment = {};
            var total = 0;

            $('.js-score-criteria').each(function () {
                var point = parseFloat($(this).find('option:selected').data('point')) || 0;
                var assessmentId = $(this).data('assessment-id');
                totalsByAssessment[assessmentId] = (totalsByAssessment[assessmentId] || 0) + point;
                total += point;
            });

            $('[data-assessment-subtotal]').each(function () {
                var subtotal = totalsByAssessment[$(this).data('assessment-subtotal')] || 0;
                $(this).text(Number(subtotal.toFixed(2)));
            });
            $('#score-total').text(Number(total.toFixed(2)));
        }

        $('.js-score-criteria').on('change', updateScoreTotals);
        updateScoreTotals();

        $('#score-form').on('submit', function () {
            $(this).find('button[type="submit"]')
                .prop('disabled', true)
                .html('<i class="fa fa-spinner fa-spin"></i> Menyimpan...');
        });

        $('#score-period, #score-category').on('change', function () {
            var form = $('#score-period').closest('form');
            if (this.id === 'score-period') {
                $('#score-team').val('');
            }
            $('#score-assessor').val('');
            form[0].submit();
        });
    });
</script>
@endsection

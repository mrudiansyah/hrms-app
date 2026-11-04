@extends('layouts/admin')

@section('Contents')
<style>
    .qcc-summary-table thead th {
        color: #fff;
        border-color: #315a7d !important;
        vertical-align: middle;
    }

    .qcc-summary-table thead tr:first-child th {
        background-color: #4b708d !important;
        background-image: none !important;
    }

    .qcc-summary-table thead tr:nth-child(2) th {
        background-color: #4b708d !important;
        background-image: none !important;
        color: #fff;
        border-color: #3d617c !important;
    }

    .qcc-summary-table thead th[rowspan="2"]:last-child {
        background-color: #4b708d !important;
        background-image: none !important;
        border-color: #3d617c !important;
    }

    .qcc-summary-table tbody tr:hover {
        background-color: #f0f8fc;
    }
</style>
<div class="content-wrapper">
    <section class="content-header">
        <h1>QCC <small>Summary Penilaian</small></h1>
    </section>
    <section class="content">
        @include('qcc._navigation')
        <div style="height:15px;"></div>
        @include('qcc._messages')

        <div class="box box-primary">
            <div class="box-header with-border">
                <h3 class="box-title">Pilih Periode dan Kategori Assessment</h3>
            </div>
            <div class="box-body">
                <form action="{{ url('/qcc_assesment_summary') }}" method="GET">
                    <div class="row">
                        <div class="col-md-5">
                            <div class="form-group">
                                <label for="summary-period">Periode</label>
                                <select class="form-control" id="summary-period" name="periode_id" required>
                                    <option value="">Pilih periode</option>
                                    @foreach($periods as $period)
                                        <option value="{{ $period->id }}" @selected((int) $selectedPeriodId === (int) $period->id)>
                                            {{ $period->sugested_thema }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="col-md-5">
                            <div class="form-group">
                                <label for="summary-category">Kategori</label>
                                <select class="form-control" id="summary-category" name="category_id" required>
                                    <option value="">Pilih kategori</option>
                                    <option value="all" @selected($allCategories)>Semua Kategori</option>
                                    @foreach($categories as $category)
                                        <option value="{{ $category->id }}" @selected((int) $selectedCategoryId === (int) $category->id)>
                                            {{ $category->asses_category }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                    </div>
                    <button class="btn btn-primary" type="submit"><i class="fa fa-search"></i> Tampilkan Summary</button>
                </form>
            </div>
        </div>

        @if($selectedPeriod && ($selectedCategory || $allCategories))
            <div class="box box-primary">
                <div class="box-header with-border">
                    <h3 class="box-title">
                        {{ $selectedPeriod->sugested_thema }} — {{ $allCategories ? 'Semua Kategori' : $selectedCategory->asses_category }}
                    </h3>
                    <p class="text-muted" style="margin-top:8px;margin-bottom:0;">
                        Nilai per Assessment dan total merupakan rata-rata nilai assessor yang sudah menilai.
                    </p>
                </div>
                <div class="box-body table-responsive">
                    @if($assessments->isEmpty())
                        <p class="text-center text-muted">Belum ada Assessment pada kategori ini.</p>
                    @elseif($summaryRows->isEmpty())
                        <p class="text-center text-muted">Belum ada team yang terdaftar pada periode ini.</p>
                    @else
                        <table class="table table-bordered table-hover qcc-summary-table">
                            <thead>
                                <tr class="bg-gray">
                                    <th rowspan="2" style="width:60px;vertical-align:middle;">No</th>
                                    <th rowspan="2" style="min-width:180px;vertical-align:middle;">Nama Team</th>
                                    <th rowspan="2" style="min-width:150px;vertical-align:middle;">Department</th>
                                    @foreach($assessments->groupBy('id_asses_category') as $categoryAssessments)
                                        <th colspan="{{ $categoryAssessments->count() }}" class="text-center">
                                            {{ $categoryAssessments->first()->asses_category }}
                                        </th>
                                    @endforeach
                                    <th rowspan="2" style="min-width:120px;vertical-align:middle;text-align:center;">Total Point</th>
                                </tr>
                                <tr class="bg-gray">
                                    @foreach($assessments as $assessment)
                                        <th style="min-width:130px;text-align:center;">{{ $assessment->index_assesment }}</th>
                                    @endforeach
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($summaryRows as $index => $row)
                                    <tr>
                                        <td>{{ $index + 1 }}</td>
                                        <td>{{ $row->team->group_name }}</td>
                                        <td>{{ $row->team->department ?: '-' }}</td>
                                        @foreach($assessments as $assessment)
                                            <td class="text-center">
                                                @if($row->assessmentPoints[$assessment->id] !== null)
                                                    {{ number_format($row->assessmentPoints[$assessment->id], 2) }}
                                                @else
                                                    -
                                                @endif
                                            </td>
                                        @endforeach
                                        <td class="text-center">
                                            {{ $row->totalPoint === null ? '-' : number_format($row->totalPoint, 2) }}
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    @endif
                </div>
            </div>
        @endif
    </section>
</div>
@endsection

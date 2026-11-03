@extends('layouts/admin')

@section('Contents')
<div class="content-wrapper">
    <section class="content-header">
        <h1>QCC <small>Master Steps</small></h1>
    </section>
    <section class="content">
        @include('qcc._navigation')
        <div style="height:15px;"></div>
        @include('qcc._messages')

        <div class="box box-primary">
            <div class="box-header with-border">
                <h3 class="box-title">Daftar Step Improvement</h3>
                <p class="text-muted">Urutan 1–8 menentukan durasi default 2, 1, 3, 3, 6, 3, 2, dan 1 minggu.</p>
            </div>
            <div class="box-body table-responsive">
                <table class="table table-bordered table-hover">
                    <thead>
                        <tr class="bg-gray">
                            <th style="width:110px;">Urutan</th>
                            <th>Deskripsi Step</th>
                            <th style="width:180px;">Diperbarui</th>
                            <th style="width:180px;">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($steps as $step)
                            @php($formId = 'update-step-' . $step->id)
                            <tr>
                                <td>
                                    @if($isCommittee)
                                        <select class="form-control" name="step_sequance" form="{{ $formId }}" required>
                                            @foreach(range(1, 8) as $sequence)
                                                <option value="{{ $sequence }}" {{ (int) $step->step_sequance === $sequence ? 'selected' : '' }}>
                                                    Step {{ $sequence }}
                                                </option>
                                            @endforeach
                                        </select>
                                    @else
                                        Step {{ $step->step_sequance }}
                                    @endif
                                </td>
                                <td>
                                    @if($isCommittee)
                                        <input class="form-control" name="step_description" form="{{ $formId }}"
                                            value="{{ $step->step_description }}" maxlength="255" required>
                                    @else
                                        {{ $step->step_description }}
                                    @endif
                                </td>
                                <td>{{ $step->updated_by ?? '-' }}</td>
                                <td>
                                    @if($isCommittee)
                                        <form id="{{ $formId }}" action="{{ url('/qcc_step/' . $step->id) }}" method="POST" style="display:inline;">
                                            @csrf
                                            @method('PUT')
                                            <button class="btn btn-primary btn-sm" type="submit" title="Simpan">
                                                <i class="fa fa-save"></i> Simpan
                                            </button>
                                        </form>
                                        <form action="{{ url('/qcc_step/' . $step->id) }}" method="POST" style="display:inline;"
                                            onsubmit="return confirm('Hapus master step ini?')">
                                            @csrf
                                            @method('DELETE')
                                            <button class="btn btn-danger btn-sm" type="submit" title="Hapus">
                                                <i class="fa fa-trash"></i>
                                            </button>
                                        </form>
                                    @else
                                        <span class="text-muted">-</span>
                                    @endif
                                </td>
                            </tr>
                        @endforeach

                        @if($isCommittee && $steps->count() < 8)
                            <tr>
                                @php($createFormId = 'create-qcc-step')
                                <td>
                                    <select class="form-control" name="step_sequance" form="{{ $createFormId }}" required>
                                        <option value="">Pilih urutan</option>
                                        @foreach(range(1, 8) as $sequence)
                                            @if(!$steps->contains(fn ($step) => (int) $step->step_sequance === $sequence))
                                                <option value="{{ $sequence }}" {{ old('step_sequance') == $sequence ? 'selected' : '' }}>
                                                    Step {{ $sequence }}
                                                </option>
                                            @endif
                                        @endforeach
                                    </select>
                                </td>
                                <td>
                                    <input class="form-control" name="step_description" form="{{ $createFormId }}" maxlength="255"
                                        value="{{ old('step_description') }}" placeholder="Nama step improvement" required>
                                </td>
                                <td>-</td>
                                <td>
                                    <form id="{{ $createFormId }}" action="{{ url('/qcc_step') }}" method="POST">
                                        @csrf
                                        <button class="btn btn-success btn-sm" type="submit">
                                            <i class="fa fa-plus"></i> Tambah
                                        </button>
                                    </form>
                                </td>
                            </tr>
                        @endif
                    </tbody>
                </table>
            </div>
        </div>
    </section>
</div>
@endsection

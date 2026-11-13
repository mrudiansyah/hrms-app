@extends('layouts.app')

@section('content')
<div class="container">
    <h1 class="h3 mb-3">PIN Akses Kandidat</h1>

    @if (session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif
    @if ($errors->any())
        <div class="alert alert-danger">{{ $errors->first() }}</div>
    @endif

    <form method="POST" action="{{ route('candidate.pins.generate') }}" class="mb-4">
        @csrf
        <div class="form-check mb-2">
            <input type="checkbox" class="form-check-input" id="skip_email_verification" name="skip_email_verification" value="1">
            <label class="form-check-label" for="skip_email_verification">
                Lewati verifikasi email untuk PIN ini
            </label>
            <small class="form-text text-muted">Gunakan hanya jika kandidat memang diizinkan melanjutkan tanpa verifikasi email.</small>
        </div>
        <button type="submit" class="btn btn-primary">Generate PIN 4 Angka</button>
    </form>

    <div class="table-responsive">
        <table class="table table-bordered table-striped">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>PIN</th>
                    <th>Dibuat oleh</th>
                    <th>Dibuat pada</th>
                    <th>Kedaluwarsa</th>
                    <th>Skip verifikasi email</th>
                    <th>Status</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($pins as $pin)
                    @php($active = \Illuminate\Support\Carbon::parse($pin->expired_at)->isFuture())
                    <tr>
                        <td>{{ $pin->id }}</td>
                        <td><strong>{{ $pin->pin_access }}</strong></td>
                        <td>{{ $pin->created_by }}</td>
                        <td>{{ $pin->created_at }}</td>
                        <td>{{ $pin->expired_at }}</td>
                        <td>{{ $pin->skip_email_verification ? 'Ya' : 'Tidak' }}</td>
                        <td>{{ $active ? 'Aktif' : 'Kedaluwarsa' }}</td>
                        <td>
                            @if ($active)
                                <form method="POST" action="{{ route('candidate.pins.expire', $pin->id) }}" onsubmit="return confirm('Nonaktifkan PIN ini?')">
                                    @csrf
                                    @method('PATCH')
                                    <button type="submit" class="btn btn-sm btn-danger">Nonaktifkan</button>
                                </form>
                            @else
                                -
                            @endif
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="8" class="text-center">Belum ada PIN.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection

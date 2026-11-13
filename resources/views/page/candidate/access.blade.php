<!doctype html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Formulir Kandidat</title>
    <link href="{{ asset('css/app.css') }}" rel="stylesheet">
</head>
<body class="bg-light">
    <main class="container py-5" style="max-width: 720px">
        <h1 class="h3 mb-3">Formulir Kandidat</h1>
        <p>Masukkan PIN aktif dan data awal. Link verifikasi akan dikirim ke email dan berlaku 1 jam.</p>

        @if (session('success'))
            <div class="alert alert-success">{{ session('success') }}</div>
        @endif
        @if ($errors->any())
            <div class="alert alert-danger">
                <ul class="mb-0">@foreach ($errors->all() as $error)<li>{{ $error }}</li>@endforeach</ul>
            </div>
        @endif

        <form method="POST" action="{{ route('candidate.send-verification') }}">
            @csrf
            <div class="form-group">
                <label for="pin_access">PIN (4 angka)</label>
                <input id="pin_access" name="pin_access" class="form-control" inputmode="numeric" pattern="[0-9]{4}" maxlength="4" required>
            </div>
            <div class="form-group">
                <label for="employee_name">Nama lengkap</label>
                <input id="employee_name" name="employee_name" class="form-control" value="{{ old('employee_name') }}" maxlength="255" required>
            </div>
            <div class="form-group">
                <label for="email_address">Email aktif</label>
                <input id="email_address" type="email" name="email_address" class="form-control" value="{{ old('email_address') }}" maxlength="255" required>
            </div>
            <div class="form-group">
                <label for="gender">Jenis kelamin</label>
                <select id="gender" name="gender" class="form-control" required>
                    <option value="">Pilih</option>
                    <option value="Laki-laki" @selected(old('gender') === 'Laki-laki')>Laki-laki</option>
                    <option value="Perempuan" @selected(old('gender') === 'Perempuan')>Perempuan</option>
                </select>
            </div>
            <button type="submit" class="btn btn-primary">Kirim link verifikasi</button>
        </form>
    </main>
</body>
</html>

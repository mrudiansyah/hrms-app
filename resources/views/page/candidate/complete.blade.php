<!doctype html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Data Kandidat Tersimpan</title>
    <link href="{{ asset('css/app.css') }}" rel="stylesheet">
</head>
<body class="bg-light">
    <main class="container py-5" style="max-width: 720px">
        <div class="alert alert-success">
            <h1 class="h4">Terima kasih, {{ $candidate->employee_name }}.</h1>
            @if ($candidate->verified_at)
                <p class="mb-0">Email telah terverifikasi dan data kandidat berhasil disimpan.</p>
            @else
                <p class="mb-0">Data kandidat berhasil disimpan. Verifikasi email dilewati oleh HR.</p>
            @endif
        </div>
    </main>
</body>
</html>

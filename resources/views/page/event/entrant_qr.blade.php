<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <title>Kartu Peserta Event</title>
    <style>
        @page {
            margin: 24px;
        }

        body {
            color: #222;
            font-family: DejaVu Sans, sans-serif;
            text-align: center;
        }

        .ticket {
            border: 1px solid #bbb;
            margin: 0 auto;
            padding: 24px;
            width: 420px;
        }

        .qr-code {
            height: 280px;
            margin: 16px auto;
            width: 280px;
        }

        .entrant-code {
            font-size: 20px;
            font-weight: bold;
            letter-spacing: 1px;
        }

        .muted {
            color: #666;
            font-size: 12px;
        }
    </style>
</head>
<body>
    <div class="ticket">
        <h2>{{ $attendance->event_name }}</h2>
        <p>{{ \Carbon\Carbon::parse($attendance->date)->format('d-m-Y') }}</p>
        <p>Peserta: <strong>{{ $attendance->entrant_name }}</strong></p>
        <img class="qr-code" src="data:image/svg+xml;base64,{{ $qrCode }}" alt="QR Code {{ $attendance->entrant_code }}">
        <p class="entrant-code">{{ $attendance->entrant_code }}</p>
        <p class="muted">Tunjukkan QR Code ini saat memasuki ruangan event.</p>
    </div>
</body>
</html>

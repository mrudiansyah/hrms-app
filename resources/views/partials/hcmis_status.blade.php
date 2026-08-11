@php
    $hcmis = session('hcmis_login');
@endphp

@if(!empty($hcmis))
    @if(!empty($hcmis['success']))
        <div class="container mt-3">
            <div class="alert alert-success" role="alert">
                HCMIS login berhasil. Token tersimpan.
                @if(!empty($hcmis['token']))
                    <div class="small text-monospace">Token: {{ Str::limit($hcmis['token'], 40, '...') }}</div>
                @endif
            </div>
        </div>
    @else
        <div class="container mt-3">
            <div class="alert alert-warning" role="alert">
                HCMIS login gagal. <span class="small">{{ is_string($hcmis['error']) ? $hcmis['error'] : json_encode($hcmis['error']) }}</span>
            </div>
        </div>
    @endif
@endif

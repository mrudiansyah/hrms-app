<?php

return [
    'url' => rtrim(env('WUZAPI_URL', 'http://127.0.0.1:8000'), '/'),
    'token' => env('WUZAPI_TOKEN'),
    'session' => env('WUZAPI_SESSION', 'default'),
    'send_api_url' => env(
        'WUZAPI_SEND_API_URL',
        'https://ems.summitadyawinsa.co.id/EMS/api/wuzapi/send'
    ),
    'api_key' => env('WUZAPI_API_KEY'),
    'ca_bundle' => env('WUZAPI_CA_BUNDLE'),
];

<?php

return [
    'url' => rtrim(env('WUZAPI_URL', 'http://192.168.1.4:8000'), '/'),
    'token' => env('WUZAPI_TOKEN', 'rahasia123'),
    'session' => env('WUZAPI_SESSION', 'default'),
    'send_api_url' => env(
        'WUZAPI_SEND_API_URL',
        'https://ems.summitadyawinsa.co.id/EMS/api/wuzapi/send'
    ),
    'api_key' => env('WUZAPI_API_KEY', 'SummitAdyawinsa2014'),
    'ca_bundle' => env('WUZAPI_CA_BUNDLE'),
];

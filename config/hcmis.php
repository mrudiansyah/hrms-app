<?php
return [
    'protocol' => env('HCMIS_PROTOCOL', 'https'),
    'host' => env('HCMIS_HOST', 'hcmis.adyawinsadinamika.com'),
    'port' => env('HCMIS_PORT', ''),
    'base_uri' => rtrim(env('HCMIS_PROTOCOL', 'https').'://'.env('HCMIS_HOST', 'hcmis.adyawinsadinamika.com').(env('HCMIS_PORT') ? ':'.env('HCMIS_PORT') : ''), '/'),
    'token' => env('HCMIS_TOKEN', null),
    'username' => env('HCMIS_USERNAME', null),
    'password' => env('HCMIS_PASSWORD', null),
    'company_code' => env('HCMIS_COMPANY', 'SAI'),
    'org_code' => env('HCMIS_ORG', 'SAI'),
];

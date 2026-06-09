<?php

return [
    'password_hash' => env('ADMIN_PASSWORD_HASH'),

    'password_timeout' => (int) env('ADMIN_PASSWORD_TIMEOUT', 10800),

    'session_key' => 'admin.authenticated',

    'confirmed_at_key' => 'admin.password_confirmed_at',
];

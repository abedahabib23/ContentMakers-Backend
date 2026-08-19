<?php

return [
    'ttl_days' => (int) env('REFRESH_TOKEN_TTL_DAYS', 30),

    'cookie_name' => env('REFRESH_TOKEN_COOKIE', 'refresh_token'),

    // 'lax' works for local dev (the frontend proxies /api/* same-origin)
    // and for a production frontend/backend that share a registrable
    // domain (e.g. app.example.com + api.example.com — SameSite is scoped
    // to the "site", not the exact origin). Set to 'none' only if they end
    // up on genuinely different top-level domains (then Secure is implied).
    'same_site' => env('REFRESH_TOKEN_SAME_SITE', 'lax'),
];

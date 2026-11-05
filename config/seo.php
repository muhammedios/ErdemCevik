<?php

return [
    // Indexing is enabled on the permanent production site, not local previews.
    'url' => env('SITE_URL', 'https://dterdemcevik.com'),
    'indexable' => env('SEO_INDEXABLE', env('APP_ENV') === 'production'),
    'google_verification' => env('GOOGLE_SITE_VERIFICATION'),
    'location' => ['tr' => 'Arnavutköy, İstanbul', 'en' => 'Arnavutköy, Istanbul'],
];

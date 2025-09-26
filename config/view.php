<?php

return [
    // Paths where the framework should look for views
    'paths' => [
        resource_path('views'),
    ],

    // Where to store compiled Blade templates
    'compiled' => env('VIEW_COMPILED_PATH', storage_path('framework/views')),
];


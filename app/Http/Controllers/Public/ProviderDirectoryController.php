<?php

// This file provides a runtime alias to avoid using the reserved keyword
// `public` in a PHP namespace. The actual controller lives under
// App\Http\Controllers\PublicSite\ProviderDirectoryController.

if (!class_exists('App\\Http\\Controllers\\Public\\ProviderDirectoryController')) {
    class_alias(\App\Http\Controllers\PublicSite\ProviderDirectoryController::class, 'App\\Http\\Controllers\\Public\\ProviderDirectoryController');
}


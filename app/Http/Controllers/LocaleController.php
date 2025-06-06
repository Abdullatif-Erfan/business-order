<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class LocaleController extends Controller
{
    public function setLocale($locale)
    {
        // Check if the provided locale is valid
        if (in_array($locale, ['en', 'fa','pa'])) {
            // Log the previous session locale
            Log::info("Prev session locale: " . session('locale'));

            // Set the session with the selected locale
            session(['locale' => $locale]);

            app()->setLocale($locale);
            // Log the locale change
            Log::info("Locale changed to: {$locale}");

            // Optionally, log the session value to verify
            Log::info("Current session locale: " . session('locale'));
        } else {
            // Log when an invalid locale is provided
            Log::warning("Attempted to set an invalid locale: {$locale}");
        }

        // Redirect back to the previous page
        return back();
    }
}

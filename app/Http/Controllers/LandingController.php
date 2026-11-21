<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class LandingController extends Controller
{
    /**
     * Display the landing page.
     *
     * @return \Illuminate\View\View
     */
    public function index()
    {
        return view('landing'); // points to resources/views/landing.blade.php
    }

    /**
     * Serve the released Android APK from public/downloads/mauzosheet.apk.
     *
     * Drop the signed release build at that path when going live. The route
     * streams it with the correct Android MIME type so browsers download it
     * instead of trying to open it.
     */
    public function downloadApk()
    {
        $path = public_path('downloads' . DIRECTORY_SEPARATOR . 'mauzosheet.apk');

        if (!is_file($path)) {
            abort(404, 'APK bado haijapatikana. Tafadhali jaribu tena baadaye.');
        }

        return response()->download($path, 'mauzosheet.apk', [
            'Content-Type' => 'application/vnd.android.package-archive',
            'Cache-Control' => 'no-cache, no-store, must-revalidate',
        ]);
    }
}

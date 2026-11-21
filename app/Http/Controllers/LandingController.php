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

    private function sendApk(string $filename)
    {
        $path = public_path('downloads' . DIRECTORY_SEPARATOR . $filename);

        if (!is_file($path)) {
            abort(404, 'APK bado haijapatikana. Tafadhali jaribu tena baadaye.');
        }

        return response()->download($path, $filename, [
            'Content-Type' => 'application/vnd.android.package-archive',
            'Cache-Control' => 'no-cache, no-store, must-revalidate',
        ]);
    }

    public function downloadArm64Apk()
    {
        return $this->sendApk('mauzosheet-arm64.apk');
    }

    public function downloadArm32Apk()
    {
        return $this->sendApk('mauzosheet-arm32.apk');
    }
}

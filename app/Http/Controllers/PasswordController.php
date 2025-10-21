<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;

class PasswordController extends Controller
{
    // Show change password form
    public function showChangeForm()
    {
        return view('auth.change-password');
    }

    // Update password
 public function update(Request $request)
{
    $request->validate([
        'current_password' => ['required'],
        'password' => ['required', 'confirmed', 'min:8'],
    ]);

    $user = Auth::user();

    // Check current password
    if (!Hash::check($request->current_password, $user->password)) {
        return back()->withErrors(['current_password' => 'Neno la siri la sasa halilingani.']);
    }

    // Prevent using the old password
    if (Hash::check($request->password, $user->password)) {
        return back()->withErrors(['password' => 'Neno la siri jipya haliwezi kuwa sawa na neno la sasa.']);
    }

    // Update password
    $user->password = Hash::make($request->password);
    $user->save();

    // Logout user after password change
    Auth::logout();

    return redirect()->route('login')->with('success', 'Neno siri limebadilishwa. Tafadhali ingia kwa kutumia neno jipya.');
}

}

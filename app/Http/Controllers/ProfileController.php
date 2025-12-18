<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ProfileController extends Controller
{
    public function editPassword()
    {
        return view('auth.change-password');
    }

    public function companyInfo()
    {
        $company = Auth::user()->company; // assuming relation user->company
        return view('company.info', compact('company'));
    }
}

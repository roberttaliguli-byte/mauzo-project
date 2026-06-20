<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Company;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;

class CompanyController extends Controller
{
    /**
     * Update the company info - with email uniqueness check and logo upload
     */
    public function update(Request $request)
    {
        try {
            $user = Auth::user();
            
            // Check if user is authenticated
            if (!$user) {
                return redirect()
                    ->route('login')
                    ->with('error', '❌ Tafadhali ingia kwanza.');
            }
            
            // Check if user has a company_id
            if (!$user->company_id) {
                return redirect()
                    ->back()
                    ->with('error', '❌ Hakuna kampuni iliyopatikana kwa akaunti yako.')
                    ->with('active_tab', 'edit');
            }
            
            // Get the company that belongs to this user ONLY
            $company = Company::where('id', $user->company_id)->first();
            
            // Verify company exists and belongs to this user
            if (!$company) {
                return redirect()
                    ->back()
                    ->with('error', '❌ Kampuni yako haikupatikana. Tafadhali wasiliana na msimamizi.')
                    ->with('active_tab', 'edit');
            }

            // Validate incoming data with email uniqueness check and logo validation
            $validated = $request->validate([
                'company_name' => 'required|string|max:255',
                'owner_name'   => 'required|string|max:255',
                'owner_dob'    => 'required|date',
                'owner_gender' => 'nullable|string|in:male,female',
                'region'       => 'required|string|max:100',
                'location'     => 'required|string|max:255',
                'phone'        => 'required|string|max:20',
                'business_type' => 'required|string|max:100',
                'email'        => [
                    'required',
                    'email',
                    'max:255',
                    Rule::unique('companies', 'email')->ignore($company->id)
                ],
                'logo'         => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048'
            ], [
                'email.unique' => 'Barua pepe hii tayari imesajiliwa na kampuni nyingine. Tafadhali tumia barua pepe nyingine.',
                'business_type.required' => 'Tafadhali chagua aina ya biashara.',
                'logo.image' => 'Tafadhali pakia picha halali (JPG, PNG, GIF, au SVG).',
                'logo.max' => 'Saizi ya picha haipaswi kuzidi 2MB.',
                'logo.mimes' => 'Aina zinazokubalika: jpeg, png, jpg, gif, svg.'
            ]);

            // Check if email is being changed
            if ($company->email !== $validated['email']) {
                // Double-check manually if email exists (extra safety)
                $existingCompany = Company::where('email', $validated['email'])
                    ->where('id', '!=', $company->id)
                    ->first();
                
                if ($existingCompany) {
                    return redirect()
                        ->back()
                        ->withInput()
                        ->withErrors(['email' => 'Barua pepe hii tayari imesajiliwa na kampuni nyingine.'])
                        ->with('active_tab', 'edit')
                        ->with('error', '❌ Barua pepe tayari ipo kwenye mfumo.');
                }
                
                // Log email change for security
                Log::info('Company email changed', [
                    'user_id' => $user->id,
                    'company_id' => $company->id,
                    'old_email' => $company->email,
                    'new_email' => $validated['email'],
                    'ip' => $request->ip()
                ]);
            }

            // Handle logo upload - STORE IN DATABASE VIA STORAGE
            if ($request->hasFile('logo')) {
                $logo = $request->file('logo');
                
                // Generate unique filename
                $logoName = time() . '_' . preg_replace('/[^a-zA-Z0-9._-]/', '', $logo->getClientOriginalName());
                $logoPath = $logo->storeAs('company-logos', $logoName, 'public');
                
                // Delete old logo if exists
                if ($company->logo && Storage::disk('public')->exists($company->logo)) {
                    Storage::disk('public')->delete($company->logo);
                    Log::info('Old logo deleted', [
                        'company_id' => $company->id,
                        'old_logo' => $company->logo
                    ]);
                }
                
                // STORE THE PATH IN DATABASE
                $company->logo = $logoPath;
                
                Log::info('Company logo updated', [
                    'user_id' => $user->id,
                    'company_id' => $company->id,
                    'new_logo' => $logoPath,
                    'ip' => $request->ip()
                ]);
            }

            // Log business type change if it changed
            if ($company->business_type !== $validated['business_type']) {
                Log::info('Company business type changed', [
                    'user_id' => $user->id,
                    'company_id' => $company->id,
                    'old_business_type' => $company->business_type,
                    'new_business_type' => $validated['business_type'],
                    'ip' => $request->ip()
                ]);
            }

            // Update company data
            $company->company_name = $validated['company_name'];
            $company->owner_name = $validated['owner_name'];
            $company->owner_dob = $validated['owner_dob'];
            $company->owner_gender = $validated['owner_gender'] ?? $company->owner_gender;
            $company->region = $validated['region'];
            $company->location = $validated['location'];
            $company->phone = $validated['phone'];
            $company->email = $validated['email'];
            $company->business_type = $validated['business_type'];

            // Save the changes - THIS SAVES LOGO PATH TO DATABASE
            $company->save();

            // Log the update for security audit
            Log::info('Company info updated successfully', [
                'user_id' => $user->id,
                'company_id' => $company->id,
                'ip' => $request->ip()
            ]);

            return redirect()
                ->route('company.info')
                ->with('success', '✅ Taarifa za kampuni yako zimehifadhiwa kwa mafanikio!')
                ->with('active_tab', 'edit')
                ->with('hash', 'edit');

        } catch (\Illuminate\Validation\ValidationException $e) {
            // Check for specific validation errors
            $errors = $e->validator->errors();
            
            $errorMessages = [];
            if ($errors->has('email')) {
                $errorMessages[] = $errors->first('email');
            }
            if ($errors->has('business_type')) {
                $errorMessages[] = $errors->first('business_type');
            }
            if ($errors->has('logo')) {
                $errorMessages[] = $errors->first('logo');
            }
            
            if (!empty($errorMessages)) {
                return redirect()
                    ->back()
                    ->withErrors($e->validator)
                    ->withInput()
                    ->with('active_tab', 'edit')
                    ->with('error', '❌ ' . implode(' ', $errorMessages));
            }
            
            return redirect()
                ->back()
                ->withErrors($e->validator)
                ->withInput()
                ->with('active_tab', 'edit')
                ->with('error', '❌ Tafadhali sahihisha makosa kwenye fomu.');
                
        } catch (\Exception $e) {
            Log::error('Company update error for user ' . (Auth::id() ?? 'unknown') . ': ' . $e->getMessage());
            
            return redirect()
                ->back()
                ->withInput()
                ->with('error', '❌ Kuna tatizo limejitokeza. Tafadhali jaribu tena.')
                ->with('active_tab', 'edit');
        }
    }

    /**
     * Delete company logo
     */
    public function deleteLogo(Request $request)
    {
        try {
            $user = Auth::user();
            
            if (!$user || !$user->company_id) {
                return response()->json([
                    'success' => false,
                    'message' => 'Unauthorized'
                ], 401);
            }
            
            $company = Company::find($user->company_id);
            
            if (!$company) {
                return response()->json([
                    'success' => false,
                    'message' => 'Company not found'
                ], 404);
            }
            
            // Delete logo from storage
            if ($company->logo && Storage::disk('public')->exists($company->logo)) {
                Storage::disk('public')->delete($company->logo);
            }
            
            // Remove logo path from database
            $company->logo = null;
            $company->save();
            
            Log::info('Company logo deleted', [
                'user_id' => $user->id,
                'company_id' => $company->id,
                'ip' => $request->ip()
            ]);
            
            return response()->json([
                'success' => true,
                'message' => 'Logo imefutwa kwa mafanikio'
            ]);
            
        } catch (\Exception $e) {
            Log::error('Error deleting logo: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => 'Kuna tatizo limejitokeza'
            ], 500);
        }
    }

    /**
     * Check if email exists (AJAX endpoint for real-time validation)
     */
    public function checkEmail(Request $request)
    {
        try {
            $user = Auth::user();
            
            if (!$user || !$user->company_id) {
                return response()->json([
                    'exists' => false,
                    'message' => 'Unauthorized'
                ], 401);
            }
            
            $email = $request->input('email');
            $companyId = $user->company_id;
            
            // Check if email exists for another company
            $exists = Company::where('email', $email)
                ->where('id', '!=', $companyId)
                ->exists();
            
            if ($exists) {
                return response()->json([
                    'exists' => true,
                    'message' => 'Barua pepe hii tayari imesajiliwa na kampuni nyingine.'
                ]);
            }
            
            return response()->json([
                'exists' => false,
                'message' => 'Barua pepe inapatikana'
            ]);
            
        } catch (\Exception $e) {
            return response()->json([
                'exists' => false,
                'error' => true,
                'message' => 'Kuna tatizo limejitokeza'
            ], 500);
        }
    }
}
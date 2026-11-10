<?php

namespace App\Http\Controllers;

use App\Models\CompanySetting;
use App\Models\ActivityLog;
use Illuminate\Http\Request;

class SettingController extends Controller
{
    public function index()
    {
        $setting = CompanySetting::get();
        $recentLogs = ActivityLog::with(['user', 'project'])->latest()->take(20)->get();

        return view('settings.index', compact('setting', 'recentLogs'));
    }

    public function update(Request $request)
    {
        $setting = CompanySetting::get();

        $validated = $request->validate([
            'company_name' => 'required|string|max:255',
            'tagline' => 'nullable|string|max:255',
            'company_email' => 'required|email|max:255',
            'company_phone' => 'nullable|string|max:50',
            'website' => 'nullable|url|max:255',
            'address' => 'nullable|string|max:500',
            'currency' => 'required|string|max:10',
            'currency_symbol' => 'required|string|max:5',
            'timezone' => 'required|string|max:100',
        ]);

        $setting->update($validated);

        ActivityLog::log('Settings Updated', auth()->user()->name . ' updated enterprise company profile & configuration.');

        return back()->with('success', 'Company settings updated successfully!');
    }
}

<?php

namespace App\Http\Controllers;

use App\Models\Setting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class SettingController extends Controller
{
    public function index()
    {
        return view('settings.index');
    }

    public function editPassword()
    {
        return view('settings.password');
    }

    public function updatePassword(Request $request)
    {
        $request->validate([
            'current_password' => 'required',
            'new_password' => 'required|min:8|confirmed',
        ]);

        if (!Hash::check($request->current_password, Auth::user()->password)) {
            return back()->withErrors(['current_password' => 'Current password is incorrect.']);
        }

        Auth::user()->update(['password' => Hash::make($request->new_password)]);

        return back()->with('status', 'Password changed successfully!');
    }

    public function editPredictDay()
    {
        $settings = Setting::pluck('value', 'key')->toArray();

        return view('settings.predict-day', compact('settings'));
    }

    public function updatePrediction(Request $request)
    {
        $request->validate([
            // 'predictedSalesDay' => 'required|integer|min:1',
            'historicalDataDays' => 'required|integer|min:1',
        ]);

        // Setting::where('key', 'predictedSalesDay')->update(['value' => $request->predictedSalesDay]);
        Setting::where('key', 'historicalDataDays')->update(['value' => $request->historicalDataDays]);

        return redirect()->route('settings')->with('success', 'Settings updated successfully.');
    }
}

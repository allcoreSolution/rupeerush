<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AdminSetting;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class SupportSettingsController extends Controller
{
    /**
     * Show dedicated Support Contacts (WhatsApp & Email) Settings page.
     */
    public function index(): View
    {
        $setting = AdminSetting::current();

        return view('admin.support.index', [
            'setting' => $setting,
        ]);
    }

    /**
     * Update Official WhatsApp Support Number and Email.
     */
    public function update(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'support_whatsapp' => ['required', 'string', 'max:50'],
            'support_email' => ['required', 'string', 'max:100'],
        ], [
            'support_whatsapp.required' => 'WhatsApp support number is required.',
            'support_email.required' => 'Official support email address is required.',
        ]);

        $setting = AdminSetting::current();
        $setting->support_whatsapp = trim($validated['support_whatsapp']);
        $setting->support_email = trim($validated['support_email']);
        $setting->save();

        return redirect()->route('admin.support.index')
            ->with('success', 'WhatsApp Support number and Email updated successfully! Player website floating buttons have been updated in real-time.');
    }
}

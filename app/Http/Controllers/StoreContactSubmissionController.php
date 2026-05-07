<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreContactSubmissionRequest;
use App\Mail\ContactSubmissionSubmitted;
use App\Models\ContactSubmission;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Mail;

class StoreContactSubmissionController extends Controller
{
    public function __invoke(StoreContactSubmissionRequest $request): RedirectResponse
    {
        $validated = $request->validated();

        $submission = ContactSubmission::query()->create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'phone' => $validated['phone'],
            'message' => $validated['message'],
            'prefers_whatsapp' => $validated['prefers_whatsapp'] ?? true,
            'ip_address' => $request->ip(),
            'user_agent' => $request->userAgent(),
        ]);

        $recipients = config('contact.notification_recipients', []);

        if ($recipients !== []) {
            Mail::to($recipients)->send(new ContactSubmissionSubmitted($submission));
        }

        return back()->with('success', true);
    }
}

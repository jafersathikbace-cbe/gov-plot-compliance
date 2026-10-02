<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;
use Illuminate\View\View;

class PasswordResetLinkController extends Controller
{
    /**
     * Display the password reset link request form.
     */
    public function create(): View
    {
        return view('auth.forgot-password');
    }

    /**
     * Send a password reset link.
     */
    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'email' => [
                'required',
                'email',
            ],
        ]);

        $email = Str::lower(
            $request->string('email')->toString()
        );

        $user = User::where('email', $email)->first();

        /*
         * Always return the same response so that the application
         * does not reveal whether an email address is registered.
         */
        if (! $user) {
            return back()->with(
                'status',
                'If that email is registered, a reset link has been sent.'
            );
        }

        /*
         * Generate a random reset token.
         *
         * Only the SHA-256 hash is stored in MongoDB.
         * The raw token is sent to the user.
         */
        $token = Str::random(64);

        $tokens = DB::connection('mongodb')
            ->table('password_reset_tokens');

        /*
         * Remove any previous reset token for this email.
         */
        $tokens
            ->where('email', $email)
            ->delete();

        /*
         * Store the hashed token in MongoDB.
         */
        $tokens->insert([
            'email' => $email,
            'token_hash' => hash('sha256', $token),
            'created_at' => now(),
            'expires_at' => now()->addMinutes(60),
        ]);

        /*
         * Generate reset URL.
         */
        $resetUrl = route('password.reset', [
            'token' => $token,
            'email' => $email,
        ]);

        /*
         * Send reset email.
         */
        Mail::raw(
            "Use the following link to reset your password.\n\n"
            . $resetUrl
            . "\n\n"
            . "This link is valid for 60 minutes.",
            function ($message) use ($email): void {
                $message
                    ->to($email)
                    ->subject(
                        'Government Plot Compliance - Password Reset'
                    );
            }
        );

        return back()->with(
            'status',
            'If that email is registered, a reset link has been sent.'
        );
    }
}
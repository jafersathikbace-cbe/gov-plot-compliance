<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Illuminate\Validation\Rules;
use Illuminate\View\View;

class NewPasswordController extends Controller
{
    /**
     * Display the password reset form.
     */
    public function create(Request $request): View
    {
        return view('auth.reset-password', [
            'request' => $request,
        ]);
    }

    /**
     * Reset the user's password.
     */
    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'token' => [
                'required',
                'string',
            ],

            'email' => [
                'required',
                'email',
            ],

            'password' => [
                'required',
                'confirmed',
                Rules\Password::defaults(),
            ],
        ]);

        $email = Str::lower(
            $request->string('email')->toString()
        );

        $token = $request
            ->string('token')
            ->toString();

        /*
         * Find the reset record in MongoDB.
         */
        $record = DB::connection('mongodb')
            ->table('password_reset_tokens')
            ->where('email', $email)
            ->first();

        /*
         * Validate:
         * 1. Record exists.
         * 2. Token hash exists.
         * 3. Expiry exists.
         * 4. Submitted token matches stored hash.
         * 5. Token has not expired.
         */
        $valid = $record
            && isset($record->token_hash)
            && isset($record->expires_at)
            && hash_equals(
                (string) $record->token_hash,
                hash('sha256', $token)
            )
            && Carbon::parse($record->expires_at)->isFuture();

        if (! $valid) {
            return back()
                ->withInput(
                    $request->only('email')
                )
                ->withErrors([
                    'email' =>
                        'This password reset link is invalid or has expired.',
                ]);
        }

        /*
         * Find the MongoDB user.
         */
        $user = User::where('email', $email)->first();

        if (! $user) {
            return back()->withErrors([
                'email' =>
                    'Unable to reset the password for this account.',
            ]);
        }

        /*
         * Update the password.
         */
        $user->forceFill([
            'password' => Hash::make(
                $request->password
            ),

            'remember_token' => Str::random(60),
        ])->save();

        /*
         * Delete the reset token after successful use.
         */
        DB::connection('mongodb')
            ->table('password_reset_tokens')
            ->where('email', $email)
            ->delete();

        return redirect()
            ->route('login')
            ->with(
                'status',
                'Your password has been reset successfully.'
            );
    }
}
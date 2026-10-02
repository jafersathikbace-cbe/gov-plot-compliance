<?php

namespace App\Services;

use App\Models\AllotmentCase;
use App\Models\User;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class NotificationService
{
    public function sendCaseAlert(AllotmentCase $case, string $subject, string $message, ?User $recipient = null): void
    {
        if ($recipient?->email) {
            Mail::raw($message, function ($mail) use ($recipient, $subject) {
                $mail->to($recipient->email)->subject($subject);
            });
        }

        $token = env('TELEGRAM_BOT_TOKEN');
        $chatId = $recipient?->telegram_chat_id ?: env('TELEGRAM_CHAT_ID');

        if ($token && $chatId) {
            try {
                Http::post("https://api.telegram.org/bot{$token}/sendMessage", [
                    'chat_id' => $chatId,
                    'text' => $subject."\n\n".$message,
                ]);
            } catch (\Throwable $e) {
                Log::warning('Telegram notification failed', ['error' => $e->getMessage()]);
            }
        }
    }
}

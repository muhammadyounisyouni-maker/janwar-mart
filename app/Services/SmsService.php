<?php
namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class SmsService
{
    public function send(string $phone, string $text): void
    {
        match (config('janwar.sms_provider')) {
            'twilio' => $this->twilio($phone, $text),
            'webhook' => $this->webhook($phone, $text),
            default => Log::info("[SMS to {$phone}] {$text}"),   // console: read it in storage/logs/laravel.log
        };
    }
    private function twilio(string $phone, string $text): void
    {
        $c = config('janwar.twilio');
        $to = '+92'.substr($phone, 1);
        Http::asForm()->withBasicAuth($c['sid'], $c['token'])
            ->post("https://api.twilio.com/2010-04-01/Accounts/{$c['sid']}/Messages.json", ['To' => $to, 'From' => $c['from'], 'Body' => $text])->throw();
    }
    private function webhook(string $phone, string $text): void
    {
        $c = config('janwar.webhook');
        Http::withToken((string) $c['token'])->post($c['url'], ['to' => $phone, 'text' => $text])->throw();
    }
}

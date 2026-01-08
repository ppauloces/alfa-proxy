<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;

class MetaConversionService
{
    public static function completeRegistration($user, string $eventId = null): bool
    {
        $payload = [
            'data' => [
                [
                    'event_name' => 'CompleteRegistration',
                    'event_time' => time(),
                    'action_source' => 'website',
                    'user_data' => [
                        'em' => hash('sha256', strtolower(trim($user->email))),
                        'client_ip_address' => request()->ip(),
                        'client_user_agent' => request()->userAgent(),
                    ],
                ]
            ],
            'test_event_code' => 'TEST18042',
        ];

        $url = 'https://graph.facebook.com/v24.0/' . env('META_PIXEL_ID') . '/events?access_token=' . env('META_ACCESS_TOKEN');

        $response = Http::post($url, $payload);

        logger()->info('META RESPONSE', [
            'status' => $response->status(),
            'body' => $response->json(),
        ]);

        return $response->successful();

    }
}

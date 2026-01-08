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
                    'event_id' => $eventId,
                    'user_data' => [
                        'em' => hash('sha256', strtolower(trim($user->email))),
                        'client_ip_address' => request()->ip(),
                        'client_user_agent' => request()->userAgent(),
                    ],
                ]
            ],
            // usar apenas em teste
            'test_event_code' => 'TEST18042',
        ];

        $response = Http::withHeaders([
            'Authorization' => 'Bearer ' . env('META_ACCESS_TOKEN'),
            'Content-Type' => 'application/json',
        ])
            ->post(
                'https://graph.facebook.com/v24.0/' . env('META_PIXEL_ID') . '/events',
                $payload
            );

        logger()->info('META RESPONSE', [
            'status' => $response->status(),
            'body' => $response->json(),
        ]);

        return $response->successful();
    }

}

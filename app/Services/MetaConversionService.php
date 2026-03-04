<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;

class MetaConversionService
{
    public static function completeRegistration($user, string $eventId = null): bool
    {
        return self::sendEvent(
            eventName: 'CompleteRegistration',
            user: $user,
            eventId: $eventId
        );
    }

    public static function purchase($user, $order): bool
    {
        return self::sendEvent(
            eventName: 'Purchase',
            user: $user,
            eventId: (string) $order->id,
            customData: [
                'currency' => 'BRL',
                'value' => $order->valor,
            ]
        );
    }

    private static function sendEvent($eventName, $user, $eventId = null, array $customData = []): bool
    {
        $event = [
            'event_name' => $eventName,
            'event_time' => time(),
            'action_source' => 'website',
            'event_id' => $eventId,
            'user_data' => self::buildUserData($user),
        ];

        if (!empty($customData)) {
            $event['custom_data'] = $customData;
        }

        $payload = [
            'data' => [
                $event,
            ],
            // usar apenas em teste
            //'test_event_code' => 'TEST62268',
        ];

        $response = Http::withHeaders([
            'Authorization' => 'Bearer ' . "EAAiMopXR0xQBQzsqJiLYvnvrLZBZCQUR3HwxzF31OyoRb3gE6gYQYbUCj5Ocldi6wpNXcZBS7FoOdmraPKyRbZAJeu16gkO7JDZCLrUZCN0hIeVDJXcbU2bHcIpXN0fAHfckoMDFg4IkfY0QqZCMtIH9xNSb9vwZBAJRVsEF0hU03ZCzBLSELJPl7H9z8KG4TtwkJVgZDZD",
            'Content-Type' => 'application/json',
        ])
            ->post(
                'https://graph.facebook.com/v24.0/' . '729296842350946' . '/events',
                $payload
            );

        return $response->successful();
    }

    private static function buildUserData($user): array
    {
        $request = app()->bound('request') ? request() : null;
        $phone = preg_replace('/\D/', '', $user->phone ?? '');

        return array_filter([
            'em' => hash('sha256', strtolower(trim($user->email))),
            'ph' => $phone !== '' ? hash('sha256', $phone) : null,
            'client_ip_address' => $request ? $request->ip() : null,
            'client_user_agent' => $request ? $request->userAgent() : null,
        ], static function ($value) {
            return $value !== null && $value !== '';
        });
    }
}

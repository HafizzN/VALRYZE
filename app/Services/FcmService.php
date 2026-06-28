<?php

namespace App\Services;

use App\Models\User;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class FcmService
{
    public static function sendPushNotification(User $user, string $title, string $body, array $data = [])
    {
        if (!$user->fcm_token) {
            return false;
        }

        $credentials = env('FIREBASE_CREDENTIALS');
        $serviceAccount = null;

        if ($credentials) {
            $decoded = base64_decode($credentials, true);
            if ($decoded !== false && json_decode($decoded, true) !== null) {
                $serviceAccount = json_decode($decoded, true);
            } else {
                $serviceAccount = json_decode($credentials, true);
            }
        } else {
            $serviceAccountPath = storage_path('app/firebase-service-account.json');
            if (file_exists($serviceAccountPath)) {
                $serviceAccount = json_decode(file_get_contents($serviceAccountPath), true);
            }
        }

        if (!$serviceAccount) {
            Log::warning("Firebase credentials not found in env (FIREBASE_CREDENTIALS) or storage/app/firebase-service-account.json");
            return false;
        }

        try {
            $accessToken = self::getAccessToken($serviceAccount);
            if (!$accessToken) {
                return false;
            }

            $projectId = $serviceAccount['project_id'];
            $url = "https://fcm.googleapis.com/v1/projects/{$projectId}/messages:send";

            $payload = [
                'message' => [
                    'token' => $user->fcm_token,
                    'notification' => [
                        'title' => $title,
                        'body' => $body,
                    ],
                ]
            ];

            if (!empty($data)) {
                $payload['message']['data'] = array_map('strval', $data);
            }

            $response = Http::withHeaders([
                'Authorization' => 'Bearer ' . $accessToken,
                'Content-Type' => 'application/json',
            ])->post($url, $payload);

            if ($response->successful()) {
                return true;
            }

            Log::error("FCM Send failed: " . $response->body());
            return false;
        } catch (\Exception $e) {
            Log::error("Error sending FCM notification: " . $e->getMessage());
            return false;
        }
    }

    private static function getAccessToken(array $serviceAccount)
    {
        $privateKey = $serviceAccount['private_key'];
        $clientEmail = $serviceAccount['client_email'];

        $header = json_encode(['alg' => 'RS256', 'typ' => 'JWT']);
        $now = time();
        $payload = json_encode([
            'iss' => $clientEmail,
            'scope' => 'https://www.googleapis.com/auth/firebase.messaging',
            'aud' => 'https://oauth2.googleapis.com/token',
            'exp' => $now + 3600,
            'iat' => $now,
        ]);

        $base64UrlHeader = self::base64UrlEncode($header);
        $base64UrlPayload = self::base64UrlEncode($payload);

        $signatureInput = $base64UrlHeader . "." . $base64UrlPayload;
        $signature = '';
        openssl_sign($signatureInput, $signature, $privateKey, 'SHA256');
        $base64UrlSignature = self::base64UrlEncode($signature);

        $jwt = $signatureInput . "." . $base64UrlSignature;

        $response = Http::asForm()->post('https://oauth2.googleapis.com/token', [
            'grant_type' => 'urn:ietf:params:oauth:grant-type:jwt-bearer',
            'assertion' => $jwt,
        ]);

        if ($response->successful()) {
            return $response->json()['access_token'] ?? null;
        }

        Log::error("OAuth2 Token generation failed: " . $response->body());
        return null;
    }

    private static function base64UrlEncode(string $data)
    {
        return str_replace(['+', '/', '='], ['-', '_', ''], base64_encode($data));
    }
}

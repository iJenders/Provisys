<?php

class JWT
{
    public static function base64url_encode($data)
    {
        $encoded = base64_encode($data);
        $encoded = str_replace(['+', '/', '='], ['-', '_', ''], $encoded);
        return $encoded;
    }

    public static function generateToken($payload, $secret)
    {
        $header = json_encode([
            'alg' => 'HS256',
            'typ' => 'JWT'
        ]);
        $payload = json_encode($payload);

        $base64url_header = JWT::base64url_encode($header);
        $base64url_payload = JWT::base64url_encode($payload);

        $signature = hash_hmac(
            'sha256',
            $base64url_header . '.' . $base64url_payload,
            $secret
        );

        $token = $base64url_header . '.' . $base64url_payload . '.' . JWT::base64url_encode($signature);

        return $token;
    }

    public static function validateToken($token, $secret)
    {
        $parts = explode('.', $token);
        if (count($parts) !== 3) {
            return false;
        }

        list($header, $payload, $signature) = $parts;

        // Verificar la firma
        $expected_signature = JWT::base64url_encode(
            hash_hmac('sha256', "$header.$payload", $secret)
        );

        if ($signature !== $expected_signature) {
            return false;
        }

        // Decodificar el payload
        $decoded_payload = json_decode(base64_decode(str_replace(['-', '_'], ['+', '/'], $payload)), true);

        // Verificar la expiración
        if (isset($decoded_payload['exp']) && time() > $decoded_payload['exp']) {
            return false;
        }

        return $decoded_payload;
    }

    public static function getPayload($token)
    {
        $parts = explode('.', $token);
        if (count($parts) !== 3) {
            return null;
        }

        $payload = base64_decode(str_replace(['-', '_'], ['+', '/'], $parts[1]));
        return json_decode($payload, true);
    }
}
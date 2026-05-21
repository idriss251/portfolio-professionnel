<?php
/**
 * JwtHelper - Génération et validation de JWT tokens
 */

class JwtHelper
{
    public static function generate($data, $expiresIn = null)
    {
        $secret = Config::get('JWT_SECRET');
        $expiresIn = $expiresIn ?? Config::get('JWT_EXPIRE', 7200);
        
        $header = [
            'alg' => Config::get('JWT_ALGORITHM', 'HS256'),
            'typ' => 'JWT'
        ];
        
        $payload = array_merge($data, [
            'iat' => time(),
            'exp' => time() + $expiresIn
        ]);
        
        $headerEncoded = self::base64UrlEncode(json_encode($header));
        $payloadEncoded = self::base64UrlEncode(json_encode($payload));
        
        $signature = hash_hmac(
            'sha256',
            "$headerEncoded.$payloadEncoded",
            $secret,
            true
        );
        
        $signatureEncoded = self::base64UrlEncode($signature);
        
        return "$headerEncoded.$payloadEncoded.$signatureEncoded";
    }

    public static function verify($token)
    {
        $secret = Config::get('JWT_SECRET');
        
        $parts = explode('.', $token);
        if (count($parts) !== 3) {
            return null;
        }
        
        list($headerEncoded, $payloadEncoded, $signatureEncoded) = $parts;
        
        $signature = hash_hmac(
            'sha256',
            "$headerEncoded.$payloadEncoded",
            $secret,
            true
        );
        
        $expectedSignature = self::base64UrlEncode($signature);
        
        if ($signatureEncoded !== $expectedSignature) {
            return null;
        }
        
        $payload = json_decode(self::base64UrlDecode($payloadEncoded), true);
        
        // Check expiration
        if (isset($payload['exp']) && $payload['exp'] < time()) {
            return null;
        }
        
        return $payload;
    }

    public static function getFromHeader()
    {
        $header = $_SERVER['HTTP_AUTHORIZATION'] ?? '';
        
        if (preg_match('/Bearer\s+(\S+)/', $header, $matches)) {
            return $matches[1];
        }
        
        return null;
    }

    private static function base64UrlEncode($str)
    {
        return rtrim(strtr(base64_encode($str), '+/', '-_'), '=');
    }

    private static function base64UrlDecode($str)
    {
        return base64_decode(strtr($str, '-_', '+/'));
    }
}

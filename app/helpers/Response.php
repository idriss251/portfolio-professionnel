<?php
/**
 * Response - Format réponses API en JSON standardisé
 */

class Response
{
    public static function json($data = [], $statusCode = 200, $message = null)
    {
        http_response_code($statusCode);
        header('Content-Type: application/json; charset=utf-8');
        
        $response = [
            'success' => $statusCode >= 200 && $statusCode < 300,
            'status' => $statusCode,
        ];
        
        if ($message) {
            $response['message'] = $message;
        }
        
        if (is_array($data) && !empty($data)) {
            $response['data'] = $data;
        } elseif ($data) {
            $response['data'] = $data;
        }
        
        echo json_encode($response, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);
        exit();
    }

    public static function success($data = [], $message = 'Success', $statusCode = 200)
    {
        self::json($data, $statusCode, $message);
    }

    public static function error($message = 'Error', $statusCode = 400, $data = [])
    {
        self::json($data, $statusCode, $message);
    }

    public static function notFound($message = 'Resource not found')
    {
        self::error($message, 404);
    }

    public static function unauthorized($message = 'Unauthorized')
    {
        self::error($message, 401);
    }

    public static function forbidden($message = 'Forbidden')
    {
        self::error($message, 403);
    }

    public static function badRequest($message = 'Bad request', $errors = [])
    {
        self::json(['errors' => $errors], 400, $message);
    }
}

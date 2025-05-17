<?php

// Middleware para manejar solicitudes JSON.
// Este verifica que el cuerpo de la solicitud sea JSON válido.

class JsonMiddleware
{
    public static function handle($request, $params)
    {
        $headers = getallheaders();

        if (!isset($headers['Content-Type'])) {
            Responses::json(['message' => 'Header Content-Type no especificado'], 415);
            exit;
        }

        if (isset($headers['Content-Type']) && $headers['Content-Type'] !== 'application/json') {
            Responses::json(['message' => 'Header Content-Type inválido. Se esperaba application/json'], 415);
            exit;
        }

        // Intenta decodificar el cuerpo de la solicitud JSON para asegurarse de que sea válido
        // Aunque $body no se usa, es útil para validar el JSON
        $body = json_decode(file_get_contents('php://input'), true);
        if (json_last_error() !== JSON_ERROR_NONE) {
            Responses::json(['message' => 'JSON inválido: ' . json_last_error_msg()], 400);
            exit;
        }
    }
}
?>
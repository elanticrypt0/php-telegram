<?php
declare(strict_types=1);

namespace TelegramBridge;

class TelegramBot {
    private string $token;
    private string $apiUrl = 'https://api.telegram.org/bot';
    private array $allowedChatIds = [];
    
    public function __construct(
        string $token,
        array $allowedChatIds = []
    ) {
        $this->token = $token;
        $this->allowedChatIds = $allowedChatIds;
        $this->apiUrl .= $token;
    }

    /**
     * Envía un mensaje a un chat específico
     */
    public function sendMessage(
        int|string $chatId, 
        string $message, 
        array $extra = []
    ): array {
        // Validar que el chat_id esté permitido
        if (!empty($this->allowedChatIds) && !in_array($chatId, $this->allowedChatIds)) {
            throw new \Exception("Chat ID no autorizado");
        }

        $data = [
            'chat_id' => $chatId,
            'text' => $message,
            'parse_mode' => 'HTML',
        ] + $extra;

        return $this->makeRequest('sendMessage', $data);
    }

    /**
     * Envía una imagen con caption opcional
     */
    public function sendPhoto(
        int|string $chatId, 
        string $photo, 
        string $caption = '',
        array $extra = []
    ): array {
        $data = [
            'chat_id' => $chatId,
            'photo' => $photo,
            'caption' => $caption,
            'parse_mode' => 'HTML',
        ] + $extra;

        return $this->makeRequest('sendPhoto', $data);
    }

    /**
     * Envía un documento con caption opcional
     */
    public function sendDocument(
        int|string $chatId, 
        string $document, 
        string $caption = '',
        array $extra = []
    ): array {
        $data = [
            'chat_id' => $chatId,
            'document' => $document,
            'caption' => $caption,
            'parse_mode' => 'HTML',
        ] + $extra;

        return $this->makeRequest('sendDocument', $data);
    }

    /**
     * Realiza la petición a la API de Telegram
     */
    private function makeRequest(string $endpoint, array $data, int $retries = 3): array {
        $url = "{$this->apiUrl}/$endpoint";
        
        $ch = curl_init();
        curl_setopt_array($ch, [
            CURLOPT_URL => $url,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_POST => true,
            CURLOPT_POSTFIELDS => $data,
            CURLOPT_SSL_VERIFYPEER => true,
            CURLOPT_TIMEOUT => 10,
        ]);

        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $error = curl_error($ch);
        curl_close($ch);

        if ($response === false) {
            if ($retries > 0) {
                sleep(1);
                return $this->makeRequest($endpoint, $data, $retries - 1);
            }
            throw new \Exception("Error en la petición: $error");
        }

        $result = json_decode($response, true);
        
        if ($httpCode !== 200) {
            if ($retries > 0 && in_array($httpCode, [429, 500, 502, 503, 504])) {
                sleep(1);
                return $this->makeRequest($endpoint, $data, $retries - 1);
            }
            throw new \Exception(
                "Error en la API de Telegram: " . 
                ($result['description'] ?? "HTTP Code: $httpCode")
            );
        }

        return $result;
    }
}

// Clase para el manejo de webhooks
class WebhookHandler {
    private string $secretToken;
    private array $handlers = [];

    public function __construct(string $secretToken) {
        $this->secretToken = $secretToken;
    }

    /**
     * Registra un manejador para un comando específico
     */
    public function onCommand(string $command, callable $handler): void {
        $this->handlers[$command] = $handler;
    }

    /**
     * Procesa los webhooks entrantes
     */
    public function handleRequest(): void {
        // Verificar el token secreto
        $headerToken = $_SERVER['HTTP_X_TELEGRAM_BOT_API_SECRET_TOKEN'] ?? '';
        if (!hash_equals($this->secretToken, $headerToken)) {
            http_response_code(403);
            die('Acceso no autorizado');
        }

        // Obtener y validar el payload
        $payload = json_decode(file_get_contents('php://input'), true);
        if (empty($payload)) {
            http_response_code(400);
            die('Payload inválido');
        }

        // Procesar el mensaje
        $message = $payload['message'] ?? null;
        if ($message && isset($message['text'])) {
            $command = strtolower(trim($message['text']));
            if (isset($this->handlers[$command])) {
                $this->handlers[$command]($message);
            }
        }

        http_response_code(200);
        echo 'OK';
    }
}
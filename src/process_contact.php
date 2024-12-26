<?php
declare(strict_types=1);

require_once(__DIR__.'/bot/TelegramBridge.php');

// Archivo process_contact.php
$config = require_once(__DIR__."/bot/config/config_bot.php");

class ContactFormHandler {
    private TelegramBridge\TelegramBot $bot;
    private string $targetChatId;
    
    public function __construct(
        string $botToken,
        string $targetChatId,
        array $allowedChats = []
    ) {
        $this->bot = new TelegramBridge\TelegramBot($botToken, $allowedChats);
        $this->targetChatId = $targetChatId;
    }
    
    public function processForm(): void {
        header('Content-Type: application/json');
        
        try {
            // Validar que sea una petición POST
            if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
                throw new \Exception('Método no permitido');
            }
            
            // Validar y sanitizar los datos del formulario
            $name = $this->validateField('name', 'Nombre');
            $email = $this->validateEmail();
            $message = $this->validateField('message', 'Mensaje');
            
            // Construir el mensaje para Telegram
            $telegramMessage = $this->buildTelegramMessage($name, $email, $message);
            
            // Enviar el mensaje a Telegram
            $this->bot->sendMessage($this->targetChatId, $telegramMessage);
            
            // Opcional: Guardar en base de datos
            // $this->saveToDatabase($name, $email, $message);
            
            // Enviar respuesta de éxito
            echo json_encode([
                'success' => true,
                'message' => '¡Mensaje enviado con éxito!'
            ]);
            
        } catch (\Exception $e) {
            http_response_code(400);
            echo json_encode([
                'success' => false,
                'message' => $e->getMessage()
            ]);
        }
    }
    
    private function validateField(string $fieldName, string $label): string {
        $value = trim($_POST[$fieldName] ?? '');
        if (empty($value)) {
            throw new \Exception("El campo {$label} es requerido.");
        }
        return htmlspecialchars($value, ENT_QUOTES, 'UTF-8');
    }
    
    private function validateEmail(): string {
        $email = trim($_POST['email'] ?? '');
        if (empty($email) || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
            throw new \Exception('Email inválido.');
        }
        return htmlspecialchars($email, ENT_QUOTES, 'UTF-8');
    }
    
    private function buildTelegramMessage(
        string $name,
        string $email,
        string $message
    ): string {
        return "📬 <b>Nuevo mensaje de contacto</b>\n\n" .
               "👤 <b>Nombre:</b> {$name}\n" .
               "📧 <b>Email:</b> {$email}\n" .
               "💬 <b>Mensaje:</b>\n{$message}\n\n" .
               "📅 <b>Fecha:</b> " . date('Y-m-d H:i:s');
    }
    
    // Opcional: Método para guardar en base de datos
    private function saveToDatabase(
        string $name,
        string $email,
        string $message
    ): void {
        // Implementar si se requiere
    }
}


$handler = new ContactFormHandler(
    $config['bot_token'],
    $config['target_chat_id'],
    $config['allowed_chats']
);

$handler->processForm();
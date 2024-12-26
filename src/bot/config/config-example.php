<?php

// generar automáticamente con generate_config.py

$config = [
    'bot_token' => 'TELEGRAM_API_KEY',
    'target_chat_id' => 'ID_DEL_CHAT_DESTINO',
    'allowed_chats' => ['ID_DEL_CHAT_DESTINO'] // Por seguridad
];

return $config;
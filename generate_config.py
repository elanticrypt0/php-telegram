import telebot
import os
from keys import BOT_TOKEN

# Verificar que el token esté configurado
if not BOT_TOKEN:
    print("Error: Debes configurar la variable de entorno TELEGRAM_BOT_TOKEN")
    exit(1)

# Crear el bot
bot = telebot.TeleBot(BOT_TOKEN)

# Manejador para el comando /start
@bot.message_handler(commands=['php'])
def send_welcome(message):
    # Obtener información del chat y del usuario
    chat_id = message.chat.id
    user_id = message.from_user.id
    first_name = message.from_user.first_name
    username = message.from_user.username or "Sin username"

    # Ruta del archivo de configuración
    config_file_path = './src/bot/config/config_bot.php'

    # Verificar si el archivo ya existe
    if not os.path.exists(config_file_path):
        # Contenido del archivo de configuración PHP
        config_content = f"""<?php
$config = [
    'bot_token' => '{BOT_TOKEN}',
    'target_chat_id' => '{chat_id}',
    'allowed_chats' => ['{chat_id}'] // Por seguridad
];
return $config;
"""
        # Escribir el archivo de configuración
        try:
            with open(config_file_path, 'w') as config_file:
                config_file.write(config_content)
            
            # Mensaje de confirmación
            response = (
                f"¡Hola {first_name}! 👋\n\n"
                f"📍 ID de Conversación (Chat ID): <code>{chat_id}</code>\n"
                f"👤 ID de Usuario: <code>{user_id}</code>\n"
                f"🏷️ Username: {username}\n\n"
                f"✅ Archivo de configuración <code>config_bot.php</code> generado con éxito."
            )
        except Exception as e:
            response = (
                f"¡Hola {first_name}! 👋\n\n"
                f"📍 ID de Conversación (Chat ID): <code>{chat_id}</code>\n"
                f"👤 ID de Usuario: <code>{user_id}</code>\n"
                f"🏷️ Username: {username}\n\n"
                f"❌ Error al generar el archivo de configuración: {str(e)}"
            )
    else:
        # Si el archivo ya existe
        response = (
            f"¡Hola {first_name}! 👋\n\n"
            f"📍 ID de Conversación (Chat ID): <code>{chat_id}</code>\n"
            f"👤 ID de Usuario: <code>{user_id}</code>\n"
            f"🏷️ Username: {username}\n\n"
            f"ℹ️ El archivo <code>config_bot.php</code> ya existe. No se modificó."
        )

    # Enviar el mensaje con formato HTML
    bot.reply_to(message, response, parse_mode='HTML')

# Manejador para otros mensajes
@bot.message_handler(func=lambda message: True)
def echo_all(message):
    bot.reply_to(message, "Envía /start para obtener tus IDs.")

# Función principal con generación de enlace e información
def main():
    # Generar enlace de inicio para el bot
    bot_link = f"https://t.me/{bot.get_me().username}"

    # Imprimir información de inicio
    print(f"🤖 Bot iniciado con éxito!")
    print(f"📱 Link de inicio: {bot_link}")
    print("Presiona Ctrl+C para detener.")

    # Iniciar el bot
    bot.infinity_polling(timeout=10, long_polling_timeout=5)

if __name__ == '__main__':
    main()
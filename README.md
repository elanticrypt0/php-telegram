# Bot de Notificaciones para Formulario de Contacto

Este proyecto implementa un bot de Telegram que recibe y notifica mensajes de un formulario de contacto web. Es una solución perfecta para mantener un registro de los mensajes de contacto directamente en tu chat de Telegram.

## ¿Cómo funciona?

1. Cuando alguien completa el formulario de contacto en tu sitio web, el sistema:
   - Valida la información ingresada
   - Formatea el mensaje de manera legible
   - Envía una notificación instantánea a tu chat de Telegram

2. La notificación incluye:
   - Nombre del contacto
   - Email
   - Mensaje
   - Fecha y hora del envío

## Instalación

### 1. Configurar el Bot de Telegram

1. Crea un nuevo bot en Telegram:
   - Habla con [@BotFather](https://t.me/BotFather) en Telegram
   - Usa el comando `/newbot`
   - Sigue las instrucciones y guarda el token que te proporciona

2. Configura el token:
   - Renombra el archivo `keys-example.py` a `keys.py`
   - Coloca el token de tu bot en este archivo

3. Genera la configuración:
```bash
pip install pyTelegramBotAPI
python generate_config.py
```

4. Inicia el bot y envía el comando `/php` para generar la configuración automáticamente

### 2. Iniciar el Entorno de Docker

1. Construye e inicia los contenedores:
```bash
docker-compose up -d
```

2. El servicio estará disponible en:
   - http://localhost:8081

## Pruebas

Para probar el sistema:
1. Accede a http://localhost:8081
2. Completa el formulario de contacto
3. Deberías recibir una notificación en el chat de Telegram configurado

## Notas Importantes
- Asegúrate de que el bot tenga permisos para enviar mensajes en el chat configurado
- El sistema está configurado para enviar mensajes solo a los chats autorizados por seguridad
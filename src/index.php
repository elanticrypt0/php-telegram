<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Formulario de Contacto</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            line-height: 1.6;
            max-width: 600px;
            margin: 2rem auto;
            padding: 0 1rem;
        }
        
        .contact-form {
            background: #f9f9f9;
            padding: 2rem;
            border-radius: 8px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }
        
        .form-group {
            margin-bottom: 1rem;
        }
        
        label {
            display: block;
            margin-bottom: 0.5rem;
            font-weight: bold;
        }
        
        input, textarea {
            width: 100%;
            padding: 0.5rem;
            border: 1px solid #ddd;
            border-radius: 4px;
            font-size: 1rem;
        }
        
        button {
            background: #0066cc;
            color: white;
            padding: 0.75rem 1.5rem;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            font-size: 1rem;
        }
        
        button:hover {
            background: #0052a3;
        }
        
        .alert {
            padding: 1rem;
            border-radius: 4px;
            margin-bottom: 1rem;
            display: none;
        }
        
        .alert-success {
            background: #d4edda;
            color: #155724;
            border: 1px solid #c3e6cb;
        }
        
        .alert-error {
            background: #f8d7da;
            color: #721c24;
            border: 1px solid #f5c6cb;
        }
    </style>
</head>
<body>
    <div class="contact-form">
        <h2>Formulario de Contacto</h2>
        
        <div id="success-alert" class="alert alert-success">
            ¡Mensaje enviado con éxito!
        </div>
        
        <div id="error-alert" class="alert alert-error">
            Ha ocurrido un error. Por favor, intente nuevamente.
        </div>
        
        <form id="contactForm" method="post" action="process_contact.php">
            <div class="form-group">
                <label for="name">Nombre:</label>
                <input type="text" id="name" name="name" required>
            </div>
            
            <div class="form-group">
                <label for="email">Email:</label>
                <input type="email" id="email" name="email" required>
            </div>
            
            <div class="form-group">
                <label for="message">Mensaje:</label>
                <textarea id="message" name="message" rows="4" required></textarea>
            </div>
            
            <button type="submit">Enviar Mensaje</button>
        </form>
    </div>

    <script>
        document.getElementById('contactForm').addEventListener('submit', function(e) {
            e.preventDefault();
            
            const formData = new FormData(this);
            
            fetch('process_contact.php', {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                const successAlert = document.getElementById('success-alert');
                const errorAlert = document.getElementById('error-alert');
                
                successAlert.style.display = 'none';
                errorAlert.style.display = 'none';
                
                if (data.success) {
                    successAlert.style.display = 'block';
                    this.reset();
                } else {
                    errorAlert.style.display = 'block';
                    errorAlert.textContent = data.message || 'Ha ocurrido un error. Por favor, intente nuevamente.';
                }
            })
            .catch(error => {
                const errorAlert = document.getElementById('error-alert');
                errorAlert.style.display = 'block';
                errorAlert.textContent = 'Error de conexión. Por favor, intente nuevamente.';
            });
        });
    </script>
</body>
</html>
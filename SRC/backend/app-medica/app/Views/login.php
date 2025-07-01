<!DOCTYPE html> 
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login</title>
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=DM+Sans&display=swap" rel="stylesheet">
    <link rel="icon" href="<?= base_url('icon.png') ?>" type="image/png">
    <style>
        body {
            font-family: 'DM Sans', sans-serif;
            background-color: #f4f4f4;
            display: flex;
            align-items: stretch;
            height: 100vh;
            font-weight: 300;
            flex-direction: column;
            flex-wrap: wrap;
            align-content: space-around;
            justify-content: center;
        }

        .form {
            font-family: 'DM Sans', sans-serif;
            background-color: white;
            padding: 30px;
            border-radius: 10px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            border: 2px solid #e1e1e1;
            text-align: center;
        }

        .input-group {
            display: flex;
            align-items: center;
            margin-bottom: 34px;
            border: 1px solid #ccc;
            border-radius: 6px;
            padding: 5px 10px;
            background-color: #fff;
        }

        .input-group .material-icons {
            margin-right: 8px;
            color: #666;
        }

        .input-group input {
            border: none;
            outline: none;
            width: 100%;
            font-size: 14px;
            padding: 8px 0;
        }

        .input-group .toggle-password {
            cursor: pointer;
            color: #666;
        }

        h1 {
            font-family: 'DM Sans', sans-serif;
            color: #009fdf;
            width: 300px;
            font-size: 35px;
        }

        button {
            background-color: #009fdf;
            color: white;
            border: none;
            padding: 10px 20px;
            border-radius: 6px;
            cursor: pointer;
            font-size: 16px;
            
    margin: 0 auto; 
    display: flex;
    justify-content: center;
    align-items: center; 
    gap: 8px;
        }

        button:hover {
            background-color: #007bbd;
        }

        .alert {
            padding: 10px 20px;
            margin-bottom: 20px;
            border-radius: 6px;
            font-size: 14px;
            opacity: 1;
            transition: opacity 0.5s ease, height 0.5s ease, padding 0.5s ease;
        }

        .alert.hidden {
            opacity: 0;
            height: 0;
            padding: 0 20px;
            overflow: hidden;
            border: none;
            margin: 0;
        }

        .alert.error {
            background-color: #ffe0e0;
            color: #d8000c;
            border: 1px solid #d8000c;
        }

        .alert.success {
            background-color: #e0f9e0;
            color: #267a2f;
            border: 1px solid #267a2f;
        }

        button:disabled {
            background-color: #009fdf61;
            cursor: not-allowed;
        }

        .alert {
        padding: 15px;
        margin-top: 20px;
        border-radius: 8px;
        text-align: center;
        font-weight: bold;
        transition: all 0.3s ease;
    }

    .alert.success {
        background-color: #d4edda;
        color: #155724;
    }

    .alert.error {
        background-color: #f8d7da;
        color: #721c24;
    }

    .alert.hidden {
        display: none;
    }

    /* Estilo del modal */
    .modal {
        display: none;
    position: fixed;
    z-index: 9999;
    left: 0;
    top: 0;
    width: 100%;
    height: 100%;
    overflow: auto;
    background-color: rgba(0, 0, 0, 0.5);
    color: #007bbd;
    }

    .modal-content {
        background-color: #fff;
        margin: 13% auto;
        padding: 31px;
        border-radius: 15px;
        width: 90%;
        max-width: 400px;
        text-align: center;
        box-shadow: 0px 0px 10px rgba(0,0,0,0.25);
    }

    .modal-content h2 {
        margin-bottom: 52px;
        font-size: 22px;
    }

    .modal-buttons {
        display: flex;
        justify-content: space-around;
        margin-top: 7px;
    }

    .modal-buttons button {
        padding: 10px 20px;
        border: none;
        border-radius: 10px;
        font-weight: bold;
        cursor: pointer;
        transition: background 0.3s ease;
    }

    .btn-confirm {
        background-color: #009fdf;
        color: white;
        width: 111px;
    }

    .btn-confirm:hover {
        background-color: #007bbd;
    }

    .btn-cancel {
        background-color: #e7656d;
    }

    .btn-cancel:hover {
        background-color: #999;
    }
    .spinner {
    border: 4px solid rgba(255, 255, 255, 0.3); /* Color de fondo del spinner */
    border-radius: 50%;
    border-top: 4px solid #fff; /* Color principal del spinner */
    width: 20px;
    height: 20px;
    -webkit-animation: spin 1s linear infinite; /* Animación para navegadores basados en Webkit */
    animation: spin 1s linear infinite;
    display: inline-block; /* Permite que el spinner se muestre en línea */
    vertical-align: middle; /* Alineación vertical con el texto si hay */
    margin-left: 8px; /* Espacio a la izquierda del spinner si hay texto */
    box-sizing: border-box;
}

/* Animación de giro del spinner */
@-webkit-keyframes spin {
    0% { -webkit-transform: rotate(0deg); }
    100% { -webkit-transform: rotate(360deg); }
}

@keyframes spin {
    0% { transform: rotate(0deg); }
    100% { transform: rotate(360deg); }
}

/* Clase de utilidad para ocultar/mostrar elementos */
.hidden {
    display: none !important;
}

/* Estilos para el botón cuando el spinner está activo */
#submitBtn {
    display: flex; /* Usamos flexbox para centrar y alinear los elementos internos */
    justify-content: center; /* Centra horizontalmente */
    align-items: center; /* Centra verticalmente */
    gap: 5px; /* Espacio entre el texto y el spinner */
}

/* Opcional: mejora visual para el botón deshabilitado */
button:disabled {
    cursor: not-allowed;
    opacity: 0.8; /* Ligeramente más transparente cuando está deshabilitado */
}
    </style>
</head>

<body>
    <!-- Alerta reutilizable -->
    <div id="alert-message" class="alert hidden"></div>

    <div class="form">
        <h1>Iniciar Sesión</h1>  
        <form id="loginForm">
            <div class="input-group">
                <span class="material-icons">person</span>
                <input type="text" placeholder="Usuario" name="usuario" id="usuario">
            </div>

            <div class="input-group">
                <span class="material-icons">lock</span>
                <input type="password" placeholder="Contraseña" id="password">
    <span class="material-icons toggle-password" onclick="togglePassword()">visibility</span>
            </div>

            <button type="submit" id="submitBtn" disabled>
            <span id="buttonText">Ingresar</span>
            <span id="spinner" class="spinner hidden"></span>
            </button>

            <div style="margin-top: 27px;">
                <a href="#" onclick="event.preventDefault(); enviarRecuperacion()" style="color: #009fdf; font-size: 17px; text-decoration: none;">
                    ¿Olvidaste tu contraseña?
                </a>
            </div>
        </form>
    </div>
<!-- Modal de confirmación -->
<div id="modalRecuperacion" class="modal">
    <div class="modal-content">
        <h2>¿Estás seguro que deseas restablecer tu contraseña?</h2>
        <div class="modal-buttons">
            <button class="btn-cancel" onclick="cerrarModal()">Cancelar</button>
            <button class="btn-confirm" onclick="confirmarRecuperacion()">Sí</button>
        </div>
    </div>
</div>
<script>
    function togglePassword() {
    const passwordInput = document.getElementById('password');
    const toggleIcon = passwordInput.nextElementSibling;

    if (passwordInput.type === 'password') {
        passwordInput.type = 'text';
        toggleIcon.textContent = 'visibility_off';
    } else {
        passwordInput.type = 'password';
        toggleIcon.textContent = 'visibility';
    }
}
 function enviarRecuperacion() {
    document.getElementById("modalRecuperacion").style.display = "block";
}

function cerrarModal() {
    document.getElementById("modalRecuperacion").style.display = "none";
}

function confirmarRecuperacion() {
    cerrarModal();

    fetch('<?= site_url('solicitar-cambio-password'); ?>', {
    method: 'POST'
})
.then(async response => {
    const text = await response.text();

    try {
        const data = JSON.parse(text);
        if (data.status === 'success') {
            mostrarAlerta(data.message, 'success');
        } else {
            mostrarAlerta(data.message || 'Ocurrió un error', 'error');
        }
    } catch (e) {
        mostrarAlerta('Respuesta inesperada del servidor', 'error');
    }
})
.catch(error => {
    mostrarAlerta('Error al conectar con el servidor.', 'error');
});
}

    const submitBtn = document.getElementById('submitBtn');
    const usuario = document.getElementById("usuario");
    const password = document.getElementById("password");
    const buttonText = document.getElementById('buttonText');
    const spinner = document.getElementById('spinner');    
    function showLoadingState() {
        buttonText.classList.add('hidden');    // Oculta el texto "Ingresar"
        spinner.classList.remove('hidden'); // Muestra el spinner
        submitBtn.disabled = true;          // Deshabilita el botón para evitar múltiples envíos
        submitBtn.style.cursor = 'wait';    // Cambia el cursor a "espera"
    }

    function hideLoadingState() {
        buttonText.classList.remove('hidden'); // Muestra el texto "Ingresar"
        spinner.classList.add('hidden');    // Oculta el spinner
        submitBtn.style.cursor = 'pointer'; // Restaura el cursor
        validarCampos(); // Vuelve a habilitar el botón si los campos son válidos (o lo mantiene deshabilitado si no lo son)
    }
    document.getElementById("loginForm").addEventListener("submit", function(event) {
        event.preventDefault();

        const usuarioVal = usuario.value;
        const passwordVal = password.value;

        clearTimeout(window.alertTimeout);

        fetch('<?= site_url('login'); ?>', {
            method: "POST",
            headers: {
                "Content-Type": "application/json"
            },
            body: JSON.stringify({
                nombre_usuario: usuarioVal,
                pass: passwordVal
            })
        })
        .then(response => response.json())
        .then(data => {
            if (data.status === 'success') {
               
                if (data.data.pidio_cambio === "1") {
                    localStorage.setItem('expiracion', data.data.expiracion); 
            window.location.href = "reset";
        } else {
            localStorage.setItem('expiracion', data.data.expiracion); 
            window.location.href = "formulario";
        }
            } else {
                mostrarAlerta('Usuario o Contraseña incorrectos', 'error');
                hideLoadingState(); 
            }
        })
        .catch(error => {
            console.error("Error en la solicitud:", error);
            mostrarAlerta('Error en la conexión con el servidor', 'error');
            hideLoadingState(); 
        });
    });

    function validarCampos() {
        const usuarioVal = usuario.value.trim();
        const passwordVal = password.value.trim();
        submitBtn.disabled = usuarioVal === '' || passwordVal === '' || !spinner.classList.contains('hidden');
    }

    window.addEventListener('DOMContentLoaded', () => {
        validarCampos();
        document.getElementById('alert-message').classList.add('hidden');
    });

    usuario.addEventListener('keyup', validarCampos);
    password.addEventListener('keyup', validarCampos);

  

    function mostrarAlerta(mensaje, tipo = 'error') {
        const alertDiv = document.getElementById("alert-message");

        alertDiv.textContent = mensaje;
        alertDiv.className = `alert ${tipo}`; // Aplica clase 'alert success' o 'alert error'
        alertDiv.classList.remove("hidden");

        clearTimeout(window.alertTimeout);
        window.alertTimeout = setTimeout(() => {
            alertDiv.classList.add("hidden");
        }, 4000);
    }
</script>
</body>
</html>
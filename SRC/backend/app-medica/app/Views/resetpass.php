<?php include('header.php'); ?>

<style>
    .form-container {
        display: flex;
    padding-top: 85px;
    padding-right: 10px;
    flex-wrap: wrap;
    align-content: center;
    flex-direction: column;
}


    .form {
        background-color: white;
        padding: 37px;
        border-radius: 10px;
        box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        border: 2px solid #e1e1e1;
        width: 30%;
        max-width: 400px;
        text-align: center;
    }

    .form h1 {
        color: #009fdf;
        font-size: 28px;
        margin-bottom: 42px;
    }

    .input-group {
        display: flex;
        align-items: center;
        margin-bottom: 36px;
        border: 1px solid #ccc;
        border-radius: 6px;
        padding: 8px;
        background-color: #fff;
    }

    .input-group .material-icons {
        margin-right: 10px;
        color: #666;
    }

    .input-group input {
        border: none;
        outline: none;
        width: 100%;
        font-size: 16px;
        padding: 5px 0;
        background: transparent;
    }

    button {
        width: 100px;
        padding: 10px;
        background-color: #009fdf;
        border: none;
        border-radius: 6px;
        color: white;
        font-size: 16px;
        cursor: pointer;
        transition: background-color 0.3s ease;
    }

    button:hover {
        background-color: #007bbd;
    }

    button:disabled {
        background-color: #009fdf61;
        cursor: not-allowed;
        width: 100px;
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
        padding: 0;
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
    .toggle-password {
    cursor: pointer;
    color: #666;
    margin-left: 8px;
}
</style>

<div class="form-container">
    <div id="alert-message" class="alert hidden"></div>
    <div class="form">
        <h1>Cambiar contraseña</h1>

        <form id="changePassForm">
            <div class="input-group">
                <span class="material-icons">lock</span>
                <input type="password" placeholder="Contraseña nueva" id="passwordnueva" required>
                <span class="material-icons toggle-password" data-target="passwordnueva">visibility</span>
                
            </div>

            <div class="input-group">
                <span class="material-icons">lock</span>
                <input type="password" placeholder="Repetir contraseña" id="passwordrepetida" required>
                <span class="material-icons toggle-password" data-target="passwordrepetida">visibility</span>
            </div>

            <button type="submit" id="submitBtn" disabled>Cambiar</button>
        </form>
    </div>
</div>


<script>
    const passwordNuevaInput = document.getElementById("passwordnueva");
    const passwordRepetidaInput = document.getElementById("passwordrepetida");
    const submitBtn = document.getElementById("submitBtn");

    function validarCampos() {
        const nueva = passwordNuevaInput.value.trim();
        const repetida = passwordRepetidaInput.value.trim();
        submitBtn.disabled = nueva === '' || repetida === '';
    }

    passwordNuevaInput.addEventListener("input", validarCampos);
    passwordRepetidaInput.addEventListener("input", validarCampos);

    document.getElementById("changePassForm").addEventListener("submit", function (event) {
        event.preventDefault();

        const pass1 = passwordNuevaInput.value.trim();
        const pass2 = passwordRepetidaInput.value.trim();

        if (!pass1 || !pass2) {
            mostrarAlerta("Por favor completá ambos campos.", "error");
            return;
        }

        if (pass1 !== pass2) {
            mostrarAlerta("Las contraseñas no coinciden.", "error");
            return;
        }

        // Enviar al backend
        
        const USUARIO_LOGUEADO = <?= json_encode(session()->get()) ?>;

        fetch('<?= site_url('cambio'); ?>', {
            method: "PUT",
            headers: {
                "Content-Type": "application/json"
            },
            body: JSON.stringify({
                id_usuario: USUARIO_LOGUEADO.id_usuario,
                password_nuevo: pass1,
                password_confirmar: pass2
            })
        })
        .then(response => response.json())
        .then(data => {
            
            if (data.status === "success") {
                mostrarAlerta("Contraseña cambiada con éxito.", "success");
                passwordNuevaInput.value = '';
                passwordRepetidaInput.value = '';
                validarCampos();
                window.location.href = "logout";
            } else {
                mostrarAlerta("Error al cambiar la contraseña.", "error");
            }
        })
        .catch(error => {
            console.error("Error:", error);
            mostrarAlerta("Error de conexión con el servidor.", "error");
        });
    });

    function mostrarAlerta(mensaje, tipo = 'error') {
        const alerta = document.getElementById("alert-message");
        alerta.textContent = mensaje;
        alerta.className = `alert ${tipo}`;
        alerta.classList.remove("hidden");

        clearTimeout(window.alertTimeout);
        window.alertTimeout = setTimeout(() => {
            alerta.classList.add("hidden");
        }, 3000);
    }
     // Mostrar/ocultar contraseña
     document.querySelectorAll('.toggle-password').forEach(icon => {
        icon.addEventListener('click', () => {
            const targetId = icon.getAttribute('data-target');
            const input = document.getElementById(targetId);
            const isPassword = input.type === 'password';
            input.type = isPassword ? 'text' : 'password';
            icon.textContent = isPassword ? 'visibility_off' : 'visibility';
        });
    });
</script>
<?php include('header.php'); ?>
<style>
    .formulario {
        padding: 20px;
    width: 600px;
    margin: 20px auto;
    background-color: #ffffff;
    border: 2px solid #e1e1e1;
    border-radius: 10px;
    box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
    }

    h2 {
        text-align: center;
        color: #007bbd;
        margin-bottom: 20px;
    }

    h3 {
        text-align: center;
        color: #007bbd;
        margin-bottom: 10px;
    }

    .form label {
        display: block;
        font-weight: bold;
        margin: 10px 0 5px;
    }

    /* Estilo para los inputs */
    .form input[type="text"],
    .form input[type="number"],
    .form input[type="email"],
    .form input[type="date"] {
        width: 241px;
    padding: 10px;
    margin-bottom: 16px;
    border: 1px solid #ccc;
    border-radius: 5px;
    font-size: 16px;
    }

    .form input[type="text"]:last-child,
    .form input[type="number"]:last-child,
    .form input[type="email"]:last-child,
    .form input[type="date"]:last-child {
        margin-right: 0;
    }

    /* Para los campos uno al lado del otro */
    .datos1 {
        display: flex;
        justify-content:  space-between;
       
    }

    /* Estilo para el área de texto */
    .form textarea {
        width: 96%;
        max-width: 96%;
        min-width: 96%;
        padding: 10px;
        margin-bottom: 16px;
        border: 1px solid #ccc;
        border-radius: 5px;
        font-size: 16px;
        height: 150px;
    }

    .form input[type="file"] {
        width: 96%;
        padding: 10px;
        margin-bottom: 16px;
        border: 1px solid #ccc;
        border-radius: 5px;
    }

    .form button {
        width: 100%;
        padding: 12px;
        background-color: #007bbd;
        color: white;
        border: none;
        border-radius: 5px;
        font-size: 16px;
        cursor: pointer;
    }

    .form button:hover {
        background-color: #005f8c;
    }
    select {
    height: 45px;
    border-radius: 4px;
    width: 260px;
    border-color: #c6bcbc;
    }
    .asterisco {
        color: #007bbd;
    }
    button:disabled {
            background-color: #009fdf61;
            cursor: not-allowed;
        }
        .alert {
            padding: 10px 20px;
            margin-bottom: 20px;
            border-radius: 6px;
            font-size: 14px;
            opacity: 1;
            transition: opacity 0.5s ease, height 0.5s ease, padding 0.5s ease;
        }

        

        button:disabled {
            background-color: #009fdf61;
            cursor: not-allowed;
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
    #alert-message {
    position: fixed;
    top: 10px;
    left: 50%;
    transform: translateX(-50%);
    z-index: 9999;
    width: 100%;
    max-width: 90%;
    text-align: center;
}

.mensaje-error {
    color: red;
    font-size: 0.9em;
    display: flex

}
  #close-alert {
    margin-left: 10px;
    font-size: 18px;
}
.autocomplete-container {
        position: relative;
        
    }

    .autocomplete-items {
        position: absolute;
        border-top: none;
        z-index: 99;
        top: 100%;
        left: 0;
        right: 0;
        background: white;
        max-height: 150px;
        overflow-y: auto;
        border-radius: 0 0 5px 5px;
    }

    .autocomplete-item {
        padding: 10px;
        cursor: pointer;
    }

    .autocomplete-item:hover {
        background-color: #f0f0f0;
    }
</style>


<div id="alert-message" class="alert hidden">
    <span id="close-alert" style="float:right; cursor:pointer; font-weight: bold;">&times;</span>
    <span id="alert-text"></span>
    </div>
<div class="formulario">
   <label>(<span class="asterisco">*</span>) Datos obligatorios</label> 
    <h2>Carga de Informe</h2>
    <form id="formInforme" class="form" enctype="multipart/form-data">

        <div class="datos1">
            <div>
                <label>Fecha <span class="asterisco">*</span></label>
                <input type="date" name="fecha" class="required-field">
                <span class="mensaje-error">Este campo es obligatorio</span>
            </div>
            <div>
                <label>Tipo de estudio <span class="asterisco">*</span></label>
                <select name="tipo_informe" id="tipoEstudio" class="required-field">
                    <option value="VEDA">VIDEOESOFAGASTRODUODENOSCOPIA</option>
                    <option value="VCC">VIDEOCOLONOSCOPIA</option>
                </select>
                <span class="mensaje-error">Este campo es obligatorio</span>
            </div>
        </div>

        <h3>Datos del paciente</h3>

        <div class="datos1">
            <div>
                <label>Apellido y Nombre <span class="asterisco">*</span></label>
                <input type="text" name="nombre_paciente" class="required-field">
                <span class="mensaje-error">Este campo es obligatorio</span>
            </div>
            <div>
                <label>Fecha de nacimiento</label>
                <input type="date" name="fecha_nacimiento_paciente" id="fecha_nacimiento_paciente">
            </div>
        </div>

        <div class="datos1">
            <div >
                <label>Edad</label>
                <input type="number" name="edad" id="edad" readonly>
            </div>
            <div>
                <label>Número de documento <span class="asterisco">*</span></label>
                <input type="text" name="dni_paciente" class="required-field">
                <span class="mensaje-error">Este campo es obligatorio</span>
            </div>
        </div>

        <div class="datos1">
            <div>
                <label>Tipo de cobertura <span class="asterisco">*</span></label>
                <select id="cobertura" name="id_cobertura" class="required-field">
                    <option value="">Cargando...</option>
                </select>
                <span class="mensaje-error">Este campo es obligatorio</span>
            </div>
            <div>
                <label>Número de afiliado</label>
                <input type="text" name="numero_afiliado" id="numero_afiliado" disabled>
            </div>
        </div>

        <div class="datos1">
            <div>
                <label>Mail <span class="asterisco">*</span></label>
                <input type="email" name="mail_paciente" class="required-field">
            
                <span class="mensaje-error">Este campo es obligatorio</span>
            </div>
           <div class="autocomplete-container">
                 <label>Médico que envía el estudio</label>
                 <input type="text" name="medico_envia_estudio" id="medico_envia_estudio" autocomplete="off">
                 <div id="autocomplete-medico-list" class="autocomplete-items"></div>
            </div>
        </div>

        <div class="datos1">
            <div>
                <label>Motivo del estudio</label>
                <input type="text" name="motivo_estudio">
            </div>
        </div>

        <h3>Informe del estudio</h3>
        <div id="inputinforme" >

            <label>Informe</label>
            <textarea name="informe"></textarea>
        </div>
        <div class="datos1">
    
</div>

<!-- Inputs adicionales ocultos inicialmente -->
<div id="vedaInputs" >
    <div>
        <div >
            <label>Esófago</label>
            <textarea type="text" name="esofago" ></textarea>
        </div>
    </div>
    <div >
        <div>
            <label>Estómago</label>
            <textarea type="text" name="estomago"></textarea>
        </div>
    </div>
    <div > 
        <div>
            <label>Duodeno</label>
            <textarea type="text" name="duodeno"></textarea>
        </div>
    </div>
    
</div>


        <label>Conclusión</label>
        <textarea name="conclusion" style="height: 60px;"></textarea><br>

        <div class="datos1">
            <div>
                <label>¿Se efectuó terapéutica? </label>
                <select name="efectuo_terapeutica">
                    <option value="0">NO</option>
                    <option value="1">SI</option>
                </select>
            </div>
             <div class="autocomplete-container">
                <label for="tipo_terapeutica">¿Cuál?</label>
                <input type="text" name="tipo_terapeutica" id="tipo_terapeutica" autocomplete="off">
                <div id="autocomplete-list" class="autocomplete-items"></div>
               
            </div>
        </div>

        <div class="datos1">
            <div>
                <label>¿Se efectuó biopsia? </label>
                <select name="efectuo_biopsia" >
                    <option value="0">NO</option>
                    <option value="1">SI</option>
                </select>
            </div>
            <div>
                <label>Cantidad de frascos</label>
                <input type="number" name="fracos_biopsia">
            </div>
        </div>

        <label>Subir fotos <span class="asterisco">*</span></label>
        <input type="file" name="archivo[]" accept="image/*" multiple class="required-field">
        

        <button type="submit"  id="btnEnviar" disabled="true" >Enviar</button>
    </form>
</div>
<script>
    document.addEventListener('DOMContentLoaded', function() {
    const tipoEstudioSelect = document.getElementById('tipoEstudio');
    const informeInputDiv = document.getElementById('inputinforme');
    const vedaInputsDiv = document.getElementById('vedaInputs');

    function toggleInformeFields() {
        const selectedValue = tipoEstudioSelect.value;
        localStorage.setItem('tipoEstudioSeleccionado', selectedValue);
        if (selectedValue === 'VEDA') {
            
            informeInputDiv.style.display = 'none';
           
            vedaInputsDiv.style.display = 'block';
        } else if (selectedValue === 'VCC') {
           
            informeInputDiv.style.display = 'block';
            
            vedaInputsDiv.style.display = 'none';
        }
    }

    const savedTipoEstudio = localStorage.getItem('tipoEstudioSeleccionado');
        if (savedTipoEstudio) {
            tipoEstudioSelect.value = savedTipoEstudio;
        }
    tipoEstudioSelect.addEventListener('change', toggleInformeFields);

    
    toggleInformeFields();

    
});
    document.addEventListener("DOMContentLoaded", function () {
    const selectBiopsia = document.querySelector('select[name="efectuo_biopsia"]');
    const inputFrascos = document.querySelector('input[name="fracos_biopsia"]');

    // Mostrar u ocultar el input de frascos
    function toggleFrascos(valor) {
        if (valor === '1') {
            inputFrascos.parentElement.style.display = 'block';
        } else {
            inputFrascos.parentElement.style.display = 'none';
        }
    }

    // Restaurar valor si existe en localStorage
    const valorGuardado = localStorage.getItem('efectuo_biopsia');
    if (valorGuardado) {
        selectBiopsia.value = valorGuardado;
        toggleFrascos(valorGuardado);
    } else {
        toggleFrascos(selectBiopsia.value); // si no hay nada guardado, usamos el valor por defecto
    }

    // Guardar en localStorage cuando cambia
    selectBiopsia.addEventListener('change', function () {
        const valor = selectBiopsia.value;
        localStorage.setItem('efectuo_biopsia', valor);
        toggleFrascos(valor);
    });
});

document.addEventListener("DOMContentLoaded", function () {
    const campoCual = document.getElementById('tipo_terapeutica');
    const selectTerapeutico = document.querySelector('select[name="efectuo_terapeutica"]');

    const valorTerapeutico = localStorage.getItem('efectuo_terapeutica');

    // Establecer el valor del select si estaba guardado
    if (valorTerapeutico) {
        selectTerapeutico.value = valorTerapeutico;
    }

    // Mostrar u ocultar el campo 'cual'
    function toggleCampoCual(valor) {
        if (valor === '1') {
            campoCual.style.display = 'block';
        } else {
            campoCual.style.display = 'none';
        }
    }

    // Ejecutar al cargar la página
    toggleCampoCual(selectTerapeutico.value);

    // Escuchar cambios en el select y guardar en localStorage
    selectTerapeutico.addEventListener('change', function () {
        const valor = selectTerapeutico.value;
        localStorage.setItem('efectuo_terapeutica', valor);
        toggleCampoCual(valor);
    });
});

    function esEmailValido(email) {
    const regex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
    return regex.test(email);
}

document.addEventListener('DOMContentLoaded', function () {
    const form = document.getElementById('formInforme');
    const requiredFields = form.querySelectorAll('.required-field');
    const btnEnviar = document.getElementById('btnEnviar');

    // Validación al salir del campo (blur)
    requiredFields.forEach(field => {
        const errorSpan = field.parentElement.querySelector('.mensaje-error');

        // Ocultar el mensaje al inicio
        errorSpan.style.display = 'none';

        // Mostrar error si está vacío al salir del campo
        field.addEventListener('blur', () => {
            if (!field.value.trim()) {
                errorSpan.style.display = 'flex';
            } else {
                errorSpan.style.display = 'none';
            }
            validarFormulario(); // validar después de tocar el input
        });

        // Ocultar error al escribir
        field.addEventListener('input', () => {
            if (field.value.trim()) {
                errorSpan.style.display = 'none';
            }
            validarFormulario(); // revalidar al escribir
        });
    });

    // Validación del email aparte
    const emailField = form.querySelector('input[name="mail_paciente"]');
    

    // Habilita o deshabilita el botón según la validez
    function validarFormulario() {
        let valid = true;

        requiredFields.forEach(field => {
            if (!field.value.trim()) {
                valid = false;
            }
        });

        if (emailField.value && !/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(emailField.value)) {
            valid = false;
        }

        btnEnviar.disabled = !valid;
    }

    form.addEventListener('submit', function(event) {
    event.preventDefault(); // Prevenir el envío tradicional
    
    const formData = new FormData(form);




    const tipoEstudio = form.querySelector('select[name="tipo_informe"]').value;
    const terapeuticoSelect = form.querySelector('select[name="efectuo_terapeutica"]').value;
    const biopsia = form.querySelector('select[name="efectuo_biopsia"]').value;
    // Si el estudio es VCC, eliminar los campos específicos
    if (tipoEstudio === 'VCC') {
        formData.delete('estomago');
        formData.delete('duodeno');
        formData.delete('esofago');
    }
    if(tipoEstudio === 'VEDA') {
        formData.delete('informe');
    }
    if(terapeuticoSelect === '0'){
        formData.delete('tipo_terapeutica');
    }
    if(biopsia === '0' ){
        formData.delete('fracos_biopsia');
    }

    const btnEnviar = document.getElementById('btnEnviar');
    btnEnviar.disabled = true;
    const originalText = btnEnviar.innerHTML;
    btnEnviar.innerHTML = 'Enviando...';

    fetch('<?= site_url('/informe/alta'); ?>', {
        method: 'POST',
        body: formData
    })
    .then(response => response.json())
        .then(data => {
           if(data.success){
            mostrarAlerta('Fomulario creado con éxito','success');
            form.reset(); // Opcional: reiniciar el formulario
            localStorage.removeItem('coberturaSeleccionada'); // Borrar cobertura al enviar
           }
           
            if (!data.success) {
               
                const mensaje = `${data.message}`;
               
                mostrarAlerta('Error al crear el formulario. '+ mensaje,'error');
            } else {
                btnEnviar.disabled = true;
            }
        })
        .catch(error => {
            
            mostrarAlerta('Error al enviar el formulario: ' + error.message,'error');
        })
    
    .finally(() => {
        btnEnviar.disabled = false;
        btnEnviar.innerHTML = originalText;
    });
});
    const coberturaSelect = document.getElementById('cobertura');
    const afiliadoInput = document.getElementById('numero_afiliado');

    // Aquí podrías continuar con el resto de tu lógica para cargar opciones
    // Ejemplo: cargarCoberturas();
});

  document.addEventListener('DOMContentLoaded', function () {
    const coberturaSelect = document.getElementById('cobertura');
    const afiliadoInput = document.getElementById('numero_afiliado');

    function verificarCobertura() {
        const coberturaTexto = coberturaSelect.options[coberturaSelect.selectedIndex]?.text?.trim().toUpperCase();
        const coberturaValor = coberturaSelect.value;

        if (coberturaValor && coberturaTexto !== 'SIN COBERTURA') {
            afiliadoInput.disabled = false;
        } else {
            afiliadoInput.value = '';
            afiliadoInput.disabled = true;
        }
        localStorage.setItem('coberturaSeleccionada', coberturaValor);
    }

    // Ejecutar al cambiar la cobertura
    coberturaSelect.addEventListener('change', verificarCobertura);

    // Si las opciones se cargan dinámicamente
    const observer = new MutationObserver(verificarCobertura);
    observer.observe(coberturaSelect, { childList: true });
});


 // Validación de campos obligatorios
 function validarFormulario() {
        const campos = document.querySelectorAll('.required-field');
        let formularioValido = true;

        campos.forEach(campo => {
            // Para archivos, verificar que se haya seleccionado al menos uno
            if (campo.type === "file") {
                if (campo.files.length === 0) formularioValido = false;
            } else if (campo.tagName === "SELECT") {
                if (campo.value === '') formularioValido = false;
            } else {
                if (campo.value.trim() === '') formularioValido = false;
            }
            if (campo.type === "email") {
            const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
            if (!emailRegex.test(campo.value.trim())) {
                formularioValido = false;
                campo.classList.add("input-invalido");
            } else {
                campo.classList.remove("input-invalido");
            }
        }
        });

        // Habilita o deshabilita el botón
        document.getElementById('btnEnviar').disabled = !formularioValido;
    }

    // Escuchar cambios en todos los campos obligatorios
    window.addEventListener('DOMContentLoaded', () => {
        const campos = document.querySelectorAll('.required-field');
        campos.forEach(campo => {
            campo.addEventListener('input', validarFormulario);
            campo.addEventListener('change', validarFormulario); // Para select y file
        });

        validarFormulario(); // Validar al cargar la página
    });
   // Escuchar el cambio en el select de tipo de estudio
  
    // Inicializar el estado del formulario dependiendo del tipo de estudio ya seleccionado (si ya está predefinido)
    



    document.getElementById('fecha_nacimiento_paciente').addEventListener('change', function() {
        var fechaNacimiento = new Date(this.value);
        var hoy = new Date();
        var edad = hoy.getFullYear() - fechaNacimiento.getFullYear();
        var mes = hoy.getMonth() - fechaNacimiento.getMonth();

        if (mes < 0 || (mes === 0 && hoy.getDate() < fechaNacimiento.getDate())) {
            edad--;
        }

        document.getElementById('edad').value = edad;
    });

  
// Función para cargar las coberturas 
function cargarCoberturas() {
        // Realiza la solicitud AJAX
        fetch('<?= site_url('coberturas'); ?>')
            .then(response => response.json())
            .then(data => {
                // Verifica los datos que recibe
                // Una vez obtenidos los datos, llena el select
                const selectCobertura = document.getElementById('cobertura');
                selectCobertura.innerHTML = ''; // Limpia el select

                // Agrega una opción por defecto
                const defaultOption = document.createElement('option');
                defaultOption.value = '';
                defaultOption.textContent = 'Seleccione una cobertura';
                selectCobertura.appendChild(defaultOption);

                // Agrega las opciones de coberturas al select
                if (Array.isArray(data)) {
                    data.forEach(cobertura => {
                        const option = document.createElement('option');
                        option.value = cobertura.id_cobertura; // El valor de la opción es el ID de la cobertura
                        option.textContent = cobertura.nombre_cobertura; // El texto es el nombre de la cobertura
                        selectCobertura.appendChild(option);
                    });
                    // Aplicar cobertura guardada si existe
                const coberturaGuardada = localStorage.getItem('coberturaSeleccionada');
                if (coberturaGuardada) {
                    selectCobertura.value = coberturaGuardada;
                    selectCobertura.dispatchEvent(new Event('change')); // Disparar evento manual
                }
                } else {
                    console.error("La respuesta no es un array:", data);
                }
            })
            .catch(error => {
                console.error('Error al cargar las coberturas:', error);
                const selectCobertura = document.getElementById('cobertura');
                selectCobertura.innerHTML = '<option value="">Error al cargar coberturas</option>';
            });
    }

    // Llama a la función para cargar las coberturas cuando la página esté lista
    window.addEventListener('DOMContentLoaded', cargarCoberturas);
 
    

  

    validarFormulario(); // Esto asegura que también se actualice el botón Enviar
    document.addEventListener('DOMContentLoaded', function () {
    const emailInput = document.querySelector('input[name="mail_paciente"]');
   

    function validarEmail(email) {
        // Expresión regular simple para validar un email
        const regex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
        return regex.test(email);
    }
});

function mostrarAlerta(mensaje, tipo) {
    const alertBox = document.getElementById("alert-message");
    const alertText = document.getElementById("alert-text");
    const closeBtn = document.getElementById("close-alert");

    // Resetear clases previas
    alertBox.classList.remove("success", "error", "hidden");

    // Agregar clase según el tipo
    if (tipo === "success") {
        alertBox.classList.add("success");
        closeBtn.style.display = "none"; // Ocultar botón cerrar
    } else if (tipo === "error") {
        alertBox.classList.add("error");
        closeBtn.style.display = "inline"; // Mostrar botón cerrar
    }

    alertText.innerText = mensaje;
    alertBox.classList.remove("hidden");

    // Si es éxito, ocultar automáticamente luego de 10 segundos
    if (tipo === "success") {
        setTimeout(() => {
            alertBox.classList.add("hidden");
        }, 10000);
    }

    // Agregar funcionalidad al botón cerrar
    closeBtn.onclick = () => {
        alertBox.classList.add("hidden");
    };
}
document.addEventListener('DOMContentLoaded', function () {
    const selectTerapeutico = document.querySelector('select[name="efectuo_terapeutica"]');
    const inputCual = document.querySelector('input[name="tipo_terapeutica"]');

    function actualizarCampoCual() {
        if (selectTerapeutico.value === '1') {
            inputCual.parentElement.style.display = 'block';
        } else {
            inputCual.parentElement.style.display = 'none';
            inputCual.value = ''; // limpiar el campo si está oculto
        }
    }

    // Ejecutar al cargar la página
    actualizarCampoCual();

    // Escuchar cambios
    selectTerapeutico.addEventListener('change', actualizarCampoCual);
});

document.addEventListener("DOMContentLoaded", function () {
  
    // ------- Mostrar/Ocultar "Cantidad de frascos" según biopsia -------
    const biopsiaSelect = document.querySelector('select[name="efectuo_biopsia"]');
    const inputFrascos = document.querySelector('input[name="fracos_biopsia"]');
    const labelFrascos = inputFrascos.closest('div'); // para ocultar el div completo

    function actualizarVisibilidadFrascos() {
        if (biopsiaSelect.value === "1") {
            labelFrascos.style.display = "block";
        } else {
            labelFrascos.style.display = "none";
            inputFrascos.value = ""; // limpiamos si está oculto
        }
    }

    biopsiaSelect.addEventListener("change", actualizarVisibilidadFrascos);
    actualizarVisibilidadFrascos(); // al cargar

});
document.addEventListener('DOMContentLoaded', function () {
    const form = document.getElementById('formInforme');

    // Restaurar datos del localStorage si existen
    const savedForm = localStorage.getItem('formInforme');
    if (savedForm) {
        const data = JSON.parse(savedForm);
        Object.entries(data).forEach(([key, value]) => {
            const field = form.elements[key];
            if (field) {
                if (field.type === 'select-one' || field.type === 'text' || field.type === 'email' || field.type === 'date' || field.type === 'number') {
                    field.value = value;
                } else if (field.type === 'textarea') {
                    field.value = value;
                }
            }
        });
    }

    // Escuchar cambios en los campos y guardar en localStorage
    form.addEventListener('input', () => {
        const formData = {};
        Array.from(form.elements).forEach(el => {
            if (el.name && el.type !== 'file' && !el.disabled) {
                formData[el.name] = el.value;
            }
        });
        localStorage.setItem('formInforme', JSON.stringify(formData));
    });

    // Limpiar localStorage al enviar correctamente
    form.addEventListener('submit', function (event) {
        // Dentro del .then donde `data.success === true`, agregá esto:
         localStorage.removeItem('formInforme');
    });
});

// AGREGA ESTO NOMAS 

 const sugerencias = [
        "Polipectomía/s",
        "Mucosectomía",
        "Dilatación con balón",
        "Marcación",
        "Tratamiento hemostático",
        "Argón láser"
    ];

    const input = document.getElementById('tipo_terapeutica');
    const listContainer = document.getElementById('autocomplete-list');
    
    input.addEventListener('input', function() {
        const valor = this.value.toLowerCase();
        listContainer.innerHTML = '';

        if (!valor) return;

        const filtradas = sugerencias.filter(item => item.toLowerCase().includes(valor));

        filtradas.forEach(sugerencia => {
            
            const div = document.createElement('div');
            div.classList.add('autocomplete-item');
            div.textContent = sugerencia;
            div.addEventListener('click', function() {
                input.value = sugerencia;
                listContainer.innerHTML = '';
            });
            listContainer.appendChild(div);
        });
    });

    document.addEventListener('click', function(e) {
        if (e.target !== input) {
            listContainer.innerHTML = '';
        }
    });
    document.addEventListener('DOMContentLoaded', function () {
    const campoCual = document.getElementById('tipo_terapeutica');

    // Guardar en localStorage cada vez que el campo cambia
    campoCual.addEventListener('input', function () {
        localStorage.setItem('tipo_terapeutica', campoCual.value.trim());
    });

    // Cargar valor guardado al inicio
    const cualGuardado = localStorage.getItem('tipo_terapeutica');
    if (cualGuardado) {
        campoCual.value = cualGuardado;
    }
});
// Al hacer clic en una opción de autocompletado
document.getElementById('autocomplete-list').addEventListener('click', function (e) {
    if (e.target && e.target.matches('.autocomplete-item')) {
        const inputCual = document.getElementById('tipo_terapeutica');
        inputCual.value = e.target.textContent;
        inputCual.dispatchEvent(new Event('input')); // <- necesario para que se guarde
    }
});
document.addEventListener("DOMContentLoaded", function () { 
    const listaMedicos = [
        "Manolizi juan manuel", "Gardella ana", "Trillo silvina", "Pardo Mariel",
        "Crespo marcelo", "Arinovich barbara", "Larraburu Alfredo", "Albamonte Mirta",
        "Galván daniel", "Baulos Gustavo", "Erlich Romina", "Cuesta maria Celia",
        "Roel José", "Dardanelli miguel", "Coqui ricardo", "Menéndez José", "Diana Estrin"
    ];

    function setupAutocomplete(input, lista, containerId) {
        const container = document.getElementById(containerId);

        input.addEventListener("input", function () {
            const valor = this.value.toLowerCase();
            container.innerHTML = "";

            if (!valor) return;

            const coincidencias = lista.filter(item =>
                item.toLowerCase().includes(valor)
            );

            coincidencias.forEach(coincidencia => {
                const itemDiv = document.createElement("div");
                itemDiv.classList.add("autocomplete-item");
                itemDiv.textContent = coincidencia;
                itemDiv.addEventListener("click", function () {
                    input.value = coincidencia;
                    container.innerHTML = "";

                    // Guardar en localStorage
                    localStorage.setItem("medicoSeleccionado", coincidencia);
                });
                container.appendChild(itemDiv);
            });
        });

        document.addEventListener("click", function (e) {
            if (!container.contains(e.target) && e.target !== input) {
                container.innerHTML = "";
            }
        });

        // Recuperar valor guardado si existe
        const valorGuardado = localStorage.getItem("medicoSeleccionado");
        if (valorGuardado) {
            input.value = valorGuardado;
        }
    }

    const inputMedico = document.getElementById("medico_envia_estudio");
    setupAutocomplete(inputMedico, listaMedicos, "autocomplete-medico-list");
});
</script>
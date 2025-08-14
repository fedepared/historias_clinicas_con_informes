<?php include('header.php'); ?>

<style>
    /* Contenedor principal para centrar la tabla debajo del header */
    .content {
        display: flex;
        flex-direction: column;
        align-items: center;
        width: 100%;
        margin-top: 20px; /* Espacio entre el header y la tabla */
    }

    .container {
        width: 222%;
        max-width: 1081px;
        background: white;
        padding: 20px;
        border-radius: 10px;
        box-shadow: 0px 4px 10px rgba(0, 0, 0, 0.1);
        border: 1px solid #ddd;
        text-align: center;
        margin: auto; /* Centra horizontalmente */
    }

    h2 {
        color: #007bbd;
        margin-bottom: 43px;
    }

    form {
        margin-bottom: 20px;
        display: flex;
        justify-content: space-between;
        gap: 10px;
        flex-direction: row;
        align-items: center;
    }

    /* Estilo para los inputs y botones */
    input[type="date"], input[type="text"], button {
        padding: 8px;
        border: 1px solid #ddd;
        border-radius: 5px;
        font-size: 14px;
    }

    button {
        background-color: #007bbd;
        color: white;
        border: none;
        cursor: pointer;
    }

    button:hover {
        background-color: #005f99;
    }

    /* Estilización de la tabla */
    table {
        width: 100%;
        border-collapse: collapse;
        background: white;
        border-radius: 8px;
        overflow: hidden;
    }

    th, td {
        padding: 12px;
        text-align: left;
        border-bottom: 1px solid #ddd;
    }

    th {
        background-color: #007bbd;
        color: white;
        text-transform: uppercase;
    }

    tr:nth-child(even) {
        background-color: #f9f9f9;
    }

    tr:hover {
        background-color: #f1f1f1;
    }

    /* Estilo para la lupa */

    /* Estilización de la caja de búsqueda */
.search-container {
    position: relative;
    display: flex;
    align-items: center;
    width: 19%;
}

input[type="text"] {
    padding: 8px 30px 8px 10px; /* Ajuste para dar espacio al icono */
    border: 1px solid #ddd;
    border-radius: 5px;
    font-size: 14px;
    width: 81%; /* Hace que el input ocupe todo el espacio disponible */
}

/* Estilo para el icono */
.search-icon {
    position: absolute;
    right: 10px; /* Posiciona el icono a la derecha */
    color: #007bbd;
    font-size: 20px; /* Tamaño adecuado del icono */
    cursor: pointer;
}
    /* Paginador */
    .pagination {
    display: flex;
    justify-content: center;
    align-items: center;
    margin-top: 20px;
}

.pagination button {
    background-color: #007bbd;
    color: white;
    border: none;
    padding: 10px 15px;
    margin: 0 10px;
    border-radius: 5px;
    cursor: pointer;
    font-size: 14px;
    transition: background 0.3s;
}

.pagination button:hover {
    background-color: #005f8a;
}

.pagination span {
    font-size: 16px;
    font-weight: bold;
}
td button {
    background-color: #007bbd;
    border: none;
    color: white;
    padding: 6px 8px;
    margin-right: 5px;
    border-radius: 5px;
    cursor: pointer;
    transition: background-color 0.3s ease;
}

td button:hover {
    background-color: #005f99;
}

.material-icons {
    vertical-align: middle;
}
.tooltip {
    position: relative;
    display: inline-block;
}

.tooltip .tooltip-text {
    visibility: hidden;
    width: 160px;
    background-color: #333;
    color: #fff;
    text-align: center;
    padding: 8px;
    border-radius: 6px;
    font-size: 16px;
    position: absolute;
    z-index: 1;
    bottom: 125%; /* Muestra el tooltip arriba del botón */
    left: 50%;
    transform: translateX(-50%);
    opacity: 0;
    transition: opacity 0.3s;
}

.tooltip:hover .tooltip-text {
    visibility: visible;
    opacity: 1;
}
.pagination button:disabled {
    background-color: #007bbda8;
    cursor: not-allowed;
    opacity: 0.7;
}
/* Estilos para el fondo oscuro del modal */
#modal-editar {
    display: none;
    position: fixed;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    background-color: rgba(0,0,0,0.5);
    z-index: 999;
    padding: 20px 0; 
    box-sizing: border-box; 
    justify-content: center; 
    align-items: center;
}

/* Estilo del contenido del modal */
#modal-editar .modal-content {
    background-color: white;
    padding: 18px;
    border-radius: 15px;
    width: 59%;
    max-height: 86vh;
    overflow-y: auto;
    overflow-x: hidden; 
    box-shadow: 0px 8px 20px rgba(0, 0, 0, 0.2);
    text-align: left;
    margin-top: 5vh; 
    margin-bottom: 5vh;
    box-sizing: border-box; 
}
/* Encabezado del modal */
#modal-editar h3 {
    margin-top: 0;
    color: #007bbd;
    font-size: 20px;
    text-align: justify;
}
#modal-editar .titulos {
    color: #007bbd;
    width: 100%;
    text-align: center;
}
/* Inputs del formulario */
#modal-editar input {
    width: 100%;
    padding: 10px;
    /* margin-top: 5px; */
    margin-bottom: 15px;
    border-radius: 5px;
    border: 1px solid #ccc;
    font-size: 14px;
}
#modal-editar select {
    width: 100%;
    padding: 10px;
    /* margin-top: 5px; */
    margin-bottom: 15px;
    border-radius: 5px;
    border: 1px solid #ccc;
    font-size: 14px;
}
#modal-editar textarea {
    width: 100%;
    max-width:100%;
    padding: 10px;
    margin-top: 5px;
    margin-bottom: 15px;
    border-radius: 5px;
    border: 1px solid #ccc;
    font-size: 14px;
}
/* Botones del modal */
#modal-editar button {
    padding: 10px 15px;
    margin-right: 10px;
    border: none;
    border-radius: 6px;
    cursor: pointer;
    font-size: 14px;
    transition: background 0.3s ease;
}

#modal-editar button[type="submit"] {
    background-color: #007bbd;
    color: white;
}

#modal-editar button[type="submit"]:hover {
    background-color: #005f8a;
}

#modal-editar button[type="button"] {
    background-color: #ff5a5a;
}

#modal-editar button[type="button"]:hover {
    background-color: #ff5a5ab5;
}
.grupointputs {
    display: flex;
    flex-direction: row;
    flex-wrap: nowrap;
    justify-content: space-around;
    gap: 3rem;
    width: 98%;
}
.grupotexarea {
    display: flex;
    flex-direction: column;
    flex-wrap: nowrap;
    width: 98%;
}
.intputs {
    display: flex;
    flex-direction: column;
    align-items: stretch;
    flex-wrap: nowrap;
    width: 100%;
}
.datos {
    display:flex;
    flex-direction: column;
    align-items: flex-start;
    width: 100%;
}
.modal-close {
    display: flex;
    width: 100%;
    flex-direction: column;
}
.close {
    text-align: end;
    color: #007bbd;
    font-size: x-large;
    cursor: pointer;
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
    .spin {
    animation: spin 1s linear infinite;
    display: inline-block;
}

@keyframes spin {
    0% { transform: rotate(0deg); }
    100% { transform: rotate(360deg); }
}
#close-alert {
    margin-left: 10px;
    font-size: 18px;
}
.btdescarga {
    display: flex;
    flex-direction: row-reverse;
}
.image {
    width: 98%;
}
    /* --- Autocomplete Styles --- */
    .autocomplete-items {
        /* Position the suggestions directly below the input */
        position: absolute;
        border: 1px solid #d4d4d4;
        border-top: none; /* Remove top border to connect with input visually */
        z-index: 99;
        /* Calculate top dynamically if needed, or rely on absolute positioning within a relative parent */
        left: 0;
        right: 0;
        max-height: 200px; /* Limit height and add scroll */
        overflow-y: auto; /* Add vertical scrollbar */
        background-color: #fff;
        box-shadow: 0px 4px 8px rgba(0, 0, 0, 0.1); /* Slightly more prominent shadow */
        border-radius: 0 0 5px 5px; /* Rounded bottom corners */
        /* Smooth scrolling for autocomplete suggestions */
        scroll-behavior: smooth;
    }

    .autocomplete-items::-webkit-scrollbar {
        width: 8px; /* Width of the scrollbar */
    }

    .autocomplete-items::-webkit-scrollbar-track {
        background: #f1f1f1; /* Color of the scrollbar track */
        border-radius: 10px;
    }

    .autocomplete-items::-webkit-scrollbar-thumb {
        background: #888; /* Color of the scrollbar thumb */
        border-radius: 10px;
    }

    .autocomplete-items::-webkit-scrollbar-thumb:hover {
        background: #555; /* Color of the scrollbar thumb on hover */
    }


    .autocomplete-items div {
        padding: 10px;
        cursor: pointer;
        background-color: #fff;
        border-bottom: 1px solid #eee; /* Lighter border between items */
        text-align: left;
        color: #333;
        transition: background-color 0.2s ease, color 0.2s ease; /* Smooth transition */
    }

    .autocomplete-items div:last-child {
        border-bottom: none; /* No border for the last item */
    }

    /* Style the hovered or active suggestion item */
    .autocomplete-items div:hover,
    .autocomplete-active {
        background-color: #e0f2f7 !important; /* Light blue on hover/active */
        color: #007bbd; /* Blue text on hover/active */
    }

    /* Style for when no results are found */
    .autocomplete-items .no-results {
        padding: 10px;
        color: #888;
        font-style: italic;
        text-align: center; /* Center "No results" text */
    }

    /* Adjust input padding to account for the clear button (if you add one) */
    input.has-autocomplete {
        padding-right: 40px;
    }

    /* Clear button for autocomplete input (optional) */
    .autocomplete-clear-button {
        position: absolute;
        right: 35px; /* Adjusted position to not overlap search icon */
        top: 50%;
        transform: translateY(-50%);
        cursor: pointer;
        color: #999;
        font-size: 18px;
        display: none;
    }

    .autocomplete-clear-button:hover {
        color: #555;
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
    .image-gallery {
        display: flex;
        flex-wrap: wrap;
        gap: 10px; /* Space between images */
        margin-top: 20px;
        justify-content: center;
    }

    .image-gallery img {
        max-width: 150px; /* Adjust as needed */
        height: auto;
        border: 1px solid #ddd;
        border-radius: 8px;
        box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
        cursor: pointer; /* Indicate that images are clickable (if you want to implement a larger view) */
    }
    .image-wrapper {
    position: relative;
    display: inline-block; /* Para que el wrapper se ajuste al tamaño de la imagen */
    margin: 5px; /* Espacio entre imágenes */
    border: 1px solid #ddd; /* Borde opcional para visualización */
    padding: 5px;
}

.image-wrapper img {
    max-width: 150px; /* Tamaño máximo para las miniaturas */
    height: auto;
    display: block; /* Para eliminar el espacio extra debajo de la imagen */
}

/* Estilos para el botón de eliminar */
.delete-image-button {
    position: absolute;
    top: -5px; /* Ajusta la posición vertical */
    right: -5px; /* Ajusta la posición horizontal */
    background-color: #ff0000;
    color: white;
    border-radius: 50%; /* Hace que sea circular */
    width: 20px;
    height: 20px;
    display: flex;
    justify-content: center;
    align-items: center;
    font-size: 14px;
    font-weight: bold;
    cursor: pointer;
    line-height: 1; /* Alinea el texto verticalmente */
    box-shadow: 0 2px 4px rgba(0, 0, 0, 0.2); /* Sombra para que destaque */
    z-index: 10; /* Asegura que esté por encima de la imagen */
}

.delete-image-button:hover {
    background-color: #cc0000;
}
.asterisco {
        color: #007bbd;
    }
    #btnGuardarEdicion:disabled {
    background-color: #ccc;
    cursor: not-allowed;
    opacity: 0.6;
}
.filtroCo {
    padding: 8px 30px 8px 10px;
    border: 1px solid #ddd;
    border-radius: 5px;
    font-size: 14px;
    width: 100%;
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
    /* margin-left: 8px; */ /* Si lo quieres separado del texto, ya lo manejaremos con gap en el botón flex */
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

/* Ajustes para el botón cuando contiene un spinner */
#btnGuardarEdicion {
    display: flex; /* Usamos flexbox para centrar y alinear los elementos internos */
    justify-content: center; /* Centra horizontalmente el texto/spinner */
    align-items: center; /* Centra verticalmente el texto/spinner */
    gap: 8px; /* Espacio entre el texto y el spinner */
    /* Otros estilos de botón aquí, si no los tienes ya definidos globalmente */
    background-color: #009fdf; /* Ejemplo de color de fondo */
    color: white;
    border: none;
    padding: 10px 20px;
    border-radius: 6px;
    cursor: pointer;
    font-size: 16px;
}
#filterButton:disabled {
    background-color: #ccc;
    cursor: not-allowed;
    opacity: 0.6;
}

</style>


<div id="mensaje-alerta" class="alert hidden">
    <span id="close-alert" style="float:right; cursor:pointer; font-weight: bold;">&times;</span>
    <span id="alert-text"></span>
    </div>
<div class="content">
    <div class="container">
        <h2>Reportes</h2>

        <!-- Formulario para seleccionar el rango de fechas -->
        <form id="filterForm">
            <div class="search-container">
                <!-- <label for="nombre">Nombre:</label> -->
                <input type="text" name="nombre" id="nombre" placeholder="Ingrese nombre">
                <span class="material-icons search-icon" id="search-icon" >search</span>
            </div>
            <div class="search-container">
                
                <select class="filtroCo" name="cobertura_filtro" id="cobertura_filtro">
                    <option value="">Todas las coberturas</option>
                </select>
               
            </div>
            <div>
                <label for="fecha_desde">Desde:</label>
                <input type="date" name="fecha_desde" id="fecha_desde">

                <label for="fecha_hasta">Hasta:</label>
                <input type="date" name="fecha_hasta" id="fecha_hasta">

                <button type="submit" id="filterButton" disabled>Filtrar</button>
            </div>
        </form>

        <table>
            <thead>
                <tr>
                    <th>Nombre</th>
                    <th>DNI</th>
                    <th>Fecha</th>
                    <th>Mail</th>
                    <th>Tipo</th>
                    <th>Cobertura</th>
                    <th>Acciones</th>
                </tr>
            </thead>
            <tbody>
                <tr><td colspan="4">Cargando datos...</td></tr>
            </tbody>
        </table>
        <!-- Paginación -->
        <div class="pagination">
            <button id="prevPage"  disabled>Anterior</button>
            <span id="pageNumber">1</span>
            <button id="nextPage" disabled>Siguiente</button>
        </div>
        <div class="btdescarga">
            <button id="downloadPdfsButton" disable style="display: none;">Descargar Reportes</button>
        </div>
    </div>
</div>
<!-- Modal para editar reporte -->
<div id="modal-editar">
    <div class="modal-content">
        <div class="modal-close">
            <span class="close" onclick="cerrarModal()">&times;</span>
        </div>
            <h3>Editar Reporte</h3>
            <form id="formEditarReporte" style="display: flex;flex-direction: column;align-items: stretch; width:100%">
        <div class="datos">
               <div class="titulos">
                  <h4>Datos del Paciente</h4>
               </div>
               
              <div class="grupointputs">
                <input type="hidden" id="edit-id">
                <div class="intputs">
                    <label for="edit-nombre">Apellido y Nombre:<span class="asterisco">*</span> </label>
                    <input type="text" id="edit-nombre" required disabled>
                </div>
                <div class="intputs">
                 <label for="edit-fechanacimiento">Fecha de nacimiento:</label>
                 <input type="date" id="edit-fechanacimiento" >
                </div>
                <div class="intputs">
                     <label for="edit-edad">Edad:</label>
                     <input type="number" id="edit-edad" >
                 </div>
              </div>
              <div class="grupointputs">
                <div class="intputs">
                    <label for="edit-tipoco">Tipo de cobertura :<span class="asterisco">*</span></label>
                    <select id="edit-tipoco" name="id_cobertura"  required>
                        <option value="">Cargando...</option>
                    </select>
                </div>
                <div class="intputs">
                 <label for="edit-numeroAfiliado">Número de afiliado:</label>
                 <input type="number" id="edit-numeroAfiliado" >
                </div>
                <div class="intputs">
                     <label for="edit-mail">Mail:<span class="asterisco">*</span></label>
                     <input type="email" id="edit-mail" required>
                 </div>
              </div>
              <div class="grupointputs">
                
                <div class="intputs autocomplete-container">
                    <label for="edit-medico">Médico que envía el estudio:</label>
                    <input type="text" name="edit-medico" id="edit-medico" autocomplete="off">
                    <div id="autocomplete-medico-list" class="autocomplete-items"></div>
                </div>
                <div class="intputs">
                 <label for="edit-motivo">Motivo del estudio:</label>
                 <input type="text" id="edit-motivo" >
                </div>
               
              </div>
              <div class="titulos">
                  <h4>Informe del estudio</h4>
             </div>
             <div class="grupotexarea">
                <div class="intputs">
                    <label for="edit-esofago">Esófago:</label>
                    <textarea type="text"  id="edit-esofago" name="esofago" ></textarea>
                </div>
                <div class="intputs">
                   <label for="edit-estomago">Estómago:</label>
                   <textarea type="text"  id="edit-estomago" name="estomago" ></textarea>
                </div>
                <div class="intputs" >
                    <label for="edit-duodeno">Duodeno:</label>
                    <textarea type="text"  id="edit-duodeno" name="duodeno" ></textarea>
                </div>
                <div class="intputs" >
                   <label for="edit-informe">Informe:</label>
                   <textarea type="text"  id="edit-informe" name="informe" ></textarea>
                </div>
                <div class="intputs">
                   <label for="edit-conclusion">Conclusión:</label>
                   <textarea type="text"  id="edit-conclusion" name="conclusion" ></textarea>
                </div>
              </div>
              <div class="grupointputs">
                <div class="intputs">
                    <label for="edit-terapeutica">¿Se efectuó terapéutica?:</label>
                    <select name="terapeutico" id="edit-terapeutica">
                        <option value="0">NO</option>
                        <option value="1">SI</option>
                    </select>
                </div>
                <div class="intputs autocomplete-container" id="cual-input-container">
                 <label for="edit-cual">¿Cuál?:</label>
                 <input type="text" name="edit-cual"  id="edit-cual" autocomplete="off">
                 <div id="autocomplete-list" class="autocomplete-items"></div>
                </div>
                
                
              </div>
              <div class="grupointputs">
                <div class="intputs">
                    <label for="edit-biopsia">¿Se efectuó biopsia?:</label>
                    <select name="biopsia" id="edit-biopsia" >
                        <option value="0">NO</option>
                        <option value="1">SI</option>
                    </select>
                </div>
                <div class="intputs" id="frascos-input-container">
                 <label for="edit-frascos">Cantidad de frascos:</label>
                 <input type="number" id="edit-frascos" >
                </div>
                
                
              </div>
              <div class="titulos">
                <h4>Imágenes del Estudio</h4>
            </div>
            <div class="image">
            <label>Subir fotos </label>
            <input type="file" name="archivo[]" accept="image/*" multiple  id="edit-imagenes">
            </div>
            <div class="titulos">
                <h4>Imágenes Existentes</h4>
            </div>
            <div id="image-display-container" class="image-gallery">
                <p id="no-images-message" style="display: none;">No hay imágenes para este informe.</p>
            </div>
        </div>
        <div style="display: flex; justify-content: flex-end;">
            <button type="button" onclick="cerrarModal()">Cancelar</button>
            <button type="submit" id="btnGuardarEdicion" disabled>
              <span id="buttonTextEdit">Guardar Cambios</span>
              <span id="spinnerEdit" class="spinner hidden"></span>
             </button>
        </div>
        </form>
    </div>
</div>



<script>
    function toggleFilterButton() {
    const fechaDesde = document.getElementById("fecha_desde").value;
    const fechaHasta = document.getElementById("fecha_hasta").value;
    const filterButton = document.getElementById("filterButton");

    // The button will be enabled only if both date fields have a value
    if (fechaDesde && fechaHasta) {
        filterButton.disabled = false;
    } else {
        filterButton.disabled = true;
    }
}
    function calcularEdad(fechaNacimiento) {
    if (!fechaNacimiento) {
        return ''; // Retorna vacío si no hay fecha de nacimiento
    }

    const fechaNac = new Date(fechaNacimiento);
    const hoy = new Date();

    let edad = hoy.getFullYear() - fechaNac.getFullYear();
    const mes = hoy.getMonth() - fechaNac.getMonth();

    // Ajustar edad si el cumpleaños aún no ha pasado este año
    if (mes < 0 || (mes === 0 && hoy.getDate() < fechaNac.getDate())) {
        edad--;
    }
    return edad;
}
document.addEventListener("DOMContentLoaded", function() {
    // ... tu código existente ...

    const inputFechaNacimiento = document.getElementById('edit-fechanacimiento');
    const inputEdad = document.getElementById('edit-edad');

    // Agrega el event listener para calcular la edad automáticamente
    inputFechaNacimiento.addEventListener('change', function() {
        inputEdad.value = calcularEdad(this.value);
        validarFormularioEditar(); // Vuelve a validar el formulario después de actualizar la edad
    });

    // ... tu código existente ...
});
function showLoadingStateEdit() {
    const buttonText = document.getElementById('buttonTextEdit');
    const spinner = document.getElementById('spinnerEdit');
    const submitBtn = document.getElementById('btnGuardarEdicion');

    buttonText.classList.add('hidden'); // Oculta el texto
    spinner.classList.remove('hidden'); // Muestra el spinner
    submitBtn.disabled = true;          // Deshabilita el botón
    submitBtn.style.cursor = 'wait';    // Cambia el cursor a "espera"
}

function hideLoadingStateEdit() {
    const buttonText = document.getElementById('buttonTextEdit');
    const spinner = document.getElementById('spinnerEdit');
    const submitBtn = document.getElementById('btnGuardarEdicion');

    buttonText.classList.remove('hidden'); // Muestra el texto
    spinner.classList.add('hidden');      // Oculta el spinner
    submitBtn.style.cursor = 'pointer';   // Restaura el cursor
    validarFormularioEditar();            // Vuelve a habilitar el botón si los campos son válidos
}

 document.getElementById("downloadPdfsButton").addEventListener("click", function() {
            downloadPdfsByDateRange();
        });
        function downloadPdfsByDateRange() {
        const fechaDesde = document.getElementById("fecha_desde").value;
        const fechaHasta = document.getElementById("fecha_hasta").value;
        const cobertura = document.getElementById("cobertura_filtro").value; 

        // Basic validation: ensure at least one date is provided for a meaningful range
        if (!fechaDesde && !fechaHasta) {
            mostrarMensaje("error", "Por favor, selecciona al menos una fecha (Desde o Hasta) para descargar los reportes.");
            return;
        }

        const params = new URLSearchParams();
        if (fechaDesde) {
            params.append("fecha_inicio", fechaDesde);
        }
        if (fechaHasta) {
            params.append("fecha_fin", fechaHasta);
        }
        if (cobertura) { 
        params.append("cobertura", cobertura);
        }

        const url = `<?= site_url('informes/descargar-pdfs'); ?>?${params.toString()}`;
        
        // Open the URL in a new tab/window to trigger the download
        
        mostrarMensaje("success", "Iniciando descarga de reportes...");
    }  
function cargarCoberturas() {
        // Realiza la solicitud AJAX
        fetch('<?= site_url('coberturas'); ?>')
            .then(response => response.json())
            .then(data => {
                // Verifica los datos que recibe
                // Una vez obtenidos los datos, llena el select
                const selectCobertura = document.getElementById('edit-tipoco');
                const selectCoberturaFiltro = document.getElementById('cobertura_filtro');
                selectCobertura.innerHTML = ''; // Limpia el select
                selectCoberturaFiltro.innerHTML = '';
                // Agrega una opción por defecto
                const defaultOption = document.createElement('option');
                defaultOption.value = '';
                defaultOption.textContent = 'Seleccione una cobertura';
                selectCobertura.appendChild(defaultOption);
                const defaultFilterOption = document.createElement('option');
                defaultFilterOption.value = '';
                defaultFilterOption.textContent = 'Todas las coberturas';
                selectCoberturaFiltro.appendChild(defaultFilterOption);
                // Agrega las opciones de coberturas al select
                if (Array.isArray(data)) {
                    data.forEach(cobertura => {
                        const option = document.createElement('option');
                        option.value = cobertura.id_cobertura; // El valor de la opción es el ID de la cobertura
                        option.textContent = cobertura.nombre_cobertura; // El texto es el nombre de la cobertura
                        selectCobertura.appendChild(option);
                        const optionFiltro = document.createElement('option');
                        optionFiltro.value = cobertura.nombre_cobertura; // ¡IMPORTANTE! El filtro usa el nombre
                        optionFiltro.textContent = cobertura.nombre_cobertura;
                        selectCoberturaFiltro.appendChild(optionFiltro);
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
                const selectCobertura = document.getElementById('edit-tipoco');
                selectCobertura.innerHTML = '<option value="">Error al cargar coberturas</option>';
            });
    }

    // Llama a la función para cargar las coberturas cuando la página esté lista
    window.addEventListener('DOMContentLoaded', cargarCoberturas);
 
    


    function updateDownloadButtonState() {
    const downloadButton = document.getElementById("downloadPdfsButton");
    const fechaDesde = document.getElementById("fecha_desde").value;
    const fechaHasta = document.getElementById("fecha_hasta").value;

    if (reportFilterApplied && fechaDesde && fechaHasta) {
        downloadButton.disabled = false;
        downloadButton.style.display = "block"; // Muestra el botón
    } else {
        downloadButton.disabled = true;
        downloadButton.style.display = "none"; // Oculta el botón
    }
}


document.addEventListener("DOMContentLoaded", function () {
    fetchInformes();

    // Eventos para la búsqueda por nombre
    document.getElementById("search-icon").addEventListener("click", function () {
        filtrarTabla();
    });

    document.getElementById("nombre").addEventListener("keyup", function () {
        filtrarTabla();
    });
    document.getElementById("cobertura_filtro").addEventListener("change", function () {
            filtrarTabla();
    });

    document.getElementById("filterForm").addEventListener("submit", function (event) {
        event.preventDefault(); // Evita recargar la página
        const fechaDesde = document.getElementById("fecha_desde").value;
        const fechaHasta = document.getElementById("fecha_hasta").value;

        if (fechaDesde && fechaHasta) {
            filtrarTabla();
            reportFilterApplied = true; // Set the flag only if both dates are provided and filter is clicked
        } else {
            reportFilterApplied = false; // Ensure flag is false if dates are not complete
            mostrarMensaje("error", "Por favor, ingresa ambas fechas (Desde y Hasta) para filtrar.");
        }
        updateDownloadButtonState(); // Always update state after form submission
    });

    // Añade event listeners a los campos de fecha para controlar el botón "Filtrar"
    document.getElementById("fecha_desde").addEventListener("change", function() {
        toggleFilterButton();
        reportFilterApplied = false; // Reset flag when dates change
        updateDownloadButtonState(); // Update download button state
    });
    document.getElementById("fecha_hasta").addEventListener("change", function() {
        toggleFilterButton();
        reportFilterApplied = false; // Reset flag when dates change
        updateDownloadButtonState(); // Update download button state
    });

    // Llama a toggleFilterButton y updateDownloadButtonState al cargar la página para establecer el estado inicial
    toggleFilterButton();
    updateDownloadButtonState();
    // Paginación
    document.getElementById("prevPage").addEventListener("click", function() {
        changePage(-1);
    });

    document.getElementById("nextPage").addEventListener("click", function() {
        changePage(1);
    });
});

let reportes = [];
let currentPage = 1; // Cambia este valor para ajustar la cantidad de elementos por página
let totalPages = 1;
let itemsPerPage = 20;
function fetchInformes() {
    const params = new URLSearchParams({
        page: currentPage,
        per_page: itemsPerPage
    });

    fetch(`<?= site_url('informes-paginado'); ?>?${params.toString()}`, {
        method: "GET",
        headers: {
            "Content-Type": "application/json",
        },
    })
    .then(response => response.json())
    .then(data => {
        reportes = data.data;
        currentPage = parseInt(data.meta.pagina_actual);
        totalPages = parseInt(data.meta.total_paginas);
        updatePagination();
    })
    .catch(error => console.error("Error en la solicitud:", error));
}

function updatePagination() {
    document.getElementById("pageNumber").textContent = currentPage;

    // Deshabilitar botones si estamos en el inicio o final
    document.getElementById("prevPage").disabled = currentPage === 1;
    document.getElementById("nextPage").disabled = currentPage === totalPages;

    mostrarPagina();
}
function mostrarPagina() {
    let tbody = document.querySelector("table tbody");
    tbody.innerHTML = "";

    if (reportes.length === 0) {
        tbody.innerHTML = `<tr><td colspan="7">No hay reportes disponibles.</td></tr>`;
        return;
    }

    reportes.forEach(reporte => {
        let fila = `
            <tr>
                <td>${reporte.nombre_paciente || ""}</td>
                <td>${reporte.dni_paciente || ""}</td>
             <td>${formatearFecha(reporte.fecha) || ""}</td>
                <td>${reporte.mail_paciente || ""}</td>
                <td>${reporte.tipo_informe || ""}</td>
                <td>${reporte.nombre_cobertura || ""}</td>
                <td>
                    <div class="tooltip">
                        <button onclick="descargarReporte('${reporte.url_archivo}')">
                            <span class="material-icons">download</span>
                        </button>
                        <div class="tooltip-text">Descargar Reporte</div>
                    </div>
                    <div class="tooltip">
                        <button onclick="reenviarReporte(${reporte.id_informe}, this)">
                            <span class="material-icons">send</span>
                        </button>
                        <div class="tooltip-text">Reenviar Reporte</div>
                    </div>
                    <div class="tooltip">
                        <button onclick='abrirModalEditar(${JSON.stringify(reporte)})'>
                            <span class="material-icons">edit</span>
                        </button>
                        <div class="tooltip-text">Editar Reporte</div>
                    </div>
                </td>
            </tr>
        `;
        tbody.innerHTML += fila;
    });
}

// Función para cambiar de página
function changePage(direction) {
    if ((direction === -1 && currentPage > 1) || (direction === 1 && currentPage < totalPages)) {
        currentPage += direction;
        fetchInformes();
    }
}

// 🔹 Función para filtrar la tabla por nombre y rango de fecha
function filtrarTabla() {
    const nombre = document.getElementById("nombre").value;
    const cobertura = document.getElementById("cobertura_filtro").value;
    const startDate = document.getElementById("fecha_desde").value;
    const endDate = document.getElementById("fecha_hasta").value;
    const params = new URLSearchParams({
        page: 1,
        per_page: itemsPerPage
    });
    // Armamos la URL con los parámetros (puedes usar POST si prefieres)
    
    if (nombre) params.append("nombre", nombre);
    if (startDate) params.append("fecha_desde", startDate);
    if (cobertura) params.append("cobertura", cobertura);
    if (endDate) params.append("fecha_hasta", endDate);

    fetch(`<?= site_url('informes-paginado'); ?>?${params.toString()}`, {
        method: "GET",
        headers: {
            "Content-Type": "application/json",
        },
    })
    .then(response => response.json())
    .then(data => {
        reportes = data.data;
        currentPage = parseInt(data.meta.pagina_actual);
        totalPages = parseInt(data.meta.total_paginas);
        updatePagination();
    })
    .catch(error => console.error("Error al filtrar:", error));
}

function descargarReporte(rutaRelativa) {
    const baseUrl = "<?= site_url('descargar-archivo') ?>";
    const url = `${baseUrl}?ruta=${encodeURIComponent(rutaRelativa)}`;

    fetch(url)
        .then(response => {
            if (!response.ok) {
                return response.text().then(texto => {
                    mostrarMensaje("error", "Error al descargar: " + texto);
                });
            }
            return response.blob().then(blob => {
                const urlBlob = window.URL.createObjectURL(blob);

                // Extraer carpetas intermedias
                const partes = rutaRelativa.split('/');
                const nombreDescarga = `${partes[1]}-${partes[2]}.pdf`;

                const a = document.createElement('a');
                a.href = urlBlob;
                a.download = nombreDescarga;
                document.body.appendChild(a);
                a.click();
                a.remove();
                window.URL.revokeObjectURL(urlBlob);
            });
        })
        .catch(error => {
            mostrarMensaje("error", "Ocurrió un error al intentar descargar el archivo.");
            console.error("Error:", error);
        });
}



function reenviarReporte(id, buttonElement) {
    const url = '<?= site_url('/reenviar-informe/'); ?>' + id;

    // Obtener el ícono dentro del botón
    const icon = buttonElement.querySelector('span');

    // Guardar el ícono original
    const originalIcon = icon.innerHTML;

    // Mostrar spinner y desactivar botón
    icon.innerHTML = 'autorenew'; // spinner material icon
    icon.classList.add('spin'); // aplicamos clase CSS para rotar
    buttonElement.disabled = true;

    fetch(url, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json'
        },
        body: JSON.stringify({})
    })
    .then(response => response.json())
    .then(data => {
        if (data.status === 'success') {
            mostrarMensaje("success", "Se reenvió el informe correctamente");
        } else {
            mostrarMensaje("error", "Error: " + data.message);
        }
    })
    .catch(error => {
        console.error(error);
        mostrarMensaje("error", "Error al reenviar el informe");
    })
    .finally(() => {
        // Restaurar estado original
        icon.innerHTML = originalIcon;
        icon.classList.remove('spin');
        buttonElement.disabled = false;
    });
}


function validarFormularioEditar() {
        const form = document.getElementById("formEditarReporte");
        const botonGuardar = document.getElementById("btnGuardarEdicion");
        const spinnerActive = !document.getElementById('spinnerEdit').classList.contains('hidden');
        // Selecciona todos los campos requeridos que no estén deshabilitados
        const camposRequeridos = form.querySelectorAll("[required]:not([disabled])");
        let formularioValido = true;

        camposRequeridos.forEach(campo => {
            if (!campo.value.trim()) {
                formularioValido = false;
            }
        });

        // Habilita o deshabilita el botón
        botonGuardar.disabled = !formularioValido || spinnerActive;
    }

    // Ejecutar validación al cargar y al escribir
    document.addEventListener("DOMContentLoaded", () => {
        const form = document.getElementById("formEditarReporte");
        const campos = form.querySelectorAll("input, select, textarea");

        campos.forEach(campo => {
            campo.addEventListener("input", validarFormularioEditar);
            campo.addEventListener("change", validarFormularioEditar);
        });

        validarFormularioEditar(); // Ejecutar una vez al cargar
    });

    let imagenesExistentes = [];
function abrirModalEditar(reporte) {
    imagenesExistentes = []; 
    document.getElementById('edit-id').value = reporte.id_informe;
    document.getElementById('edit-nombre').value = reporte.nombre_paciente || '';
    document.getElementById('edit-mail').value = reporte.mail_paciente || '';
    document.getElementById('edit-fechanacimiento').value = reporte.fecha_nacimiento_paciente || '';
    document.getElementById('edit-edad').value = calcularEdad(reporte.fecha_nacimiento_paciente) || '';
    document.getElementById('edit-tipoco').value = reporte.id_cobertura || '';
    document.getElementById('edit-numeroAfiliado').value = reporte.numero_afiliado || '';
    document.getElementById('edit-medico').value = reporte.medico_envia_estudio || '';
    document.getElementById('edit-motivo').value = reporte.motivo_estudio || '';
    document.getElementById('edit-esofago').value = reporte.esofago || '';
    document.getElementById('edit-estomago').value = reporte.estomago || '';
    document.getElementById('edit-duodeno').value = reporte.duodeno || '';
    document.getElementById('edit-informe').value = reporte.informe || '';
    document.getElementById('edit-conclusion').value = reporte.conclusion || '';
    document.getElementById('edit-terapeutica').value = reporte.efectuo_terapeutica || '';
    document.getElementById('edit-cual').value = reporte.tipo_terapeutica || '';
    document.getElementById('edit-biopsia').value = reporte.efectuo_biopsia || '';
    document.getElementById('edit-frascos').value = reporte.fracos_biopsia || '';
    document.getElementById('edit-imagenes').value = ''; 
    const cualInputContainer = document.getElementById('cual-input-container');
    const frascosInputContainer = document.getElementById('frascos-input-container');
 // *** Cargar y mostrar imágenes ***
    const imageDisplayContainer = document.getElementById('image-display-container');
    // Clear previous images
    imageDisplayContainer.innerHTML = ''; 

    const noImagesMessage = document.getElementById('no-images-message');
    if (noImagesMessage) { // <--- Added this check
        noImagesMessage.style.display = 'none'; 
    }

    fetch(`<?= site_url('informe/imagenes'); ?>/${reporte.id_informe}`)
        .then(response => response.json())
        .then(data => {
            if (data.success && data.imagenes.length > 0) {
                data.imagenes.forEach(imageUrl => {
                    const imageWrapper = document.createElement('div');
                    imageWrapper.classList.add('image-wrapper'); // Añade una clase para envolver la imagen y la "x"
                    imagenesExistentes.push(imageUrl);

                    const img = document.createElement('img');
                    img.src = imageUrl + '?' + new Date().getTime(); 
                    img.alt = 'Imagen del informe';
                    imageWrapper.appendChild(img);

                    const deleteButton = document.createElement('span');
                    deleteButton.classList.add('delete-image-button');
                    deleteButton.innerHTML = '&times;'; // El carácter 'x'
                    deleteButton.onclick = function() {
                        // Simplemente oculta la imagen del DOM
                        imagenesExistentes = imagenesExistentes.filter(url => url !== imageUrl);
                        imageWrapper.remove(); 
                        // Verifica si no quedan más imágenes y muestra el mensaje
                        if (imageDisplayContainer.querySelectorAll('.image-wrapper').length === 0) {
                        // Crucial: verificar de nuevo si 'noImagesMessage' no es nulo
                        if (noImagesMessage) { 
                            noImagesMessage.style.display = 'block';
                            }
                        }
                    };
                    imageWrapper.appendChild(deleteButton);

                    imageDisplayContainer.appendChild(imageWrapper);
                });
            } else {
                // Si no hay imágenes, muestra el mensaje
                if (noImagesMessage) {
                    noImagesMessage.style.display = 'block';
                }
            }
        })
        .catch(error => {
            console.error('Error al cargar las imágenes:', error);
            if (noImagesMessage) {
                noImagesMessage.style.display = 'block';
            }
        });

    // Función para actualizar la visibilidad de los campos dependientes
    function updateVisibility() {
        // Visibilidad de 'edit-cual'
        if (document.getElementById('edit-terapeutica').value === '0') {
            cualInputContainer.style.display = 'none';
            document.getElementById('edit-cual').required = false; // Ya no es requerido si está oculto
        } else {
            cualInputContainer.style.display = 'flex'; // Usar 'flex' ya que el padre tiene display: flex
            document.getElementById('edit-cual').required = true;
        }

        // Visibilidad de 'edit-frascos'
        if (document.getElementById('edit-biopsia').value === '0') {
            frascosInputContainer.style.display = 'none';
            document.getElementById('edit-frascos').required = false; // Ya no es requerido si está oculto
        } else {
            frascosInputContainer.style.display = 'flex'; // Usar 'flex'
            document.getElementById('edit-frascos').required = true;
        }
    }

    updateVisibility();

 
    document.getElementById('edit-terapeutica').onchange = updateVisibility;
    document.getElementById('edit-biopsia').onchange = updateVisibility;

    const esofagoInputContainer = document.querySelector('label[for="edit-esofago"]').parentNode;
    const estomagoInputContainer = document.querySelector('label[for="edit-estomago"]').parentNode;
    const duodenoInputContainer = document.querySelector('label[for="edit-duodeno"]').parentNode;
    const informeInputContainer = document.querySelector('label[for="edit-informe"]').parentNode;

    // Set values for esophageal, stomach, and duodenal fields, then handle visibility
    document.getElementById('edit-esofago').value = reporte.esofago || '';
    document.getElementById('edit-estomago').value = reporte.estomago || '';
    document.getElementById('edit-duodeno').value = reporte.duodeno || '';
    document.getElementById('edit-informe').value = reporte.informe || '';

    if (reporte.tipo_informe === 'VEDA') {
        esofagoInputContainer.style.display = 'block';
        estomagoInputContainer.style.display = 'block';
        duodenoInputContainer.style.display = 'block';
        informeInputContainer.style.display = 'none';
    } else if (reporte.tipo_informe === 'VCC') {
        esofagoInputContainer.style.display = 'none';
        estomagoInputContainer.style.display = 'none';
        duodenoInputContainer.style.display = 'none';
        informeInputContainer.style.display = 'block';
    } else {
        // Default behavior for other report types: show all or hide all based on your preference
        esofagoInputContainer.style.display = 'block';
        estomagoInputContainer.style.display = 'block';
        duodenoInputContainer.style.display = 'block';
        informeInputContainer.style.display = 'block';
    }

    document.getElementById('modal-editar').style.display = 'flex';
    setTimeout(() => {
            validarFormularioEditar(); // Valida al abrir
        }, 100);
}

function cerrarModal() {
    document.getElementById('modal-editar').style.display = 'none';
}
document.getElementById("formEditarReporte").addEventListener("submit", function (e) {
    e.preventDefault(); // Evita el envío tradicional del formulario
    showLoadingStateEdit();
    const id = document.getElementById("edit-id").value;
    const inputImagenes = document.getElementById('edit-imagenes');
    const newFiles = Array.from(inputImagenes.files); // Obtiene los archivos seleccionados

    // 1. Prepara los datos del informe (texto)
    let efectuo_terapeutica = document.getElementById('edit-terapeutica').value;
    let efectuo_biopsia = document.getElementById('edit-biopsia').value;

    let tipo_terapeutica_value = document.getElementById('edit-cual').value;
    let fracos_biopsia_value = document.getElementById('edit-frascos').value;

    // Lógicas condicionales para tipo_terapeutica y fracos_biopsia
    if (efectuo_terapeutica === "0") {
        tipo_terapeutica_value = null; // O '' si tu backend lo prefiere
    }

    if (efectuo_biopsia === "0") {
        fracos_biopsia_value = null; // O '' si tu backend lo prefiere
    }

    const datosInforme = { // Cambiado a 'datosInforme' para distinguirlo
        id_informe: id,
        nombre_paciente: document.getElementById('edit-nombre').value,
        mail_paciente: document.getElementById('edit-mail').value,
        fecha_nacimiento_paciente: document.getElementById('edit-fechanacimiento').value,
        edad: document.getElementById('edit-edad').value,
        id_cobertura: document.getElementById('edit-tipoco').value,
        numero_afiliado: document.getElementById('edit-numeroAfiliado').value,
        medico_envia_estudio: document.getElementById('edit-medico').value,
        motivo_estudio: document.getElementById('edit-motivo').value,
        esofago: document.getElementById('edit-esofago').value,
        estomago: document.getElementById('edit-estomago').value,
        duodeno: document.getElementById('edit-duodeno').value,
        informe: document.getElementById('edit-informe').value,
        conclusion: document.getElementById('edit-conclusion').value,
        efectuo_terapeutica: efectuo_terapeutica,
        tipo_terapeutica: tipo_terapeutica_value,
        efectuo_biopsia: efectuo_biopsia,
        fracos_biopsia: fracos_biopsia_value
    };

    // 2. Realiza la primera llamada fetch para actualizar los datos del informe (JSON)
    fetch(`<?= site_url('informe/editar/'); ?>${id}`, {
        method: "PUT",
        headers: {
            "Content-Type": "application/json",
        },
        body: JSON.stringify(datosInforme),
    })
    .then(response => {
        if (!response.ok) {
            return response.json().then(err => {
                throw new Error(err.message || 'Error al actualizar los datos del informe.');
            });
        }
        return response.json();
    })
    .then(result => {
        if (result.status === "success") {
            // Si la actualización de datos fue exitosa, procedemos con la actualización de imágenes
            mostrarMensaje("success", "Datos del reporte actualizados correctamente.");

            // 3. Prepara los datos de las imágenes (FormData)
            const allImagesToUpload = [...newFiles];
            const fetchImageAsFile = async (url) => {
                try {
                    const response = await fetch(url);
                    const blob = await response.blob();
                    const filename = url.substring(url.lastIndexOf('/') + 1); // Extrae el nombre del archivo de la URL
                    return new File([blob], filename, { type: blob.type });
                } catch (error) {
                    console.error('Error al recuperar imagen existente para re-subir:', url, error);
                    return null; // Retorna null si la recuperación falla
                }
            };
            const existingImagePromises = imagenesExistentes.map(fetchImageAsFile);
            return Promise.all(existingImagePromises)
                .then(keptFiles => {
                    // Filtra cualquier imagen que no se pudo recuperar (nulls) y añádelas al array principal
                    keptFiles.forEach(file => {
                        if (file) {
                            allImagesToUpload.push(file);
                        }
                    });

                    // Si no hay imágenes en total (ni nuevas ni existentes a conservar),
                    // no tiene sentido llamar al endpoint de imágenes.
                    if (allImagesToUpload.length === 0) {
                        mostrarMensaje("info", "No hay imágenes para actualizar. Las imágenes antiguas se eliminaron si existían.");
                        cerrarModal();
                        fetchInformes();
                        return Promise.resolve(); // Salimos de la cadena de promesas
                    }

                    // Crea el objeto FormData para enviar todas las imágenes
                    const formDataImagenes = new FormData();
                    
                    // Añade TODOS los archivos (nuevos y re-subidos) bajo el nombre 'archivo[]'
                    // Esto es lo que tu función PHP 'updateInformeImages' espera.
                    allImagesToUpload.forEach(file => {
                        formDataImagenes.append('archivo[]', file);
                    });
                    
                    // 3. Realiza la segunda llamada fetch para actualizar las imágenes
                    return fetch(`<?= site_url('informe/imagenes/update/'); ?>${id}`, {
                        method: 'POST', // Tu backend espera POST para subir archivos
                        body: formDataImagenes, // NO se usa JSON.stringify para FormData
                    });
                });
        } else {
            // Si la actualización de los datos principales falló, muestra el error y detiene el proceso de imágenes
            mostrarMensaje("error", "Error al actualizar los datos del reporte: " + result.message);
            return Promise.reject(new Error("Falló la actualización de datos, no se intentó subir imágenes."));
        }
    })
    .then(response => {
        // Esta es la respuesta de la SEGUNDA llamada fetch (la de las imágenes)
        if (!response.ok) {
            return response.json().then(err => {
                throw new Error(err.message || 'Error al actualizar las imágenes del informe.');
            });
        }
        return response.json();
    })
    .then(resultImagenes => {
        cerrarModal();
        fetchInformes(); 
        // Esta es la respuesta final de la actualización de imágenes
        if (resultImagenes.success) { // Tu función PHP `updateInformeImages` devuelve 'success'
            mostrarMensaje("success", "Imágenes del reporte actualizadas y PDF regenerado correctamente.");
        } else {
            mostrarMensaje("error", "Error al actualizar las imágenes: " + resultImagenes.message);
        }
        // Independientemente del resultado de las imágenes, cerramos el modal
        // y refrescamos la lista de informes para ver los cambios.
       
    })
    .catch(error => {
        // Este 'catch' general maneja errores de red o errores lanzados desde los '.then' anteriores.
        cerrarModal();
        fetchInformes(); 
        console.error('Error general en la actualización:', error);
        mostrarMensaje("error", "Ocurrió un error general en la solicitud: " + error.message);
    })
    .finally(() => {
        hideLoadingStateEdit(); // <-- Oculta el spinner al finalizar (éxito o error)
    });
   fetchInformes(); 
});

function mostrarMensaje(tipo, mensaje) {
    const alerta = document.getElementById('mensaje-alerta');
    const texto = document.getElementById('alert-text');
    const cerrar = document.getElementById('close-alert');

    alerta.classList.remove('hidden', 'success', 'error');
    alerta.classList.add(tipo); // success o error
    texto.innerText = mensaje;

    // Mostrar el botón de cerrar solo si es error
    cerrar.style.display = tipo === 'error' ? 'inline' : 'none';

    if (tipo === 'success') {
        setTimeout(() => {
            alerta.classList.add('hidden');
        }, 10000); // 10 segundos
    }
}

// Permitir cerrar manualmente el mensaje
document.getElementById('close-alert').addEventListener('click', () => {
    document.getElementById('mensaje-alerta').classList.add('hidden');
});

function formatearFecha(fechaOriginal) {
  if (!fechaOriginal) {
    return "";
  }

  const partesFecha = fechaOriginal.split('-');
  if (partesFecha.length !== 3) {
    console.error("Error: Formato de fecha inválido:", fechaOriginal);
    return fechaOriginal;
  }

  const anio = partesFecha[0];
  const mes = partesFecha[1];
  const dia = partesFecha[2];

  return `${dia}-${mes}-${anio}`;
}
const sugerencias = [
        "Polipectomía/s",
        "Mucosectomía",
        "Dilatación con balón",
        "Marcación",
        "Tratamiento hemostático",
        "Argón láser"
    ];

    const input = document.getElementById('edit-cual');
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
    const campoCual = document.getElementById('edit-cual');

    // Guardar en localStorage cada vez que el campo cambia
    campoCual.addEventListener('input', function () {
        localStorage.setItem('edit-cual', campoCual.value.trim());
    });

    // Cargar valor guardado al inicio
    const cualGuardado = localStorage.getItem('edit-cual');
    if (cualGuardado) {
        campoCual.value = cualGuardado;
    }
});
// Al hacer clic en una opción de autocompletado
document.getElementById('autocomplete-list').addEventListener('click', function (e) {
    if (e.target && e.target.matches('.autocomplete-item')) {
        const inputCual = document.getElementById('edit-cual');
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

    const inputMedico = document.getElementById("edit-medico");
    setupAutocomplete(inputMedico, listaMedicos, "autocomplete-medico-list");
});

</script>
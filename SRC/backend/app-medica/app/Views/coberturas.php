<?php include('header.php'); ?> 

<style>
    body {
        background-color: #f8f9fa;
        font-family: Arial, sans-serif;
    }

    .container {
        width: 55%;
        margin: 19px auto;
    }

    .card {
        background: white;
        padding: 27px;
        border-radius: 10px;
        box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        border: 2px solid #e1e1e1;
        
    }

    h2 {
        text-align: center;
        color: #007bbd;
    }

    table {
        width: 100%;
        border-collapse: collapse;
        margin-top: 20px;
        border-radius: 8px;
        overflow: hidden;
    }

    th, td {
        border: 1px solid #ddd;
        padding: 12px;
        text-align: left;
    }

    th {
        background-color: #007bbd;
        color: white;
    }

    .btn {
        padding: 8px 12px;
        text-decoration: none;
        border: none;
        cursor: pointer;
        border-radius: 5px;
        font-size: 14px;
        transition: 0.3s;
    }

    .btn-add {
        background-color: #28a745;
        color: white;
        margin-bottom: 15px;
        display: inline-block;
        padding: 10px 15px;
        border-radius: 5px;
        font-weight: bold;
    }

    
    .btn:hover {
        opacity: 0.8;
    }

    /* Estilo para el modal */
   /* Estilo para el modal */
.modal {
    display: none;
    position: fixed;
    z-index: 1;
    left: 0;
    top: 0;
    width: 100%;
    height: 100%;
    overflow: auto;
    background-color: rgba(0, 0, 0, 0.4); /* Fondo semitransparente */
    padding-top: 60px;
    transition: all 0.4s ease-in-out;
}

.modal-content {
    background-color: #fff;
    margin: 5% auto;
    padding: 20px;
    border: 1px solid #888;
    width: 400px; /* Tamaño común para todos los modales */
    border-radius: 10px;
    display: flex;
    flex-direction: column;
    align-items: center;
}

.modal-header {
    font-size: 1.5em;
    margin-bottom: 18px;
    color: #007bbd;
    text-align: center;
}

.modal-footer {
    text-align: center;
    margin-top: 15px;
}

.modal-button {
    padding: 10px 15px;
    background-color: #007bbf;
    color: white;
    border-radius: 5px;
    cursor: pointer;
    border: none;
    font-weight: bold;
    margin-right: 10px;
}

.modal-button.cancel {
    background-color: #dc3545;
}

.modal-button:hover {
    opacity: 0.8;
}

/* Estilo para el botón de cerrar */
.close {
    text-align: end;
    color: #007bbd;
    font-size: x-large;
    cursor: pointer;
}

/* Estilo de los inputs dentro del modal */
input[type="text"] {
    width: 100%;
    padding: 10px;
    margin: 8px 0;
    border: 1px solid #ddd;
    border-radius: 8px;
    font-size: 14px;
    transition: border 0.3s ease, box-shadow 0.3s ease;
}

input[type="text"]:focus {
    border: 1px solid #007bbd;
    box-shadow: 0 0 5px rgba(0, 123, 189, 0.5);
    outline: none;
}

label {
    font-size: 16px;
    color: #333;
    margin-bottom: 5px;
}


    /* Estilo para los botones en la tabla */
    .btn-edit, .btn-delete {
        padding: 6px 12px;
        border-radius: 5px;
        font-size: 14px;
        cursor: pointer;
    }

  
    /* Estilo general para los inputs */
input[type="text"] {
    width: 100%;
    padding: 10px;
    margin: 8px 0;
    border: 1px solid #ddd;
    border-radius: 8px;
    font-size: 14px;
    transition: border 0.3s ease, box-shadow 0.3s ease;
}

/* Estilo cuando el input está en foco */
input[type="text"]:focus {
    border: 1px solid #007bbd;
    box-shadow: 0 0 5px rgba(0, 123, 189, 0.5);
    outline: none;
}

/* Estilo de las etiquetas */
label {
    font-size: 16px;
    color: #333;
    margin-bottom: 5px;
}

/* Estilo para los inputs dentro del modal */
.modal-content input[type="text"] {
    margin-top: 8px;
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

.modal-close {
    display: flex;
    width: 100%;
    flex-direction: column;
}

.pagination button:disabled {
    background-color: #007bbddb;
    color: #ffffff;
    cursor: not-allowed;
    opacity: 0.7;
}
.modal-button:disabled,
.modal-button.disabled-button {
    background-color: #0087ce !important;
    cursor: not-allowed;
    opacity: 0.6;
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
    #close-alert {
    margin-left: 10px;
    font-size: 18px;
}

</style>
<div id="alert-message" class="alert hidden">
    <span id="close-alert" style="float:right; cursor:pointer; font-weight: bold;">&times;</span>
    <span id="alert-text"></span>
    </div>
<div class="container">
    <div class="card">
        <h2>Lista de Coberturas</h2>
        <button class="btn btn-add" onclick="showModal('addModal')">Agregar Cobertura</button>
        
        <!-- Tabla de coberturas -->
        <table>
            <thead>
                <tr>
                    <th>Nombre Cobertura</th>
                    <th>Acciones</th>
                </tr>
            </thead>
            <tbody id="tablaCoberturas">
                <tr>
                    <td colspan="2">Cargando coberturas...</td>
                    
                </tr>
            </tbody>
        </table>

        <!-- Paginación -->
        <div class="pagination">
            <button id="prevPage" onclick="changePage(-1)" disabled>Anterior</button>
            <span id="pageNumber">1</span>
            <button id="nextPage" onclick="changePage(1)" disabled>Siguiente</button>
        </div>
    </div>
</div>
<!-- Modal para agregar cobertura -->
<div id="addModal" class="modal">
    <div class="modal-content">
        <div class="modal-close">

            <span class="close" onclick="closeModal('addModal')">&times;</span>
        </div>
        <div class="modal-header">
            <h3>Agregar Cobertura</h3>
        </div>
        <form id="formAgregarCobertura">
            <label for="nombre_cobertura">Nombre Cobertura:</label>
            <input type="text" id="nombre_cobertura" name="nombre_cobertura" required required style="text-transform: uppercase;"><br><br>
            <div class="modal-footer">
                <button type="button" class="modal-button cancel" onclick="closeModal('addModal')">Cancelar</button>
                
                <button type="submit" class="modal-button" id="btnGuardar" disabled>Guardar</button>

            </div>
        </form>
    </div>
</div>

<!-- Modal para editar cobertura -->
<div id="editModal" class="modal">
    <div class="modal-content">
        <div class="modal-close">

            <span class="close" onclick="closeModal('editModal')">&times;</span>
        </div>
        <div class="modal-header">
            <h3>Editar Cobertura</h3>
        </div>
        <form id="editForm" method="post" action="">
    <input type="hidden" name="_method" value="PUT">
    <input type="hidden" name="id_cobertura" id="edit_id">
    <label for="edit-nombre_cobertura">Nombre Cobertura:</label>
    <input type="text" name="nombre_cobertura" id="edit_nombre" required required style="text-transform: uppercase;">
    <div class="modal-footer">
        <button type="button" class="modal-button cancel" onclick="closeModal('editModal')">Cancelar</button>
        <button type="submit" class="modal-button" id="btnGuardarEditar">Guardar</button>
    </div>
</form>
    </div>
</div>

<!-- Modal para eliminar cobertura -->
<div id="deleteModal" class="modal">
    <div class="modal-content">
        <div class="modal-close">

            <span class="close" onclick="closeModal('deleteModal')">&times;</span>
        </div>
        <div class="modal-header">
            <h3>Eliminar Cobertura</h3>
        </div>
        <form id="deleteForm" method="post" action="<?= site_url('cobertura/borrar') ?>">
        <input type="hidden" name="_method" value="DELETE">
        <input type="hidden" name="id_cobertura" id="delete_id">
    <p style="padding-bottom: 27px;">¿Estás seguro de eliminar esta cobertura?</p>
    <div class="modal-footer">
        <button type="button" class="modal-button cancel" onclick="closeModal('deleteModal')">Cancelar</button>
        <button type="submit" class="modal-button">Eliminar</button>
    </div>
</form> 
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function () {
    const inputCobertura = document.getElementById('nombre_cobertura');
    const btnGuardar = document.getElementById('btnGuardar');
    const inputEditar = document.getElementById('edit_nombre');
    const btnEditar = document.getElementById('btnGuardarEditar');

    // Validación para agregar
    inputCobertura.addEventListener('input', function () {
        if (inputCobertura.value.trim() === '') {
            btnGuardar.disabled = true;
            btnGuardar.classList.add('disabled-button');
        } else {
            btnGuardar.disabled = false;
            btnGuardar.classList.remove('disabled-button');
        }
    });

    // Validación para editar
    inputEditar.addEventListener('input', function () {
        if (inputEditar.value.trim() === '') {
            btnEditar.disabled = true;
            btnEditar.classList.add('disabled-button');
        } else {
            btnEditar.disabled = false;
            btnEditar.classList.remove('disabled-button');
        }
    });

    // Inicializar el estado del botón editar cuando se abra el modal
    document.getElementById('editModal').addEventListener('show', function () {
        btnEditar.disabled = inputEditar.value.trim() === '';
    });
});
let coberturas = []; // Variable global para almacenar las coberturas
    let currentPage = 1;
    const itemsPerPage = 5; // Cantidad de coberturas por página

    window.onload = function() {
        cargarCoberturas();
    };

    function cargarCoberturas() {
        fetch('<?= site_url('coberturas'); ?>')
        .then(response => response.json())
        .then(data => {
            console.log("Datos recibidos:", data);  
            coberturas = data; // Guarda los datos en la variable global
            renderTable(); // Renderiza la tabla
        })
        .catch(error => {
            console.error('Error al cargar las coberturas:', error);
        });
    }

    function renderTable() {
        const tablaCoberturas = document.getElementById('tablaCoberturas');
        tablaCoberturas.innerHTML = ''; // Limpia la tabla antes de agregar filas

        if (coberturas.length === 0) {
            tablaCoberturas.innerHTML = '<tr><td colspan="2">No hay coberturas disponibles.</td></tr>';
            return;
        }

        const startIndex = (currentPage - 1) * itemsPerPage;
        const endIndex = startIndex + itemsPerPage;
        const paginatedItems = coberturas.slice(startIndex, endIndex); // Obtiene los elementos de la página actual

        paginatedItems.forEach(cobertura => {
            const fila = document.createElement('tr');
            let acciones = '';
            if (cobertura.nombre_cobertura !== 'SIN COBERTURA') {

                acciones = `
       <button style="width: 63px;height: 30px;border: none;border-radius: 6px;color: #000000; background-color: #ffc107;" 
                        onclick="showModal('editModal', ${cobertura.id_cobertura}, '${cobertura.nombre_cobertura}')">Editar</button>
                    <button style="height: 30px;border: none; border-radius: 6px;width: 71px; color: #fff; background-color: #dc3545;"
                        onclick="showModalDelete(${cobertura.id_cobertura})">Eliminar</button>
    `;
            }

           
fila.innerHTML = `
    <td>${cobertura.nombre_cobertura}</td>
    <td>${acciones}</td>
`;
            tablaCoberturas.appendChild(fila);
        });

         // Actualizar número de página
    document.getElementById('pageNumber').innerText = currentPage;

// Control de botones
const prevBtn = document.getElementById('prevPage');
const nextBtn = document.getElementById('nextPage');
const totalPages = Math.ceil(coberturas.length / itemsPerPage);

prevBtn.disabled = currentPage === 1;
nextBtn.disabled = currentPage >= totalPages;
    }

    function actualizarPaginacion() {
        document.getElementById('pageNumber').textContent = currentPage;
        document.getElementById('prevPage').disabled = currentPage === 1;
        document.getElementById('nextPage').disabled = currentPage * itemsPerPage >= coberturas.length;
    }

    function changePage(offset) {
        currentPage += offset;
        renderTable();
    }


  






    function closeModal(modalId) {
        var modal = document.getElementById(modalId);
        modal.style.display = "none";
    }
    function showModal(modalId, id = null, nombre = '') {
    let modal = document.getElementById(modalId);
    if (modalId === 'editModal') {
        // Asigna el ID y nombre a los campos del formulario
        document.getElementById('edit_id').value = id;
        document.getElementById('edit_nombre').value = nombre;

        // Asigna la URL correcta para el formulario de edición
        document.getElementById('editForm').action = '<?= site_url('cobertura/editar/'); ?>' + id;
    }
    modal.style.display = "block";

    }
    function showModalDelete(id) {
    document.getElementById('delete_id').value = id;
    document.getElementById('deleteModal').style.display = 'block';
}
document.getElementById('deleteForm').addEventListener('submit', function (e) {
    e.preventDefault();

    const formData = new FormData(this);
    const id = formData.get('id_cobertura');

    fetch(`<?= site_url('cobertura/borrar'); ?>/${id}`, {
        method: 'DELETE',
        headers: {
            'X-Requested-With': 'XMLHttpRequest'
        },
    })
    .then(response => response.json())
    .then(data => {
        if (data.status === 'error') {
            mostrarAlerta(data.message, 'error');
        } else {
            closeModal('deleteModal');
            mostrarAlerta('Cobertura eliminada exitosamente.', 'success');
            cargarCoberturas(); // Vuelve a cargar la tabla
        }
    })
    .catch(error => {
        console.error('Error en la solicitud:', error);
        mostrarAlerta('Error al eliminar la cobertura.', 'error');
    });
});
document.getElementById('formAgregarCobertura').addEventListener('submit', function (e) {
    e.preventDefault();

    const form = e.target;
    const formData = new FormData(form);

    fetch('<?= site_url('cobertura/alta'); ?>', {
        method: 'POST',
        body: formData
    })
    .then(async response => {
        const data = await response.json();

        if (!response.ok) {
            mostrarAlerta(data.message, 'error');
            closeModal('addModal');
        } else {
            closeModal('addModal');
            mostrarAlerta('Cobertura creada exitosamente.', 'success');
            cargarCoberturas(); // Refresca la tabla
            form.reset(); // Limpia el formulario
            document.getElementById('btnGuardar').disabled = true;
        }
    })
    .catch(error => {
        console.error('Error en la solicitud:', error);
        mostrarAlerta('Ocurrió un error al enviar la solicitud.', 'error');
    });
});
document.getElementById('editForm').addEventListener('submit', function (e) { 
    e.preventDefault(); // Evita que se recargue la página

    const id = document.getElementById('edit_id').value;
    const nombre = document.getElementById('edit_nombre').value.trim();

    fetch(`<?= site_url('cobertura/editar/') ?>${id}`, {
        method: 'PUT',
        headers: {
            'Content-Type': 'application/json'
        },
        body: JSON.stringify({ nombre_cobertura: nombre })
    })
    .then(async response => {
        const result = await response.json();

        if (response.ok) {
            mostrarAlerta('Cobertura actualizada exitosamente', 'success');
            closeModal('editModal');
            cargarCoberturas();
        } else {
            // Muestra el mensaje específico que devuelve el backend, si existe
            const mensajeError = result?.error || 'Error al actualizar cobertura';
            closeModal('editModal');
            mostrarAlerta(mensajeError, 'error');
        }
    })
    .catch(error => {
        closeModal('editModal');
        mostrarAlerta('Error al actualizar: ' + error.message, 'error');
    });
});

function mostrarAlerta(mensaje, tipo) {
    const alertBox = document.getElementById('alert-message');
    const alertText = document.getElementById('alert-text');
    const closeBtn = document.getElementById('close-alert');

    alertText.innerText = mensaje;

    // Remover clases previas y mostrar la nueva alerta
    alertBox.classList.remove('hidden', 'success', 'error');
    alertBox.classList.add(tipo === 'success' ? 'success' : 'error');

    // Mostrar el botón de cerrar solo si es error
    closeBtn.style.display = tipo === 'error' ? 'inline' : 'none';

    // Mostrar la alerta
    alertBox.style.display = 'block';

    // Si es éxito, ocultar la alerta después de 10 segundos
    if (tipo === 'success') {
        setTimeout(() => {
            alertBox.classList.add('hidden');
            alertBox.style.display = 'none';
        }, 10000);
    }

    // Cierre manual
    closeBtn.onclick = function () {
        alertBox.classList.add('hidden');
        alertBox.style.display = 'none';
    };
}
</script>
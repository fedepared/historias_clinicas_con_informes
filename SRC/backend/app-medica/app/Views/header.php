<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Diana Estrin</title>
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=DM+Sans&display=swap" rel="stylesheet">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.5.1/jspdf.umd.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/html2canvas/1.4.1/html2canvas.min.js"></script>
    <link rel="icon" href="<?= base_url('icon.png') ?>" type="image/png">

    <style>
        * {
            font-family: 'DM Sans', sans-serif;;
        }
        .header{
            background-color:  #007bbd;
            padding: 5px;
            margin: -8px;
            color: #ffffff;
            text-decoration: none;
        }
        header .container-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        .ul-menu {
            list-style: none;
            display: flex;
            gap: 18px;
            margin-right: 22px;
            font-size: 18px;
        }
        .nombre {
            margin-left: 13px;
            font-size: 25px;
        }
        .menu {
            text-decoration: none;
            color: #ffffff;
            cursor: pointer;
        }
    </style>
</head>
<body>
    <header class="header">
        <div class="container-header">
            <h1 class="nombre">Diana Estrin</h1>
            <nav class="nav">
                <ul class="ul-menu">
                    <li><a  class="menu" href="formulario" >Carga de Informes</a></li>
                    <li><a class="menu" href="reportes" >Reportes</a></li>
                    <li><a class="menu" href="coberturas_view" >Coberturas</a></li>
                    <li><a class="menu" href="reset" >Contraseña</a></li>
                    <li><a class="menu" href="logout" >Salir</a></li>
                </ul>
            </nav>
        </div>

    </header>
</body>
 <script>
//         function verificarSesion() {
//      fetch('<?= site_url('/usuarios/verificarSesion'); ?>')
//                 .then(response => response.json())
//                 .then(data => {
//                     if (data.status === 'expirado') {
//                         window.location.href = '<?= site_url('/error'); ?>';
//                     }
//                 })
//                 .catch(err => {
//                     console.error('Error al verificar sesión:', err);
//                     // Redirige en caso de error en la petición
//                     window.location.href = '<?= site_url('/error'); ?>';
//                 });
// }

// setInterval(verificarSesion, 5000);
// Función para verificar la expiración
function checkSessionExpiration() {
    const expiration = localStorage.getItem('expiracion');
    const currentTime = Math.floor(Date.now() / 1000); 
    
    
    if (expiration && currentTime > expiration) {
        localStorage.clear();
        window.location.href = '<?= site_url('/error'); ?>'; 
    }
}

setInterval(checkSessionExpiration, 10000);

checkSessionExpiration();
</script>
</html>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Acceso no autorizado</title>
    <meta http-equiv="refresh" content="3;url=<?php echo base_url('login'); ?>">
    <style>
        body {
            background-color: #f8f9fa;
            font-family: Arial, sans-serif;
            text-align: center;
            padding-top: 100px;
        }

        .card {
            background-color: white;
            display: inline-block;
            padding: 30px;
            border-radius: 10px;
            box-shadow: 0px 0px 10px #ccc;
        }

        h1 {
            color: #dc3545;
        }

        p {
            margin-top: 10px;
        }

        .small {
            font-size: 0.9em;
            color: #777;
        }
    </style>
</head>
<body>
    <div class="card">
        <h1>⚠️ Acceso no autorizado</h1>
        <p>No tienes permiso para acceder a esta página.</p>
        <p class="small">Serás redirigido al inicio de sesión en 3 segundos...</p>
    </div>
</body>
<script>
    
</script>
</html>
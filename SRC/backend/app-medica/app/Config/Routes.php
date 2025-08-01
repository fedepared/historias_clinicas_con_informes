<?php

use CodeIgniter\Router\RouteCollection;

/**
 * @var RouteCollection $routes
 */
// rutas view
$routes->get('/', 'Home::index'); // Ruta por defecto
$routes->get('/login', 'Home::index'); // Ruta por defecto
$routes->get('/formulario', 'Home::formulario');
$routes->get('/coberturas_view', 'Home::coberturas');
$routes->get('/reportes', 'Home::reportes');
$routes->get('/reset', 'Home::resetpass');
$routes->get('/error', 'Home::error');
// fin rutas view

$routes->group('', ['filter' => 'cors'], static function (RouteCollection $routes): void {

    // --- Rutas de Autenticación ---
    $routes->post('login', 'Usuarios::login');
    $routes->options('login', static function () {
        return service('response')->setStatusCode(204);
    });

    $routes->get('usuarios/verificarSesion', 'Usuarios::verificarSesion');
    $routes->options('usuarios/verificarSesion', static function () {
        return service('response')->setStatusCode(204);
    });

    $routes->post('logout', 'Usuarios::logout'); 
    $routes->options('logout', static function () {
        return service('response')->setStatusCode(204);
    });

    $routes->put('cambio', 'Usuarios::cambiarPassword');
    $routes->options('cambio', static function () {
        return service('response')->setStatusCode(204);
    });

    $routes->post('solicitar-cambio-password', 'Usuarios::solicitarCambioPassword');
    $routes->options('solicitar-cambio-password', static function () {
        return service('response')->setStatusCode(204);
    });

    $routes->post('verificar-codigo-cambio', 'Usuarios::verificarYActualizarPassword');
    $routes->options('verificar-codigo-cambio', static function () {
        return service('response')->setStatusCode(204);
    });

    // --- Rutas de Gestión de Usuarios ---
    $routes->get('usuarios', 'Usuarios::getUsuarios');
    $routes->options('usuarios', static function () {
        return service('response')->setStatusCode(204);
    });

    $routes->get('usuario/(:num)', 'Usuarios::getByIdUsuarios/$1');
    $routes->options('usuario/(:num)', static function () {
        return service('response')->setStatusCode(204);
    });

    $routes->post('usuario/alta', 'Usuarios::postUsuarios');
    $routes->options('usuario/alta', static function () {
        return service('response')->setStatusCode(204);
    });

    $routes->put('usuario/editar/(:num)', 'Usuarios::updateUsuarios/$1'); // Cambiado de GET a PUT
    $routes->options('usuario/editar/(:num)', static function () {
        return service('response')->setStatusCode(204);
    });

    $routes->delete('usuario/borrar/(:num)', 'Usuarios::deleteUsuarios/$1'); // Cambiado de GET a DELETE
    $routes->options('usuario/borrar/(:num)', static function () {
        return service('response')->setStatusCode(204);
    });


    // --- Rutas de Informes ---
    $routes->get('informes', 'Informes::getInformes');
    $routes->options('informes', static function () {
        return service('response')->setStatusCode(204);
    });

    $routes->get('informe/(:num)', 'Informes::getByIdInformes/$1');
    $routes->options('informe/(:num)', static function () {
        return service('response')->setStatusCode(204);
    });

    $routes->post('informe/alta', 'Informes::postInforme');
    $routes->options('informe/alta', static function () {
        return service('response')->setStatusCode(204);
    });

    $routes->put('informe/editar/(:num)', 'Informes::updateInforme/$1');
    $routes->options('informe/editar/(:num)', static function () {
        return service('response')->setStatusCode(204);
    });

    $routes->delete('informe/borrar/(:num)', 'Informes::deleteInforme/$1'); // Cambiado de GET a DELETE
    $routes->options('informe/borrar/(:num)', static function () {
        return service('response')->setStatusCode(204);
    });

    $routes->post('reenviar-informe/(:num)', 'Informes::reenviarInformePorId/$1');
    $routes->options('reenviar-informe/(:num)', static function () {
        return service('response')->setStatusCode(204);
    });

    $routes->get('mai', 'Informes::enviarCorreoPrueba');
    $routes->options('mai', static function () {
        return service('response')->setStatusCode(204);
    });

    $routes->get('descargar-archivo', 'Informes::descargarInformeCompleto');
    $routes->options('descargar-archivo', static function () {
        return service('response')->setStatusCode(204);
    });

    $routes->get('informe/imagenes/(:num)', 'Informes::getImagenesByInformeId/$1');
    $routes->options('informe/imagenes/(:num)', static function () {
        return service('response')->setStatusCode(204);
    });

    $routes->post('informe/imagenes/update/(:num)', 'Informes::updateInformeImages/$1');
    $routes->options('informe/imagenes/update/(:num)', static function () {
        return service('response')->setStatusCode(204);
    });

    $routes->get('informes/por-cobertura/(:segment)', 'Informes::getInformesByCobertura/$1');
    $routes->options('informes/por-cobertura/(:segment)', static function () {
        return service('response')->setStatusCode(204);
    });

    $routes->get('informes/descargar-pdfs', 'Informes::downloadPdfsByDateRangeAndCoverage');
    $routes->options('informes/descargar-pdfs', static function () {
        return service('response')->setStatusCode(204);
    });
    
    $routes->get('informes-paginado', 'Informes::getInformesPaginado');
    $routes->options('informes-paginado', static function () {
        return service('response')->setStatusCode(204);
    });


    // --- Rutas de Coberturas ---
    $routes->get('coberturas', 'Coberturas::getCoberturas');
    $routes->options('coberturas', static function () {
        return service('response')->setStatusCode(204);
    });

    $routes->get('cobertura/(:num)', 'Coberturas::getByIdCoberturas/$1');
    $routes->options('cobertura/(:num)', static function () {
        return service('response')->setStatusCode(204);
    });

    $routes->post('cobertura/alta', 'Coberturas::postCobertura');
    $routes->options('cobertura/alta', static function () {
        return service('response')->setStatusCode(204);
    });

    $routes->put('cobertura/editar/(:num)', 'Coberturas::updateCobertura/$1');
    $routes->options('cobertura/editar/(:num)', static function () {
        return service('response')->setStatusCode(204);
    });

    $routes->delete('cobertura/borrar/(:num)', 'Coberturas::deleteCobertura/$1');
    $routes->options('cobertura/borrar/(:num)', static function () {
        return service('response')->setStatusCode(204);
    });

    /*preparaciones*/

    $routes->get('/preparacion/(:num)', 'Preparaciones::getByIdPreparaciones/$1');
       $routes->options('/preparacion/(:num)', static function () {
        return service('response')->setStatusCode(204);
    });
    $routes->get('/preparaciones/tipo/(:segment)', 'Preparaciones::getByTipoPreparacion/$1');
       $routes->options('/preparaciones/tipo/(:segment)', static function () {
        return service('response')->setStatusCode(204);
    });
    $routes->put('/preparacion/(:num)', 'Preparaciones::updateTexto/$1');
       $routes->options('/preparacion/(:num)', static function () {
        return service('response')->setStatusCode(204);
    });

    $routes->get('preparaciones/generate-preparacion-pdf', 'Preparaciones::generatePreparacionPDF');
 
    $routes->options('preparaciones/generate-preparacion-pdf', static function () {
        return service('response')->setStatusCode(204);
    });


$routes->post('email/send-cuestionario-word', 'Preparaciones::sendCuestionarioWordEmail');
    $routes->options('email/send-cuestionario-word', static function () {
        return service('response')->setStatusCode(204);
    }       
);

});
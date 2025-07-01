<?php

namespace Config;

use CodeIgniter\Config\BaseConfig;
use CodeIgniter\Filters\CSRF;
use CodeIgniter\Filters\DebugToolbar;
use CodeIgniter\Filters\Honeypot;
use CodeIgniter\Filters\InvalidChars;
use CodeIgniter\Filters\SecureHeaders;
use App\Filters\AuthGuard;
use App\Filters\CORS;

class Filters extends BaseConfig
{
    public array $aliases = [
        'csrf'          => CSRF::class,
        'toolbar'       => DebugToolbar::class,
        'honeypot'      => Honeypot::class,
        'invalidchars'  => InvalidChars::class,
        'secureheaders' => SecureHeaders::class,
        'auth'          => AuthGuard::class, 
        'cors'          => CORS::class,
        
    ];

    public array $globals = [
        'before' => [
            // 'honeypot',
            // 'csrf',
            // 'invalidchars',
            'cors'
        ],
        'after' => [
            'toolbar',
            
            // 'honeypot',
            // 'secureheaders',
        ],
    ];

    public array $methods = [];

    public array $filters = [
      
        'auth' => [
            'before' => [
                'usuarios*',
                'usuario/*',
                'informes*',
                'informe/*',
                'coberturas*',
                'cobertura/*',
                'descargar-archivo',
                'formulario',
                'reportes',
                'coberturas_view',
            ],
            'except' => [
                'login',
               
            ],
        ],
    ];
}
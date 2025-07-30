// src/app/app.config.ts
import { ApplicationConfig, provideZoneChangeDetection } from '@angular/core';
import { provideRouter } from '@angular/router';
import { provideAnimations } from '@angular/platform-browser/animations';
import { routes } from './app.routes';
import { providePrimeNG } from 'primeng/config';
import {
  provideHttpClient,
  withInterceptorsFromDi, // <--- ¡IMPORTA ESTO! Para interceptores de CLASE
  HTTP_INTERCEPTORS // <--- ¡IMPORTA ESTO! Para registrar interceptores de CLASE
} from '@angular/common/http';
import { es } from 'primelocale/es.json';
import { provideAnimationsAsync } from '@angular/platform-browser/animations/async';
import MyPreset from './mypreset';

import { AuthInterceptor } from './interceptor/auth.interceptor'; // <--- ¡Importa tu CLASE AuthInterceptor!

export const appConfig: ApplicationConfig = {
  providers: [
    provideZoneChangeDetection({ eventCoalescing: true }),
    provideRouter(routes),
    provideAnimations(),
    // --- ESTA ES LA SECCIÓN CLAVE PARA EL HTTPCLIENT E INTERCEPTORES ---
    provideHttpClient(
      withInterceptorsFromDi() // Habilita el soporte para interceptores de clase DI
    ),
    {
      // Registra tu interceptor de CLASE
      provide: HTTP_INTERCEPTORS,
      useClass: AuthInterceptor,
      multi: true // Permite que otros interceptores también puedan ser añadidos
    },
    // --- FIN DE LA SECCIÓN HTTPCLIENT ---
    provideAnimationsAsync(),
    providePrimeNG({
      ripple: true,
      translation: es,
      theme: {
        preset: MyPreset,
        options: {
          darkModeSelector: '.my-app-lithg'
        }
      }
    })
  ]
};
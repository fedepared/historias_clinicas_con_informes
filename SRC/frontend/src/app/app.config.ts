import { ApplicationConfig, provideZoneChangeDetection } from '@angular/core';
import { provideRouter } from '@angular/router';
import { provideAnimations } from '@angular/platform-browser/animations';
import { routes } from './app.routes';
import { providePrimeNG } from 'primeng/config';
import { provideHttpClient } from '@angular/common/http';
import { es } from 'primelocale/es.json';
import { provideAnimationsAsync } from '@angular/platform-browser/animations/async';
import Aura  from '@primeng/themes/aura';

export const appConfig: ApplicationConfig = {
  providers: [
    provideZoneChangeDetection({ eventCoalescing: true }),
    provideRouter(routes),
    provideAnimations(),
    provideHttpClient(), // Generalmente necesario en apps modernas
    provideAnimationsAsync(),
    providePrimeNG({
      ripple: true, // Esto es opcional, activa el efecto ripple en botones
      translation: es, // ¡Aquí es donde se aplica la traducción!
      theme:{
        preset: Aura,
        options: {
          darkModeSelector: '.my-app-lithg'
      }
      }
    })
  ]
};

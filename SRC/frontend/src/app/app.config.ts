import { ApplicationConfig, provideZoneChangeDetection } from '@angular/core';
import { provideRouter } from '@angular/router';
import { provideAnimations } from '@angular/platform-browser/animations';
import { routes } from './app.routes';
import { providePrimeNG } from 'primeng/config';
import { provideHttpClient } from '@angular/common/http';
import { es } from 'primelocale/es.json';
import { provideAnimationsAsync } from '@angular/platform-browser/animations/async';
import Aura  from '@primeng/themes/aura';
import MyPreset from './mypreset';

export const appConfig: ApplicationConfig = {
  providers: [
    provideZoneChangeDetection({ eventCoalescing: true }),
    provideRouter(routes),
    provideAnimations(),
    provideHttpClient(), 
    provideAnimationsAsync(),
    providePrimeNG({
      ripple: true,
      translation: es, 
      theme:{
        preset: MyPreset,
        options: {
          darkModeSelector: '.my-app-lithg'
      }
      }
    })
  ]
};

import { Injectable } from '@angular/core';
import { Router } from '@angular/router';
import { interval, Subscription } from 'rxjs';
import { GenericService } from './generic.service';

@Injectable({
  providedIn: 'root'
})
export class AuthService {

  private checkInterval$: Subscription | null = null;

  constructor(private router: Router, private genericService: GenericService) {
    this.startAuthWatcher();
    console.log('Entro')


  }

  startAuthWatcher(): void {

    if (this.checkInterval$) return;

    this.checkInterval$ = interval(3000).subscribe(() => {
      const usuarioToken = localStorage.getItem('usuarioToken');
      const expiracionStr = localStorage.getItem('expiracion');
      const expiracion = Number(expiracionStr);
      const ahora = Math.floor(Date.now() / 1000);

      if (!usuarioToken) {
        console.log('AuthGuard: No se encontró usuarioToken. Redirigiendo a /error.');
        this.stopAuthWatcher();
        this.clearAndRedirect();
        return false;
      }

      if (!expiracionStr || isNaN(expiracion)) {
        console.log('AuthGuard: Expiración no encontrada o no es un número. Redirigiendo a /error.');
        this.stopAuthWatcher();
        this.clearAndRedirect();
        return false;
      }

      if (ahora > expiracion) {
        console.log('AuthGuard: Token expirado. Redirigiendo a /error.');
        this.stopAuthWatcher();
        this.clearAndRedirect();
        return false;
      }
      console.log('AuthGuard: Autenticado y token válido. Acceso permitido.');
      return true;
    });
  }

  stopAuthWatcher(): void {
    this.checkInterval$?.unsubscribe();
    this.checkInterval$ = null;
  }
  private clearAndRedirect(): void {
    localStorage.clear(); // Limpia cualquier token inválido o expirado
    this.router.navigate(['/error']);
  }
}

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


  }

  startAuthWatcher(): void {

    if (this.checkInterval$) return;

    this.checkInterval$ = interval(3000).subscribe(() => {
      const usuarioToken = localStorage.getItem('usuarioToken');
      const expiracionStr = localStorage.getItem('expiracion');
      const expiracion = Number(expiracionStr);
      const ahora = Math.floor(Date.now() / 1000);

      if (!usuarioToken || !expiracionStr) {
        this.stopAuthWatcher();
        this.clearAndRedirect();
        return;
      }

      if (isNaN(expiracion) || ahora >= expiracion) {
        console.log('Token expirado o inválido. Cerrando sesión.');
        this.stopAuthWatcher();
        this.clearAndRedirect();
      }
      console.log('AuthGuard: Autenticado y token válido. Acceso permitido.');
     
    });
  }

  stopAuthWatcher(): void {
    this.checkInterval$?.unsubscribe();
    this.checkInterval$ = null;
  }
  private clearAndRedirect(): void {
    localStorage.clear(); 
    this.router.navigate(['/']);
  }
}

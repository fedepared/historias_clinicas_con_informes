import { Injectable } from '@angular/core';
import { CanActivate, Router, ActivatedRouteSnapshot, RouterStateSnapshot, UrlTree } from '@angular/router'; // Añadimos ActivatedRouteSnapshot, RouterStateSnapshot, UrlTree
import { Observable } from 'rxjs'; // Añadimos Observable si quieres mantener la firma completa de CanActivate

@Injectable({
  providedIn: 'root',
})
export class AuthGuard implements CanActivate {
  constructor(private router: Router) {
    console.log('AuthGuard: Constructor llamado');
  }

  canActivate(
    route: ActivatedRouteSnapshot,
    state: RouterStateSnapshot
  ): boolean | UrlTree | Observable<boolean | UrlTree> | Promise<boolean | UrlTree> {
    const usuarioToken = localStorage.getItem('usuarioToken');
    const expiracionStr = localStorage.getItem('expiracion');
    const expiracion = Number(expiracionStr);
    const ahora = Math.floor(Date.now() / 1000);

    console.log('ENTRO al AuthGuard - Verificando autenticación...');
    console.log('UsuarioToken (existencia):', !!usuarioToken);
    console.log('Expiración (raw):', expiracionStr);
    console.log('Expiración (parsed):', isNaN(expiracion) ? 'NaN' : new Date(expiracion * 1000));
    console.log('Ahora:', new Date(ahora * 1000));

    // Validación principal
    if (!usuarioToken) {
      console.log('AuthGuard: No se encontró usuarioToken. Redirigiendo a /error.');
      this.clearAndRedirect();
      return false;
    }

    if (!expiracionStr || isNaN(expiracion)) {
      console.log('AuthGuard: Expiración no encontrada o no es un número. Redirigiendo a /error.');
      this.clearAndRedirect();
      return false;
    }

    if (ahora > expiracion) {
      console.log('AuthGuard: Token expirado. Redirigiendo a /error.');
      this.clearAndRedirect();
      return false;
    }

    console.log('AuthGuard: Autenticado y token válido. Acceso permitido.');
    return true;
  }

  private clearAndRedirect(): void {
    localStorage.clear(); // Limpia cualquier token inválido o expirado
    this.router.navigate(['/error']);
  }
}
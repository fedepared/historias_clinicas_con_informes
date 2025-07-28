import { Injectable, OnInit } from '@angular/core';
import { CanActivate, Router } from '@angular/router';
import { GenericService } from '../services/generic.service';

@Injectable({
  providedIn: 'root',
  
})
export class AuthGuard implements CanActivate {

  constructor(private router: Router, private genericService: GenericService) { 
    setInterval(this.canActivate, 3000);
  }
   

  canActivate(): boolean {
    const isLoggedIn = !!localStorage.getItem('usuarioToken');
    const expiracion = Number(localStorage.getItem('expiracion'));
    const ahora = Math.floor(Date.now() / 1000);


    
    if (!isLoggedIn || !expiracion || ahora > expiracion) {
      console.log('ENTRO')
      localStorage.clear(); 
      this.router.navigate(['/error']);
      return false;
    }

    return true;
  }
  
}
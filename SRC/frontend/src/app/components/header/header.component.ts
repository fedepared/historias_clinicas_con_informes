import { Component, OnInit } from '@angular/core';
import { GenericService } from '../../services/generic.service';
import { Router } from '@angular/router';
import { AuthGuard } from '../../guards/auth.guard';
import { AuthService } from '../../services/auth.service';

@Component({
  selector: 'app-header',
  imports: [],
  templateUrl: './header.component.html',
  styleUrl: './header.component.css'
})
export class HeaderComponent {

  constructor(private router: Router, private authService: AuthService) {
    this.authService.startAuthWatcher();

  }

  /*  salir(){
     this.genericService.post('logout').subscribe({
        next: (response) => {
 
             localStorage.clear();
             this.router.navigate(['/']); 
         },
         error: (err) => {
             console.error('Error cerrar la session', err);
             
         }
     })
 
   }  */

  salir() {
    localStorage.removeItem('usuarioToken');
    localStorage.removeItem('expiracion');
    this.router.navigate(['/']);
  }

}

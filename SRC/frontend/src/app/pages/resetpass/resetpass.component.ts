import { Component } from '@angular/core';
import { HeaderComponent } from "../../components/header/header.component";
import { PasswordModule } from 'primeng/password';
import { MessageService } from 'primeng/api';
import { CommonModule } from '@angular/common';
import { FormsModule } from '@angular/forms';
import { ButtonModule } from 'primeng/button';
import { ToastModule } from 'primeng/toast';
import { GenericService } from '../../services/generic.service';
@Component({
  selector: 'app-resetpass',
  imports: [HeaderComponent, PasswordModule, CommonModule, FormsModule, ButtonModule, ToastModule],
  templateUrl: './resetpass.component.html',
  styleUrl: './resetpass.component.css',
  providers: [MessageService]
})
export class ResetpassComponent {

  pass1!: string;
  pass2!: string;

  constructor(public messageService: MessageService, private genericService: GenericService) { }

  validar() {
    if (this.pass1 != this.pass2) {
      this.messageService.add({
        severity: 'error',
        summary: 'Error',
        detail: 'Las contraseñas deben ser iguales',
        sticky: true
      });


    }
    this.putContraseña();
  }
  putContraseña() {

    const body = {
      password_nuevo: this.pass1,
      password_confirmar: this.pass2
    }
    this.genericService.put('cambio', body).subscribe({
      next: (response) => {
        if (response.status === 'success') {
          this.messageService.add({
            severity: 'success',
            summary: 'Éxito',
            detail: response.message,
            sticky: true
          });
        } else {
          this.messageService.add({
            severity: 'error',
            summary: 'Error',
            detail: response.message,
            sticky: true
          });
        }



      },
      error: (err) => {
        console.error('Error al cambiar la contraseña:', err);
        this.messageService.add({
            severity: 'error',
            summary: 'Error',
            detail: err.error.message,
            sticky: true
          });

      }
    })

  }
}

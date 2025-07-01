import { Component, OnInit } from '@angular/core';
import { CardModule } from 'primeng/card';
import { InputTextModule } from 'primeng/inputtext';
import { PasswordModule } from 'primeng/password';
import { ButtonModule } from 'primeng/button';
import { RippleModule } from 'primeng/ripple';
import { CommonModule } from '@angular/common';
import { FormsModule } from '@angular/forms';
import { ConfirmationService, MessageService } from 'primeng/api';
import { ConfirmDialog } from 'primeng/confirmdialog';
import { ToastModule } from 'primeng/toast';
import { GenericService } from '../../services/generic.service';
import { IResponse } from '../../Interfaces/iresponse';
import { Router } from '@angular/router';
@Component({
  selector: 'app-login',
  standalone: true,
  imports: [
    CommonModule,
    FormsModule, 
    CardModule,
    InputTextModule,
    PasswordModule,
    ButtonModule,
    RippleModule,ConfirmDialog,ToastModule
  ],
  templateUrl: './login.component.html',
  styleUrl: './login.component.css',
  providers: [ConfirmationService, MessageService]
})
export class LoginComponent implements OnInit {
  username: string = '';
  password: string = '';
  isLoginButtonDisabled: boolean = true;
  constructor(private confirmationService: ConfirmationService,private router: Router, private messageService: MessageService,private genericService: GenericService) {}
  ngOnInit(): void {
    // Initial check for button disable state
    this.checkLoginButtonStatus();
  }
  checkLoginButtonStatus(): void {
    this.isLoginButtonDisabled = !this.username || !this.password;
  }
  confirm() {
    this.confirmationService.confirm({
        header: 'Are you sure?',
        message: 'Please confirm to proceed.',
        accept: () => {
            this.messageService.add({ severity: 'info', summary: 'Confirmed', detail: 'You have accepted' });
        },
        reject: () => {
            this.messageService.add({ severity: 'info', summary: 'Rejected', detail: 'You have rejected' });
        },
    });
 }
 login(): void {
  const loginPayload = {
    nombre_usuario: this.username,
    pass: this.password
  };
  this.genericService.post('login', loginPayload).subscribe({
    next: (response: IResponse<any>) => {
   
      if (response.status === 'success') { 
        this.messageService.add({
          severity: 'success',
          summary: 'Éxito',
          detail: response.message || 'Inicio de sesión exitoso',
          life: 3000
        });
       
        this.router.navigate(['/formulario']); 
        
      } else {
       
        this.messageService.add({
          severity: 'error',
          summary: 'Error',
          detail: response.message || 'Error al iniciar sesión',
          life: 3000
        });
        
      }
    },
    error: (err) => {
      
      
      let errorMessage = 'Ocurrió un error inesperado al intentar iniciar sesión.';
      if (err.error && err.error.message) { 
        errorMessage = err.error.message;
      } else if (err.message && typeof err.message === 'string' && err.message.includes("Http failure response")) {
        errorMessage = "Problema de conexión con el servidor o CORS. Revise la consola del navegador.";
      } else if (err.message) {
        errorMessage = err.message;
      }
      this.messageService.add({
        severity: 'error',
        summary: 'Error',
        detail: errorMessage,
        life: 5000
      });
    },
    complete: () => {
      
    }
  });
}
}

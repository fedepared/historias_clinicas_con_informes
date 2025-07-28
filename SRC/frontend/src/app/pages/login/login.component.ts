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
import { MessageModule } from 'primeng/message';
import { InputGroupModule } from 'primeng/inputgroup';
import { InputGroupAddonModule } from 'primeng/inputgroupaddon';
import { DialogModule } from 'primeng/dialog';

@Component({
  selector: 'app-login',
  standalone: true,
  imports: [
    CommonModule,
    FormsModule,
    CardModule,
    InputTextModule,
    PasswordModule, InputGroupModule,
    ButtonModule, InputGroupAddonModule,
    RippleModule, ConfirmDialog, ToastModule, MessageModule, DialogModule
  ],
  templateUrl: './login.component.html',
  styleUrl: './login.component.css',
  providers: [ConfirmationService, MessageService]
})
export class LoginComponent implements OnInit {
  username: string = '';
  password: string = '';
  isLoginButtonDisabled: boolean = true;
  enviando: boolean = false;
  visible: boolean = false;
  gmail: string = '';

  constructor(private confirmationService: ConfirmationService, private router: Router, private messageService: MessageService, private genericService: GenericService) { }
  ngOnInit(): void {
    // Initial check for button disable state
    this.checkLoginButtonStatus();
  }
  checkLoginButtonStatus(): void {
    this.isLoginButtonDisabled = !this.username || !this.password;
  }
  confirm() {
    this.confirmationService.confirm({
      header: 'Cambio de contraseña',
      message: '¿Estás seguro que deseas restablecer tu contraseña?',
      closable: true,
      icon: 'pi pi-exclamation-triangle',
      rejectButtonProps: {
        label: 'Cancelar',
        severity: 'danger',

      },
      acceptButtonProps: {
        label: 'Continuar',
        severity: 'info'

      },
      accept: () => {
        this.visible = true;
      },
    });
  }

  login(): void {
    this.enviando = true;
    const loginPayload = {
      nombre_usuario: this.username,
      pass: this.password
    };
    this.genericService.post('login', loginPayload).subscribe({
      next: (response: IResponse<any>) => {

        localStorage.setItem('usuarioToken', 'activo');
        localStorage.setItem('expiracion', response.data.expiracion.toString());
        if (response.status === 'success') {
          this.messageService.add({
            severity: 'success',
            summary: 'Éxito',
            detail: response.message ,
            sticky: true
          });
          if(response.data.pidio_cambio === '1'){
            this.router.navigate(['/reset']);
          }else {this.router.navigate(['/formulario']);}

        } else {

          this.messageService.add({
            severity: 'error',
            summary: 'Error',
            detail: response.message || 'Error al iniciar sesión',
            sticky: true
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
          sticky: true
        });
        this.enviando = false;
      },
      complete: () => {
        this.enviando = false;
      }
    });
  }
  cambioPass() {
    const body = {
      mail: this.gmail,
    }
    this.genericService.post('solicitar-cambio-password', body).subscribe({
      next: (response: IResponse<any>) => {
      
        this.messageService.add({
          severity: 'success',
          summary: 'Éxito',
          detail: response.message,
          sticky: true
        });
        this.visible = false;
        this.gmail = '';
      },
      error: (err) => {
       
        this.messageService.add({
          severity: 'error',
          summary: 'Error',
          detail: err.error.messages.error,
          sticky: true
        });
      }
    })
  }
}

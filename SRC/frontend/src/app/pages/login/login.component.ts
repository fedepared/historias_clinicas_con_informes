import { Component } from '@angular/core';
import { CardModule } from 'primeng/card';
import { InputTextModule } from 'primeng/inputtext';
import { PasswordModule } from 'primeng/password';
import { ButtonModule } from 'primeng/button';
import { RippleModule } from 'primeng/ripple';
import { CommonModule } from '@angular/common';
import { FormsModule } from '@angular/forms';
@Component({
  selector: 'app-login',
  standalone: true,
  imports: [
    CommonModule,
    FormsModule, // Permite el two-way data binding [(ngModel)]
    CardModule,
    InputTextModule,
    PasswordModule,
    ButtonModule,
    RippleModule
  ],
  templateUrl: './login.component.html',
  styleUrl: './login.component.css'
})
export class LoginComponent {
  username: string = '';
  password: string = '';

  constructor() { }

  onLogin(): void {
    console.log('Usuario:', this.username);
    console.log('Contraseña:', this.password);
    // Aquí puedes añadir tu lógica de autenticación
    alert(`Intentando iniciar sesión con ${this.username}`);
  }

  onForgotPassword(): void {
    alert('Función de "Olvidaste tu contraseña?"');
    // Aquí podrías navegar a otra ruta o abrir un diálogo
  }
}

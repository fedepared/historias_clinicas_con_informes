import { Component } from '@angular/core';
import { RouterOutlet } from '@angular/router';
import { UsuarioService } from './services/usuario.service';
import { CommonModule } from '@angular/common';
@Component({
  selector: 'app-root',
  imports: [RouterOutlet,CommonModule],
  templateUrl: './app.component.html',
  styleUrl: './app.component.css'
})
export class AppComponent {

  usuarios: any[] = [];

  constructor(private usuarioService: UsuarioService) {}

 ngOnInit() {
  this.usuarioService.getUsuarios().subscribe(data => {
    this.usuarios = [data]; // lo metemos en un array para poder usar *ngFor
  });
}



}

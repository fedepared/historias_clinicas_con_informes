import { HttpClient } from '@angular/common/http';
import { Injectable } from '@angular/core';

@Injectable({
  providedIn: 'root'
})
export class UsuarioService {



    private apiUrl = 'http://localhost/phpAngular/backend/public/index.php/api/prueba';

  constructor(private http: HttpClient) {}

  getUsuarios() {
    return this.http.get<any[]>(this.apiUrl);
  }
}

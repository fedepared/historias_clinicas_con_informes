import { Routes } from '@angular/router';
import { FormularioComponent } from './components/formulario/formulario.component';
import { HeaderComponent } from './components/header/header.component';
import { LoginComponent } from './components/login/login.component';

export const routes: Routes = [
    { path: 'login', component: LoginComponent },
    { path: 'header', component: HeaderComponent },
    { path: 'formulario', component: FormularioComponent },
];

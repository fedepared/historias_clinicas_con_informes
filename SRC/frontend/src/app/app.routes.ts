import { Routes } from '@angular/router';


import { FormularioComponent } from './pages/formulario/formulario.component';
import { LoginComponent } from './pages/login/login.component';
import { ReportesComponent } from './pages/reportes/reportes.component';
import { CoberturasComponent } from './pages/coberturas/coberturas.component';
import { ResetpassComponent } from './pages/resetpass/resetpass.component';
import { PacientesComponent } from './pages/pacientes/pacientes.component';
import { ErrorComponent } from './pages/error/error.component';


export const routes: Routes = [
    { path: '', component: LoginComponent },
    { path: 'formulario', component: FormularioComponent },
    { path: 'reportes', component: ReportesComponent },
    { path: 'coberturas', component: CoberturasComponent},
    { path: 'reset', component: ResetpassComponent},
    { path: 'pacientes', component: PacientesComponent},
    { path: 'error', component: ErrorComponent},
];

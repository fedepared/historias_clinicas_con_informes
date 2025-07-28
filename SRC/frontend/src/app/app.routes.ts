import { Routes } from '@angular/router';


import { FormularioComponent } from './pages/formulario/formulario.component';
import { LoginComponent } from './pages/login/login.component';
import { ReportesComponent } from './pages/reportes/reportes.component';
import { CoberturasComponent } from './pages/coberturas/coberturas.component';
import { ResetpassComponent } from './pages/resetpass/resetpass.component';
import { PacientesComponent } from './pages/pacientes/pacientes.component';
import { ErrorComponent } from './pages/error/error.component';
import { AuthGuard } from './guards/auth.guard';


export const routes: Routes = [
    { path: '', component: LoginComponent },
    { path: 'formulario', component: FormularioComponent , canActivate: [AuthGuard] },
    { path: 'reportes', component: ReportesComponent, canActivate: [AuthGuard]  },
    { path: 'coberturas', component: CoberturasComponent, canActivate: [AuthGuard] },
    { path: 'reset', component: ResetpassComponent, canActivate: [AuthGuard] },
    { path: 'pacientes', component: PacientesComponent, canActivate: [AuthGuard] },
    { path: 'error', component: ErrorComponent},
];

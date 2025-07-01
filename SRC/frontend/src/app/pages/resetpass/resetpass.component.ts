import { Component } from '@angular/core';
import { HeaderComponent } from "../../components/header/header.component";
import { PasswordModule } from 'primeng/password';
@Component({
  selector: 'app-resetpass',
  imports: [HeaderComponent,PasswordModule],
  templateUrl: './resetpass.component.html',
  styleUrl: './resetpass.component.css'
})
export class ResetpassComponent {

}

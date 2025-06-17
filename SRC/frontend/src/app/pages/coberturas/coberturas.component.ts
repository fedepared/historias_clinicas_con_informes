import { Component, OnInit } from '@angular/core';
import { HeaderComponent } from "../../components/header/header.component";
import { TableModule } from 'primeng/table';
import { CommonModule } from '@angular/common';
import { ButtonModule } from 'primeng/button';
@Component({
  selector: 'app-coberturas',
  imports: [HeaderComponent,TableModule,CommonModule,ButtonModule],
  templateUrl: './coberturas.component.html',
  styleUrl: './coberturas.component.css'
})
export class CoberturasComponent implements OnInit {
  coberturas!: any[];
  ngOnInit(): void {
    this.coberturas = [
      {name: 'SIN COBERTURA',code: 1},
      {name: 'IOSFA',code: 2},
      {name: 'OSDE',code: 3},
      {name: 'ALALALA',code: 4},
    ]
  }
}

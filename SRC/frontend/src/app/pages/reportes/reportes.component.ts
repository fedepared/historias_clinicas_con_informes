import { Component, OnInit } from '@angular/core';
import { HeaderComponent } from "../../components/header/header.component";
import { DatePickerModule } from 'primeng/datepicker';
import { CommonModule } from '@angular/common';
import { FormsModule } from '@angular/forms';
import { TableLazyLoadEvent, TableModule } from 'primeng/table';
import { ButtonModule } from 'primeng/button';
import { GenericService } from '../../services/generic.service';
import { InputTextModule } from 'primeng/inputtext';
import { LazyLoadEvent } from 'primeng/api';
import { coberturas } from '../../Interfaces/coberturas';
import { SelectModule } from 'primeng/select';
import { DynamicDialogModule } from 'primeng/dynamicdialog';
import { TooltipModule } from 'primeng/tooltip';
@Component({
  selector: 'app-reportes',
  imports: [HeaderComponent, DatePickerModule, CommonModule, TableModule, ButtonModule, InputTextModule,TooltipModule,
    FormsModule,SelectModule,DynamicDialogModule],
  templateUrl: './reportes.component.html',
  styleUrl: './reportes.component.css'
})
export class ReportesComponent implements OnInit {
  reporteslist!: any[];
  totalRecords: number = 0;
  rows: number = 10;
  first: number = 0; 
  loading: boolean = false; 

  
  nombreFilter: string = '';
  coberturaFilter: string = '';
  fechaDesdeFilter: Date | undefined;
  fechaHastaFilter: Date | undefined;
  coberturas: coberturas[] = [];

  constructor(private genericService: GenericService) { }
  ngOnInit(): void {
    this.loadReports({ first: 0, rows: this.rows });
    this.getCoberturas();
  }

  loadReports(event: TableLazyLoadEvent): void {
    this.loading = true; 

    
    this.first = event.first || 0; 
    this.rows = event.rows || this.rows; 
    const page = (this.first / this.rows) + 1; 

    
    const params: { [key: string]: any } = {
      page: page,
      per_page: this.rows
    };

    if (this.nombreFilter) {
      params['nombre'] = this.nombreFilter;
    }
    if (this.coberturaFilter) {
      params['cobertura'] = this.coberturaFilter;
    }
    if (this.fechaDesdeFilter) {

      params['fecha_desde'] = this.fechaDesdeFilter.toISOString().split('T')[0];
    }
    if (this.fechaHastaFilter) {
     
      params['fecha_hasta'] = this.fechaHastaFilter.toISOString().split('T')[0];
    }

    
    this.genericService.getAll('informes-paginado', params).subscribe({
      next: (response: any) => {
        this.reporteslist = response.data; 
        this.totalRecords = response.meta.total_registros; 
        this.loading = false; 
      },
      error: (err) => {
        console.error('Error fetching informes paginated:', err);
        this.reporteslist = [];
        this.totalRecords = 0;
        this.loading = false; 
      }
    });
  }
  searchReports(): void {
    this.first = 0; 
    this.loadReports({ first: this.first, rows: this.rows });
  }

  reset() {
    this.nombreFilter = '';
    this.coberturaFilter = '';
    this.fechaDesdeFilter = undefined;
    this.fechaHastaFilter = undefined;
    this.loadReports({ first: 0, rows: this.rows });
  }

  getCoberturas(): void {
    this.genericService.getAll('coberturas').subscribe({
      next: (response: any) => {
        this.coberturas = response as coberturas[];

      },
      error: (err) => {
        console.error('Error fetching coberturas:', err);
        this.coberturas = [];
      }
    });
  }
  nombreCobertura(id: number){
    const idCo = this.coberturas[id] ;
    return idCo.nombre_cobertura

  }
}

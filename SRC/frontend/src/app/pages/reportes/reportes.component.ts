import { Component, OnInit } from '@angular/core';
import { HeaderComponent } from "../../components/header/header.component";
import { DatePickerModule } from 'primeng/datepicker';
import { CommonModule } from '@angular/common';
import { FormsModule } from '@angular/forms';
import { TableLazyLoadEvent, TableModule } from 'primeng/table';
import { ButtonModule } from 'primeng/button';
import { GenericService } from '../../services/generic.service';
import { InputTextModule } from 'primeng/inputtext';
import { LazyLoadEvent, MessageService } from 'primeng/api';
import { coberturas } from '../../Interfaces/coberturas';
import { SelectModule } from 'primeng/select';
import { DialogService, DynamicDialogModule, DynamicDialogRef } from 'primeng/dynamicdialog';
import { TooltipModule } from 'primeng/tooltip';
import { EditReportesComponent } from './edit-reportes/edit-reportes/edit-reportes.component';
import { ToastModule } from 'primeng/toast';
import { IResponse } from '../../Interfaces/iresponse';
import { HttpClient, HttpParams } from '@angular/common/http';
import { environment } from '../../../environments/environment';
@Component({
  selector: 'app-reportes',
  imports: [HeaderComponent, DatePickerModule, CommonModule, TableModule, ButtonModule, InputTextModule, TooltipModule,
    FormsModule, SelectModule, DynamicDialogModule, ToastModule],
  templateUrl: './reportes.component.html',
  styleUrl: './reportes.component.css',
  providers: [DialogService, MessageService]
})
export class ReportesComponent implements OnInit {
  reporteslist!: any[];
  totalRecords: number = 0;
  rows: number = 10;
  first: number = 0;
  loading: boolean = false;
  spinnerD: boolean = false;
  spinnerR: boolean = false;
  visible: boolean = false;

  nombreFilter: string = '';
  coberturaFilter: string = '';
  fechaDesdeFilter: Date | undefined;
  fechaHastaFilter: Date | undefined;
  coberturas: coberturas[] = [];
  ref: DynamicDialogRef | undefined;

  constructor(private http: HttpClient, private genericService: GenericService, public dialogService: DialogService, public messageService: MessageService) { this.getCoberturas(); }
  ngOnInit(): void {
    this.loadReports({ first: 0, rows: this.rows });

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
  searchReportsDates(): void {
    this.visible = true;
    this.first = 0;
    this.loadReports({ first: this.first, rows: this.rows });

  }

  reset() {
    this.nombreFilter = '';
    this.coberturaFilter = '';
    this.fechaDesdeFilter = undefined;
    this.fechaHastaFilter = undefined;
    this.visible = false;
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
  nombreCobertura(id: number):string {
    const cobertura = this.coberturas.find(cob => cob.id_cobertura === id);
    return cobertura ? cobertura.nombre_cobertura : '--';

  }
  abrirdialigo(id?: number) {

    this.ref = this.dialogService.open(EditReportesComponent, {
      data: { id: id },
      header: 'Editar Reporte',
      closable: true
    })
    this.ref.onClose.subscribe((data: any[]) => {
      if (data) {
        data.forEach((data) => {
          if (data.status === 'success' || data.success === true) {
            this.messageService.add({
              severity: 'success',
              summary: 'Éxito',
              detail: data.message,
              sticky: true
            });

          } else {
            this.messageService.add({
              severity: 'error',
              summary: 'Error',
              detail: data.message,
              sticky: true
            });
          }


        });

      }




    });


  }
  reenviarInforme(id?: number) {
    this.spinnerR = true

    this.genericService.post('reenviar-informe/' + id).subscribe({
      next: (response: any) => {

        if (response.email_status.success == true) {
          this.messageService.add({
            severity: 'success',
            summary: 'Éxito',
            detail: response.email_status.message,
            sticky: true
          });
          this.spinnerR = false
        } else {
          this.messageService.add({
            severity: 'error',
            summary: 'Error',
            detail: response.email_status.message,
            sticky: true
          });
          this.spinnerR = false
        }


      },
      error: (err) => {
        this.spinnerR = false

        this.messageService.add({
          severity: 'error',
          summary: 'Error',
          detail: err.error.message,
          sticky: true
        });

      },
      complete: () => {
        this.spinnerR = false



      }
    })
  }
  descargarInforme(url: string) {
    this.spinnerD = true;
    const ruta = environment.baseUrl+'descargar-archivo?ruta=' + url;
    this.http.get(ruta, { responseType: 'blob' as 'blob', withCredentials: true, }).subscribe({
      next: (blob: Blob) => {

        const a = document.createElement('a');
        const objectUrl = URL.createObjectURL(blob);
        a.href = objectUrl;
        a.download = url.split('/').pop() || 'informe.pdf';
        a.click();
        URL.revokeObjectURL(objectUrl);

        this.messageService.add({
          severity: 'success',
          summary: 'Éxito',
          detail: 'Archivo descargado correctamente',
          sticky: true
        });


      },
      error: (err) => {


        if (err.error.type === 'text/html') {
          const reader = new FileReader();
          reader.onload = () => {
            const errorText = reader.result as string;

            this.messageService.add({
              severity: 'error',
              summary: 'Error',
              detail: errorText,
              sticky: true
            });
            this.spinnerD = false
          };
          reader.readAsText(err.error);
        } else {

          this.messageService.add({
            severity: 'error',
            summary: 'Error',
            detail: 'Error desconocido al descargar el informe.',
            sticky: true
          });
          this.spinnerD = false
        }
      },
      complete: () => {
        this.spinnerD = false



      }
    });
  }
  descargarVariosInformes() {

    const fechaInicio = this.fechaDesdeFilter!.toISOString().split('T')[0];
    const fechaFin = this.fechaHastaFilter!.toISOString().split('T')[0];
    const params = new HttpParams()
      .set('fecha_inicio', fechaInicio)
      .set('fecha_fin', fechaFin)
      .set('cobertura', this.coberturaFilter);

    this.http.get(environment.baseUrl + 'informes/descargar-pdfs', {
      params: params,
      responseType: 'blob',
      withCredentials: true,
      observe: 'response'
    }).subscribe({
      next: (response) => {
        const blob = new Blob([response.body!], { type: 'application/zip' });

        // Extraer nombre del archivo del header si está disponible
        const contentDisposition = response.headers.get('Content-Disposition');
        let fileName = 'informes.zip';
        if (contentDisposition) {
          const match = contentDisposition.match(/filename="(.+)"/);
          if (match && match[1]) {
            fileName = match[1];
          }
        }

        // Forzar descarga en el navegador
        const link = document.createElement('a');
        link.href = window.URL.createObjectURL(blob);
        link.download = fileName;
        link.click();
        window.URL.revokeObjectURL(link.href);
        this.messageService.add({
          severity: 'success',
          summary: 'Éxito',
          detail: 'Archivos descargados correctamente',
          sticky: true
        });
      },
      error: (err) => {
        console.error('Error al descargar los informes:', err);

        this.messageService.add({
          severity: 'error',
          summary: 'Error',
          detail: err.error.message,
          sticky: true
        });
      }
    });
  }


}

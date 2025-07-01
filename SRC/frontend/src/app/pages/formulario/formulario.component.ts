import { Component, OnInit } from '@angular/core';

import { CommonModule } from '@angular/common';
import { FormsModule, ReactiveFormsModule } from '@angular/forms';
import { InputTextModule } from 'primeng/inputtext';
import { CalendarModule } from 'primeng/calendar';
import { ButtonModule } from 'primeng/button';
import { DropdownModule } from 'primeng/dropdown';
import { CardModule } from 'primeng/card';
import { RippleModule } from 'primeng/ripple';
import { FileSelectEvent, FileUpload, FileUploadModule } from 'primeng/fileupload';

import { DatePickerModule } from 'primeng/datepicker';
import { FluidModule } from 'primeng/fluid';
import { BrowserAnimationsModule } from '@angular/platform-browser/animations';
import { BrowserModule } from '@angular/platform-browser';
import { FileUploadEvent } from 'primeng/fileupload';
import { ToastModule } from 'primeng/toast';
import { ProgressBar, ProgressBarModule } from 'primeng/progressbar';
import { BadgeModule } from 'primeng/badge';
import { MessageService } from 'primeng/api';
import { SelectModule } from 'primeng/select';
import { AutoCompleteCompleteEvent, AutoCompleteModule } from 'primeng/autocomplete';
import { HeaderComponent } from '../../components/header/header.component';
import { TextareaModule } from 'primeng/textarea';
import { GenericService } from '../../services/generic.service';
import { IResponse } from '../../Interfaces/iresponse';
import { coberturas } from '../../Interfaces/coberturas';
@Component({
  selector: 'app-formulario',
  standalone: true,
  imports: [HeaderComponent,
    CommonModule,
    FormsModule,
    ReactiveFormsModule,
    InputTextModule,
    CalendarModule, ButtonModule, DropdownModule, CardModule, RippleModule, FileUploadModule,
    DatePickerModule, FluidModule, AutoCompleteModule, TextareaModule,
    FileUpload, BadgeModule, ProgressBarModule, ToastModule, SelectModule,

  ],
  providers: [MessageService],
  templateUrl: './formulario.component.html',
  styleUrl: './formulario.component.css'
})
export class FormularioComponent implements OnInit {
  fecha: Date | undefined; // Para el campo de fecha, es un objeto Date
  siNo: any[] | undefined;
  tipoEstudio: any[] | undefined;
  tipoTerapeutica: any[] = [];
  filteredTerapeuticaSuggestions: any[] = [];
  selectTipoTerapeutica: any;
  selectTipoEstudio: any;
  selectTerapeutica: any;
  selectBiopsia: any;
  fechaNacimiento!: Date; // Para la fecha de nacimiento
  uploadedFiles: File[] = [];
  totalSize: number = 0;
  totalSizePercent: number = 0;
  files = [];
  apellidoNombrePaciente!: string;
  edad!: number;
  numeroDocumento!: string; 
  numeroAfiliado!: string; 
  mailPaciente!: string; 
  medicoEnvia!: string; 
  motivoEstudio!: string;
  esofago!: string;
  estomago!: string;
  duodeno!: string;
  informeEstudio!: string; 
  conclusion!: string;
  cantidadFrascos!: number;
  tipoCobertura!: string;
  coberturas:coberturas[] = [];
  constructor(private messageService: MessageService, private genericService: GenericService) { }


  ngOnInit() {
    this.getCoberturas();
    this.tipoEstudio = [
      { name: 'VIDEOESOFAGASTRODUODENOSCOPIA', code: 'VEDA' },
      { name: 'VIDEOCOLONOSCOPIA', code: 'VCC' },

    ];
    this.selectTipoEstudio = this.tipoEstudio[0];
    this.siNo = [
      { name: 'SI',code: 1 },
      { name: 'NO', code: 0 },

    ];
    this.selectTerapeutica = this.siNo[0];
    this.selectBiopsia = this.siNo[0];
    this.tipoTerapeutica = [
      { name: 'Polipectomía/s' },
      { name: 'Mucosectomía' },
      { name: 'Dilatación con balón' },
      { name: 'Marcación' },
      { name: 'Tratamiento hemostático' },
      { name: 'Argón láser' },
    ]
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
  formatSize(bytes: number): string {
    if (bytes === 0) return '0 B';
    const k = 1024;
    const sizes = ['B', 'KB', 'MB', 'GB'];
    const i = Math.floor(Math.log(bytes) / Math.log(k));
    return parseFloat((bytes / Math.pow(k, i)).toFixed(2)) + ' ' + sizes[i];
  }

  onSelectedFiles(event: any) {
    for (let file of event.files) {
      this.totalSize += file.size;
    }
    this.updateTotalSizePercent();
  }

  onTemplatedUpload() {
    this.messageService.add({ severity: 'info', summary: 'Success', detail: 'Files Uploaded' });
  }

  uploadEvent(callback: Function) {
    callback(); // dispara la subida de archivos
    this.uploadedFiles = []; // podrías añadir los archivos si quieres
    this.totalSize = 0;
    this.updateTotalSizePercent();
  }

  choose(event: any, callback: Function) {
    callback(); // abre el explorador de archivos
  }

  onRemoveTemplatingFile(event: Event, file: File, callback: Function, index: number) {
    this.totalSize -= file.size;
    this.updateTotalSizePercent();
    callback(index);
  }

  updateTotalSizePercent() {
    const maxSize = 1024 * 1024; // 1MB en bytes
    this.totalSizePercent = Math.min((this.totalSize / maxSize) * 100, 100);
  }
  searchTerapeutica(event: AutoCompleteCompleteEvent) {
    let filtered: any[] = [];
    let query = event.query; // Lo que el usuario está escribiendo

    for (let i = 0; i < this.tipoTerapeutica.length; i++) {
      let terapeutica = this.tipoTerapeutica[i];
      // Filtra si el nombre de la opción contiene la query (sin importar mayúsculas/minúsculas)
      if (terapeutica.name.toLowerCase().includes(query.toLowerCase())) {
        filtered.push(terapeutica);
      }
    }
    // Asigna las sugerencias filtradas a la propiedad que el autocomplete usa
    this.filteredTerapeuticaSuggestions = filtered;
  }

  postInforme(): void{
    const formData = new FormData();
    formData.append('fecha', this.fecha ? this.fecha.toISOString().split('T')[0] : ''); // Formatear fecha a YYYY-MM-DD
    formData.append('tipo_informe', this.selectTipoEstudio?.code || ''); // 'VEDA' o 'VCC'
    formData.append('conclusion', this.conclusion || '');
    formData.append('efectuo_terapeutica', this.selectTerapeutica?.name === 'SI' ? '1' : '0');
    if (this.selectTerapeutica?.name === 'SI') {
      formData.append('tipo_terapeutica', this.selectTipoTerapeutica?.name || '');
    }
    formData.append('efectuo_biopsia', this.selectBiopsia?.name === 'SI' ? '1' : '0');
    if (this.selectBiopsia?.name === 'SI') {
      formData.append('fracos_biopsia', this.cantidadFrascos ? this.cantidadFrascos.toString() : '');
    }

    
    if (this.selectTipoEstudio?.code === 'VEDA') {
      formData.append('esofago', this.esofago || '');
      formData.append('estomago', this.estomago || '');
      formData.append('duodeno', this.duodeno || '');
      formData.append('informe', ''); 
    } else if (this.selectTipoEstudio?.code === 'VCC') {
      formData.append('informe', this.informeEstudio || '');
      formData.append('esofago', ''); 
      formData.append('estomago', '');
      formData.append('duodeno', '');
    }


    // Datos del Paciente (Frontend -> Backend)
    formData.append('nombre_paciente', this.apellidoNombrePaciente || '');
    formData.append('fecha_nacimiento_paciente', this.fechaNacimiento ? this.fechaNacimiento.toISOString().split('T')[0] : '');
    formData.append('edad', this.edad ? this.edad.toString() : '');
    formData.append('dni_paciente', this.numeroDocumento || '');
    // Nota: 'id_cobertura' en el backend. Necesitas mapear 'inputTipoCobertura' (string) a un ID si lo requiere.
    // Por ahora, lo enviaré como el string directamente, asumiendo que tu backend lo manejará o buscará.
    formData.append('id_cobertura', this.tipoCobertura || ''); // ¡Ojo aquí! Si el backend espera un ID, esto puede ser un problema.
    formData.append('numero_afiliado', this.numeroAfiliado || '');
    formData.append('mail_paciente', this.mailPaciente || '');
    formData.append('medico_envia_estudio', this.medicoEnvia || '');
    formData.append('motivo_estudio', this.motivoEstudio || '');


    // Añadir los archivos al FormData
    // Tu backend espera 'archivo[]', así que cada archivo debe ir con la clave 'archivo'
    this.uploadedFiles.forEach((file: File) => {
      formData.append('archivo[]', file, file.name);
    });

    // 2. Enviar los datos usando GenericService
    // Define el endpoint para tu API de carga de informe
    // Asegúrate de que tu GenericService.post esté preparado para enviar FormData
    // No necesitas establecer 'Content-Type': 'multipart/form-data'; el navegador lo hace automáticamente con FormData.
    this.genericService.post('informe/alta', formData).subscribe({
      next: (response: IResponse<any>) => {
        if (response.status === 'success') {
          this.messageService.add({
            severity: 'success',
            summary: 'Éxito',
            detail: response.message || 'Informe cargado exitosamente',
            life: 3000
          });
          console.log('Informe cargado:', response.data);
          // Opcional: limpiar el formulario o redirigir
          // this.resetForm();
          // this.router.navigate(['/lista-informes']);
        } else {
          this.messageService.add({
            severity: 'error',
            summary: 'Error',
            detail: response.message || 'Error al cargar informe',
            life: 3000
          });
          console.error('Error al cargar informe (respuesta no exitosa):', response);
        }
      },
      error: (err) => {
        console.error('Error en la solicitud de carga de informe:', err);
        let errorMessage = 'Ocurrió un error inesperado al cargar el informe.';
        if (err.error && err.error.message) {
          errorMessage = err.error.message;
        } else if (err.message) {
          errorMessage = err.message;
        }
        this.messageService.add({
          severity: 'error',
          summary: 'Error',
          detail: errorMessage,
          life: 5000
        });
      },
      complete: () => {
        console.log('Solicitud de carga de informe completada.');
      }
    });
  }
  
}

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

@Component({
  selector: 'app-formulario',
  standalone: true,
  imports: [HeaderComponent,
    CommonModule,
    FormsModule,
    ReactiveFormsModule,
    InputTextModule,
    CalendarModule, ButtonModule, DropdownModule, CardModule, RippleModule, FileUploadModule,
    DatePickerModule, FluidModule,AutoCompleteModule,
    FileUpload,BadgeModule,ProgressBarModule ,ToastModule,SelectModule,

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
  uploadedFiles: any[] = [];
  totalSize: number = 0;
  totalSizePercent: number = 0;
  files = [];
  constructor(private messageService: MessageService) {}
  ngOnInit() {
    this.tipoEstudio = [
        { name: 'VIDEOESOFAGASTRODUODENOSCOPIA', code: 'VEDA' },
        { name: 'VIDEOCOLONOSCOPIA', code: 'VCC' },
       
    ];
    this.siNo = [
      { name: 'SI' },
      { name: 'NO' },
     
  ];
  this.tipoTerapeutica = [
    {name:'Polipectomía/s'},
    {name:'Mucosectomía'},
    {name:'Dilatación con balón'},
    {name:'Marcación'},
    {name:'Tratamiento hemostático'},
    {name:'Argón láser'},
  ]
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
}

import { Component, OnInit, ViewChild } from '@angular/core';

import { CommonModule, DatePipe } from '@angular/common';
import { FormBuilder, FormControl, FormGroup, FormsModule, ReactiveFormsModule, Validators } from '@angular/forms';
import { InputTextModule } from 'primeng/inputtext';
import { CalendarModule } from 'primeng/calendar';
import { ButtonModule } from 'primeng/button';

import { CardModule } from 'primeng/card';
import { RippleModule } from 'primeng/ripple';
import { FileUploadModule } from 'primeng/fileupload';

import { DatePickerModule } from 'primeng/datepicker';
import { FluidModule } from 'primeng/fluid';

import { ToastModule } from 'primeng/toast';
import { ProgressBarModule } from 'primeng/progressbar';
import { BadgeModule } from 'primeng/badge';
import { MessageService } from 'primeng/api';
import { SelectModule } from 'primeng/select';
import { AutoCompleteCompleteEvent, AutoCompleteModule } from 'primeng/autocomplete';
import { HeaderComponent } from '../../components/header/header.component';
import { TextareaModule } from 'primeng/textarea';
import { GenericService } from '../../services/generic.service';
import { IResponse } from '../../Interfaces/iresponse';
import { coberturas } from '../../Interfaces/coberturas';
import { MessageModule } from 'primeng/message';
@Component({
  selector: 'app-formulario',
  standalone: true,
  imports: [HeaderComponent,
    CommonModule,
    FormsModule,
    ReactiveFormsModule,
    InputTextModule, MessageModule,
    CalendarModule, ButtonModule, CardModule, RippleModule, FileUploadModule,
    DatePickerModule, FluidModule, AutoCompleteModule, TextareaModule,
    BadgeModule, ProgressBarModule, ToastModule, SelectModule,

  ],
  providers: [MessageService],
  templateUrl: './formulario.component.html',
  styleUrl: './formulario.component.css'
})
export class FormularioComponent implements OnInit {
  totalSize!: number;
  totalSizePercent!: number;
  form!: FormGroup;
  tipoEstudio: any[] = [];
  coberturas: any[] = [];
  siNo: any[] = [];
  tipoTerapeutica: any[] = [];
  medicoslist: any[] = [];
  filteredTerapeuticaSuggestions: any[] = [];
  filteredMedicosSuggestions: any[] = [];
  uploadedFiles: any[] = [];
  enviando: boolean = false;

  constructor(private messageService: MessageService, private genericService: GenericService, private fb: FormBuilder) {
    this.form = this.fb.group({
      fecha: new FormControl(null, Validators.required),
      tipo_informe: new FormControl(null, Validators.required),


      nombre_paciente: new FormControl('', Validators.required),
      fecha_nacimiento_paciente: new FormControl(''),
      edad: new FormControl({ value: '', disabled: true }),


      dni_paciente: new FormControl('', Validators.required),
      id_cobertura: new FormControl(null, Validators.required),
      numero_afiliado: new FormControl(''),
      mail_paciente: new FormControl('', [Validators.required, Validators.email]),
      medico_envia_estudio: new FormControl(''),
      motivo_estudio: new FormControl(''),


      esofago: new FormControl(''),
      estomago: new FormControl(''),
      duodeno: new FormControl(''),
      informe: new FormControl(''),
      conclusion: new FormControl(''),


      efectuo_terapeutica: new FormControl(''),
      tipo_terapeutica: new FormControl(''),
      efectuo_biopsia: new FormControl(''),
      fracos_biopsia: new FormControl(''),
    });
  }


  ngOnInit() {



    this.getCoberturas();
    this.tipoEstudio = [
      { name: 'VIDEOESOFAGASTRODUODENOSCOPIA', code: 'VEDA' },
      { name: 'VIDEOCOLONOSCOPIA', code: 'VCC' },

    ];
    this.form.get('tipo_informe')?.setValue(this.tipoEstudio[0]);
    this.siNo = [
      { name: 'SI', code: 1 },
      { name: 'NO', code: 0 },

    ];
    this.form.get('efectuo_terapeutica')?.setValue(this.siNo[1]);
    this.form.get('efectuo_biopsia')?.setValue(this.siNo[1]);

    this.tipoTerapeutica = [
      { name: 'Polipectomía/s' },
      { name: 'Mucosectomía' },
      { name: 'Dilatación con balón' },
      { name: 'Marcación' },
      { name: 'Tratamiento hemostático' },
      { name: 'Argón láser' },
    ];
    this.medicoslist = [
      { name: "Manolizi juan manuel" },
      { name: "Gardella ana" },
      { name: "Trillo silvina" },
      { name: "Pardo Mariel" },
      { name: "Crespo marcelo" },
      { name: "Arinovich barbara" },
      { name: "Larraburu Alfredo" },
      { name: "Albamonte Mirta" },
      { name: "Galván daniel" },
      { name: "Baulos Gustavo" },
      { name: "Erlich Romina" },
      { name: "Cuesta maria Celia" },
      { name: "Roel José" },
      { name: "Dardanelli miguel" },
      { name: "Coqui ricardo" },
      { name: "Menéndez José" },
      { name: "Diana Estrin" }
    ];

    this.cargarDatos();
    this.form.valueChanges.subscribe(() => {
      this.guardarDatos();
    });

  }
  guardarDatos() {
    const formValue = this.form.getRawValue();
    localStorage.setItem('formularioDatos', JSON.stringify(formValue));
   

  }
  cargarDatos() {
    const saved = localStorage.getItem('formularioDatos');
    if (saved) {
      const values = JSON.parse(saved);


      if (values.tipo_informe) {
        const tipo = this.tipoEstudio.find(t => t.code === values.tipo_informe.code);
        values.tipo_informe = tipo || null;
      }

      if (values.id_cobertura) {
       values.id_cobertura = values.id_cobertura.toString();
      }

      if (values.efectuo_terapeutica) {
        const terapeutica = this.siNo.find(op => op.code === values.efectuo_terapeutica.code);
        values.efectuo_terapeutica = terapeutica || null;
      }

      if (values.efectuo_biopsia) {
        const biopsia = this.siNo.find(op => op.code === values.efectuo_biopsia.code);
        values.efectuo_biopsia = biopsia || null;
      }

      if (values.tipo_terapeutica) {
        const tipoT = this.tipoTerapeutica.find(t => t.name === values.tipo_terapeutica.name);
        values.tipo_terapeutica = tipoT || null;
      }

      if (values.medico_envia_estudio) {
        const medico = this.medicoslist.find(m => m.name === values.medico_envia_estudio.name);
        values.medico_envia_estudio = medico || null;
      }
      if (values.fecha) {
        values.fecha = new Date(values.fecha);
      }

      if (values.fecha_nacimiento_paciente) {
        values.fecha_nacimiento_paciente = new Date(values.fecha_nacimiento_paciente);
        this.calcularEdad();
      }


      this.form.patchValue(values);
      
    }
    

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
    this.uploadedFiles = [];
    this.totalSize = 0;

    for (let file of event.files) {
      this.uploadedFiles.push(file); // Store the file
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

    this.uploadedFiles.splice(index, 1);

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

      if (terapeutica.name.toLowerCase().includes(query.toLowerCase())) {
        filtered.push(terapeutica);
      }
    }

    this.filteredTerapeuticaSuggestions = filtered;
  }
  searchMedico(event: AutoCompleteCompleteEvent) {
    let filtered: any[] = [];
    let query = event.query; // Lo que el usuario está escribiendo

    for (let i = 0; i < this.medicoslist.length; i++) {
      let medicos = this.medicoslist[i];

      if (medicos.name.toLowerCase().includes(query.toLowerCase())) {
        filtered.push(medicos);
      }
    }

    this.filteredMedicosSuggestions = filtered;
  }

  postInforme(): void {
    this.enviando = true;
    const formData = new FormData();
    const formValues = this.form.value;

    const tipoInformeValue = formValues['tipo_informe'] && formValues['tipo_informe'].code ? formValues['tipo_informe'].code : null;
    const biopsia = formValues['efectuo_biopsia'] && formValues['efectuo_biopsia'].code;
    const terapeutica = formValues['efectuo_terapeutica'] && formValues['efectuo_terapeutica'].code;


    for (const key in formValues) {
      if (formValues.hasOwnProperty(key)) {
        let value = formValues[key];

        if (tipoInformeValue === 'VCC') {
          if (key === 'esofago' || key === 'estomago' || key === 'duodeno') {
            continue;
          }
        } else if (tipoInformeValue === 'VEDA') {
          if (key === 'informe') {
            continue;
          }
        }
        if (biopsia === 0) {
          if (key === 'fracos_biopsia') {
            continue;
          }
        }
        if (terapeutica === 0) {
          if (key === 'tipo_terapeutica') {
            continue;
          }
        }


        if (key === 'tipo_informe' && value && value.code) {
          formData.append(key, value.code);
        } else if (key === 'efectuo_terapeutica' && value && value.code !== undefined) {
          formData.append(key, value.code.toString());
        } else if (key === 'efectuo_biopsia' && value && value.code !== undefined) {
          formData.append(key, value.code.toString());
        } else if (key === 'medico_envia_estudio' && value && value.name) {
          formData.append(key, value.name);
        } else if (key === 'tipo_terapeutica' && value && value.name) {
          formData.append(key, value.name);
        } else if (key === 'fecha' && value instanceof Date) {

          const day = String(value.getDate()).padStart(2, '0');
          const month = String(value.getMonth() + 1).padStart(2, '0');
          const year = value.getFullYear();
          formData.append(key, `${year}-${month}-${day}`);
        } else if (key === 'fecha_nacimiento_paciente' && value instanceof Date) {
          const day = String(value.getDate()).padStart(2, '0');
          const month = String(value.getMonth() + 1).padStart(2, '0');
          const year = value.getFullYear();
          formData.append(key, `${year}-${month}-${day}`);
        }
        else if (value !== null && value !== undefined) {

          formData.append(key, value);
        }

      }
    }


    this.uploadedFiles.forEach((file: File) => {
      formData.append('archivo[]', file, file.name);
    });


    this.genericService.post('informe/alta', formData).subscribe({
      next: (response: IResponse<any>) => {
        if (response.status === 'success') {
          this.messageService.add({
            severity: 'success',
            summary: 'Éxito',
            detail: response.message || 'Informe cargado exitosamente',
            sticky: true,
          });
          this.form.reset();
          this.uploadedFiles = [];
          localStorage.removeItem('formularioDatos');
        } else {
          this.messageService.add({
            severity: 'error',
            summary: 'Error',
            detail: response.message || 'Error al cargar informe',
            sticky: true
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
          sticky: true
        });
      },
      complete: () => {
        console.log('Solicitud de carga de informe completada.');
        this.enviando = false;
      }
    });
  }

  calcularEdad() {
    const fecha = this.form.get('fecha_nacimiento_paciente')?.value
    if (fecha) {
      const hoy = new Date();
      const fechaNac = new Date(fecha);
      let edadCalculada = hoy.getFullYear() - fechaNac.getFullYear();
      const mes = hoy.getMonth() - fechaNac.getMonth();

      if (mes < 0 || (mes === 0 && hoy.getDate() < fechaNac.getDate())) {
        edadCalculada--;
      }
      return this.form.get('edad')?.setValue(edadCalculada);
    } else {
      return this.form.get('edad')?.setValue(null);
    }


  }
}

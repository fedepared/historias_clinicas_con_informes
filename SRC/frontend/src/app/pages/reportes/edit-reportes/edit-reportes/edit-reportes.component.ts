import { CommonModule } from '@angular/common';
import { Component, OnInit, ViewChild } from '@angular/core';
import { FormBuilder, FormControl, FormGroup, FormsModule, ReactiveFormsModule, Validators } from '@angular/forms';
import { MessageModule } from 'primeng/message';
import { ToastModule } from 'primeng/toast';
import { DatePickerModule } from 'primeng/datepicker';
import { InputTextModule } from 'primeng/inputtext';
import { AutoCompleteCompleteEvent, AutoCompleteModule } from 'primeng/autocomplete';
import { BadgeModule } from 'primeng/badge';
import { ButtonModule } from 'primeng/button';
import { CalendarModule } from 'primeng/calendar';
import { CardModule } from 'primeng/card';
import { FileUpload, FileUploadModule } from 'primeng/fileupload';
import { FluidModule } from 'primeng/fluid';
import { ProgressBarModule } from 'primeng/progressbar';
import { RippleModule } from 'primeng/ripple';
import { SelectModule } from 'primeng/select';
import { TextareaModule } from 'primeng/textarea';
import { MessageService } from 'primeng/api';
import { GenericService } from '../../../../services/generic.service';
import { coberturas } from '../../../../Interfaces/coberturas';
import { DynamicDialogConfig, DynamicDialogRef } from 'primeng/dynamicdialog';
import { Informe, InformeUpdate } from '../../../../Interfaces/informe';
import { IResponse } from '../../../../Interfaces/iresponse';
import { ImageModule } from 'primeng/image';
import { flatMap } from 'rxjs';
@Component({
  selector: 'app-edit-reportes',
  imports: [CommonModule,
    FormsModule,
    ReactiveFormsModule,
    InputTextModule, MessageModule,
    CalendarModule, ButtonModule, CardModule, RippleModule, FileUploadModule, ImageModule,
    DatePickerModule, FluidModule, AutoCompleteModule, TextareaModule,
    BadgeModule, ProgressBarModule, ToastModule, SelectModule,],
  templateUrl: './edit-reportes.component.html',
  styleUrl: './edit-reportes.component.css',
  providers: [MessageService]
})
export class EditReportesComponent implements OnInit {
  totalSize!: number;
  @ViewChild('fileUpload') fileUpload!: FileUpload;
  totalSizePercent!: number;
  form!: FormGroup;

  coberturas: any[] = [];
  siNo: any[] = [];
  tipoTerapeutica: any[] = [];
  medicoslist: any[] = [];
  filteredTerapeuticaSuggestions: any[] = [];
  filteredMedicosSuggestions: any[] = [];
  uploadedFiles: any[] = [];
  imagenesE: any[] = [];
  imagenesInicio: any[] = [];
  enviando: boolean = false;

  respuesta: any[] = [];




  constructor(private messageService: MessageService, private genericService: GenericService, private fb: FormBuilder, private dialogConfi: DynamicDialogConfig, private ref: DynamicDialogRef) {
    this.getByIdReporte(this.dialogConfi.data.id);
    this.getImagenes(this.dialogConfi.data.id);
    this.form = this.fb.group({
      fecha: new FormControl('', Validators.required),
      tipo_informe: new FormControl('', Validators.required),


      nombre_paciente: new FormControl({ value: '', disabled: true }, Validators.required),
      fecha_nacimiento_paciente: new FormControl(''),
      edad: new FormControl({ value: '', disabled: true }),


      dni_paciente: new FormControl('', Validators.required),
      id_cobertura: new FormControl('', Validators.required),
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


    this.siNo = [
      { name: 'SI', code: 1 },
      { name: 'NO', code: 0 },

    ];


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
  calcularEdad(fecha: Date) {

    if (fecha) {
      const hoy = new Date();
      const fechaNac = new Date(fecha);
      let edadCalculada = hoy.getFullYear() - fechaNac.getFullYear();
      const mes = hoy.getMonth() - fechaNac.getMonth();

      if (mes < 0 || (mes === 0 && hoy.getDate() < fechaNac.getDate())) {
        edadCalculada--;
      }
      this.form.get('edad')?.setValue(edadCalculada)

      return edadCalculada;
    } else {
      return this.form.get('edad')?.setValue(null);
    }


  }
  getByIdReporte(id: number) {
    this.genericService.get('informe/' + id).subscribe({
      next: (response: IResponse<any>) => {

        this.asignarValor(response.data);

      },
      error: (err) => {
        console.error('Error fetching informe:', err);

      }

    });
  }
  getImagenes(id: number) {
    this.genericService.get('informe/imagenes/' + id).subscribe({
      next: (response: any) => {

        this.imagenesE = response.imagenes;
        this.imagenesInicio = JSON.parse(JSON.stringify(response.imagenes))

      },
      error: (err) => {
        console.error('Error fetching informe:', err);

      }
    })
  }
  eliminarImagen(index: number) {

    this.imagenesE.splice(index, 1);

  }
  asignarValor(valor: any) {

    this.form.setValue({
      fecha: valor.fecha,
      tipo_informe: valor.tipo_informe,


      nombre_paciente: valor.nombre_paciente,
      fecha_nacimiento_paciente: new Date(valor.fecha_nacimiento_paciente),
      edad: Number(valor.edad),


      dni_paciente: valor.dni_paciente,
      id_cobertura: valor.id_cobertura,
      numero_afiliado: valor.numero_afiliado,
      mail_paciente: valor.mail_paciente,
      medico_envia_estudio: valor.medico_envia_estudio,
      motivo_estudio: valor.motivo_estudio,

      esofago: valor.esofago,
      estomago: valor.estomago,
      duodeno: valor.duodeno,
      informe: valor.informe,
      conclusion: valor.conclusion,

      efectuo_terapeutica: Number(valor.efectuo_terapeutica),
      tipo_terapeutica: valor.tipo_terapeutica,
      efectuo_biopsia: Number(valor.efectuo_biopsia),
      fracos_biopsia: Number(valor.fracos_biopsia)

    });




  }


  putReporte() {
    this.enviando = true;
    const payload: InformeUpdate = {};
    const formValues = this.form.value;
    const tipoInformeValue = formValues['tipo_informe'];
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



        if (key === 'efectuo_terapeutica' && value && value.code !== undefined) {
          payload[key] = value.code;
        } else if (key === 'efectuo_biopsia' && value && value.code !== undefined) {
          payload[key] = value.code;
        } else if (key === 'medico_envia_estudio' && value && value.name) {
          payload[key] = value.name;
        } else if (key === 'tipo_terapeutica' && value && value.name) {
          payload[key] = value.name;
        } else if (key === 'fecha_nacimiento_paciente' && value instanceof Date) {
          const day = String(value.getDate()).padStart(2, '0');
          const month = String(value.getMonth() + 1).padStart(2, '0');
          const year = value.getFullYear();
          payload[key] = `${year}-${month}-${day}`;

        } else if (value !== null && value !== undefined ) {

          (payload as any)[key] = value;
        }
        payload.edad = Number(this.calcularEdad(formValues['fecha_nacimiento_paciente']))

      }
    }

    this.genericService.put('informe/editar/' + this.dialogConfi.data.id, payload).subscribe({
      next: (response: IResponse<any>) => {
        this.respuesta.push(response);
       
      },
      complete: () => {
        console.log('Solicitud de edicion de informe completada.');
        if(this.uploadedFiles.length !== 0 || JSON.stringify(this.imagenesE) !== JSON.stringify(this.imagenesInicio)){

          this.putImagenes();
        }else{
          this.ref.close(this.respuesta)
        }
        

      }
    })

  }
 
  putImagenes() {
    this.enviando = true;


    const formData = new FormData();

    this.imagenesE.forEach((img: any) => {
      const file = this.base64ToFile(img.base64, img.nombre);
      formData.append('archivo[]', file);
    });
    this.uploadedFiles.forEach((file: File) => {
      formData.append('archivo[]', file, file.name)
    });

    this.genericService.post('informe/imagenes/update/' + this.dialogConfi.data.id, formData).subscribe({
      next: (response: IResponse<any>) => {
     
        this.respuesta.push(response);

      
        this.ref.close(this.respuesta)
      },
      complete: () => {
        console.log('Solicitud de edicion de informe completada.');
        

      }
    })
  }
  base64ToFile(base64: string, filename: string): File {
    const arr = base64.split(',');
    const mime = arr[0].match(/:(.*?);/)?.[1] || 'image/png';
    const bstr = atob(arr[1]);
    let n = bstr.length;
    const u8arr = new Uint8Array(n);
    while (n--) {
      u8arr[n] = bstr.charCodeAt(n);
    }
    return new File([u8arr], filename, { type: mime });
  }

  
}

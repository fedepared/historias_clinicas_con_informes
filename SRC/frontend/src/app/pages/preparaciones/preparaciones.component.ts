import { Component, OnInit } from '@angular/core';
import { SelectModule } from 'primeng/select';
import { CommonModule } from '@angular/common';
import { FormsModule } from '@angular/forms';
import { ButtonModule } from 'primeng/button';

import { TextareaModule } from 'primeng/textarea';
import { InputGroupModule } from 'primeng/inputgroup';
import { InputGroupAddonModule } from 'primeng/inputgroupaddon';
import { MessageModule } from 'primeng/message';
import { HeaderComponent } from '../../components/header/header.component';
import { GenericService } from '../../services/generic.service';
import { MessageService } from 'primeng/api';
import { ToastModule } from 'primeng/toast';

@Component({
  selector: 'app-preparaciones',
  imports: [HeaderComponent, MessageModule, InputGroupAddonModule, SelectModule, CommonModule, FormsModule, ButtonModule, TextareaModule, InputGroupModule, ToastModule, MessageModule,],
  templateUrl: './preparaciones.component.html',
  styleUrl: './preparaciones.component.css',
  providers: [MessageService]
})
export class PreparacionesComponent implements OnInit {


  tipoEnviar!: any[];
  selectTipoEnviar: any;
  tipoPreparacion!: any[];
  selectTPreparacion: any;
  preparacion!: any[];
  mail: any;
  estudioModificado!: any[];
  preparacionModif!: any[];
  desabilitado: boolean = false;

  constructor(private generictServce: GenericService, private messageService: MessageService) {

  }
  ngOnInit(): void {
    this.tipoEnviar = [
      { name: 'Enviar cuestionario', code: 1 },
      { name: 'Enviar preparacion', code: 2 },
      { name: 'Enviar cuestionario y preparacion', code: 3 },
    ];
    this.tipoPreparacion = [
      { name: 'Preparacion Colonica por la tarde', code: 'vcc_tarde' },
      { name: 'Preparacion Colonica por la mañana', code: 'vcc_manana' },
    ];
    this.selectTipoEnviar = this.tipoEnviar[0];



  }
  getPreparacioByID(tipo: any) {
    this.generictServce.get('preparaciones/tipo/' + tipo).subscribe({
      next: (response: any) => {

        this.preparacion = response.data
      }
    })
  }
  reset() {
    this.preparacion = [];
    this.selectTipoEnviar = this.tipoEnviar[0];
    this.mail = '';
    this.selectTPreparacion = '';


  }

  enviar() {
    if (this.selectTipoEnviar.code == 1) {
      this.eviarCuestionario();

    } else if (this.selectTipoEnviar.code == 2) {
      this.enviarPreparacion();
      
    } else {
      this.enviarPreparacion();
      this.eviarCuestionario();

    }



  }
  eviarCuestionario() {
    this.desabilitado = true;
    const mail = {
      email_destinatario: this.mail,
    };
    this.generictServce.post('email/send-cuestionario-word', mail).subscribe({
      next: (response: any) => {
        console.log('enviar cuestionario', response)
        if (response.status === true) {
          this.messageService.add({
            severity: 'success',
            summary: 'Éxito',
            detail: response.message,
            sticky: true
          });
          this.desabilitado = false;
          this.reset();
        } else {

          this.messageService.add({
            severity: 'error',
            summary: 'Error',
            detail: response.message,
            sticky: true
          });
          this.desabilitado = false;
        }

      }
    });

  }
  enviarPreparacion() {
    this.desabilitado = true;
    const json = {
      email_destinatario: this.mail,
      preparacion: this.preparacion

    }
    this.generictServce.post('preparaciones/generate-preparacion-pdf', json).subscribe({
      next: (response: any) => {
        console.log('generatePDF', response)
        if (response.success === true) {
          this.messageService.add({
            severity: 'success',
            summary: 'Éxito',
            detail: response.message,
            sticky: true
          });
          this.desabilitado = false;
          this.reset();
        } else {

          this.messageService.add({
            severity: 'error',
            summary: 'Error',
            detail: response.message,
            sticky: true
          });
          this.desabilitado = false;
        }


      }
    });

  }

  //por si acaso
  // toggleEdition(item:any){

  //   if(item.es_editable == '0'){
  //     item.es_editable = '1';
  //   }else{
  //     item.es_editable = '0';
  //   }
  //   console.log(item);
  // }
}

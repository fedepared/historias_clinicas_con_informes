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

@Component({
  selector: 'app-preparaciones',
  imports: [HeaderComponent, MessageModule, InputGroupAddonModule, SelectModule, CommonModule, FormsModule, ButtonModule, TextareaModule, InputGroupModule],
  templateUrl: './preparaciones.component.html',
  styleUrl: './preparaciones.component.css'
})
export class PreparacionesComponent implements OnInit {


  tipoEnviar!: any[];
  selectTipoEnviar: any;
  tipoPreparacion!: any[];
  selectTPreparacion: any;
  preparacion!: any[];
  mail: any;
  estudioModificado!: any[];

  constructor(private generictServce: GenericService) {

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
    const mail = {
      email_destinatario: this.mail,
    };
    const json = {
      email_destinatario: this.mail,
      tipo_preparacion:  this.selectTPreparacion.code,
      preparacion: this.preparacion

    }

    if (this.selectTipoEnviar.code == 1) {
      this.generictServce.post('email/send-cuestionario-word', mail).subscribe({
        next: (response: any) => {
          console.log('enviar cuestionario', response)

        }
      });
    } else if (this.selectTipoEnviar.code == 2) {
      this.generictServce.post('preparaciones/generate-preparacion-pdf', json).subscribe({
        next: (response: any) => {
          console.log('generatePDF', response)

        }
      });
    } else {
      this.generictServce.post('email/send-cuestionario-word', mail).subscribe({
        next: (response: any) => {
          console.log('enviar cuestionario', response)

        }
      });
       this.generictServce.post('preparaciones/generate-preparacion-pdf', json).subscribe({
        next: (response: any) => {
          console.log('generatePDF', response)

        }
      });

    }



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

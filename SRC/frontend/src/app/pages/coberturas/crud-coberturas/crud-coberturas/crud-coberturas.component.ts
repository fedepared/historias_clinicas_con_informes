import { Component, OnInit } from '@angular/core';
import { DynamicDialogConfig, DynamicDialogRef } from 'primeng/dynamicdialog';
import { ButtonModule } from 'primeng/button';
import { CommonModule } from '@angular/common';
import { FormsModule } from '@angular/forms';
import { InputTextModule } from 'primeng/inputtext';
import { GenericService } from '../../../../services/generic.service';
import { IResponse } from '../../../../Interfaces/iresponse';
import { MessageService } from 'primeng/api';
import { ToastModule } from 'primeng/toast';
@Component({
  selector: 'app-crud-coberturas',
  imports: [ButtonModule, CommonModule, InputTextModule,ToastModule,
    FormsModule],
  templateUrl: './crud-coberturas.component.html',
  styleUrl: './crud-coberturas.component.css',
  providers: [MessageService],
})
export class CrudCoberturasComponent implements OnInit {
  title!: string;
  nombreCobertura!: string;
  nombrebt!: string;
  enviando: boolean = false;

  constructor( private messageService: MessageService, private dialogConfi: DynamicDialogConfig, private ref: DynamicDialogRef, private genericService: GenericService) { }


  ngOnInit(): void {


    console.log('DATOS RECIBIDOS', this.dialogConfi.data)

    this.title = this.dialogConfi.data.title
    this.nombrebt = this.dialogConfi.data.title
    if (!this.dialogConfi.data.id) {
      this.nombreCobertura = '';
    } else {
      
      this.getCobertura()
    }



  }
  cerrarDialogo() {

    this.ref.close();
  }
  operacion() {
    let op = this.dialogConfi.data.title;
    switch (op) {
      case ('Agregar'): {
        this.postCobertura();
        break;
      }
      case ('Editar'): {
        this.editarCobertura();
        break;
      }
      default :{
        this.eliminarCobertura();
      }

    }
  }
  getCobertura(): void {
      this.genericService.get('cobertura/' + this.dialogConfi.data.id ).subscribe({
        next: (response: IResponse<any>) => {
          
          console.log('COBERTURA:', response)
          this.nombreCobertura = response.data.nombre_cobertura
  
        },
        error: (err) => {
          console.error('Error fetching coberturas:', err);
          
        }
      });
    }
  postCobertura() {
    const formData = {
      nombre_cobertura: this.nombreCobertura,

    };
    this.genericService.post('cobertura/alta',formData ).subscribe({
      next: (response: IResponse<any>) => {
        if (response.status === 'success') {
          this.messageService.add({
            severity: 'success',
            summary: 'Éxito',
            detail: response.message || 'Cobertura creada exitosamente',
            sticky: true
          });
          

        } else {
          this.messageService.add({
            severity: 'error',
            summary: 'Error',
            detail: response.message || 'Error al crear Cobertura',
            sticky: true
          });
          console.error('Error al crear Cobertura (respuesta no exitosa):', response);
        }
      },
      error: (err) => {
        console.error('Error en la solicitud de carga de Cobertura:', err);
        let errorMessage = 'Ocurrió un error inesperado al cargar la Cobertura.';
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
        console.log('Solicitud de carga de cobertura completada.');
      }
    })
  }
  editarCobertura(){
   
   const body = {
    nombre_cobertura: this.nombreCobertura
  };

  this.genericService.put(
    'informe/editar/' + this.dialogConfi.data.id,
    body,
    {
      headers: {
        'Content-Type': 'application/json'
      }
    }
  ).subscribe({
      next: (response: IResponse<any>) => {
        if (response.status === 'success') {
          this.messageService.add({
            severity: 'success',
            summary: 'Éxito',
            detail: response.message || 'Cobertura modificada exitosamente',
            sticky: true
          });
          

        } else {
          this.messageService.add({
            severity: 'error',
            summary: 'Error',
            detail: response.message || 'Error al modificar Cobertura',
            sticky: true
          });
          console.error('Error al modificar Cobertura (respuesta no exitosa):', response);
        }
      },
      error: (err) => {
        console.error('Error en la solicitud de modificar de Cobertura:', err);
        let errorMessage = 'Ocurrió un error inesperado al modificar la Cobertura.';
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
        console.log('Solicitud de modificacion de cobertura completada.');
      }
    
    })

  }
  eliminarCobertura(){
    
  }
}

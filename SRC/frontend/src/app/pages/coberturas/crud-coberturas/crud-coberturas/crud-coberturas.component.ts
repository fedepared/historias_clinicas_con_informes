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
  imports: [ButtonModule, CommonModule, InputTextModule, ToastModule,
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

  constructor(private messageService: MessageService, private dialogConfi: DynamicDialogConfig, private ref: DynamicDialogRef, private genericService: GenericService) { }


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
      default: {
        this.eliminarCobertura();
      }

    }
  }
  getCobertura(): void {
    this.genericService.get('cobertura/' + this.dialogConfi.data.id).subscribe({
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
    this.genericService.post('cobertura/alta', formData).subscribe({
      next: (response: IResponse<any>) => {

        this.ref.close(response)


      },
      error: (err) => {
        this.ref.close(err)

      },
      complete: () => {

      }
    })
  }
  editarCobertura() {

    const payload = {
      nombre_cobertura: this.nombreCobertura
    };
    console.log('NOMBRE COBERTURA', this.nombreCobertura)
    this.genericService.put('cobertura/editar/' + this.dialogConfi.data.id, payload).subscribe({
      next: (response: IResponse<any>) => {
        this.ref.close(response)
      },
      error: (err) => {
        this.ref.close(err)
        console.error('Error en la solicitud de modificar de Cobertura:', err);

      },
      complete: () => {
        console.log('Solicitud de modificacion de cobertura completada.');
      }

    })

  }
  eliminarCobertura() {

    this.genericService.delete('cobertura/borrar/'  + this.dialogConfi.data.id).subscribe({
      next: (response: IResponse<any>) => {
        this.ref.close(response)
      },
      error: (err) => {
        this.ref.close(err)
        console.error('Error en la solicitud de eliminar Cobertura:', err);

      },
      complete: () => {
        console.log('Solicitud de eliminacion de cobertura completada.');
      }

    })

  }
}

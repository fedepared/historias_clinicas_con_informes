import { Component, OnInit } from '@angular/core';
import { HeaderComponent } from "../../components/header/header.component";
import { TableModule } from 'primeng/table';
import { CommonModule } from '@angular/common';
import { ButtonModule } from 'primeng/button';
import { GenericService } from '../../services/generic.service';
import { coberturas } from '../../Interfaces/coberturas';
import { DialogService, DynamicDialogModule, DynamicDialogRef } from 'primeng/dynamicdialog';
import { MessageService } from 'primeng/api';
import { CrudCoberturasComponent } from './crud-coberturas/crud-coberturas/crud-coberturas.component';
import { Title } from '@angular/platform-browser';
import { TooltipModule } from 'primeng/tooltip';
import { Toast } from "primeng/toast";
@Component({
  selector: 'app-coberturas',
  imports: [HeaderComponent, TableModule, CommonModule, ButtonModule, DynamicDialogModule, TooltipModule, Toast],
  templateUrl: './coberturas.component.html',
  styleUrl: './coberturas.component.css',
  providers: [DialogService, MessageService],
})
export class CoberturasComponent implements OnInit {
  coberturas!: any[];
  ref: DynamicDialogRef | undefined;
  constructor(private genericService: GenericService, public dialogService: DialogService, public messageService: MessageService) { }
  ngOnInit(): void {
    this.getCoberturas();
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

  abrirdialigo(title: any, id?: number){

    this.ref = this.dialogService.open(CrudCoberturasComponent, {
      data: {title:title, id: id},
      closable: true
    })
      this.ref.onClose.subscribe((data: any) => {
        console.log('DATA',data)
      if (data) {
       
          if (data.status === 'success' || data.success === true) {
            this.messageService.add({
              severity: 'success',
              summary: 'Éxito',
              detail: data.message,
              sticky: true
            });
            this.getCoberturas();

          } else {
            this.messageService.add({
              severity: 'error',
              summary: 'Error',
              detail: data.error.message,
              sticky: true
            });
            this.getCoberturas();

          }

      }




    });
    
  }
}

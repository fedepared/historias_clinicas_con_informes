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
@Component({
  selector: 'app-coberturas',
  imports: [HeaderComponent, TableModule, CommonModule, ButtonModule, DynamicDialogModule],
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
        console.log('COBERTURAS:', this.coberturas)

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
    
  }
}

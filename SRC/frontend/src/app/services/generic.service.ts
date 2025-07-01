import { Injectable } from '@angular/core';
import { ApiHandlerService } from './api-handler.service';
import { environment } from '../../environments/environment';

@Injectable({
  providedIn: 'root'
})
export class GenericService {

  constructor(private apiService:ApiHandlerService) { }


  get(endpoint:string){
    return this.apiService.Get(`${environment.baseUrl}`+endpoint);
  }

  getAll(endpoint:string,params?:any){
    
    return this.apiService.GetAll(`${environment.baseUrl}`+endpoint,params);
  }


  post(endpoint:string,body?:any){
    return this.apiService.Post(`${environment.baseUrl}`+endpoint,body);
  }

  delete(endpoint:string){
    return this.apiService.Delete(`${environment.baseUrl}`+endpoint);
  }

  put(endpoint:string,body?:any,params?:any){
    return this.apiService.Put(`${environment.baseUrl}`+endpoint,body,params);
  }
}

import { Injectable } from '@angular/core';
import { IApiBaseActions, ParamsType } from '../Interfaces/iapi-base-actions';
import { HttpClient, HttpErrorResponse, HttpParams } from '@angular/common/http';
import { Observable, tap, throwError } from 'rxjs';
import { IResponse } from '../Interfaces/iresponse';

@Injectable({
  providedIn: 'root'
})
export class ApiHandlerService implements IApiBaseActions {

  constructor(public httpClient:HttpClient) { }

  Get(url:string,params?:ParamsType):Observable<IResponse<any>>{
    return this.httpClient
      .get<IResponse<any>>(url,{params:this.createParams(params)})
      .pipe(tap((x)=>this.handleResponse(x)))
  }

  GetAll(url:string,params?:ParamsType):Observable<IResponse<any>>{
    return this.httpClient
      .get<IResponse<any[]>>(url,{params:this.createParams(params)})
      .pipe(
        tap((x)=>this.handleResponse(x))
      )
  }

  Post(url:string,data:any,params?:ParamsType):Observable<IResponse<any>>{
    return this.httpClient
      .post<IResponse<any>>(url,data,{params:this.createParams(params)})
      .pipe(
          //catchError((err:HttpErrorResponse)=>{this.handleErrorResponse(err)})
        )
      
  }

  Delete(url: string, data?: any, params?: ParamsType):Observable<IResponse<any>>{
      return this.httpClient
        .delete<IResponse<any>>(url,{params:this.createParams(params)})
        .pipe(tap((x)=>this.handleResponse(x)))
  }

  Put(url: string, data: any, params?: ParamsType):Observable<IResponse<any>>{
     
    return this.httpClient
        .put<IResponse<any>>(url,data,{params:this.createParams(params)})
        .pipe(tap((x)=>{this.handleResponse(x)}))
  }

  handleResponse(response:any){
    //TO DO: manejar respuestas
    console.log(response);
  }

  handleErrorResponse(error:HttpErrorResponse):Observable<any>{
    
    return throwError(()=>new Error(error.message))
  }

  createParams(params?: ParamsType){
    let httpParams = new HttpParams();
    if(params){
      Object.entries(params).forEach(([key,value]) => {
        httpParams = httpParams.append(key,value);
      });
    }
    return httpParams;
  }
}

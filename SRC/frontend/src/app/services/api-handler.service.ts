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
  
  private getHttpOptions(params?: ParamsType) {
    return {
      params: this.createParams(params),
      withCredentials: true // <--- ¡AQUÍ ESTÁ EL CAMBIO CLAVE!
    };
  }
  Get(url: string, params?: ParamsType): Observable<IResponse<any>> {
    return this.httpClient
      .get<IResponse<any>>(url, this.getHttpOptions(params)) // Usa las opciones
      .pipe(tap((x) => this.handleResponse(x)));
  }

  GetAll(url: string, params?: ParamsType): Observable<IResponse<any>> {
    return this.httpClient
      .get<IResponse<any[]>>(url, this.getHttpOptions(params)) // Usa las opciones
      .pipe(
        tap((x) => this.handleResponse(x))
      );
  }

  Post(url: string, data: any, params?: ParamsType): Observable<IResponse<any>> {
    return this.httpClient
      .post<IResponse<any>>(url, data, this.getHttpOptions(params)) // Usa las opciones
      .pipe(
        // catchError((err:HttpErrorResponse)=>{this.handleErrorResponse(err)})
      );
  }

  Delete(url: string, data?: any, params?: ParamsType): Observable<IResponse<any>> {
    
    return this.httpClient
      .delete<IResponse<any>>(url, this.getHttpOptions(params)) // Usa las opciones
      .pipe(tap((x) => this.handleResponse(x)));
  }

  Put(url: string, data: any, params?: ParamsType): Observable<IResponse<any>> {
    return this.httpClient
      .put<IResponse<any>>(url, data, this.getHttpOptions(params)) // Usa las opciones
      .pipe(tap((x) => { this.handleResponse(x); }));
  }

  handleResponse(response: any) {
    // TO DO: manejar respuestas
    console.log(response);
  }

  handleErrorResponse(error: HttpErrorResponse): Observable<any> {
    return throwError(() => new Error(error.message));
  }

  createParams(params?: ParamsType) {
    let httpParams = new HttpParams();
    if (params) {
      Object.entries(params).forEach(([key, value]) => {
        // Asegúrate de que los valores sean strings para HttpParams, si no lo son ya
        httpParams = httpParams.append(key, String(value));
      });
    }
    return httpParams;
  }
}
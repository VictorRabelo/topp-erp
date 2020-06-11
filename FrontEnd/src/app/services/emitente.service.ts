import { Injectable } from "@angular/core";
import { HttpClient, HttpBackend } from '@angular/common/http';

import { environment } from '../../environments/environment';
import { Observable } from 'rxjs';

@Injectable({
   providedIn: 'root'
})
export class EmitenteService {
   public url = environment.url;

   constructor(
      public http: HttpClient, private httpBackend: HttpBackend
   ) { }

   getList(queryParams: any): Observable<any[]> {
      return this.http.get<any[]>(`${this.url}/emitente`, { params: queryParams });
   }

   create(data: any): Observable<any[]> {
      return this.http.post<any[]>(`${this.url}/emitente`, data);
   }

   update(data: any, id: number): Observable<any[]> {
      return this.http.put<any[]>(`${this.url}/emitente/${id}`, data);
   }

   getById(id: number): Observable<any[]> {
      return this.http.get<any[]>(`${this.url}/emitente/${id}`);
   }

   delete(id: number): Observable<any[]> {
      return this.http.delete<any[]>(`${this.url}/emitente/${id}`);
   }

   //consulta cnpj: https://www.receitaws.com.br/v1/cnpj/12750852000185
   getAdress(cep: number) {
      const http = new HttpClient(this.httpBackend);
      return http.get(`https://viacep.com.br/ws/${cep}/json`);
   }
   getCNPJ(cep: number) {
      const http = new HttpClient(this.httpBackend);
      return http.get(`https://www.receitaws.com.br/v1/cnpj/${cep}`);
   }

   //configurações
   getByIdConfig(id: number): Observable<any[]> {
      return this.http.get<any[]>(`${this.url}/emitente/config/${id}`);
   }

   createConfig(data: any): Observable<any[]> {
      return this.http.post<any[]>(`${this.url}/emitente/config`, data);
   }

   updateConfig(data: any, id: number): Observable<any[]> {
      return this.http.put<any[]>(`${this.url}/emitente/config/${id}`, data);
   }

   updateCertificate(data: any, id: number): Observable<any[]> {
      return this.http.put<any[]>(`${this.url}/emitente/certificate/${id}`, data);
   }

}